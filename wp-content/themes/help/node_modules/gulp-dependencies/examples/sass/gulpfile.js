var dependencies, gulp;

gulp = require('gulp');

dependencies = require('../../index');

module.exports = function() {
  gulp.task('build-sass', function() {
    return gulp.src("examples/sass/*.scss").pipe(dependencies({
      dependencies_file: "examples/sass/dependencies.json",
      basepath: "examples/sass",
      dest: "examples/sass",
      ext: ".css",
      match: /@import\s+'(.+)'/g,
      replace: function(f) {
        return f + ".scss";
      },
      debug: true
    }));
  });
  return gulp.task('watch-sass', function() {
    return gulp.watch("examples/sass/*.scss", ['build-sass']);
  });
};
