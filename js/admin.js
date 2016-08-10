/************************************************
Archivo javascript para el admin

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Variables globales
var nav = navigator.appName;
var version = 10;
var codigo = Array("<", ">","){","(",")","\n", "[b]", "[/b]", "[I]", "[/I]",
	"[u]", "[/u]","[t]","[/t]","'","javascript:","array:","text/html");
var replace = Array("[-","-]",") {","&-","-&","<br>","<b>","</b>","<i>","</i>",
	"<u>","</u>","<strike>","</strike>",'"',"javascript :","array :","text/htm");
var url = 'include/server_admin.php';
var count_cap = 0;
var tema_orden = 0;

if(nav == "Microsoft Internet Explorer"){
    // Convertimos en minusculas la cadena que devuelve userAgent
    var ie = navigator.userAgent.toLowerCase();
    // Extraemos de la cadena la version de IE
    version = parseInt(ie.split('msie')[1]);
}

docReady(function (){
	// Fijamos un limite de caracteres a los textarea
	$('textarea[maxlength]').on('keyup', function (){
		var limit = $(this).attr('maxlength');
		var id = $(this).attr('id');
		var val = $(this).val();
		var count = limit - val.length;

		$('#count_'+id).html(count);

		if(limit < val.length){
			$(this).val(val.substring(0, limit));
		}
	});

	// Fijamos un limite de caracteres a lis input
	$('input[maxlength]').on('keyup', function (){
		var limit = $(this).attr('maxlength');
		var id = $(this).attr('id');
		var val = $(this).val();
		var count = limit - val.length;

		$('#count_'+id).html(count);

		if(limit < val.length){
			$(this).val(val.substring(0, limit));
		}
	});

	// Impedimos enviar formularios con la tecla enter
	var formulario = document.getElementsByTagName('form');
	for(var i = 0; i < formulario.length; i++){
		formulario[i].onsubmit = function (e){
			if(version <= 8){
				// Soporte a navegadores antiguos
				window.event.returnValue = false;
			} else {
				e.preventDefault();
			}
		}
	}

	// Formulario para crear categoria
	var form_categoria = document.getElementById('form_categoria') || 'form';
	form_categoria.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		var name = document.getElementById('name').value;
		if(check(name)) { return }

		// Ocultamos el formulario
		document.getElementById('form_categoria').style.display = 'none';

		// Mostramos imagen cargando
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando').innerHTML = '<img src="/img/cargando.gif">';
		}

		ajax(url, {
			name: name,
			type: 'form_categoria'
		}, function (data){
			// Ocultamos la imagen cargando
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = '';
				}
			} else {
				document.querySelector('.cargando').innerHTML = '';
			}

			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Si el objeto esta vacio
				// Mostramos el formulario
				document.getElementById('form_categoria').style.display = 'block';
				// Asignamos focus
				document.getElementById('name').value = '';
				document.getElementById('name').focus();
				success(data.status);
			} else {
				// Si hay error
				// Mostramos el formulario
				document.getElementById('form_categoria').style.display = 'block';
				// Asignamos focus
				document.getElementById('name').value = data.name;
				document.getElementById('name').focus();

				error(data.error);
			}
		}, 'Json');
	}

	// Formulario para editar categoria
	var form_categoria_edit = document.getElementById('form_categoria_edit') || 'form';
	form_categoria_edit.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		var id = document.getElementById('id').value;
		var name = document.getElementById('name').value;

		if(check(name)) { return }

		// Ocultamos el formulario
		document.getElementById('form_categoria_edit').style.display = 'none';

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando').innerHTML = '<img src="/img/cargando.gif">';
		}

		ajax(url, {
			id: id,
			name: name,
			type: 'form_categoria_edit'
		}, function (data){
			// Ocultamos la imagen cargando
				if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = '';
				}
			} else {
				document.querySelector('.cargando').innerHTML = '';
			}

			// Mostramos el formulario
			document.getElementById('form_categoria_edit').style.display = 'block';

			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Si el objeto esta vacio
				document.getElementById('name').value = '';
				document.getElementById('name').focus();

				success(data.status);
			} else {
				// Si hay error
				document.getElementById('name').value = data.name;
				document.getElementById('name').focus();

				error(data.error);
			}
		}, 'Json');
	}

	// Formulario para editar cursos
	var form_curso_editar = document.getElementById('form_curso_editar') || 'form';
	form_curso_editar.onsubmit = function (e){
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		var title = document.getElementById('title').value;
		var subtitle = document.getElementById('subtitle').value;
		var category = document.getElementById('category').value;
		var id = document.getElementById('id').value;
		var description = document.getElementById('description').value;
		var requeriment = document.getElementById('requeriment').value;

		if(check(title)) { return }
		if(check(subtitle)) { return }
		if(check(description)) { return }
		if(check(requeriment)) { return }

		// Reemplazamos los saltos de linea
		description = description.replace(/\n/g,'<br>');
		requeriment = requeriment.replace(/\n/g,'<br>');

		// Ocultamos el formulario editar
		document.getElementById('form_curso_editar').style.display = 'none';

		// Mostramos imagen cargando
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_edit');
			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando_edit').innerHTML = '<img src="/img/cargando.gif">';
		}

		ajax(url, {
			category: category,
			description: description,
			requeriment: requeriment,
			id: id,
			title: title,
			subtitle: subtitle,
			type: 'form_curso_editar'
		}, function (data){
			// Ocultamos la imagen cargando
			// Mostramos imagen cargando
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando_edit');
				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = '';
				}
			} else {
				document.querySelector('.cargando_edit').innerHTML = '';
			}

			// Mostramos el formulario editar
			document.getElementById('form_curso_editar').style.display = 'block';

			success(data.status);
		}, 'Json');
	}

	// Formulario para guardar cursos nuevos
	var form_curso_nuevo = document.getElementById('form_curso_nuevo') || 'form';
	form_curso_nuevo.onsubmit = function (e){
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		var title = document.getElementById('title').value;
		var subtitle = document.getElementById('subtitle').value;
		var category = document.getElementById('category').value;
		var description = document.getElementById('description').value;
		var requeriment = document.getElementById('requeriment').value;

		if(check(title)) { return }
		if(check(subtitle)) { return }
		if(check(description)) { return }
		if(check(requeriment)) { return }

		// Reemplazamos los saltos de linea
		description = description.replace(/\n/g,'<br>');
		requeriment = requeriment.replace(/\n/g,'<br>');

		// Ocultamos el formulario
		document.getElementById('form_curso_nuevo').style.display = 'none';

		// Mostramos imagen cargando	
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando').innerHTML = '<img src="/img/cargando.gif">';
		}

		ajax(url, {
			category: category,
			description: description,
			requeriment: requeriment,
			title: title,
			subtitle: subtitle,
			type: 'form_curso_nuevo'
		}, function (data){
			var texto = '<li><a href="/admin-co/?category=course" class="icon-back"></a></li>';
			var msg = '<p>Al curso se le asigno una imagen por defecto, puede cambiarla en la edición del curso.</p><p><a href="/admin-co/?category=course">Ir a cursos creados</a></p>'

			// Ocultamos imagen cargando y mostramos mensaje
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = msg;
				}
			} else {
				document.querySelector('.cargando').innerHTML = msg;
			}

			// Agregamos boton de retroceso
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('menu_articulos');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML += texto;
				}
			} else {
				document.querySelector('.menu_articulos').innerHTML += texto;
			}

			success(data.status);
		}, 'Json');
	}

	// Procesamos el formulario de cambio de avatar
	var form_logo = document.getElementById('form_logo') || 'form';
	form_logo.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		// Obtenemos los valores del formulario
		var file = document.getElementById('img_logo').files[0];
		var form_data = new FormData(form_logo);
		var id = document.getElementById('id_curso').value;
		var url_img = "http://"+document.domain+"/include/server_logo.php";
		var name = '';
		var size = '';

		// Obtenemos los datos del archivo
		if('name' in file){
			name = file.name;
		}
		if('size' in file){
			size = file.size;
		}

		// Verificamos el tamaño
		if(size > 10240){
			error('Tamaño maximo excedido');
			return
		}

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_logo');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando_logo').innerHTML = '<img src="/img/cargando.gif">';
		}

		// Ocultamos el boton de envio
		document.getElementById('submit_logo').style.display = 'none';

		$.ajax({
			url: url_img,
			type: 'POST',
			// Datos del formulario
			data: form_data,
			// Necesario para subir archivos via ajax
			cache: false,
			contentType: false,
			processData: false,
			success: function (data){
				// Mostramos el suceso
				success('Su logo se actualizo con exito');

				// Mostramos avizo
				var msg = 'Su logo se actualizo';
				document.getElementById('logo_msg').innerHTML = msg;

				// Generamos un parametro fantasma para la url de la imagen
				var aleatorio = Math.ceil(Math.random() * 100);

				// Actualizamos la imagen
				document.getElementById('logo_img').setAttribute('src', '/img_curso/'+data+'?'+aleatorio);

				// Limpiamos el input
				document.getElementById('img_logo').value = '';

				// Quitamos la imagen cargando
				if(version <= 8){
					// Soporte a navegadores antiguos
					var temp = getElementsByClassName('cargando_logo');

					for(var i = 0; i < temp.length; i++){
						temp[i].innerHTML = '';
					}
				} else {
					document.querySelector('.cargando_logo').innerHTML = '';
				}

				 // Mostramos el boton submit
				 document.getElementById('submit_logo').style.display = 'block';
			},
			error: function (){
				error('Error al subir la imagen, intente nuevamente');
			}
		});
	}

	// Formulario para actualizar el perfile del usuario
	var form_perfil = document.getElementById('form_perfil') || 'form';
	form_perfil.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		var bio = document.getElementById('acerca').value;
		var google = document.getElementById('google').value;
		var twitter = document.getElementById('twitter').value;

		if(check(bio)) { return }
		if(check(google)) { return }
		if(check(twitter)) { return }

		// Reemplazamos los saltos de linea
		bio = bio.replace(/\n/g,'<br>');

		// Ocultamos el formulario
		document.getElementById('form_perfil').style.display = 'none';

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_perfil');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando_perfil').innerHTML = '<img src="/img/cargando.gif">';
		}

		ajax(url,{
			bio: bio,
			google: google,
			twitter: twitter,
			type: 'form_perfil'
		}, function (data){
			// Mostramos el perfil de usuario
			document.getElementById('form_perfil').style.display = 'block';

			// Ocultamos la imagen cargando
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando_perfil');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = '';
				}
			} else {
				document.querySelector('.cargando_perfil').innerHTML = '';
			}

			success(data.status);
		}, 'Json');
	}

	var form_password = document.getElementById('form_password') || 'form';
	form_password.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		// Obtenemos los valores del formulario
		var pass1 = document.getElementById('perfil_pass').value;
		var pass2 = document.getElementById('perfil_pass1').value;
		var pass3 = document.getElementById('perfil_pass2').value;

		// Verificamos los datos obtenidos
		if(check(pass1)){ return }
		if(check(pass2)){ return }
		if(check(pass3)){ return }
		if(pass2.length < 8){
			error('La contraseña debe tener al menos 8 caracteres');
			return
		}
		if(pass2 != pass3){
			error('Las contraseñas no coinciden, intente nuevamente');
			return
		}

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_password');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando_password').innerHTML = '<img src="/img/cargando.gif">';
		}

		document.getElementById('submit_password').style.display = 'none';

		ajax(url, {
			pass: pass1,
			new_pass: pass2,
			type: 'form_password'
		}, function (data){
			// Ponemos en blanco los campos input
			document.getElementById('perfil_pass').value = '';
			document.getElementById('perfil_pass1').value = '';
			document.getElementById('perfil_pass2').value = '';

			// Quitamos la imagen cargando
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando_password');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = '';
				}
			} else {
				document.querySelector('.cargando_password').innerHTML = '';
			}

			// Mostramos el boton submit
			document.getElementById('submit_password').style.display = 'inline-block';

			// Verificamos si no hubo un error al enviar el formulario
			if(isEmpty(data.error)){
				// Si no hubo error
				// Mostramos el suceso
				success(data.status);

				// Mostramos avizo
				var msg = 'Su contraseña se cambio correctamente';
				document.getElementById('password_msg').innerHTML = msg;
			} else {
				// Si hubo error
				document.getElementById('perfil_pass').focus();
				error(data.error);
			}

			return
		}, 'Json');
	}

	// Procesamos el formulario del avatar
	var form_avatar = document.getElementById('form_avatar') || 'form';
	form_avatar.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		// Obtenemos los valores del formulario
		var file = document.getElementById('avatar_avatar').files[0];
		var form_data = new FormData(form_avatar);
		var url_avatar = "include/server_avatar.php";
		var name = '';
		var size = '';

		// Obtenemos los datos del archivo
		if('name' in file){
			name = file.name;
		}
		if('size' in file){
			size = file.size;
		}

		// Verificamos el tamaño
		if(size > 10240){
			error('Tamaño maximo excedido');
			return
		}

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_avatar');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando_avatar').innerHTML = '<img src="/img/cargando.gif">';
		}

		document.getElementById('submit_avatar').style.display = 'none';

		$.ajax({
			url: url_avatar,
			type: 'POST',
			// Datos del formulario
			data: form_data,
			// Necesario para subir archivos via ajax
			cache: false,
			contentType: false,
			processData: false,
			success: function (data){
				// Mostramos el suceso
				success('Su avatar se actualizo con exito');

				// Mostramos avizo
				var msg = 'Su avatar se actualizo';
				document.getElementById('avatar_msg').innerHTML = msg;

				// Generamos un parametro fantasma para la url de la imagen
				var aleatorio = Math.ceil(Math.random() * 100);

				// Actualizamos la imagen
				document.getElementById('avatar_img').setAttribute('src', '/avatar/'+data+'?'+aleatorio);
				document.getElementById('header_avatar').setAttribute('src', '/avatar/'+data+'?'+aleatorio);

				// Limpiamos el input
				document.getElementById('avatar_avatar').value = '';

				// Quitamos la imagen cargando
				if(version <= 8){
					// Soporte a navegadores antiguos
					var temp = getElementsByClassName('cargando_avatar');

					for(var i = 0; i < temp.length; i++){
						temp[i].innerHTML = '';
					}
				} else {
					document.querySelector('.cargando_avatar').innerHTML = '';
				}

				 // Mostramos el boton submit
				 document.getElementById('submit_avatar').style.display = 'block';
			},
			error: function (){
				error('Error al subir la imagen, intente nuevamente');
			}
		});
	}

	// Formulario para solicitar categoria nueva
	var form_solicitud = document.getElementById('form_solicitud') || 'form';
	form_solicitud.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		var name = document.getElementById('name').value;

		if(check(name)) { return }

		// Ocultamos el formulario
		document.getElementById('form_solicitud').style.display = 'none';

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando').innerHTML = '<img src="/img/cargando.gif">';
		}

		ajax(url, {
			name: name,
			type: 'form_categoria_solicitud'
		}, function (data){
			var msg = "Recibira una respuesta a la brevedad posible";
			// Ocultamos la imagen cargando
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = msg;
				}
			} else {
				document.querySelector('.cargando').innerHTML = msg;
			}

			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Si el objeto esta vacio

				success(data.status);
			} else {
				// Si hay error

				error(data.error);
			}
		}, 'Json');
	}

	// Formulario para enviar un aviso
	var form_aviso = document.getElementById('form_aviso') || 'form';
	form_aviso.onsubmit = function (e){
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		var id_curso = document.getElementById('id_curso').value;
		var contenido = document.getElementById('aviso').value;

		if(contenido.length <= 20){
			error('El aviso debe tener al menos 20 caracteres');
			return
		}

		// Reemplazamos todo
		for(var i=0; i <= codigo.length; i++){
			while(contenido.indexOf(codigo[i]) >= 0){
				contenido = contenido.replace(codigo[i], replace[i]);
			}
		}

		// Ocultamos el formulario
		document.getElementById('form_aviso').style.display = 'none';

		// Mostramos imagen cargando	
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando').innerHTML = '<img src="/img/cargando.gif">';
		}

		ajax(url, {
			id_curso: id_curso,
			contenido: contenido,
			type: 'form_aviso'
		}, function (data){
			// Generemos mensaje a mostrar
			var msg = "<p>El mensaje fue enviado a cada uno de los usuarios suscritos al curso.</p>"

			// Ocultamos imagen cargando y mostramos mensaje
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = msg;
				}
			} else {
				document.querySelector('.cargando').innerHTML = msg;
			}

			// Verificamos que no alla error
			if(isEmpty(data.error)){
				// Si no hay error
				success(data.status);
			} else {
				// Mostramos el error
				error(data.error);
			}
		}, 'Json');
	}
});

//
// Opciones para manejar los cursos
//

// Funcion que cambia el capitulo entre publico o borrador
function cap_visibility(id, visibility)
{
	if(visibility == 'public'){
		document.getElementById('cap_public_'+id).style.display = 'none';
	} else if(visibility == 'draw'){
		document.getElementById('cap_draw_'+id).style.display = 'none';
	}

	ajax(url,{
		visibility: visibility,
		id: id,
		type: 'cap_visibility'
	}, function (data){
		document.getElementById('cap_'+data.return +'_'+data.id).style.display = 'block';

		success(data.status);
	}, 'Json');
}
function curso_delete(id)
{
	// Preguntamos si queremos eliminar un curso
	var text = 'Esta seguro de eliminar el curso <a class="icon-yes" onclick="javascript:curso_delete_yes('+id+')"></a> <a class="icon-no" onclick="javascript:curso_delete_no('+id+')"></a>';

	document.getElementById('curso_'+id).style.display = 'none';
	document.getElementById('info_'+id).style.display = 'block';
	document.getElementById('info_'+id).innerHTML = text;
}
function curso_delete_no(id)
{
	// Si no queremos eliminar un curso
	document.getElementById('info_'+id).innerHTML = '';
	document.getElementById('info_'+id).style.display = 'none';
	document.getElementById('curso_'+id).style.display = 'block';
}
function curso_delete_yes(id)
{
	// Si queremos eliminar un curso
	document.getElementById('info_'+id).innerHTML = '<img src="/img/cargando.gif">';

	ajax(url,{
		id:id,
		type: 'eliminar_curso'
	}, function (data){
		if(isEmpty(data.error)){
			// Si el objeto esta vacio
			document.getElementById('info_'+id).innerHTML = data.status;

			success(data.status);
		} else {
			document.getElementById('info_'+id).innerHTML = data.error;

			error(data.error);

			setTimeout(function (){
				// Si no se puede eliminar regresamos todo
				document.getElementById('info_'+id).innerHTML = '';
				document.getElementById('info_'+id).style.display = 'none';
				document.getElementById('curso_'+id).style.display = 'block';
			}, 3000);
		}
	}, 'Json');
}
function curso_estadistica(id)
{
	// Mostrar estadisticas de un curso

	document.getElementById('curso_'+id).style.display = 'none';
	document.getElementById('info_'+id).style.display = 'block';
	document.getElementById('info_'+id).innerHTML = '<img src="/img/cargando.gif">';

	ajax(url, {
		id: id,
		type: 'estadisticas',
	}, function (data){
		document.getElementById('info_'+id).innerHTML = data.result;
	}, 'Json');
}
function curso_estadistica_no(id)
{
	// Mostrar estadisticas de un curso
	document.getElementById('info_'+id).innerHTML = '';
	document.getElementById('info_'+id).style.display = 'none';
	document.getElementById('curso_'+id).style.display = 'block';
}
function curso_mensaje(id)
{
	// Preguntamos si el usuario quiere mandar a revicion el curso para su publicacion

	document.getElementById('curso_'+id).style.display = 'none';
	document.getElementById('info_'+id).style.display = 'block';

	ajax(url,{
		id:id,
		type: 'curso_mensaje',
	}, function (data){
		document.getElementById('info_'+id).innerHTML = data.msg;
	}, 'Json');
}
function curso_mensaje_no(id)
{
	document.getElementById('info_'+id).innerHTML = '';	
	document.getElementById('info_'+id).style.display = 'none';
	document.getElementById('curso_'+id).style.display = 'block';
}
function curso_public(id, status)
{
	// Preguntamos si el usuario quiere mandar a revicion el curso para su publicacion
	var text;

	if(status == 'YES'){
		text = 'El curso se encuentra en revision: <a class="icon-no" onclick="javascript:curso_public_no('+id+')"></a>';
	} else {
		text = 'Quiere enviar el curso a revision para su publicacion: <a class="icon-yes" onclick="javascript:curso_public_yes('+id+')"></a> <a class="icon-no" onclick="javascript:curso_public_no('+id+')"></a>';
	}

	document.getElementById('curso_'+id).style.display = 'none';
	document.getElementById('info_'+id).style.display = 'block';
	document.getElementById('info_'+id).innerHTML = text;
}
function curso_public_no(id)
{
	// Si no queremos enviar a revicion un curso
	document.getElementById('info_'+id).innerHTML = '';
	document.getElementById('info_'+id).style.display = 'none';
	document.getElementById('curso_'+id).style.display = 'block';
}
function curso_public_yes(id)
{
	// Si queremos enviar a revicion un curso
	document.getElementById('info_'+id).innerHTML = '<img src="/img/cargando.gif">';

	ajax(url,{
		id:id,
		type: 'revicion_curso'
	}, function (data){
		document.getElementById('info_'+id).innerHTML = data.status;

		success(data.status);
	}, 'Json');
}
function curso_publicar(id)
{
	// Mostramos formulario para la publicacion del curso
	document.getElementById('curso_'+id).style.display = 'none';
	document.getElementById('publicar_'+id).style.display = 'block';
}
function curso_publicar_cancel(id)
{
	document.getElementById('publicar_'+id).style.display = 'none';
	document.getElementById('curso_'+id).style.display = 'block';
	document.getElementById('content_'+id).value = '';
}
function curso_publicar_no(id)
{
	// Ocultamos formulario para la publicacion del curso
	var content = document.getElementById('content_'+id).value;

	if(check(content)) { return }

	if(content.length < 10){
		error('Debe ingresar minimo 10 caracteres');
	}

	// Mostramos imagen cargando
	if(version <= 8){
		// Soporte a navegadores antiguos
		var temp = getElementsByClassName('cargando'+id);
		for(var i = 0; i < temp.length; i++){
			temp[i].innerHTML = '<img src="/img/cargando.gif">';
		}
	} else {
		document.querySelector('.cargando'+id).innerHTML = '<img src="/img/cargando.gif">';
	}

	// Ocultamos las opciones de submit
	document.getElementById('publicar_cargando_'+id).style.display = 'none';

	ajax(url,{
		content: content,
		id: id,
		type: 'curso_publicar_no'
	}, function (data){
		// Ocultamos la imagen cargando
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando'+id);
			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '';
			}
		} else {
			document.querySelector('.cargando'+id).innerHTML = '';
		}
		
		// Mostramos los submit
		document.getElementById('publicar_cargando_'+id).style.display = 'block';
		// Ocultamos el formulario de publicacion
		document.getElementById('publicar_'+id).style.display = 'none';
		// Mostramos opciones del curso
		document.getElementById('curso_'+id).style.display = 'block';

		document.getElementById('content_'+id).value = '';

		// Verificamos si no hay error
		if(isEmpty(data.error)){
			// Si no hay error
			success(data.status);
		} else {
			// Si hay error
			error(data.error);
		}
	}, 'Json');
}
function curso_publicar_yes(id)
{
	// Publicamos el curso
	var content = document.getElementById('content_'+id).value;

	if(check(content)){ return }
	if(content.length < 10){
		error('Debe ingresar minimo 10 caracteres');
	}

	// Mostramos imagen cargando
	if(version <= 8){
		// Soporte a navegadores antiguos
		var temp = getElementsByClassName('cargando'+id);
		for(var i = 0; i < temp.length; i++){
			temp[i].innerHTML = '<img src="/img/cargando.gif">';
		}
	} else {
		document.querySelector('.cargando'+id).innerHTML = '<img src="/img/cargando.gif">';
	}

	// Ocultamos las opciones de submit
	document.getElementById('publicar_cargando_'+id).style.display = 'none';

	ajax(url,{
		content: content,
		id: id,
		type: 'curso_publicar_yes'
	}, function (data){
		// Ocultamos la imagen cargando
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando'+id);
			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '';
			}
		} else {
			document.querySelector('.cargando'+id).innerHTML = '';
		}

		// Mostramos los submit		
		document.getElementById('publicar_cargando_'+id).style.display = 'none';
		// Ocultamos el formulario
		document.getElementById('publicar_'+id).style.display = 'none';
		// Mostramos las opciones del curso
		document.getElementById('curso_'+id).style.display = 'block';
		// Mostramos informacion sobre la publicacion
		document.getElementById('curso_'+id).innerHTML = 'El curso se publico con exito';

		if(isEmpty(data.error)){
			// Si no hay error
			success(data.status);
		} else {
			error(data.error);
		}
	}, 'Json');
}
function curso_ver(url, status)
{
	if(status == 'NO'){
		
	} else {
		$(location).attr('href', 'url')
	}
}

//
// Opciones para manejo de capitulos
//

// Ocultamos la opcion de eliminar un capitulo
function cap_cancel(id)
{
	document.getElementById('form_'+id).style.display = 'none';
	document.getElementById('cap_delete_'+id).style.display = 'none';
	document.getElementById('cap_'+id).style.display = 'block';
}
// Mostramos opcion para eliminar un capitulo
function cap_delete(id)
{
	document.getElementById('cap_'+id).style.display = 'none';
	document.getElementById('cap_delete_'+id).style.display = 'block';
}
// Ocultamos la info del tema y mostramos formulario de edicion del titulo del capitulo
function cap_form(id)
{
	document.getElementById('cap_'+id).style.display = 'none';
	document.getElementById('form_'+id).style.display = 'block';
}
// Mostramos los temas de un capitulo
function cap_mostrar(id)
{
	document.getElementById('mostrar_tema_'+id).style.display = 'block';
	document.getElementById('cap_mostrar_'+id).style.display = 'none';
	document.getElementById('cap_ocultar_'+id).style.display = 'block';
}
// Creamos un capitulo nuevo para el curso
function cap_nuevo(i,id)
{
	// Ocultamos el boton de capitulo nuevo
	document.getElementById('capitulo_nuevo').style.display = 'none';

	// Mostramos la imagen cargando
	if(version <= 8){
		// Soporte a navegadores antiguos
		var temp = getElementsByClassName('cargando');

		for(var i = 0; i < temp.length; i++){
			temp[i].innerHTML = '<img src="/img/cargando.gif">';
		}
	} else {
		document.querySelector('.cargando').innerHTML = '<img src="/img/cargando.gif">';
	}

	// Verificamos el total de capitulos
	if(parseInt(count_cap) == 0){
		// Si el total es 0 asignamos el total verdadero
		count_cap = i;
	} else {
		// Sumamos uno al total de capitulo
		count_cap = parseInt(count_cap) + 1;
	}

	ajax(url, {
		i: count_cap,
		id: id,
		type: 'cap_nuevo'
	}, function (data){
		// Ocultamos la imagen cargando
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '';
			}
		} else {
			document.querySelector('.cargando').innerHTML = '';
		}
		
		// Mostramos el boton de capitulo nuevo
		document.getElementById('capitulo_nuevo').style.display = 'block';

		// Agregamos el capitulo nuevo
		document.getElementById('u').innerHTML += "<li class='item' draggable='true' id='"+data.i+"'>"+data.texto+"</li>";

		// Reiniciamos el acomodo de los capitulos
		reinicio();

		success(data.status);
	}, 'Json');
}
// Ocultamos los temas de un capitulo
function cap_ocultar(id)
{
	document.getElementById('mostrar_tema_'+id).style.display = 'none';
	document.getElementById('cap_ocultar_'+id).style.display = 'none';
	document.getElementById('cap_mostrar_'+id).style.display = 'block';
}
// Actualizamos el titulo de un capitulo
function cap_submit(id)
{
	var title = $('#form_cap_'+id).find('#title').val();

	if(check(title)){ return }

	document.getElementById('form_cap_'+id).style.display = 'none';

	// Mostramos la imagen de cargando
	document.getElementById('cargando_line_'+id).innerHTML = '<img src="/img/cargando.gif">';

	ajax(url, {
		title: title,
		id: id,
		type: 'form_cap_edit'
	}, function (data){
		// Ocultamos la imagen
		document.getElementById('cargando_line_'+data.id).innerHTML = '';
		document.getElementById('form_cap_'+id).style.display = 'block';
		// Colocamos el nuevo titulo como valor del formulario
		$('#form_cap_'+data.id).find('#title').val(data.title);
		document.getElementById('form_'+data.id).style.display = 'none';
		document.getElementById('cap_'+data.id).style.display = 'block';
		document.getElementById('cap_title_'+data.id).innerHTML = data.title;
		// Mostramos mensaje
		success(data.status);
	}, 'Json');
}

//
// Opciones para el manejo de temas
//

// Baja el tema una posicion en su orden
function tema_bajar(id_cap,id_tema)
{
	if(tema_orden == 0){
		// Obtenemos el orden del tema a bajar y el total de temas
		var orden = document.getElementById('li_'+id_cap+id_tema).getAttribute('data-orden');
		var conteo = (parseInt(document.getElementById('conteo_'+id_cap).getAttribute('data-i')) - 1);
		
		// Si es el ultimo tema no hacemos nada
		if(orden == conteo){
			return
		} else {
			// Cambiamos el valor de la variable para bloquear otra peticion
			tema_orden = 1;

			var control = parseInt(orden) - 1; // 
			var sIndex = parseInt(orden); // 
			var object = $('.items'+id_cap); // Obtenemos los temas
			var copy;
			var identificator = []; // Array para guardar el nuevo acomo de temas

			$.each(object, function (i){
				if(i == control){
					copy = $(this).clone();
				}
			});

			object.eq(control).remove();
			copy.insertAfter(object.eq(sIndex));

			// Obtenemos los identificadores
			var subObject = $('.items'+id_cap);

			$.each(subObject, function (i){
				identificator.push({id: parseInt(this.getAttribute('data-id'))});
			});

			// Cambiamos los identificadores
			for(var j in identificator){
				$('.number_tema_'+parseInt(identificator[j]['id'])).html('Tema '+(parseInt(j) + 1)+': ');
				$('.orden'+parseInt(identificator[j]['id'])).attr('data-orden',(parseInt(j) + 1));
			}

			var control_2 = parseInt(control) + 2;
			ajax(url,{
				current: control_2,
				current_id: identificator[sIndex]['id'],
				prev: orden,
				prev_id: identificator[control]['id'],
				type: 'tema_subir'
			}, function (data){
				tema_orden = 0; // Regresamos el valor a 0 para recibir otra peticion
				success(data.status);
			}, 'Json');
		}
	} else {
		return
	}
}
function tema_cancel(id)
{
	// Ocultamos formulario de edicion y mostramos info del tema
	document.getElementById('form_'+id).style.display = 'none';
	document.getElementById('tema_delete_'+id).style.display = 'none';
	document.getElementById('tema_'+id).style.display = 'block';
}
function tema_doc(id, id_tema)
{
	// Guarda la documentacion de un tema en especifico
	var contenido = document.getElementById('doc'+id).value;

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(contenido.indexOf(codigo[i]) >= 0){
			contenido = contenido.replace(codigo[i], replace[i]);
		}
	}

	// Reemplazamos las url planas
	// contenido = url_replace(contenido);

	// Ocultamos el boton submit del formulario
	document.getElementById('button_doc_'+id).style.display = 'none';
	// Mostramos mensaje de guardando
	document.getElementById('data_doc_'+id).innerHTML = 'Guardando . . .';
	
	ajax(url,{
		contenido: contenido,
		id_tema: id_tema,
		type: 'form_tema_doc'
	}, function (data){
		// Mostramos boton submit
		document.getElementById('button_doc_'+id).style.display = 'inline-block';
		// Mostramos mensaje de guardado
		document.getElementById('data_doc_'+id).innerHTML = data.status;

		success(data.status);

		// Ocultamos mensaje
		setTimeout(function (){
			document.getElementById('data_doc_'+id).innerHTML = '';
		}, 4000);
	}, "Json");
}
function tema_delete(id)
{
	// Ocultamos la info del tema y mostramos pregunta de eliminar tema
	document.getElementById('tema_'+id).style.display = 'none';
	document.getElementById('tema_delete_'+id).style.display = 'block';
}
function tema_delete_yes(id_tema,id_cap,id_curso)
{
	// Elimina un capitulo de la DB y el contenido
	document.getElementById('tema_delete_'+id_cap+id_tema).innerHTML = '<div class="cargando_line"><img src="/img/cargando.gif"></div>';

	// Obtenemos el numero de items
	var count = (document.getElementById('conteo_'+id_cap).getAttribute('data-i') - 1);

	// Eliminamos de la base de datos
	ajax(url,{
		id_tema: id_tema,
		id_cap: id_cap,
		id_curso: id_curso,
		type: 'tema_delete'
	}, function (data){
		// Ocultamos los item del tema
		document.getElementById(''+id_cap+id_tema).style.display = 'none';
		document.getElementById('_info_'+id_cap+id_tema).style.display = 'none';
		
		// Como eliminamos un tema reseteamos el numero de items
		document.getElementById('conteo_'+id_cap).setAttribute('data-i', count);

		// Reacomodamos los temas resetenado su acomodo
		for(i in data){
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('number_tema_'+parseInt(data[i]['id']));

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = 'Tema '+(parseInt(i)+ 1)+': ';
				}
			} else {
				document.querySelector('.number_tema_'+parseInt(data[i]['id'])).innerHTML = 'Tema '+(parseInt(i)+ 1)+': ';
			}
		}

		success('El tema se elimino con exito');
	}, 'Json');
}
function tema_form(id)
{
	// Ocultamos la info del tema y mostramos formulario de edicion
	document.getElementById('tema_'+id).style.display = 'none';
	document.getElementById('form_'+id).style.display = 'block';
}
function tema_github(id, id_tema)
{
	// Guarda el repositorio github de un tema en especifico
	var github = document.getElementById('github'+id).value;

	// Validamos si la url es valida
	if(!validateUrl(github)){
		error('Ingrese una url valida');
		return	
	}

	// Ocultamos el boton submit
	document.getElementById('button_github_'+id).style.display = 'none';
	// Mostramos mensaje guardando
	document.getElementById('data_github_'+id).innerHTML = 'Guardando . . .';
	
	ajax(url,{
		github: github,
		id_tema: id_tema,
		type: 'form_tema_github'
	}, function (data){
		// Verificamos si hay error
		if(isEmpty(data.error)){
			// Si el objeto esta vacio
			// Mostramos boton submit
			document.getElementById('button_github_'+id).style.display = 'inline-block';
			// Mostramos mensaje sobre el repositorio
			document.getElementById('data_github_title_'+id).innerHTML = 'Repositorio: Tiene un repositorio asignado a este tema';
			// Mostramos mensaje de exito
			document.getElementById('data_github_'+id).innerHTML = data.status;

			success(data.status);

			// Ocultamos mensaje
			setTimeout(function (){
				document.getElementById('data_github_'+id).innerHTML = '';
			}, 4000);
		} else {
			// Si hay error
			// Mostramos boton submit
			document.getElementById('button_github_'+id).style.display = 'inline-block';
			// Quitamos mensaje cargando
			document.getElementById('data_github_'+id).innerHTML = '';

			error(data.error);
		}
	}, "Json");
}
function tema_info(id, id_tema)
{
	// Guarda la informacion de un tema en especifico
	var contenido = document.getElementById('info'+id).value;

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(contenido.indexOf(codigo[i]) >= 0){
			contenido = contenido.replace(codigo[i], replace[i]);
		}
	}

	// Reemplazamos las url planas
	// contenido = url_replace(contenido);

	// Ocultamos el boton submit
	document.getElementById('button_info_'+id).style.display = 'none';
	document.getElementById('data_info_'+id).innerHTML = 'Guardando . . .';
	
	ajax(url,{
		contenido: contenido,
		id_tema: id_tema,
		type: 'form_tema_info'
	}, function (data){
		// Mostramos el boton submit
		document.getElementById('button_info_'+id).style.display = 'inline-block';
		// Mostramos mensaje de guardado con exito
		document.getElementById('data_info_'+id).innerHTML = data.status;

		success(data.status);

		// Ocultamos el mensaje
		setTimeout(function (){
			document.getElementById('data_info_'+id).innerHTML = '';
		}, 3000);
	}, "Json");
}
function tema_nuevo(id_curso,id_cap)
{
	// Obtenemos el total de temas
	var count = document.getElementById('conteo_'+id_cap).getAttribute('data-i');

	// Ocultamos el boton de tema nuevo
	if(version <= 8){
		// Soporte a navegadores antiguos
		var temp = getElementsByClassName('tema_nuevo'+id_cap);

		for(var i = 0; i < temp.length; i++){
			temp[i].style.display = 'none';
		}
	} else {
		document.querySelector('.tema_nuevo'+id_cap).style.display = 'none';
	}

	// Mostramos imagen cargando
	document.getElementById('cargando_tema'+id_cap).innerHTML = '<div class="cargando"><img src="/img/cargando.gif"></div>';

	ajax(url, {
		i: count,
		id_curso: id_curso,
		id_cap: id_cap,
		type: 'tema_nuevo'
	}, function (data){
		// Ocultamos la imagen cargando
		document.getElementById('cargando_tema'+data.id).innerHTML = '';
		// Mostramos el boton de tema nuevo
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('tema_nuevo'+data.id);

			for(var i = 0; i < temp.length; i++){
				temp[i].style.display = 'block';
			}
		} else {
			document.querySelector('.tema_nuevo'+data.id).style.display = 'block';
		}

		// Agregamos el tema nuevo
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('add_temas_'+data.id);

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML += data.texto;
			}
		} else {
			document.querySelector('.add_temas_'+data.id).innerHTML += data.texto;
		}

		// Actualizamos el total de temas
		document.getElementById('conteo_'+data.id).setAttribute('data-i',(parseInt(data.i) + 1));

		success(data.status);
	}, 'Json');
}
function tema_mostrar(id)
{
	// Mostramos las opciones de un tema
	document.getElementById('iconos_'+id).style.display = 'block';
	document.getElementById('_info_'+id).style.display = 'block';
	document.getElementById('mostrar_'+id).style.display = 'none';
	document.getElementById('ocultar_'+id).style.display = 'block';
}
function tema_ocultar(id)
{
	// Ocultamos las opciones de un tema
	document.getElementById('iconos_'+id).style.display = 'none';
	document.getElementById('_info_'+id).style.display = 'none';
	document.getElementById('ocultar_'+id).style.display = 'none';
	document.getElementById('mostrar_'+id).style.display = 'block';
}
// Sube una posicion el tema
function tema_subir(id_cap,id_tema)
{
	if(tema_orden == 0){
		// Obtenemos el orden del tema a modificar
		var orden = document.getElementById('li_'+id_cap+id_tema).getAttribute('data-orden');
		
		// Si esta en el limite no hace nada
		if(orden == 1){
			return
		} else {
			tema_orden = 1;

			var control = parseInt(orden) - 1;
			var sIndex = parseInt(orden) - 2;
			var object = $('.items'+id_cap);
			var copy;
			var identificator = [];

			$.each(object, function (i){
				if(i == control){
					copy = $(this).clone();
				}
			});

			object.eq(control).remove();
			copy.insertBefore(object.eq(sIndex));

			// Obtenemos los identificadores
			var subObject = $('.items'+id_cap);

			$.each(subObject, function (i){
				identificator.push({id: parseInt(this.getAttribute('data-id'))});
			});

			// Cambiamos los identificadores
			for(var j in identificator){
				$('.number_tema_'+parseInt(identificator[j]['id'])).html('Tema '+(parseInt(j) + 1)+': ');
				$('.orden'+parseInt(identificator[j]['id'])).attr('data-orden',(parseInt(j) + 1));
			}

			ajax(url,{
				current: control,
				current_id: identificator[sIndex]['id'],
				prev: orden,
				prev_id: identificator[control]['id'],
				type: 'tema_subir'
			}, function (data){
				// Modificamos el valor de la variable para recibir mas peticiones
				tema_orden = 0;
				// Mostramos mensaje
				success(data.status);
			}, 'Json');
		}
	} else {
		return
	}
}
function tema_video(id, id_tema)
{
	// Guarda el video de un tema en especifico
	var video = document.getElementById('video'+id).value;

	// Ocultamos el boton submit
	document.getElementById('button_video_'+id).style.display = 'none';
	// Mostramos imagen de guardando
	document.getElementById('data_video_'+id).innerHTML = 'Guardando . . .';
	
	ajax(url,{
		video: video,
		id_tema: id_tema,
		type: 'form_tema_video'
	}, function (data){
		// Verificamos si hay error
		if(isEmpty(data.error)){
			// Si el objeto esta vacio
			// Mostramos el boton submit
			document.getElementById('button_video_'+id).style.display = 'inline-block';
			// Mostramos informacion sobre el video
			document.getElementById('data_video_title_'+id).innerHTML = 'Titulo: '+data['title'][0];
			// Mostramos mensaje
			document.getElementById('data_video_'+id).innerHTML = data.status;

			success(data.status);

			// Ocultamos mensaje
			setTimeout(function (){
				document.getElementById('data_video_'+id).innerHTML = '';
			}, 4000);
		} else {
			// Si hay error
			// Mostramos el boton submit
			document.getElementById('button_video_'+id).style.display = 'inline-block';
			// Quitamos mensaje guardando
			document.getElementById('data_video_'+id).innerHTML = '';

			error(data.error);
		}
	}, "Json");
}
// Funcion que cambia el tema entre publico o borrador
function tema_visibility(id, id_tema, type)
{
	if(type == 'public'){
		document.getElementById('tema_public_'+id).style.display = 'none';
	} else if(type == 'draw'){
		document.getElementById('tema_draw_'+id).style.display = 'none';
	}

	ajax(url,{
		visibility: type,
		id: id,
		id_tema: id_tema,
		type: 'tema_visibility'
	}, function (data){
		document.getElementById('tema_'+data.return +'_'+data.id).style.display = 'block';

		success(data.status);
	}, 'Json');
}
function tema_router(id,type)
{
	// Router de opciones para los temas
	if(type == 'info'){
		document.getElementById('info_'+id).style.display = 'block';
		document.getElementById('doc_'+id).style.display = 'none';
		document.getElementById('video_'+id).style.display = 'none';
		document.getElementById('github_'+id).style.display = 'none';
	} else if(type == 'doc'){
		document.getElementById('info_'+id).style.display = 'none';
		document.getElementById('doc_'+id).style.display = 'block';
		document.getElementById('video_'+id).style.display = 'none';
		document.getElementById('github_'+id).style.display = 'none';
	} else if(type == 'video'){
		document.getElementById('info_'+id).style.display = 'none';
		document.getElementById('doc_'+id).style.display = 'none';
		document.getElementById('video_'+id).style.display = 'block';
		document.getElementById('github_'+id).style.display = 'none';
	} else if(type == 'github'){
		document.getElementById('info_'+id).style.display = 'none';
		document.getElementById('doc_'+id).style.display = 'none';
		document.getElementById('video_'+id).style.display = 'none';
		document.getElementById('github_'+id).style.display = 'block';
	}
}
function tema_submit(id, id_tema)
{
	// Guardamos informacion del tema
	var title = $('#form_tema_'+id).find('#title').val();

	if(check(title)){ return }

	document.getElementById('form_tema_'+id).style.display = 'none';
	// Mostramos imagen cargando
	document.getElementById('cargando_line_'+id).innerHTML = '<img src="/img/cargando.gif">';

	ajax(url, {
		title: title,
		id: id,
		id_tema: id_tema,
		type: 'form_tema_edit'
	}, function (data){
		// Ocultamos imagen cargando
		document.getElementById('cargando_line_'+data.id).innerHTML = '';

		document.getElementById('form_tema_'+id).style.display = 'block';
		// Actualizamos el valor del formulario
		$('#form_tema_'+data['id']).find('#title').val(data['title']);
		// Ocultamos el formulario
		document.getElementById('form_'+data.id).style.display = 'none';
		// Mostramos el tema
		document.getElementById('tema_'+data.id).style.display = 'block';
		// Actualizamos el titulo
		document.getElementById('tema_title_'+data.id).innerHTML = data.title;

		success(data.status);
	}, 'Json');
}

// Eliminamos una categoria
function delete_category(id)
{
	// Mostramos imagen cargando
	document.getElementById("cat_"+id).innerHTML = '<img src="/img/cargando.gif">';

	ajax(url, {
		type: "eliminar_categoria",
		id: id
	}, function (data){
		document.getElementById("cat_"+data.id).innerHTML = data.status;

		success(data.status);
	}, "Json");
}

// Acciones para la caja de herramientas del textarea
function toolbox(id, identificador)
{
	if(id == 1){
		// Añadimos opcion de codigo
		var sel = getCursorSelection(document.getElementById(identificador));
		if(!sel){
			// Si no hay texto seleccionado usamos uno por defecto
			sel = "// aqui va el codigo";
		}
		var inner = "[code]"+sel+"[/code]"; // Contenido a insertar
		var pos = getCursorPosition(document.getElementById(identificador)); // Obtenemos la posicion del puntero
		var texto = document.getElementById(identificador).value; // Obtenemos el contenido del textarea

		// reemplazamos todo el contenido
		texto = texto.substr(0, pos) + inner + texto.substr(pos + sel.length, texto.length);

		document.getElementById(identificador).value = texto;
		setCursorPosition(document.getElementById(identificador), pos + (inner.length - 7)); // Colocamos el cursor al final del texto insertado
		document.getElementById(identificador).focus();
	} else if(id == 2){
		// Añadimos la opcion de negrita
		var sel = getCursorSelection(document.getElementById(identificador)); // Obtenemos seleccion
		var inner = "[b]"+sel+"[/b]";
		var pos = getCursorPosition(document.getElementById(identificador));
		var texto = document.getElementById(identificador).value;

		// reemplazamos el texto
		texto = texto.substr(0, pos) + inner + texto.substr(pos + sel.length, texto.length);

		document.getElementById(identificador).value = texto;
		setCursorPosition(document.getElementById(identificador), pos + inner.length);
		document.getElementById(identificador).focus();
	} else if(id == 3){
		// Añadimos la opcion de cursiva
		var sel = getCursorSelection(document.getElementById(identificador)); // Obtenemos seleccion
		var inner = "[I]"+sel+"[/I]";
		var pos = getCursorPosition(document.getElementById(identificador));
		var texto = document.getElementById(identificador).value;

		// reemplazamos el texto
		texto = texto.substr(0, pos) + inner + texto.substr(pos + sel.length, texto.length);

		document.getElementById(identificador).value = texto;
		setCursorPosition(document.getElementById(identificador), pos + inner.length);
		document.getElementById(identificador).focus();
	} else if(id == 4){
		// Añadimos la opcion de subrayado
		var sel = getCursorSelection(document.getElementById(identificador)); // Obtenemos seleccion
		var inner = "[u]"+sel+"[/u]";
		var pos = getCursorPosition(document.getElementById(identificador));
		var texto = document.getElementById(identificador).value;

		// reemplazamos el texto
		texto = texto.substr(0, pos) + inner + texto.substr(pos + sel.length, texto.length);

		document.getElementById(identificador).value = texto;
		setCursorPosition(document.getElementById(identificador), pos + inner.length);
		document.getElementById(identificador).focus();
	} else if(id == 5){
		// Añadimos la opcion de tachado
		var sel = getCursorSelection(document.getElementById(identificador)); // Obtenemos seleccion
		var inner = "[t]"+sel+"[/t]";
		var pos = getCursorPosition(document.getElementById(identificador));
		var texto = document.getElementById(identificador).value;

		// reemplazamos el texto
		texto = texto.substr(0, pos) + inner + texto.substr(pos + sel.length, texto.length);

		document.getElementById(identificador).value = texto;
		setCursorPosition(document.getElementById(identificador), pos + inner.length);
		document.getElementById(identificador).focus();
	}
}

/***********************************************************************
FUNCIONES COMPLEMENTARIAS
***********************************************************************/

// Checamos que no incluyan caracteres invalidos
function check(data)
{
	var text = '';

	if(data.length == 0){
		text = '* Debe llenar todos los campos del formulario';
	} else if(data.indexOf("<") != -1){
		text = 'Esta ingresando caracteres no permitidos "<"';
	} else if(data.indexOf(">") != -1){
		text = 'Esta ingresando caracteres no permitidos ">"';
	} else if(data.indexOf(";") != -1){
		text = 'Esta ingresando caracteres no permitidos ";"';
	}

	if(!isEmpty(text)){
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('error');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = text;
				temp[i].style.marginTop = '0px';
			}
		} else {
			document.querySelector('.error').innerHTML = text;
			document.querySelector('.error').style.transform = 'translateY(0)';
		}
		
		setTimeout(function(){
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('error');

				for(var i = 0; i < temp.length; i++){
					temp[i].style.marginTop = '-60px';
				}
			} else {
				document.querySelector('.error').style.transform = 'translateY(-60px)';
			}
		}, 3000);

		return true;
	}
}

// Mostramos un error
function error (text)
{
	if(version <= 8){
		// Soporte a navegadores antiguos
		var temp = getElementsByClassName('error');

		for(var i = 0; i < temp.length; i++){
			temp[i].innerHTML = text;
			temp[i].style.marginTop = '0px';
		}
	} else {
		document.querySelector('.error').innerHTML = text;
		document.querySelector('.error').style.transform = 'translateY(0)';
		// Soporte a IE 9 y safari
		document.querySelector('.error').style.msTransform = 'translateY(0)';
		document.querySelector('.error').style.webkitTransform = 'translateY(0)';
	}
	
	setTimeout(function(){
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('error');

			for(var i = 0; i < temp.length; i++){
				temp[i].style.marginTop = '-60px';
			}
		} else {
			document.querySelector('.error').style.transform = 'translateY(-60px)';
			// Soporte a IE 9 y safari
			document.querySelector('.error').style.msTransform = 'translateY(-60px)';
			document.querySelector('.error').style.webkitTransform = 'translateY(-60px)';
		}
	}, 3000);	
}

// Mostramos success
function success (text)
{
    if(version <= 8){
        // Soporte a navegadores antiguos
        var temp = getElementsByClassName('success');

        for(var i = 0; i < temp.length; i++){
            temp[i].innerHTML = text;
            temp[i].style.marginTop = '0px';
        }
    } else {
        document.querySelector('.success').innerHTML = text;
        document.querySelector('.success').style.transform = 'translateY(0)';
        // Soporte a IE 9 y safari
        document.querySelector('.success').style.msTransform = 'translateY(0)';
        document.querySelector('.success').style.webkitTransform = 'translateY(0)';
    }
    
    setTimeout(function(){
        if(version <= 8){
            // Soporte a navegadores antiguos
            var temp = getElementsByClassName('success');

            for(var i = 0; i < temp.length; i++){
                temp[i].style.marginTop = '-60px';
            }
        } else {
            document.querySelector('.success').style.transform = 'translateY(-60px)';
            // Soporte a IE 9 y safari
            document.querySelector('.success').style.msTransform = 'translateY(-60px)';
            document.querySelector('.success').style.webkitTransform = 'translateY(-60px)';
        }
    }, 3000);   
}