/************************************************
Configuracion de gulp para realizar tareas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var gutil = require('gulp-util');
var minifyCss = require('gulp-minify-css');

var paths = {
  scripts: 'js/*.js',
  css: 'css/*.css',
};

// Comprime los archivos javascript
gulp.task('scripts', function() {
	gulp.src(paths.scripts)
	.pipe(uglify().on('error', gutil.log))
	.pipe(gulp.dest('js/min'))
});

// Comprime los archivos css
gulp.task('minify-css', function() {
  return gulp.src(paths.css)
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(gulp.dest('css/min'));
});

// Corre las tareas cada vez que hay cambios
gulp.task('watch', function() {
	gulp.watch(paths.scripts, ['scripts']);
	gulp.watch(paths.css, ['minify-css']);
});

// Corre todas las tareas
gulp.task('default', ['watch', 'scripts', 'minify-css']);