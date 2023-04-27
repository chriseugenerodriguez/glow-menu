#------------------------------------------------------------------------------

gulp    = require "gulp"
coffee  = require "gulp-coffee"
mocha   = require "gulp-mocha"
clean   = require "gulp-clean"


#------------------------------------------------------------------------------

gulp.task "clean", ->

  gulp
    .src  [ "**/*.js", "!node_modules/**" ]
    .pipe clean force: true


#------------------------------------------------------------------------------

gulp.task "build", ->

  gulp
    .src  [ "**/*.coffee", "!node_modules/**", "!gulpfile.coffee" ]
    .pipe coffee bare: true
    .pipe gulp.dest "."



#------------------------------------------------------------------------------

gulp.task "test", [ 'build' ], ->
  gulp
    .src "test/*.js"
    .pipe mocha reporter: "spec"


#------------------------------------------------------------------------------

gulp.task 'watch', ->
  gulp.watch [ "**/*.coffee", "!node_modules/**" ], [ "test" ]


#------------------------------------------------------------------------------
# samples

# build-sass
# watch-sass
require( "./examples/sass/gulpfile.coffee" )()

# build-haml
# watch-haml
require( "./examples/haml/gulpfile.coffee" )()

#------------------------------------------------------------------------------

gulp.task "default", [ "build" ]

