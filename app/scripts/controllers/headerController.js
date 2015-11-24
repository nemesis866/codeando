/************************************************
Controlador para el header de la aplicación

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function HeaderController()
	{
		var vm = this;

		vm.nav = [
			{ title: 'Inicio', href: '/' },
			{ title: 'Cursos', href: '/cursos/' },
			{ title: 'Blog', href: 'http://blog.codeando.org' },
			{ title: 'Contacto', href: '/contacto/' }
		];
	}

	angular
		.module('app')
			.controller('headerController', HeaderController);
})();