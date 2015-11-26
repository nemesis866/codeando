/************************************************
Controlador para la ruta /cursos/

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function CoursesController($scope, courseService, loginService)
	{
		var vm = this;
		
		// Obtenemos el listado de cursos
		vm.courses = courseService.query();

		// Observamos cambios en el login
		vm.login = function ()
		{
			var bool = loginService.loggedIn();

			vm.class = {
				signup: { none: !bool},
				logout: { none: bool}
			};
		};

		// Ponemos en escucha
		$scope.$watch(vm.login);
	}

	angular
		.module('app')
			.controller('coursesController', [
				'$scope',
				'courseService',
				'loginService',
				CoursesController
			]);
})();