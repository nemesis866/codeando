/************************************************
Servicio para obtener los usuarios de la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function UserService ($resource)
	{
		var url = 'http://api.dev/users/:username/:password/';

		return $resource(url);
	}

	angular
		.module('app')
			.service('userService', [
				'$resource',
				UserService
			]);
})();