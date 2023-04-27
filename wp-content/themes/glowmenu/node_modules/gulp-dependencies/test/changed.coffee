#------------------------------------------------------------------------------

u             = require './utils'
gulp          = require 'gulp'
path          = require 'path'
expect        = require('chai').expect
dependencies  = require '../index'


#------------------------------------------------------------------------------

describe 'handling changed files', ->

  map =
    'a': ['b']
    'b': ['c']
    'c': []



  it 'not modified', (done) ->

    config = u.injectDependencies map,
      dest            : "output"
      update          : false
      insert_included : true

    gulp.src "test/null.test"

      .pipe u.insert [{ path: 'a' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 0

      .pipe u.insert [{ path: 'b' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 0

      .pipe u.insert [{ path: 'c' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 0

      .on 'finish', done




  it 'is modified', (done) ->

    config = u.injectDependencies map,
      dest              : -> "output"
      ext               : -> ".out"
      debug             : false
      update            : false
      insert_included   : false
      insert_dependents : true

    config.xfs.modified = (file) ->
      date = if path.basename( file ) == "b" then new Date 1000 else new Date 0
      return date

    gulp.src "test/null.test"

      .pipe u.insert [{ path: 'a' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 1
                .and.include.members [ 'a' ]

      .pipe u.insert [{ path: 'b' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 2
                .and.include.members [ 'a', 'b' ]

      .pipe u.insert [{ path: 'c' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 2
                .and.include.members [ 'a', 'b' ]

      .on 'finish', done


