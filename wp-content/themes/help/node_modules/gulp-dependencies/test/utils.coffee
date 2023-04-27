#------------------------------------------------------------------------------
# test helper functions

_             = require 'lodash'
util          = require 'gulp-util'
path          = require 'path'
through       = require 'through2'


#------------------------------------------------------------------------------

module.exports =

  # mock file interface

  xfs_null:
    read      : -> ""
    write     : ->
    modified  : -> new Date(0)

  # base dir

  basedir: process.cwd() + '/'

  # default settings

  defaultConfig:
    insert_dependents   : false
    insert_included     : false
    order_dependencies  : false
    recursive           : false
    clean               : true
    save                : false
    debug               : 0
    warn                : (msg) ->
    xfs                 : @xfs_null


  #------------------------------------------------------------------------------
  # pass dependencies back to test function when they are saved

  testDependencies: ( test, cfg ) ->

    xfs = _.defaults { write: (path, data) -> test JSON.parse data }, @xfs_null
    cfg = {} unless cfg?
    cfg = _.extend cfg, { save: true, xfs: xfs }
    cfg = _.defaults cfg, @defaultConfig


  #------------------------------------------------------------------------------
  # extract files from strem and call test function

  testStream: ( test ) ->

    files = []
    basedir = @basedir

    add = (file, unused, cb) ->
      this.push file
      files.push path.relative basedir, file.path
      cb()

    flush = (cb) ->
      test files
      cb()

    through.obj add, flush


  #------------------------------------------------------------------------------
  # inject dependencies - send json when the dependencies file is read

  injectDependencies: (map, config) ->

    read = (file) -> return if path.basename file == "dependencies.json" then JSON.stringify map else ""

    config = {} unless config?

    _.defaults config,
      xfs               : _.defaults { read: read }, @xfs_null
      clean             : false
      insert_dependents : true

    _.defaults config, @defaultConfig


  #------------------------------------------------------------------------------
  # test array A is the same as B

  hasOrder: (a,b) ->

    return false if a.length != b.length

    for ai,i in a
      return false if ai != b[i]

    return true


  #------------------------------------------------------------------------------
  # insert dummy files into stream

  insert: (files) ->

    basedir = @basedir

    remove_all    = (file, unused, cb) -> return cb()
    insert_files  = (cb) ->
      for file in files
        this.push new util.File
          path      : basedir + file.path
          contents  : new Buffer if file.contents? then file.contents else ""
      cb()

    through.obj remove_all, insert_files


