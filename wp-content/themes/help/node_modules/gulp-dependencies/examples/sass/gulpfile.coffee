gulp          = require 'gulp'
dependencies  = require '../../index'

module.exports = ->

  #------------------------------------------------------------------------------

  gulp.task 'build-sass', ->

    gulp

      # match all .scss files in the directory

      .src  "examples/sass/*.scss"

      # now pipe through the dependencies plugin to work out what's changed and what needs to be rebuilt

      .pipe dependencies

        # set directories for example

        dependencies_file : "examples/sass/dependencies.json"
        basepath          : "examples/sass"
        dest              : "examples/sass"

        # .scss files will be output as .css files (need this to check file modification times)

        ext               : ".css"

        # look for @import '...' and add _ and .scss to get the filename

        match             : /@import\s+'(.+)'/g
        replace           : (f) -> "_#{f}.scss"

        # turn on debugging for example

        debug             : true

      # process sass files here ...
      #.pipe sass()

  #------------------------------------------------------------------------------

  gulp.task 'watch-sass', ->

    # run build task if any .scss files change

    gulp.watch "examples/sass/*.scss", [ 'build-sass' ]

