/************************************************
Controlador para la ruta /contacto/

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function ContactController(contactService)
	{
		var vm = this;

		// Cambiamos el titulo
		document.title = 'Contactanos | Codeando.org';
		
		// Configuración del formulario
		vm.formConfig = {
			required: true
		};

		// Procesando formulario
		vm.setContact = function (model)
		{
			contactService.setContact(model);
		};
	}

	angular
		.module('app')
			.controller('contactController', [
				'contactService',
				ContactController
			]);
})();