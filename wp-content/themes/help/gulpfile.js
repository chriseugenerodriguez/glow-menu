// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var jshint = require('gulp-jshint'),
    sass = require('gulp-sass'),
    gutil = require('gulp-util'),
    dependencies = require('gulp-dependencies'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    minifyCss = require('gulp-minify-css'),
    header = require("gulp-header"),
    sourcemaps = require('gulp-sourcemaps'),
    tinypng = require('gulp-tinypng'),
    rimraf = require('gulp-rimraf'),
    bump = require('gulp-bump'),
    browserSync = require("browser-sync").create(),
    reload = browserSync.reload,
    newer = require('gulp-newer'),
    notify = require('gulp-notify'),

    input = {
        'css': 'assets/styles/*/*.scss',
        'js': 'assets/scripts/*.js',
        'img': 'assets/images/*/**.{png,jpeg,jpg}',
        'fonts': 'assets/fonts/*/**'
    },

    url = 'localhost',

    output = {
        'css': 'dist/styles',
        'js': 'dist/scripts',
        'img': 'dist/images',
        'fonts': 'dist/fonts'
    },

    pkg = require('./package.json'),
    banner = ['/*',
        ' * @name <%= pkg.name %>',
        ' * @version <%= pkg.version %>',
        ' * @desc <%= pkg.description %>',
        ' * @author <%= pkg.author %>',
        ' * @license <%= pkg.license %>',
        ' */',
        ''
    ].join('\n');

// Scss
gulp.task('css', function() {
    return gulp.src('assets/styles/main.scss')
        .pipe(sass())
        .pipe(minifyCss({ keepSpecialComments: 0 }))
        .pipe(rename('main.css'))
        .pipe(header(banner, { pkg: pkg }))
        //.pipe(sourcemaps.write())
        .pipe(gulp.dest(output.css))
        .pipe(reload({ stream: true })) // Inject Styles when min style file is created
        .pipe(notify({ message: 'CSS task complete', onLast: true }))
});

// Js
gulp.task('js', function() {
    return gulp.src('assets/scripts/init.js')
        .pipe(sourcemaps.init())
        .pipe(concat('init.js'))
        .pipe(uglify())
        .pipe(jshint())
        .pipe(jshint.reporter('default'))
        .pipe(rename('main.js'))
        .pipe(header(banner, { pkg: pkg }))
        //.pipe(sourcemaps.write())
        .pipe(gulp.dest(output.js))
        .pipe(notify({ message: 'JS task complete', onLast: true }));
});

// Img
gulp.task('images', function() {
    gulp.src(input.img)
        .pipe(newer(output.img))
        .pipe(tinypng('WdYtsLKwucIcoQHlDEFHbeB2Lf-q3l7c'))
        .pipe(gulp.dest(output.img))
        .pipe(notify({ message: 'IMG task complete', onLast: true }));
});

// Fonts
gulp.task('fonts', function() {
    return gulp.src([input.fonts])
        .pipe(newer(input.fonts))
        .pipe(gulp.dest(output.fonts))
        .pipe(notify({ message: 'Fonts task complete', onLast: true }));
});

// Version
gulp.task('version', function() {
    return gulp.src(['./package.json'])
        .pipe(bump({ type: process.argv[3] ? process.argv[3].substr(2) : 'patch' }))
        .pipe(gulp.dest('./'));
});

// Watch
gulp.task('watch', ['version'], function(callback) {
    var files = [
        '**/*.php',
        '**/*.{png,jpg,gif}'
    ];
    browserSync.init(files, {
        proxy: url,
        injectChanges: true
    });

    gulp.watch(input.img, ['images']);
    gulp.watch(input.fonts, ['fonts']);
    gulp.watch(input.css, ['css']);
    gulp.watch(input.js, ['js', browserSync.reload]);
});
