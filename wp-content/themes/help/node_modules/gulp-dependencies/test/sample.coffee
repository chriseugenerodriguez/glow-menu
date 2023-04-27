#------------------------------------------------------------------------------

u             = require './utils'
gulp          = require 'gulp'
expect        = require('chai').expect
dependencies  = require '../index'


#------------------------------------------------------------------------------

describe 'all together', ->

  it 'TODO: putting it all together', (done) ->

    gulp.src "test/null.test"

      .on 'finish', done


