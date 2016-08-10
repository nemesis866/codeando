/************************************************
Recurso para contacto

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function ContactResource ($resource)
	{
		var url = 'http://api.dev/contact/';

		return $resource(url);
	}

	angular
		.module('app')
			.service('contactResource', [
				'$resource',
				ContactResource
			]);
})();