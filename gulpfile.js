/************************************************
Configuracion de gulp para realizar tareas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

var gulp = require('gulp'),
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	minifyCss = require('gulp-minify-css'),
	connect = require('gulp-connect'),
	historyApiFallback = require('connect-history-api-fallback');

// Paths para los archivos
var paths = {
	css: './app/styles/**/*.css',
	html: './app/*.html',
	scripts: './app/scripts/**/.js'
};

// Tarea para recargar el navegador automaticamente
gulp.task('html', function (){
	gulp.src(path.html)
	.pipe(connect.reload());
});

// Comprime los archivos css
gulp.task('minify-css', function() {
  return gulp.src(paths.css)
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(gulp.dest('css/min'));
});

// Comprime los archivos javascript
gulp.task('scripts', function() {
	gulp.src(paths.scripts)
	.pipe(uglify().on('error', gutil.log))
	.pipe(gulp.dest('js/min'))
});

// Creamos un servidor web de pruebas
gulp.task('server', function (){
	connect.server({
		root: './app',
		port: 3000,
		livereload: true,
		middleware: function (connect, opt){
			return [historyApiFallback({})];
		}
	});
});

// Corre las tareas cada vez que hay cambios
gulp.task('watch', function() {
	gulp.watch(paths.css, ['minify-css']);
	gulp.watch(paths.html, ['html']);
	gulp.watch(paths.scripts, ['scripts']);
});

// Corre todas las tareas
gulp.task('default', ['watch', 'scripts', 'minify-css', 'server']);