/************************************************
Controlador para la ruta /cursos/

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function CoursesController($http)
	{
		var vm = this;
		var url = 'http://api.dev/cursos/'

		// Obtenemos los cursos disponibles
		$http.get(url).success(function (resource){
			vm.courses = resource;
		});
	}

	angular
		.module('app')
			.controller('coursesController', ['$http', CoursesController]);
})();