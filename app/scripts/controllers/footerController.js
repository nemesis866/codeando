/************************************************
Controlador principal de la aplicacion

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function FooterController($http)
	{
		var vm = this;
		var fecha = new Date();
		var url = 'http://api.dev/cursos/';

		// Menu del sitio web
		vm.menu = [
			{name: 'Inicio', href: '/'},
			{name: 'Cursos', href: '/plataforma/'},
			{name: 'Blog', href: 'http://blog.codeando.org'},
			{name: 'Contacto', href: '/contacto/'}
		];

		// Sitios de interes
		vm.interes = [
			{name: 'Programación Azteca', href: 'http://programacionazteca.mx'},
			{name: 'Github', href: 'https://github.com/programacionazteca'},
			{name: 'Youtube', href: 'http://youtube.com/channel/UCS5t7Ynr2sPoWgUfsYHrksA'}
		];

		// Datos generales
		vm.siteName = 'Codeando.org';
		vm.date = fecha.getFullYear();

		// Obtenemos los cursos disponibles
		$http.get(url).success(function (resource){
			vm.courses = resource;
		});
	}

	angular
		.module('app')
			.controller('footerController', ['$http', FooterController]);
})();