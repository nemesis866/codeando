/************************************************
Controlador para el header de la aplicación

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function HeaderController($location, $scope, fbService, fncService, loginService)
	{
		var vm = this;

		// Tiempo de expiración de la session en minutos
		vm.minutos = 120;

		vm.sessionController = function ()
		{
			// Verificamos que la session no a expirado
			if(localStorage.getItem('logged_in')){
				var session = JSON.parse(localStorage.getItem('logged_in'));

				// Verificamos si el tiempo esta en el rango
				if((new Date().getTime() - (vm.minutos * 60 * 1000)) < session.time ){
					// Actualizamos el tiempo
					session.time = new Date().getTime();
					localStorage.setItem('logged_in', JSON.stringify(session));
				} else {
					// Cerramos la session
					var msg = 'Su sesion expiro, incie nuevamente';
					fncService.error(msg);
					vm.logout();
				}
			}
		};

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
			// Mostramos el boton de facebook
    		if(document.querySelector('div#login-footer p:nth-child(3)')){
				document.querySelector('div#login-footer p:nth-child(3)').style.opacity = '1';
				document.querySelector('div#login-footer p:nth-child(3)').style.visibility = 'visible';
			}

			fbService.logout();
			loginService.logout();
			vm.login();
		};

		// Ponemos en escucha
		$scope.$watch(vm.login);
		$scope.$watch(vm.sessionController);
	}

	angular
		.module('app')
			.controller('headerController', [
				'$location',
				'$scope',
				'fbService',
				'fncService',
				'loginService',
				HeaderController
			]);
})();