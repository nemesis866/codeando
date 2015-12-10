/************************************************
Configuración del router para la aplicación

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function config ($routeProvider, $locationProvider, FacebookProvider)
	{
		// Configuración de las rutas de la APP
		$routeProvider
			.when('/', {
				controller: 'mainController',
				controllerAs: 'vm',
				templateUrl: 'views/home.html'
			})
			.when('/cursos/', {
				controller: 'coursesController',
				controllerAs: 'vm',
				templateUrl: 'views/courses.html'
			})
			.when('/curso/:uri/:idCurso/', {
				controller: 'courseController',
				controllerAs: 'vm',
				templateUrl: 'views/course.html'
			})
			.when('/contacto/', {
				controller: 'contactController',
				controllerAs: 'vm',
				templateUrl: 'views/contact.html'
			})
			.otherwise({
				redirectTo: '/'
		    });

		// Quitamos el hashtag de las url
		if(window.history && window.history.pushState) {
	       $locationProvider.html5Mode(true);
		}

		// Configuramos la API de facebook
		FacebookProvider.init('346247455575774');
	}

	angular
		.module('app')
			.config(['$routeProvider', '$locationProvider', 'FacebookProvider', config]);
})();