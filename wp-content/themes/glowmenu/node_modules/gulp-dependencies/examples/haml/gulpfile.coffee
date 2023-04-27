gulp          = require 'gulp'
dependencies  = require '../../index'

module.exports = ->

  #------------------------------------------------------------------------------
  # define how we build haml files ...

  gulp.task 'build-haml', ->

    gulp

      # match all .haml files in the directory - note we don't have to handle .rb files here
      # as they will be picked up automatically when the files are scanned for dependencies

      .src  "examples/haml/*.haml"

      # now pipe through the dependencies plugin to work out what's changed and what needs to be rebuilt

      .pipe dependencies

        # set directories for example

        dependencies_file : "examples/haml/dependencies.json"
        basepath          : "examples/haml"
        dest              : "examples/haml"


        # haml files changed to .html files when process (need this to check file modification times)

        ext               : ".html"

        # find included files by looking for require_relative '...'

        match             : /require_relative\s+'(.+)'/g

        # add the '.rb' to get the filename

        replace           : (f) -> "#{f}.rb"


        # remove any .rb files from the output stream (not actually needed here but shows custom file handling)

        override:
          ".rb" : remove: true

        # turn on debug output for example

        debug             : true

      # we would process haml files here ...
      #.pipe haml()

  #------------------------------------------------------------------------------

  gulp.task 'watch-haml', ->

    # run build task if any .haml or .rb file changes

    gulp.watch [ "examples/haml/*.haml", "examples/haml/*.rb" ], [ 'build-haml' ]

