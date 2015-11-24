/************************************************
Configuración del router para la aplicación

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function config ($routeProvider, $locationProvider)
	{
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
			.when('/contacto/', {
				controller: 'contactController',
				controllerAs: 'vm',
				templateUrl: 'views/contact.html'
			})
			.otherwise({
				redirectTo: '/'
		    });

		if(window.history && window.history.pushState) {
	       $locationProvider.html5Mode(true);
	   }
	}

	angular
		.module('app')
			.config(['$routeProvider', '$locationProvider', config]);
})();