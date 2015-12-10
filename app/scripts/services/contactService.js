/************************************************
Servicio para procesar formulario de contacto

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function ContactService (contactResource, fncService)
	{
		// Obtenemos los datos del formulario de contacto
		this.setContact = function (model)
		{
			function success ()
			{
				// Limpiamos los campos del formulario
				model.name = '';
				model.email = '';
				model.asunto = '';
				model.comment = '';

				// Mostramos mensaje de exito
				var msgSuccess = 'Su mensaje se envio con exito.';
				fncService.success(msgSuccess);
			}

			function error()
			{
				// Mostramos mensaje de error
				var msg = 'Error al conectar con el servidor intente nuevamente';
				fncService.error(msg);	
			}

			// Validamos el email
			if(fncService.checkEmail(model.email)){
				// Validamos que ningun campo este vacio
				if(model.name.length > 5 || model.comment.length > 5){
					// Enviamos el recurso
					contactResource.save({
						name: model.name,
						email: model.email,
						asunto: model.asunto,
						comment: model.comment
					}, success, error);
				} else {
					var msgField = 'Los campos deben tener al menos 5 caracteres';
					fncService.error(msgField);
				}
			} else {
				var msgEmail = 'El email que ingreso no es valido, intente nuevamente.';
				fncService.error(msgEmail);
			}
		};
	}

	angular
		.module('app')
			.service('contactService', [
				'contactResource',
				'fncService',
				ContactService
			]);
})();