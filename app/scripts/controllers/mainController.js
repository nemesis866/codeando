/************************************************
Controlador principal de la aplicacion

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function MainController()
	{
		var vm = this;

		vm.msg = 'Hola codeando';
	}

	angular
		.module('app')
			.controller('mainController', MainController);
})();