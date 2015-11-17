/************************************************
Archivo javascript para administrar los archivos

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

function files(control)
{
	// Al arrastrar los documentos
	document.getElementById('content_dis_files').ondragover = function (e)
	{
		// Permitimos copiar
		e.dataTransfer.dropEffect = 'move';
		this.style.border = '1px dashed #000';

		// Permitimos soltar
		return false;
	}

	// Al finalizar soltar y arrastrar
	document.getElementById('content_dis_files').ondragleave = function (e)
	{
		this.style.border = '1px solid #Ff8124';
		this.style.borderBottom = '3px solid #cc7013';
	}

	document.getElementById('discucion_form').ondragover = function (e){
		return false;
	};
	document.getElementById('discucion_form').ondrop = function (e){
		// Evitamos la propagacion
		e.preventDefault();
		e.stopPropagation();	
	};
	// Al soltar los documentos
	document.getElementById('content_dis_files').ondrop = function (e)
	{
		// Evitamos la propagacion
		e.preventDefault();
		e.stopPropagation();

		// Variables de reemplazo para los archivos
		var codigo = Array("<", ">","){","(",")","'","\t","javascript:","array:","text/html");
		var replace = Array("[-","-]",") {","&-","-&",'"',"&nbsp;&nbsp;&nbsp;&nbsp;","javascript :","array :","text/htm");

		// Obtenemos el total de archivos transferidos
		var count = e.dataTransfer.files.length;

		for(var i = 0; i < count; i++){
			var name = e.dataTransfer.files[i].name; // Nombre del archivo
			var size = e.dataTransfer.files[i].size / 1000; // Tamaño del archivo

			// Si el archivo es mayor a 5 kb no lo guardamos
			if(size <= 10){
				// Obtenemos la extension del archivo
				var data = name.split('.');
				var index = data.length - 1;
				var ext = data[index];

				// Mostramos imagen de cargando
				document.getElementById('content_dis_cargando').innerHTML = '<img src="/img/cargando1.gif">';

				// Verificamos que sea una extension valida
				if(ext == 'html' || ext == 'js' || ext == 'css' || ext == 'php' || ext == 'json' || ext == 'c' || ext == 'cpp' || ext == 'h'){
					// Obtenemos el contenido del archivo
					var archivo = e.dataTransfer.files[i];
					var lector = new FileReader();
					var encoding = 'UTF-8';

					// Si es un archivo .c o .cpp cambiamos el encoding
					if(ext == 'c' || ext == 'cpp'){
						encoding = 'ISO-8859-1';
					}

					// Lo cargamos en formato texto y con su codificacion
					lector.readAsText(archivo, encoding);

					// Mostramos el progreso del archivo
					lector.addEventListener('progress', function (e){
						if (e.lengthComputable) {
						    // e.loaded y e.total son propiedades del evento progress
						    //document.getElementById('content_dis_cargando').innerHTML = e.total+'% <img src="/img/cargando1.gif">';
						  }
					}, false);

					// Cargamos el archivo
					lector.addEventListener('load', function (e){
	                    var cadena = e.target.result;
	                    console.log(cadena);
	                    cadena = cadena.replace(/</g,'&lt;');
	                    cadena = cadena.replace(/>/g,'&gt;');

	                    // Remplazamos los caracteres
		                for(var ii=0; ii <= codigo.length; ii++){
							while(cadena.indexOf(codigo[ii]) >= 0){
								cadena = cadena.replace(codigo[ii], replace[ii]);
							}
						}

						// Guardamos en la base de datos temporal el archivo
		                ajax('include/server_files.php',{
		                	ext: ext,
		                	name: name,
		                	size: size,
		                	contenido: cadena,
		                	control: control,
		                	type: 'file_temp',
		                	tipo: 'DIS'
		                }, function (data){
		                	document.getElementById('content_dis_cargando').innerHTML = '';

		                	if(isEmpty(data.error)){
		                		// Si el objeto esta vacio
		                		document.getElementById('content_dis_results').innerHTML += '<div id="file_'+data['id']+'" class="files icon-'+ext+'" onclick="javascript:files_mostrar('+data['id']+')">'+name+' ('+size+'Kb)<span onclick="javascript:files_delete('+data['id']+')">X</span></div>';
		                		success(data.status);
		                	} else {
		                		// Si hay un error
		                		error(data.error);
		                	}
		                }, 'Json');
	                    //document.getElementById('content').innerHTML += '<pre>'+cadena+'</pre>';
	                }, false);

					// Si muestra un error el archivo lo mostramos en pantalla
					lector.addEventListener('error', function (e){
						if(e.target.error.name == "NotReadableError") {
							document.querySelector('.error').innerHTML = 'Error al subir el archivo intente nuevamente';
							document.querySelector('.error').style.transform = 'translateY(0)';
							
							setTimeout(function(){
								document.querySelector('.error').style.transform = 'translateY(-60px)';
							}, 3000);
  						}

						document.getElementById('content_dis_cargando').innerHTML = '';  						
					}, false);
				} else {
					// En caso de tener una extension no valida
					document.querySelector('.error').innerHTML = 'Extensión no valida';
					document.querySelector('.error').style.transform = 'translateY(0)';
					
					setTimeout(function(){
						document.querySelector('.error').style.transform = 'translateY(-60px)';
					}, 3000);

					document.getElementById('content_dis_cargando').innerHTML = '';
				}
			} else {
				// Si el archivo es mayor mostramos mensaje de error
				document.querySelector('.error').innerHTML = 'Archivo mayor a 10 Kb';
				document.querySelector('.error').style.transform = 'translateY(0)';
				
				setTimeout(function(){
					document.querySelector('.error').style.transform = 'translateY(-60px)';
				}, 3000);
			}
		}

		this.style.border = '1px solid #009957';

		return false;
	}
}

// Eliminamos un archivo temporal
function files_delete(id)
{
	ajax('include/server_files.php',{
		id: id,
		type: 'file_delete'
	}, function (data){
		// Ocultamos el archivo
		document.getElementById('file_'+data.id).style.display = 'none';
		// Ocultamos la vista previa del archivo
		back_files();

		success(data.status);
	}, 'Json');
}