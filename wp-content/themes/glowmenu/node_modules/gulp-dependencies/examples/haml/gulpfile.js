var dependencies, gulp;

gulp = require('gulp');

dependencies = require('../../index');

module.exports = function() {
  gulp.task('build-haml', function() {
    return gulp.src("examples/haml/*.haml").pipe(dependencies({
      dependencies_file: "examples/haml/dependencies.json",
      basepath: "examples/haml",
      dest: "examples/haml",
      ext: ".html",
      match: /require_relative\s+'(.+)'/g,
      replace: function(f) {
        return f + ".rb";
      },
      override: {
        ".rb": {
          remove: true
        }
      },
      debug: true
    }));
  });
  return gulp.task('watch-haml', function() {
    return gulp.watch(["examples/haml/*.haml", "examples/haml/*.rb"], ['build-haml']);
  });
};
