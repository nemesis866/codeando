/************************************************
Servicio para obtener un curso de la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

(function (){
	'use strict';

	function CourseService ($resource)
	{
		var url = 'http://api.dev/cursos/:idCurso/';

		return $resource(url);
	}

	angular
		.module('app')
			.service('courseService', [
				'$resource',
				CourseService
			]);
})();