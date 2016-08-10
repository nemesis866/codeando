/************************************************
Servicio para login con email

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function LoginService (fncService, userService)
	{
		// Obtenemos los datos del formulario de contacto
		this.getUser = function (model)
		{
			function success (data)
			{
				// Verificamos si el objeto esta vacio
				if(data.length === 0){
					var msgError = 'El username y/o password son incorrectos, intente nuevamente';
					fncService.error(msgError);
				} else {
					var msgSuccess = 'Inicio de sesion con exito';

					localStorage.setItem('logged_in', JSON.stringify({
						time: new Date().getTime(),
						data: data
					}));

					fncService.success(msgSuccess);

					// Restauramos los campos del formulario
					model.userName = '';
					model.password = '';
				}
			}

			function error()
			{
				var msg = 'Error al conectar con el servidor intente nuevamente';
				fncService.error(msg);

				// Restauramos el campo password
				model.password = '';
			}

			// Validamos que ningun campo este vacio
			if(model.userName.length > 5 || model.password.length > 5){
				userService.query({
					username: model.userName,
					password: model.password
				}, success, error);
			} else {
				var msg = 'Los campos deben tener al menos 5 caracteres';
				fncService.error(msg);
			}
		};

		this.loggedIn = function ()
		{
			return !fncService.isEmpty(JSON.parse(localStorage.getItem('logged_in')));
		};

		this.logout = function ()
		{
			//localStorage.setItem('logged_in', undefined);
			localStorage.clear();
		};
	}

	angular
		.module('app')
			.service('loginService', [
				'fncService',
				'userService',
				LoginService
			]);
})();