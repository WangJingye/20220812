const gulp = require('gulp')
const del = require('del')
const uglify = require('gulp-uglify')
const rename = require('gulp-rename')
const cssmin = require('gulp-minify-css')
const autoprefixer = require('gulp-autoprefixer')
const jsonminify = require('gulp-jsonminify2')
const babel = require('gulp-babel')
const sass = require('gulp-sass')
const runSequence = require('run-sequence');
const spritesmith = require('gulp.spritesmith');

//task
gulp.task('default', function () {
    runSequence('exchange-scss2css', 'watch-handle');
});
//exchange
gulp.task('exchange-scss2css', function () {
    return gulp.src('./scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer([
            'iOS >= 8',
            'Android >= 4.1'
        ]))
        .pipe(rename((path) => path.extname = '.css'))
        .pipe(gulp.dest('./scss'));
});
//watch
gulp.task('watch-handle', function () {
    gulp.watch('./scss/*.scss', function () {
        runSequence('exchange-scss2css');
    });
});