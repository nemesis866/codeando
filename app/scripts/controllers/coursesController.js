/************************************************
Controlador para la ruta /cursos/

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function CoursesController($scope, coursesService, loginService)
	{
		var vm = this;

		// Cambiamos el titulo
		document.title = 'Cursos disponibles | Codeando.org';
		
		// Obtenemos el listado de cursos
		vm.courses = coursesService.query();

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
				'coursesService',
				'loginService',
				CoursesController
			]);
})();