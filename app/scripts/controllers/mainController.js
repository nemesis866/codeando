/************************************************
Controlador principal de la aplicacion

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function MainController($scope, fbService, fncService, loginService)
	{
		var vm = this;

		// Cambiamos el titulo
		document.title = 'Cursos de Programación | Codeando.org';
		
		// Observamos cambios en el login por email
		vm.login = function ()
		{
			// Obtenemos un valor booleano segun el status del login
			var bool = loginService.loggedIn();

			// Variables para mostrar/ocultar elementos segun el status
			vm.class = {
				form: { none: bool},
				img: { none: !bool}
			};
		};

		// Configuraciones para el formulario
		vm.formConfig = {
			required: true,
			pattern: '/^[a-zA-Z0-9]{1,20}$/',
		};

		// Login con email
		vm.getUser = function (model)
		{
			loginService.getUser(model);
		};

		// Login con facebook
		vm.getUserFb = function ()
		{
			var bool = fbService.login();

			if(bool){
				vm.user = fbService.me();

			}
		};

		// Ponemos en escucha 
		$scope.$watch(vm.login);
	}

	angular
		.module('app')
			.controller('mainController', [
				'$scope',
				'fbService',
				'fncService',
				'loginService',
				MainController
			]);
})();