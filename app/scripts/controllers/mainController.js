/************************************************
Controlador principal de la aplicacion

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function MainController($scope, fncService, loginService)
	{
		var vm = this;
		
		// Observamos cambios en el login
		vm.login = function ()
		{
			var bool = loginService.loggedIn();

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

		// Ponemos en escucha
		$scope.$watch(vm.login);
	}

	angular
		.module('app')
			.controller('mainController', [
				'$scope',
				'fncService',
				'loginService',
				MainController
			]);
})();