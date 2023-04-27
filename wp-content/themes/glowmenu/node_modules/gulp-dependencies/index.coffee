#------------------------------------------------------------------------------
# TODO: support globs

_           = require 'lodash'
path        = require 'path'
util        = require 'gulp-util'
through     = require 'through2'
fs          = require 'fs'
PluginError = util.PluginError


# constants

PLUGIN_NAME = 'gulp-dependencies'

invocation_count = 0


#------------------------------------------------------------------------------
#

module.exports = ( options ) ->


  # process plugin options

  defaults =

    match               : /@require\s+(.+)\b/g      # default pattern to search for dependencies
    replace             : null
    override            : {}                        # override pattern by file extension

    dest                : null                      # destination directory
    ext                 : null                      # destination file extension

    dependencies_file   : "./dependencies.json"     # file to load/save dependencies map
    basepath            : process.cwd()             # base path to use for relative files

    insert_dependents   : true                      # dependent (child) files will be inserted into the stream (if not already)
    insert_included     : false                     # included files will by inserted into the stream (if not already)
    order_dependencies  : true                      # files in the stream will be ordered so parent files come before children
    recursive           : true                      # dependencies in included files will be searched recursively

    clean               : false                     # don't read dependencies - create fresh list
    save                : true                      # save depedencies file

    # debug and test

    debug               : false                     # print debug output
    update              : true

    # interfaces for override

    # error               : ( msg ) -> throw new PluginError PLUGIN_NAME, msg
    error               : ( msg ) -> util.log "[#{ invocation }] #{ util.colors.green PLUGIN_NAME } #{ util.colors.red msg }"
    warn                : ( msg ) -> util.log "[#{ invocation }] #{ util.colors.green PLUGIN_NAME } #{ util.colors.red 'WARNING: ' + msg }"
    print               : ( msg ) -> util.log "[#{ invocation }] #{ util.colors.green PLUGIN_NAME } #{ msg }" if config.debug

    xfs                 :
      read    : ( path )       -> fs.readFileSync path
      write   : ( path, data ) -> fs.writeFileSync path, data
      modified: ( path )       -> return fs.statSync( path ).mtime



  config = _.defaults options, defaults

  print = config.print
  error = config.error
  warn  = config.warn
  xfs   = config.xfs

  relative = (file) -> path.relative config.basepath, file

  #------------------------------------------------------------------------------
  # data

  dependencies  = {}
  dependents    = {}
  queue         = []
  filemap       = {}

  invocation = ++invocation_count

  warn "no destination directory - assuming all files have changed" unless config.dest?

  #------------------------------------------------------------------------------
  # add file to the queue & map

  AddToQueue = ( file, addToStream, addDependents ) ->

    try

      filemap[ file.path ] =
        file          : file
        path          : path.relative config.basepath, file.path
        included      : false
        addToStream   : addToStream
        addDependents : addDependents
        mtime         : xfs.modified file.path

      queue.push file

    catch e
      error e


  #------------------------------------------------------------------------------
  # insert a new file into the queue if we haven't seen it already

  Load = ( path, addToStream, addDependents, message, parentFile ) ->

    # exit if already included?
    return if path of filemap

    try

      file = new util.File
        path      : path
        contents  : new Buffer xfs.read path

      AddToQueue file, addToStream, addDependents

      print "added #{ util.colors.yellow relative path } #{ message } #{ util.colors.cyan relative parentFile.path }"

    catch e
      error e


  #------------------------------------------------------------------------------
  #

  CompareChanged = ( source, mtime ) ->

    # true if the source file doesn't exist

    return true unless source of filemap


    fd = filemap[ source ]

    # true if the source file has been modified later than the destination file

    return true if fd.mtime > mtime

    # otherwise check required files

    for dependency in dependencies[ fd.path ]
      dependency_path = path.resolve path.join config.basepath, dependency
      return true if CompareChanged dependency_path, mtime

    # no files have been modified later than the destination file

    return false


  HasChanged = ( file ) ->

    return true unless config.dest?

    # get last modified time for destination file

    mtime = null

    dest = if typeof config.dest == 'function' then config.dest file else config.dest
    dest = path.resolve process.cwd(), dest, file.relative

    if config.ext?
      ext = if typeof config.ext == 'function' then config.ext file else config.ext
      dest = util.replaceExtension dest, ext

    try
      mtime = xfs.modified dest
    catch
      return true # file doesn't exist?


    # compare modification time against all included files

    CompareChanged file.path, mtime



  #------------------------------------------------------------------------------
  # push files into output stream (optionally sorted by hierarchy)

  Output = (stream, file) ->

    fd = filemap[ file.path ]

    # pragma once

    return if fd.included
    fd.included = true


    # sort files by hierarchy

    if config.order_dependencies

      for dependency in dependencies[ fd.path ]
        dependency_path = path.resolve path.join config.basepath, dependency
        Output stream, filemap[ dependency_path ].file if dependency_path of filemap


    # now add this file

    if fd.addToStream

      if HasChanged file
        print "output #{ util.colors.yellow relative file.path }"
        stream.push file
      else
        print "skipping #{ util.colors.yellow relative file.path } (not changed)"

    else
      print "ignore #{ util.colors.yellow relative file.path }"


  #------------------------------------------------------------------------------
  # queue any dependent files

  AddDependents = ( file ) ->

    fd = filemap[ file.path ]
    return unless fd.addDependents and fd.path of dependents

    for dependent in dependents[ fd.path ]

      dependent_path = path.resolve path.join config.basepath, dependent
      Load dependent_path, true, true, "dependent on", file


  #------------------------------------------------------------------------------
  # queue any required files

  AddDependencies = ( file ) ->

    return unless config.insert_included or config.recursive

    fd = filemap[ file.path ]

    for dependency in dependencies[ fd.path ]
      dependency_path = path.resolve path.join config.basepath, dependency
      Load dependency_path, config.insert_included, false, "required by", file


  #------------------------------------------------------------------------------
  # parse file and extract dependencies

  Parse = ( file ) ->

    return if not config.update

    fd = filemap[ file.path ]

    dependencies[ fd.path ] = []

    match   = config.match
    replace = config.replace

    # handle custom file types

    ext = path.extname( file.path ).toLowerCase()

    if config.override? and ext of config.override

      custom = config.override[ ext ]

      if 'match' of custom
        match = custom.match
        replace = custom.replace
      else if 'replace' of custom
        replace = custom.replace

      fd.addToStream = not custom.remove if 'remove' of custom



    # parse file

    if match?

      print "parse #{ util.colors.cyan relative file.path }"

      while capture = match.exec file.contents.toString 'utf8'


        # get included file

        include = capture[ 1 ]
        include = replace include if replace?


        # resolve to absolute path

        include_path = path.resolve path.join path.dirname( file.path ), include


        # add to dependencies

        include_rel = relative include_path

        unless _.contains dependencies[ fd.path ], include_rel
          dependencies[ fd.path ].push include_rel
          print "#{ util.colors.cyan relative file.path } requires #{ util.colors.cyan include_rel }"


    else

      print "skipping #{ relative file.path } - no match"



  #------------------------------------------------------------------------------
  # whizz through all files and queue them for processing

  Transform = (file, unused, cb) ->

      if file.isNull()
        this.push file

      else if file.isStream()
        return error "Streaming not supported"

      else
        print "input " + util.colors.yellow relative file.path
        AddToQueue file, true, config.insert_dependents

      cb()


  #------------------------------------------------------------------------------
  # now do the real work ...

  Flush = (cb) ->

    # read dependencies file

    unless config.clean

      try

        print "read " + util.colors.cyan config.dependencies_file

        dependencies = JSON.parse xfs.read config.dependencies_file

        # invert dependencies to give us a list of dependants

        for file, includes of dependencies
          for include in includes
            dependents[ include ] = [] unless dependents[ include ]?
            dependents[ include ].push file

      catch

        print util.colors.red( "failed to read " ) + util.colors.cyan( config.dependencies_file )



    # parse files and add dependents / dependencies

    stream = []

    while queue.length > 0

      file  = queue.pop()

      Parse file
      AddDependents file
      AddDependencies file

      stream.push file


    # push files down stream

    Output this, file for file in stream



    # save dependencies

    if config.save
      print "save " + util.colors.cyan config.dependencies_file
      xfs.write config.dependencies_file, JSON.stringify dependencies, null, 4

    cb()


  through.obj Transform, Flush
