/************************************************
Controlador para la ruta /curso/:id_curso/

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function CourseController($routeParams, $scope, autorService, courseService, loginService)
	{
		var vm = this;

		// Cambiamos el titulo
		document.title = 'Curso | Codeando.org';
		
		// Obtenemos el listado de cursos
		courseService.query({
			idCurso: $routeParams.idCurso
		})
		.$promise.then(function (data){
			vm.course = data[0];

			document.title = data[0].titulo + '| Codeando.org';

			// Obtenemos los datos del autor
			autorService.query({
				idAutor: data[0].autor
			}).$promise.then(function (data){
				vm.autor = data[0];
			});

			// Obtenemos los capitulos del curso
		});

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
			.controller('courseController', [
				'$routeParams',
				'$scope',
				'autorService',
				'courseService',
				'loginService',
				CourseController
			]);
})();