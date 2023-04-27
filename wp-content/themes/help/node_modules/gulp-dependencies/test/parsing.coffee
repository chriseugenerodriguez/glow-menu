#------------------------------------------------------------------------------
# basic file parsing

u             = require './utils'
_             = require 'lodash'
gulp          = require 'gulp'
expect        = require('chai').expect
dependencies  = require '../index'


describe 'parsing tests', ->

  #------------------------------------------------------------------------------

  it 'basic file parsing', (done) ->

    data =
    {

      ra: "@require a.test"

      raa: """
        @require a.test
        @require a.test
        """

      mixed: """
        header
        // some stuff
        require notthis.test
        @require this.test
        // @require andthis.test //
        footer
        """

      none: """
        header
        // some stuff
        require notthis.test
        footer
        """
    }


    # test

    gulp.src "test/null.test"

      # simple parse

      .pipe u.insert [{ path: 'child.test', contents: "@require parent.test" }]
      .pipe dependencies u.testDependencies ( dependencies ) ->

            expect dependencies
              .to.have.property 'child.test'
              .that.is.an 'array'
              .and.include 'parent.test'
              .and.length 1


      # no match

      .pipe u.insert [{ path: 'a.test', contents: data.none }]
      .pipe dependencies u.testDependencies ( dependencies ) ->

            expect dependencies
              .to.have.property 'a.test'
              .that.is.an 'array'
              .and.length 0

      # multiple requires

      .pipe u.insert [{ path: 'a.test', contents: data.raa }]
      .pipe dependencies u.testDependencies ( dependencies ) ->

            expect dependencies
              .to.not.have.property 'child.test'

            expect dependencies
              .to.have.property 'a.test'
              .that.is.an 'array'
              .and.to.include 'a.test'
              .and.length 1


      # mixing it up

      .pipe u.insert [{ path: 'a.test', contents: data.mixed }]
      .pipe dependencies u.testDependencies ( dependencies ) ->

            expect dependencies
              .to.have.property 'a.test'
              .that.is.an 'array'
              .that.is.length 2
              .and.to.include 'this.test', 'andthis.test'
              .and.not.to.include 'notthis.test'


      # mulitple files

      .pipe u.insert [{ path: 'a.test', contents: data.ra }, { path: 'b.test', contents: data.ra }]
      .pipe dependencies u.testDependencies ( dependencies ) ->

            expect dependencies
              .to.have.all.keys 'a.test', 'b.test'

            expect dependencies
              .to.have.property 'a.test'
              .that.is.an 'array'
              .and.to.include 'a.test'
              .and.length 1

            expect dependencies
              .to.have.property 'b.test'
              .that.is.an 'array'
              .and.to.include 'a.test'
              .and.length 1


      .on 'finish', done


  #------------------------------------------------------------------------------
  # custom patterns by file extension

  it 'custom file parsing', (done) ->

    haml = """
    require_relative 'config'
    - require 'utils'
    """

    js = """
    // #require 'lib.js'
    // #require '/another/lib.js'
    // #require '../somefile.js'
    #include "stdio.h"
    @import "bootstrap"
    require_relative 'config'
    """

    files = [
      { path: 'main.cpp', contents: '#include "stdio.h"' }
      { path: 'a.js',     contents: js }
      { path: 'b.haml',   contents: haml }
      { path: 'c.scss',   contents: '@import "bootstrap"' }
      { path: 'x.ignore', contents: "require_relative 'config'" }
    ]


    config =

      match   : /(?:require|require_relative)\s+'(.+)'/g
      replace : (file) -> "#{ file }.rb"

      override:
        ".cpp":
          match   : /#\s*include\s+"(.+)"/g

        ".js":
          match   : /#require\s+'(.+)'/g

        ".scss":
          match   : /^\s*@import\s+"(.*)"/g
          replace : (file) -> "#{ file }.scss"

        ".ignore":
          match   : null


    test = (dependencies) ->

      expect dependencies
        .to.have.all.keys _.pluck files, 'path'

      expect dependencies
        .to.have.property 'main.cpp'
        .that.is.an 'array'
        .and.to.include 'stdio.h'
        .and.length 1

      expect dependencies
        .to.have.property 'a.js'
        .that.is.an 'array'
        .and.length 3
        .and.to.include.members [ 'lib.js', 'another/lib.js', '../somefile.js' ]

      expect dependencies
        .to.have.property 'a.js'
        .that.is.an 'array'
        .and.length 3
        .and.to.include.members [ 'lib.js', 'another/lib.js', '../somefile.js' ]
        .and.not.to.include.members [ 'stdio.h', 'config', 'config.rb' ]

      expect dependencies
        .to.have.property 'b.haml'
        .that.is.an 'array'
        .and.length 2
        .and.to.include.members [ 'config.rb', 'utils.rb' ]

      expect dependencies
        .to.have.property 'c.scss'
        .that.is.an 'array'
        .and.length 1
        .and.to.include 'bootstrap.scss'

      expect dependencies
        .to.have.property 'x.ignore'
        .that.is.an 'array'
        .and.length 0


    # run test

    gulp.src "test/null.test"

      .pipe u.insert files
      .pipe dependencies u.testDependencies test, config
      .on 'finish', done



  #------------------------------------------------------------------------------
  # custom base path
  #

  it 'custom base path', (done) ->

    gulp.src "test/null.test"

      .pipe u.insert [
        { path: 'a.test',       contents: "@require build/b.test" }
        { path: 'test/a.test',  contents: "@require x/b.test" }
        { path: 'build/a.test', contents: "@require ../build/b.test" }
      ]

      .pipe dependencies u.testDependencies(

        ( dependencies ) ->

            expect dependencies
              .to.have.property "a.test"  # build/a.test
              .that.is.an 'array'
              .and.length 1
              .and.to.include "b.test"

            expect dependencies
              .to.have.property "../a.test" # a.test
              .that.is.an 'array'
              .and.length 1
              .and.to.include "b.test"

            expect dependencies
              .to.have.property "../test/a.test"  # test/a.test
              .that.is.an 'array'
              .and.length 1
              .and.to.include "../test/x/b.test"
        ,
        {
          basepath: "#{ u.basedir }/build/"
        }
      )

      .on 'finish', done

