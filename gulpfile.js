/************************************************
Configuracion de gulp para realizar tareas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

var gulp = require('gulp'),
	// Tareas de compresion
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	minifyCss = require('gulp-minify-css'),
	concat = require('gulp-concat'),
	// Servidor local
	connect = require('gulp-connect'),
	historyApiFallback = require('connect-history-api-fallback'),
	// Inyector de dependencias
	inject = require('gulp-inject'),
	wiredep = require('wiredep').stream,
	// Manejamos errores en archivos javascript
	jshint = require('gulp-jshint'),
	stylish = require('jshint-stylish');

// Paths para los archivos
var paths = {
	css: './app/styles/**/*.css',
	cssMin: './app/css/**/*.css',
	html: './app/**/*.html',
	scripts: './app/scripts/**/*.js',
	scriptsMin: './app/js/**/*.js'
};

// Tarea para recargar el navegador automaticamente
gulp.task('html', function (){
	gulp.src(paths.html)
	.pipe(connect.reload());
});

// Injectamos los archivos js y css propios
gulp.task('inject', ['wiredep'], function (){
	var sources = gulp.src([paths.scriptsMin, paths.cssMin], {read: false});

	gulp.src('index.html', {
		cwd: './app'
	})
	.pipe(inject(sources, {
		read: false,
		ignorePath: '/app'
	}))
	.pipe(gulp.dest('./app'));
});

// Injectamos dependencias instaladas con bower
gulp.task('wiredep', function (){
	return gulp.src('index.html', {
		cwd: './app'
	})
	.pipe(wiredep({
		directory: './app/vendor',
		read: false,
		onError: function (err){
			console.log(err.code);
		}
	}))
	.pipe(gulp.dest('./app'));
});

// Comprime los archivos css
gulp.task('minify-css', function() {
  return gulp.src(paths.css)
    .pipe(minifyCss({
    	compatibility: 'ie8'
    }))
    .pipe(gulp.dest('./app/css'));
});

// Comprime los archivos javascript
gulp.task('scripts', function() {
	gulp.src(paths.scripts)
	.pipe(concat('build.js'))
	.pipe(uglify().on('error', gutil.log))
	.pipe(gulp.dest('./app/js'))
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

// Mostramos los errores javascript en consola
gulp.task('lint', function (){
	return gulp.src(paths.scripts)
	.pipe(jshint('.jshintrc'))
	.pipe(jshint.reporter('jshint-stylish'))
	.pipe(jshint.reporter('fail'));
});

// Corre las tareas cada vez que hay cambios
gulp.task('watch', function() {
	gulp.watch([paths.html], ['html']);
	gulp.watch([paths.scripts], ['scripts', 'inject', 'lint']);
	gulp.watch(['./gulpfile.js'], ['lint']);
	gulp.watch([paths.css], ['inject', 'minify-css']);
	gulp.watch(['./bower.json'], ['wiredep']);
});

// Corre todas las tareas
gulp.task('default', ['server', 'scripts', 'minify-css', 'inject', 'watch']);