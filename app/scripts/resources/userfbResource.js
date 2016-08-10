/************************************************
Servicio para obtener los usuarios de la plataforma
desde facebook

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function UserfbResource ($resource)
	{
		var url = 'http://api.dev/users-fb/';

		return $resource(url, {}, {
  			'save': { method:'POST', isArray:true }
  		});
	}

	angular
		.module('app')
			.service('userfbResource', [
				'$resource',
				UserfbResource
			]);
})();