/************************************************
Configuracion de gulp para realizar tareas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

var gulp = require('gulp'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	minifyCss = require('gulp-minify-css');

var paths = {
  scripts: 'public_html/js/*.js',
  css: 'public_html/css/*.css'
};

// Comprime los archivos javascript
gulp.task('scripts', function() {
	gulp.src(paths.scripts)
	.pipe(concat('build.js'))
	.pipe(uglify().on('error', gutil.log))
	.pipe(gulp.dest('./public_html/js/min'))
});

// Comprime los archivos css
gulp.task('minify-css', function() {
  return gulp.src(paths.css)
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(gulp.dest('public_html/css/min'));
});

// Corre las tareas cada vez que hay cambios
gulp.task('watch', function() {
	gulp.watch(paths.scripts, ['scripts']);
	gulp.watch(paths.css, ['minify-css']);
});

// Corre todas las tareas
gulp.task('default', ['watch', 'scripts', 'minify-css']);