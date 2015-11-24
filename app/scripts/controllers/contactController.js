/************************************************
Controlador para la ruta /contacto/

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function ContactController()
	{
		var vm = this;
		
		vm.msg = 'Página de contacto';
	}

	angular
		.module('app')
			.controller('contactController', ['$http', ContactController]);
})();