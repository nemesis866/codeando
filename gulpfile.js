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
	// Servidor local
	connect = require('gulp-connect'),
	historyApiFallback = require('connect-history-api-fallback'),
	// Inyector de dependencias
	inject = require('gulp-inject'),
	wiredep = require('wiredep').stream;

// Paths para los archivos
var paths = {
	css: './app/styles/**/*.css',
	cssMin: './app/css/**/*.css',
	html: './app/*.html',
	scripts: './app/scripts/**/*.js',
	scriptsMin: './app/js/**/*.js'
};

// Tarea para recargar el navegador automaticamente
gulp.task('html', function (){
	gulp.src(paths.html)
	.pipe(connect.reload());
});

// Injectamos los archivos js y css propios
gulp.task('inject', function (){
	var sources = gulp.src([paths.scriptsMin, paths.cssMin], {read: false});

	gulp.src('index.html', {cwd: './app'})
	.pipe(inject(sources, {
		read: false,
		ignorePath: '/app'
	}))
	.pipe(gulp.dest('./app'));
});

// Comprime los archivos css
gulp.task('minify-css', function() {
  return gulp.src(paths.css)
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(gulp.dest('./app/css'));
});

// Comprime los archivos javascript
gulp.task('scripts', function() {
	gulp.src(paths.scripts)
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

// Corre las tareas cada vez que hay cambios
gulp.task('watch', function() {
	gulp.watch(paths.css, ['minify-css']);
	gulp.watch(paths.html, ['html']);
	gulp.watch(paths.scripts, ['scripts']);
	gulp.watch([paths.scripts], ['inject']);
	gulp.watch([paths.css], ['inject']);
	gulp.watch(['./bower.json'], ['wiredep']);
});

// Injectamos dependencias instaladas con bower
gulp.task('wiredep', function (){
	gulp.src('./app/index.html')
	.pipe(wiredep({
		directory: './app/vendor'
	}))
	.pipe(gulp.dest('./app'));
});

// Corre todas las tareas
gulp.task('default', ['watch', 'scripts', 'minify-css', 'server', 'inject', 'wiredep']);