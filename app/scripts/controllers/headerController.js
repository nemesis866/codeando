/************************************************
Controlador para el header de la aplicación

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function HeaderController($location, $scope, loginService)
	{
		var vm = this;

		// Urls del menu de navegación
		vm.nav = [
			{ title: 'Inicio', href: '/' },
			{ title: 'Cursos', href: '/cursos/' },
			{ title: 'Blog', href: 'http://blog.codeando.org' },
			{ title: 'Contacto', href: '/contacto/' }
		];

		// Seleccionamos en enlace con la ruta actual
		vm.active = function (path)
		{
			return $location.path() === path;
		};

		// Observamos cambios en el login
		vm.login = function ()
		{
			var bool = loginService.loggedIn();

			vm.class = {
				signup: { none: bool},
				logout: { none: !bool}
			};
		};

		// Cerramos sesion
		vm.logout = function (){
			loginService.logout();
			vm.login();
		};

		// Ponemos en escucha
		$scope.$watch(vm.login);
	}

	angular
		.module('app')
			.controller('headerController', [
				'$location',
				'$scope',
				'loginService',
				HeaderController
			]);
})();