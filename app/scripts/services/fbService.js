/************************************************
Servicio para login con Facebook

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function FbService (Facebook, fncService, userfbResource)
	{
		this.login = function(){
			// Ocultamos el boton de facebook
			if(document.querySelector('div#login-footer p:nth-child(3)')){
				document.querySelector('div#login-footer p:nth-child(3)').style.opacity = '0';
				document.querySelector('div#login-footer p:nth-child(3)').style.visibility = 'hidden';
			}

	    	// From now on you can use the Facebook service just as Facebook api says
	    	Facebook.login(function (response){
	        	// Do something with response.
	        	if(response.status === 'connected') {
	        		Facebook.api('/me', function (response){
	        			// Obtenemos los datos del usuario desde la API
	        			userfbResource.save({
	        				data: response
	        			}, success, error);
			      	});
	        	} else {
	        		// Mostramos el boton de facebook
	        		if(document.querySelector('div#login-footer p:nth-child(3)')){
						document.querySelector('div#login-footer p:nth-child(3)').style.opacity = '1';
						document.querySelector('div#login-footer p:nth-child(3)').style.visibility = 'visible';
					}
	        	}
	    	});
	    };

	    this.logout = function (){
            Facebook.logout(function (response){
                console.log(response.status);
            });
        };

	    // Función para checar el status de la conexión
	    this.getLoginStatus = function() {
	      	Facebook.getLoginStatus(function(response) {
	        	if(response.status === 'connected') {
	          		return true;
	        	} else {
	        		return false;
	        	}
	      	});
	    };

	    // Función para traer datos desde facebook
	    this.me = function() {
	    	Facebook.api('/me', function(response) {
	        	return response;
	      	});
	    };

	    // Error al solicitar datos a la API
	    function error ()
		{
			// Mostramos mensaje de error
			var msg = 'Error al conectar con el servidor intente nuevamente';
			fncService.error(msg);
		}

	    // Exito al solicitar datos a la API
	    function success (data)
	    {
	    	// Creamos la sesion con el tiempo y los datos del usuario
	    	localStorage.setItem('logged_in', JSON.stringify({
				time: new Date().getTime(),
				data: JSON.stringify(data)
			}));

	    	// Mostramos mensaje de exito
			var msgSuccess = 'Inicio de sesion con exito';
			fncService.success(msgSuccess);
	    }
	}

	angular
		.module('app')
			.service('fbService', [
				'Facebook',
				'fncService',
				'userfbResource',
				FbService
			]);
})();