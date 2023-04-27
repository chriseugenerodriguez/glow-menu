#------------------------------------------------------------------------------

u             = require './utils'
gulp          = require 'gulp'
expect        = require('chai').expect
dependencies  = require '../index'

#------------------------------------------------------------------------------

describe 'handling dependencies', ->

  map =

    # a <- b <- c

    'a': ['b']
    'b': ['c']
    'c': []

    # tree

    #    +- n11 --- n21
    #    |
    # r -+
    #    |       +- n22
    #    +- n13 -+
    #            +- n23 - n33

    'r'  : []

    'n11': ['r']
    'n13': ['r']

    'n21': ['n11']
    'n22': ['n13']
    'n23': ['n13']

    'n33': ['n23']

    # diamond inheritance:

    #     +- d10 -+
    # d0 -+       + - d2
    #     +- d11 -+

    'd0' : []
    'd10': [ 'd0' ]
    'd11': [ 'd0' ]
    'd2' : [ 'd10', 'd11' ]


    # circular dependencies

    # c1 -- c2
    #   \  /
    #    c3

    'c1': ['c2']
    'c2': ['c3']
    'c3': ['c1']


  #------------------------------------------------------------------------------
  # adding dependent files

  it 'adding dependents', (done) ->

    config = u.injectDependencies map

    gulp.src "test/null.test"

      # a <- b <- c

      .pipe u.insert [{ path: 'a' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 1
                .and.include.members [ 'a' ]
                .and.not.include.members [ 'b', 'c' ]

      .pipe u.insert [{ path: 'b' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 2
                .and.include.members [ 'a', 'b' ]
                .and.not.include 'c'

      .pipe u.insert [{ path: 'c' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'a', 'b', 'c' ]


      # not in map

      .pipe u.insert [{ path: 'z' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 1
                .and.include.members [ 'z' ]

      .pipe u.insert [{ path: 'z' },{ path: 'b' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'z', 'a', 'b' ]


      # fun with trees

      .pipe u.insert [{ path: 'n13' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 4
                .and.to.include.members [ 'n13', 'n22', 'n23', 'n33' ]


      # diamond inheritance: d0 -> { d10, d11 } -> d2

      .pipe u.insert [{ path: 'd0' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 4
                .and.include.members [ 'd0', 'd10', 'd11', 'd2' ]

      .pipe u.insert [{ path: 'd10' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 2
                .and.include.members [ 'd10', 'd2' ]

      .pipe u.insert [{ path: 'd2' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 1
                .and.include.members [ 'd2' ]

      # circular dependencies

      .pipe u.insert [{ path: 'c1' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'c1', 'c2', 'c3' ]

      .pipe u.insert [{ path: 'c2' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'c1', 'c2', 'c3' ]

      .pipe u.insert [{ path: 'c3' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'c1', 'c2', 'c3' ]

      .on 'finish', done


  #------------------------------------------------------------------------------
  # insert included files

  it 'insert includes', (done) ->

    config = u.injectDependencies map, { insert_included: true, update: false, insert_dependents: false }

    gulp.src "test/null.test"

      # a <- b <- c

      .pipe u.insert [{ path: 'a' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'a', 'b', 'c' ]

      .pipe u.insert [{ path: 'b' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 2
                .and.include.members [ 'b', 'c' ]
                .and.not.include.members [ 'a' ]

      .pipe u.insert [{ path: 'c' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 1
                .and.include.members [ 'c' ]

      # diamond

      .pipe u.insert [{ path: 'd2' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 4
                .and.include.members [ 'd0', 'd10', 'd11', 'd2' ]

      .pipe u.insert [{ path: 'd10' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 2
                .and.include.members [ 'd0', 'd10' ]

      # tree

      .pipe u.insert [{ path: 'n33' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 4
                .and.include.members [ 'r', 'n13', 'n23', 'n33' ]

      .pipe u.insert [{ path: 'n22' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'r', 'n13', 'n22' ]

      .pipe u.insert [{ path: 'n22' }, { path: 'n21' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 5
                .and.include.members [ 'r', 'n11', 'n21', 'n22', 'n13' ]

      # circular

      .pipe u.insert [{ path: 'c1' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'c1', 'c2', 'c3' ]

      .pipe u.insert [{ path: 'c2' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.include.members [ 'c1', 'c2', 'c3' ]

      .on 'finish', done


  #------------------------------------------------------------------------------
  # recursive

  it 'recursively parse required files', (done) ->

    # same as 'u.insert included' except doesn't add to stream

    config = u.injectDependencies map, { recursive: true, update: false, insert_dependents: false }

    gulp.src "test/null.test"

      # a <- b <- c

      .pipe u.insert [{ path: 'a' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 1
                .and.include.members [ 'a' ]


      .on 'finish', done


  #------------------------------------------------------------------------------
  # dependency ordering

  it 'dependency ordering', (done) ->

    config = u.injectDependencies map, { update: false, order_dependencies: true, insert_included: true, insert_dependents: false }

    gulp.src "test/null.test"


      # basic check

      .pipe u.insert [{ path: 'a' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.to.satisfy (list) -> u.hasOrder list, [ 'c', 'b', 'a' ]

      .pipe u.insert [{ path: 'b' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 2
                .and.to.satisfy (list) -> u.hasOrder list, [ 'c', 'b' ]


      # u.insert unordered

      .pipe u.insert [{ path: 'b' },{ path: 'a' },{ path: 'c' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.to.satisfy (list) -> u.hasOrder list, [ 'c', 'b', 'a' ]


      # tree

      .pipe u.insert [{ path: 'n23' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.to.satisfy (list) -> u.hasOrder list, [ 'r', 'n13', 'n23' ]

      .pipe u.insert [{ path: 'n23' },{ path: 'n11' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 4
                .and.to.satisfy (list) ->
                  u.hasOrder( list, [ 'r', 'n11', 'n13', 'n23' ] ) or
                  u.hasOrder( list, [ 'r', 'n13', 'n11', 'n23' ] )

      .pipe u.insert [{ path: 'n23' },{ path: 'n11' },{ path: 'n33' },{ path: 'n21' },{ path: 'r' },{ path: 'n13' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 6
                .and.to.satisfy (list) ->
                  u.hasOrder( list, [ 'r', 'n11', 'n21', 'n13', 'n23', 'n33' ] ) or
                  u.hasOrder( list, [ 'r', 'n13', 'n11', 'n21', 'n23', 'n33' ] )


      # diamond

      #     +- d10 -+
      # d0 -+       + - d2
      #     +- d11 -+


      .pipe u.insert [{ path: 'd10' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 2
                .and.to.satisfy (list) -> u.hasOrder list, [ 'd0', 'd10' ]


      .pipe u.insert [{ path: 'd2' },{ path: 'd10' },{ path: 'd0' },{ path: 'd11' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 4
                .and.to.satisfy (list) ->
                  u.hasOrder( list, [ 'd0', 'd10', 'd11', 'd2' ] ) or
                  u.hasOrder( list, [ 'd0', 'd11', 'd10', 'd2' ] )


      .pipe u.insert [{ path: 'd2' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 4
                .and.to.satisfy (list) ->
                  u.hasOrder( list, [ 'd0', 'd10', 'd11', 'd2' ] ) or
                  u.hasOrder( list, [ 'd0', 'd11', 'd10', 'd2' ] )


      # circular

      # c1 -- c2
      #   \  /
      #    c3

      .pipe u.insert [{ path: 'c1' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.to.satisfy (list) -> u.hasOrder list, [ 'c3', 'c2', 'c1' ]

      .pipe u.insert [{ path: 'c2' }]
      .pipe dependencies config
      .pipe u.testStream (files) ->
              expect files
                .to.be.length 3
                .and.to.satisfy (list) -> u.hasOrder list, [ 'c1', 'c3', 'c2' ]


      .on 'finish', done

