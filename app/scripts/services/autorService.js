/************************************************
Servicio para obtener datos sobre autor de curso

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function AutorService ($resource)
	{
		var url = 'http://api.dev/autor/:idAutor/';

		return $resource(url);
	}

	angular
		.module('app')
			.service('autorService', [
				'$resource',
				AutorService
			]);
})();