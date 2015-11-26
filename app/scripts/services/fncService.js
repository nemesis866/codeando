/************************************************
Servicio para funciones repetitivas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function FncService ()
	{
		// Muestra un error en pantalla
		this.error = function (msg)
		{
			document.querySelector('.error').innerHTML = msg;
			document.querySelector('.error').style.transform = 'translateY(0)';
			document.querySelector('.error').style.webkitTransform = 'translateY(0)';
			setTimeout(function(){
				document.querySelector('.error').innerHTML = '';
				document.querySelector('.error').style.transform = 'translateY(-60px)';
				document.querySelector('.error').style.webkitTransform = 'translateY(-60px)';
			}, 3000);
		};

		// Verifica si un objeto esta vacio
		this.isEmpty = function isEmpty(obj)
		{
		    // null and undefined are "empty"
		    if(obj === null){
		    	return true;
		    }

		    // Assume if it has a length property with a non-zero value
		    // that that property is correct.
		    if(obj.length > 0){
		    	return false;
		    }
		    if(obj.length === 0){
		    	return true;
		    }

		    // Otherwise, does it have any properties of its own?
		    // Note that this doesn't handle
		    // toString and valueOf enumeration bugs in IE < 9
		    for(var key in obj){
		        if(hasOwnProperty.call(obj, key)){
		        	return false;
		        }
		    }

		    return true;
		};

		// Muestra un suceso en pantalla
		this.success = function (msg)
		{
			document.querySelector('.success').innerHTML = msg;
			document.querySelector('.success').style.transform = 'translateY(0)';
			document.querySelector('.success').style.webkitTransform = 'translateY(0)';
			setTimeout(function(){
				document.querySelector('.success').innerHTML = '';
				document.querySelector('.success').style.transform = 'translateY(-60px)';
				document.querySelector('.success').style.webkitTransform = 'translateY(-60px)';
			}, 3000);
		};
	}

	angular
		.module('app')
			.service('fncService', FncService);
})();