/************************************************
Archivo javascript de la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Variables globales
var codigo = Array("<", ">","){","(","))",")","\n", "[b]", "[/b]", "[I]", "[/I]",
	"[u]", "[/u]","[t]","[/t]","'","javascript:","array:","text/html");
var replace = Array("[-","-]",") {","&-","--|","-&","<br>","<b>","</b>","<i>","</i>",
	"<u>","</u>","<strike>","</strike>",'"',"javascript :","array :","text/htm");
var alertas = 0; // Control para procesar solicitud de notificaciones
var cargando_blue = '<div class="loader loader-blue"></div>'; // Imagen css
var cargando_white = '<div class="loader loader-white"></div>'; // Imagen css
var control_files = 0;
var control_files_dis = 0;
var control_form = 0;
var control_form_res = 0;
var control_notificacion = 0; // Numero de notificaciones disponibles
var control_voto = 0;
var curso_content;
var dis_cargar = "<div id='dis_cargar'><button onclick='javascript:dis_cargar_()' class='icon-reload'>Ver mas discusiones</button><div class='cargando'></div></div>";
var dis_content = '';
var files_content = '';
var notas_content = '';
var dis_default; // Texto a mostrar por default para las discuciones
var dis_link = 0;
var dis_no;
var dis_no_start = 0;
var dis_nueva;
var dis_nueva_start = 0;
var dis_pop;
var dis_pop_start = 0;
var dis_propia;
var dis_propia_start = 0;
var doc_cache;
var doc_default; // Texto a mostrar por default para la documentacion
var discucion_tiempo;
var find_cache;
var find_default; // Texto a mostrar por default para la busqueda
var find_value = '';
var id_curso = localStorage.getItem('id_curso'); // ID del curso
var indicador = 0;
var menu_count = 0;
var not_start = 0; // Control desde donde cargar notificaciones
var nota_cache;
var nota_default;
var router_dis = 1;
var router_menu = 1;
var curso_url;
var curso_title;

// Utilizamos la pantalla cargando
intro();

// Detectar si nos visitan de IE9-
var nav = navigator.appName;
if(nav == "Microsoft Internet Explorer"){
    // Convertimos en minusculas la cadena que devuelve userAgent
    var ie = navigator.userAgent.toLowerCase();
    // Extraemos de la cadena la version de IE
    var version = parseInt(ie.split('msie')[1]);

    // Dependiendo de la version mostramos un resultado
    if(version <= 9){
    	cargando_blue = '<div class="cargando"><img src="/img/cargando.gif"></div>';
    	cargando_white = '<div class="cargando"><img src="/img/cargando.gif"></div>';
    }
}

docReady(function (){
	// Deshabilitamos el menu contextual
	/*
	$(document).bind("contextmenu", function(e){  
        return false;  
    });
*/
	// Mostramos y ocultamos el menu lateral segun el tamaño de la pantalla
	document.getElementById('mostrar_menu').onclick = function (){
		// Obtenemos el ancho de la pantalla
		width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
		if(width < 600){
			menu(width);
		} else {
			menu(395);
		}
	};
	// Redimencionamos las ventanas
	wrapper();
	// Si es menor a 900 mostramos boton
	var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	if(width < 900 && width > 600){
		document.getElementById('mostrar_menu').style.display = 'block';
		menu_count = 0;
	} else if(width < 600){
		document.getElementById('mostrar_menu').style.display = 'block';
		document.getElementById('mostrar_menu').innerHTML = '<span class="icon-right"></span>';
		menu_count = 1;
	}
	// Mostramos el boton segun el tamaño de la ventana
	menu_button();
	// Cargamos los datos del curso
	curso_cargar();
	// Router para el menu
	menu_router();
	// opciones del menu
	options();
	// Mostramos mensajes en el area de usuarios al llegar al limite del scroll
	user_scroll();
});
// Cuando redimencionamos la ventana del navegador
window.onresize = function (){
	//defineSizes();
	menu_button();
	menu_principal();
	wrapper();
	intro();
	setTimeout(function(){
		defineSizes();
		menu_button();
		menu_principal();
		wrapper();
		intro();
	}, 500);
	setTimeout(function(){
		defineSizes();
		menu_button();
		menu_principal();
		wrapper();
		intro();
	}, 1500);
};
window.onload = function (){
	menu_button();
	menu_principal();
	wrapper();
	setTimeout(function (){
		if(control_notificacion > 0){
			document.getElementById('notificacion').style.display = 'inline-block';
			document.getElementById('notificacion').innerHTML = control_notificacion;

			// Modificamos el titulo si hay notificaciones
			var title = document.title;
			document.title = '('+control_notificacion+') '+title;
		} else {
			document.getElementById('notificacion').style.display = 'none';
		}
	}, 1000);
	// Define el tamaño del slider para los twitts
	defineSizes();
};
// Mostramos las alertas
function alerta() // OK
{
	// Verificamos si podemos procesar las notificaciones
	if(alertas == 0){
		// Si es asi mostramos el div de las notificaciones
		document.getElementById('alertas').style.transform = 'translateY(0)';
		document.getElementById('alertas').style.webkitTransform = 'translateY(0)';

		// Cargamos notificaciones nuevas
		notificacion_mostrar();

		alertas = 1;
	} else {
		// Si no ocultamos el div de las notificaciones
		document.getElementById('alertas').style.transform = 'translateY(-100%)';
		document.getElementById('alertas').style.webkitTransform = 'translateY(-100%)';

		// Regresamos el valor a 0 para poder procesar mas notificaciones
		alertas = 0;
	}
}
// Ocultamos un tema y regresamos al curso
function back()
{
	document.getElementById('wrapper_2').style.transform = 'translateX(-100%)';
	document.getElementById('wrapper_2').style.webkitTransform = 'translateX(-100%)';
	document.getElementById('wrapper_2').innerHTML = '';

	// Si esta activado el menu documentacion mostramos
	if(router_menu == 1){
		document.getElementById('menu_content').innerHTML = doc_default;
	}
	// Eliminamos la cache de la documentacion
	doc_cache = '';

	// Cambiamos la url
	history.replaceState({},'', curso_url);

	// Cambiamos el contenido del elemento title
	if(control_notificacion > 0){
		// Modificamos el titulo si hay notificaciones
		document.title = '('+control_notificacion+') '+curso_title+' | codeando.org';
	} else {
		document.title = curso_title+' | codeando.org';
	}
}
// Ocultamos la vista previa del archivo
function back_files()
{
	document.getElementById('wrapper_4').style.transform = 'translateX(-100%)';
	document.getElementById('wrapper_4').style.webkitTransform = 'translateX(-100%)';
	document.getElementById('wrapper_4').innerHTML = '';
}
// Ocultamos la informacion del usuario
function back_user()
{
	document.getElementById('wrapper_3').style.transform = 'translateX(-100%)';
	document.getElementById('wrapper_3').style.webkitTransform = 'translateX(-100%)';
	document.getElementById('wrapper_3').innerHTML = '';
}
// Formulario para buscar
function buscador(e) // OK
{
	// Obtenemos el valor a buscar
	var q = document.getElementById('q').value;
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Si el valor es mayor a 0 mostramos imagen cargando
	if(q.length > 0){
		// Si tiene uno o mas digitos
		document.getElementById('buscador_cargando').style.display = 'block';

		ajax('include/server_curso.php',{
			id_curso: id_curso,
			q: q,
			type: 'buscador'
		}, function (data){
			// Mostramos respuesta
			document.getElementById('buscador_respuesta').innerHTML = data.find;
			// Quitemoas la imagen cargando
			document.getElementById('buscador_cargando').style.display = 'none';
			// Actualizamos la cache del buscador
			find_cache = find_default + data.find + '</div>';
			// Actualizamos el valor de la busqueda
			find_value = q;
		}, 'Json');
	} else {
		// Si no hay nada
		// Limpiamos las respuestas
		document.getElementById('buscador_respuesta').innerHTML = '';
		// Actualizamos la cache del buscador
		find_cache = find_default + '</div>';
		// Limpiamos el valor del formulario
		find_value = '';
	}
}
// Cargamos la informacion del curso
function curso_cargar() // OK
{
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Verificamos si existe una ID del curso
	if(!isEmpty(id_curso)){
		// Si el objeto no esta vacio
		// Mostramos imagenes de cargando
		document.getElementById('wrapper_1').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';
		document.getElementById('menu_content').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';

		ajax('include/server_curso.php', {
			id: id_curso,
			id_user: localStorage.getItem('id_user'),
			type: 'curso_cargar'
		}, function (data){
	    	// Guardamos los textos cargados por default
			doc_default = data.doc;
			dis_default = data.dis;
			find_default = data.find;
			control_notificacion = data.not_num;

			// Cambiamos la imagen del usuario
			document.getElementById('img_autor').setAttribute('src', data.user);

			// Mostramos notificaciones si las hay
			if(data.not_num > 0){
				document.getElementById('notificacion').style.display = 'inline-block';
				document.getElementById('notificacion').innerHTML = data.not_num;
			} else {
				document.getElementById('notificacion').style.display = 'none';
			}

			document.getElementById('wrapper_1').innerHTML = data.curso;
			document.getElementById('menu_content').innerHTML = data.doc;

			// Quitamos la pantalla de cargando
			document.getElementById('intro').style.display = 'none';

			// Cambiamos el contenido de la etiqueta title
			curso_title = data.titulo+' | Codeando.org';
			document.title = data.titulo+' | Codeando.org';
			// Damos formato al titulo
			var titulo = data.titulo.toLowerCase();
			titulo = titulo.replace(/ /g,'-');
			history.replaceState({},'','/plataforma/'+titulo);
			// Almacenamos en la cache la url del curso
			curso_url = '/plataforma/'+titulo;

			// Obtenemos los hastag del curso
			var categoria = data.categoria;
			if(categoria == 'Moviles'){
				categoria = 'Lungo';
			}
			var hashtag = 'Curso'+categoria+'EnCodeando';
			twitter_hashtag(hashtag);
	    }, 'Json');
	} else {
		// Si no existe regresamos a la pagina de seleccion de cursos
		location.href = '/cursos/';
	}
}
// Funciones slider para los twitts
function defineSizes()
{
	// Funcion que asigna automaticamente el ancho del slider
	var size = document.getElementById('w_content').style.pixelWidth || document.getElementById('w_content').offsetWidth;

	document.getElementById('twitter_title').style.width = (size - 40)+'px';
	document.getElementById('twitter_title').style.maxWidth = (size - 40)+'px';

	var twitter = document.querySelectorAll('.twitter');
	for(var i = 0; i < twitter.length; i++){
		twitter[i].style.width = (size - 60)+"px";
		twitter[i].style.maxWidth = (size - 60)+"px";
		twitter[i].style.marginRight = '10px';
	}

	var width = document.getElementById('twitter_title').style.pixelHeight || document.getElementById('twitter_title').offsetHeight;
	document.getElementById('twitter_contenedor').style.marginLeft = -(indicador * width)+"px";
}
// Ocultamos la discucion y mostramos el menu
function dis_back()
{
	var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

	// Ocultamos la discusion
	document.getElementById('menu_discucion').style.transform = 'translateX('+width+'px)';
	document.getElementById('menu_discucion').style.webkitTransform = 'translateX('+width+'px)';

	// Regresamos a su estado original el formulario de la respuesta
	document.querySelector('#res_form #submit').style.display = 'none';
	document.querySelector('#res_form #resp_toolbox').style.display = 'none';
	document.getElementById('form_res_info').innerHTML = '';
	document.getElementById('content_res').style.height = '25px';
	document.getElementById('content_res').style.minHeight = '25px';
	document.getElementById('content_res').value = '';
	document.getElementById('res_form').style.display = 'none';

	control_form_res = 0;
}
// Cargamos mas discuciones
function dis_cargar_() // OK
{
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Ocultamos el boton submit
	document.querySelector('#dis_cargar button').style.display = 'none';
	// Mostramos la imagen cargando
	document.querySelector('#dis_cargar .cargando').innerHTML = cargando_blue;

	// Dependiendo en que parte del menu de discusiones nos encontramos, cargamos sus discusiones
	if(router_dis == 1){
		// Para discusiones nuevas
		ajax('include/server_discucion.php',{
			id_curso: id_curso,
			start: dis_nueva_start,
			type: 'dis_nueva'
		}, function (data){
			// Actualizamos el control para discusiones nuevas
			dis_nueva_start = data.start;

			// Quitamos la imagen cargando
			document.querySelector('#dis_cargar .cargando').innerHTML = '';
			// Mostramos las discusiones
			document.getElementById('discucion_mostrar').innerHTML += data.dis;
			// Actualizamos la cache de las discusiones nuevas
			dis_nueva = dis_nueva + data.dis;

			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Si no hay volvemos a mostrar el boton submit
				document.querySelector('#dis_cargar button').style.display = 'inline-block';
			} else {
				// Si hay no mostramos el boto submit y mostramos avizo
				document.querySelector('#dis_cargar button').style.display = 'none';
				document.querySelector('#dis_cargar .cargando').innerHTML = 'No hay mas discusiones';
			}
		}, 'Json');
	} else if(router_dis == 2){
		// Para discusiones populares
		ajax('include/server_discucion.php',{
			id_curso: id_curso,
			start: dis_pop_start,
			type: 'dis_pop'
		}, function (data){
			// Actualizamos control de carga
			dis_pop_start = data.start;

			// Quitamos imagen cargando
			document.querySelector('#dis_cargar .cargando').innerHTML = '';
			// Mostramos discusiones
			document.getElementById('discucion_mostrar').innerHTML += data.dis;
			// Actualizamos la cache
			dis_pop = dis_pop + data.dis;

			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Si no hay error mostramos boton submit
				document.querySelector('#dis_cargar button').style.display = 'inline-block';
			} else {
				// Si hay error no mostramos boton submit
				document.querySelector('#dis_cargar button').style.display = 'none';
				document.querySelector('#dis_cargar .cargando').innerHTML = 'No hay mas discusiones';
			}
		}, 'Json');
	} else if(router_dis == 3){
		// Para discusiones sin responder
		ajax('include/server_discucion.php',{
			id_curso: id_curso,
			start: dis_no_start,
			type: 'dis_no'
		}, function (data){
			// Actualizamos control de carga
			dis_no_start = data.start;

			// Quitamos imagen cargando
			document.querySelector('#dis_cargar .cargando').innerHTML = '';
			// Mostramos discusiones
			document.getElementById('discucion_mostrar').innerHTML += data.dis;
			// Actualizamos cache
			dis_no = dis_no + data.dis;

			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Si no hay error mostramos boton submit
				document.querySelector('#dis_cargar button').style.display = 'inline-block';
			} else {
				// Si hay error no mostramos boton submit
				document.querySelector('#dis_cargar button').style.display = 'none';
				document.querySelector('#dis_cargar .cargando').innerHTML = 'No hay mas discusiones';
			}
		}, 'Json');
	} else if(router_dis == 4){
		// Para discusiones propias
		ajax('include/server_discucion.php',{
			id_curso: id_curso,
			start: dis_propia_start,
			type: 'dis_propia'
		}, function (data){
			// Actualizamos control de carga
			dis_propia_start = data.start;

			// Ocultamos imagen cargando
			document.querySelector('#dis_cargar .cargando').innerHTML = '';
			// Mostramos discusiones
			document.getElementById('discucion_mostrar').innerHTML += data.dis;
			// Actualizamos cache
			dis_propia = dis_propia + data.dis;

			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Si no hay error mostramos boton submit
				document.querySelector('#dis_cargar button').style.display = 'inline-block';
			} else {
				// Si hay error no mostramos boton submit
				document.querySelector('#dis_cargar button').style.display = 'none';
				document.querySelector('#dis_cargar .cargando').innerHTML = 'No hay mas discusiones';
			}
		}, 'Json');
	}
}
// Mostramos la opcion de eliminar la discusion
function dis_delete() // OK
{
	document.getElementById('dis_option').style.display = 'none';
	document.getElementById('dis_delete').style.display = 'block';
}
// Ocultamos la opcion de eliminar la discusion
function dis_delete_no() // OK
{
	document.getElementById('dis_delete').style.display = 'none';
	document.getElementById('dis_option').style.display = 'block';
}
// Eliminamos la discusion
function dis_delete_yes(id) // OK
{
	ajax('include/server_discucion.php',{
		id: id,
		type: 'dis_delete'
	}, function (data){
		// Verificamos si hay error
		if(isEmpty(data.error)){
			// Si el objeto esta vacio ocultamos la discusion
			document.getElementById('res_form').style.display = 'none';
			var element = document.querySelectorAll('.dis_'+ data.id);
			for(var i = 0; i < element.length; i++){
				element[i].innerHTML = '';
				element[i].style.display = 'none';
			}
			// Mostramos mensaje
			document.getElementById('dis_content').innerHTML = data.status;
			success(data.status);

			// Eliminamos la cache de la discusion
			localStorage.removeItem('cache_discucion'+data.id);

			// Actualizamos la cache
			if(router_dis == 1){
				dis_nueva = document.getElementById('discucion_mostrar').innerHTML;
			} else if(router_dis == 2){
				dis_pop = document.getElementById('discucion_mostrar').innerHTML;
			} else if(router_dis == 3){
				dis_no = document.getElementById('discucion_mostrar').innerHTML;
			} else if(router_dis == 4){
				dis_propia = document.getElementById('discucion_mostrar').innerHTML;
			}
		} else {
			// Mostramos un error
			error(data.error);
		}
	}, 'Json');
}
// Mostramos el formulario para editar una discusion
function dis_edit(id) // OK
{
	document.getElementById('dis_content_'+id).style.display = 'none';
	document.getElementById('dis_option').style.display = 'none';
	document.getElementById('res_form').style.display = 'none';
	document.getElementById('dis_form_'+id).style.display = 'block';

	// Modificamos valor para no recibir mas peticiones
	control_files_dis++;

	// Solo activamos el gestor de archivos si no nos visitan desde un movil
	var device = navigator.userAgent;
	var valor = device.match(/Iphone/i)|| device.match(/Ipod/i)|| device.match(/Android/i)|| device.match(/J2ME/i)|| device.match(/BlackBerry/i)|| device.match(/iPhone|iPad|iPod/i)|| device.match(/Opera Mini/i)|| device.match(/IEMobile/i)|| device.match(/Mobile/i)|| device.match(/Windows Phone/i)|| device.match(/windows mobile/i)|| device.match(/windows ce/i)|| device.match(/webOS/i)|| device.match(/palm/i)|| device.match(/bada/i)|| device.match(/series60/i)|| device.match(/nokia/i)|| device.match(/symbian/i)|| device.match(/HTC/i);

	if(!valor){
		// Mostramos el area para arrastrar archivos
		document.getElementById('content_dis_edit_files').style.display = 'block';
		// Activamos arrastrar y soltar para archivos
		files_dis(control_files_dis,id);
	}
}
// ocultamos el formulario para editar una discusion
function dis_edit_cancelar(id) // OK
{
	document.getElementById('dis_form_'+id).style.display = 'none';
	document.getElementById('res_form').style.display = 'block';
	document.getElementById('dis_option').style.display = 'block';
	document.getElementById('dis_content_'+id).style.display = 'block';
}
// Editamos la discusion
function dis_editar(id) // OK
{
	// Obtenemos el contenido a editar
	var contenido = document.getElementById('content_dis_edit').value;
	// Obtenemos el Link de la discusion (En caso de haberlo)
	var link = document.getElementById('link_edit').value;

	// Verificamos si hay link
	if(!isEmpty(link)){
		// Verificamos que si se trate de un link
		if(validateUrl(link) != link){
			// Si no es un link mostramos error
			error('Ingrese una url valida');
			return
		}
	}

	// Verificamos que no este vacio el contenido
	if(contenido.length == 0){
		return
	}

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(contenido.indexOf(codigo[i]) >= 0){
			contenido = contenido.replace(codigo[i], replace[i]);
		}
	}

	// Reemplazamos las url planas
	//contenido = url_replace(contenido);

	// Ocultamos el boton submit
	document.getElementById('submit_edit').style.display = 'none';
	// Ocultamos el boton cancelar
	document.getElementById('cancelar_edit').style.display = 'none';
	// Mostramos informacion d guardando
	document.getElementById('dis_edit_info').innerHTML = 'Guardando . . .';

	ajax('include/server_discucion.php',{
		contenido: contenido,
		link: link,
		id: id,
		type : 'discucion_editar'
	}, function (data){
		// Verificamos que icono mostrar
		var temp = document.getElementById('icon'+data.id);
		if(data.control == 'none'){
			// Removemos las dos clases	
			removeClass(temp, 'icon-archive-icon');
			removeClass(temp, 'icon-link-icon');
		} else if(data.control == 'link'){
			// Removes la clase file
			removeClass(temp, 'icon-archive-icon');
			// Agregamos la clase link
			addClass(temp, 'icon-link-icon');
		} else if(data.control == 'file'){
			// Removemos la clase link
			removeClass(temp, 'icon-link-icon');
			// Agregamos la clase file
			addClass(temp, 'icon-archive-icon');
		}

		// Actualizamos el contenido del form edicion
		document.getElementById('content_dis_edit').value = data.contenido_edit;
		// Limpiamos la informacion
		document.getElementById('dis_edit_info').innerHTML = '';
		// Mostramos el boron submit
		document.getElementById('submit_edit').style.display = 'inline-block';
		// Mostramos el boton cancelar
		document.getElementById('cancelar_edit').style.display = 'inline-block';

		// Ocultamos el formulario de edicion
		document.getElementById('dis_form_'+id).style.display = 'none';
		// Mostramos el formulario para respuestas
		document.getElementById('res_form').style.display = 'block';
		// Mostramos las opciones de la discusion
		document.getElementById('dis_option').style.display = 'block';
		// Mostramos el contenido de la discusion
		document.getElementById('dis_content_'+id).style.display = 'block';
		// Actualizamos el titulo de la discusion
		var element1 = document.querySelectorAll('.dis_'+data.id+' .d_title');
		for(var i = 0; i < element1.length; i++){
			element1[i].innerHTML = data.titulo;
		}
		// Actualizamos el contenido de la discusion (resumen)
		var element2 = document.querySelectorAll('.dis_'+data.id+' .d_content');
		for(var i = 0; i < element2.length; i++){
			element2[i].innerHTML = data.content;
		}

		// Actualizamos el contenido de la discucion
		document.getElementById('dis_content_'+id).innerHTML = data.contenido + data.files;

		// Actualizamos la cache
		if(router_dis == 1){
			dis_nueva = document.getElementById('discucion_mostrar').innerHTML;
		} else if(router_dis == 2){
			dis_pop = document.getElementById('discucion_mostrar').innerHTML;
		} else if(router_dis == 3){
			dis_no = document.getElementById('discucion_mostrar').innerHTML;
		} else if(router_dis == 4){
			dis_propia = document.getElementById('discucion_mostrar').innerHTML;
		}

		// Removemos la cache de la discusion
		localStorage.removeItem('cache_discucion'+data.id);

		resaltador();
	}, 'Json');
}
// Mostramos el boton del lin
function dis_enlace()
{
	if(dis_link == 0){
		document.querySelector('#form_discucion #link').style.display = 'block';
		dis_link = 1;
	} else {
		document.querySelector('#form_discucion #link').style.display = 'none';
		dis_link = 0;
	}
}
// Mostramos el contenido de una discucion
function dis_mostrar(id) // OK
{
	// Obtenemos el ancho de la pantalla
	var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

	// Mostramos el div para la discusion
	if(width >= 900){
		var win = width - 525;

		document.getElementById('menu_discucion').style.transform = 'translateX('+win+'px)';
		document.getElementById('menu_discucion').style.webkitTransform = 'translateX('+win+'px)';
	} else if(width > 600 && width < 899){
		var win = width - 395;

		document.getElementById('menu_discucion').style.transform = 'translateX('+win+'px)';
		document.getElementById('menu_discucion').style.webkitTransform = 'translateX('+win+'px)';
	} else if(width <= 600){
		var win = 0;

		document.getElementById('menu_discucion').style.transform = 'translateX('+win+'px)';
		document.getElementById('menu_discucion').style.webkitTransform = 'translateX('+win+'px)';
	}

	// Opciones para cargar la discucion
	document.getElementById('dis_content').innerHTML = '';
	document.getElementById('res_content').innerHTML = '';

	// Verificamos si existe la cache de la discucion
	var cache = JSON.parse(localStorage.getItem('cache_discucion'+id));

	if(cache){
		// Si existe la cache la cargamos
		var minutos = 5;

		// Verificamos si el tiempo esta en el rango
		if( (new Date().getTime() - (minutos * 60 * 1000)) < cache.time ) {
			document.querySelector('#menu_discucion #dis_content').innerHTML = cache.res.dis;
			document.querySelector('#menu_discucion #res_content').innerHTML = cache.res.res;
			document.getElementById('res_form').style.display = 'block';
			document.querySelector('#menu_discucion .cargando').innerHTML = '';

			resaltador();

			return false;
		} else {
			// Si la cache ya expiro volvemos a cargar la discusion
			document.querySelector('#menu_discucion .cargando').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';
			ajax('include/server_discucion.php',{
				id: id,
				type: 'dis_mostrar'
			}, function (data){
				if(isEmpty(data.error)){
					// Si el objeto esta vacio
					document.querySelector('#menu_discucion #dis_content').innerHTML = data.dis;
					document.querySelector('#menu_discucion #res_content').innerHTML = data.res;
					document.getElementById('res_form').style.display = 'block';
					document.querySelector('#menu_discucion .cargando').innerHTML = '';

					resaltador();

					// Eliminamos la cache anterior
					localStorage.removeItem('cache_discucion'+data.id);

					// Guardamos en la cache
					localStorage.setItem('cache_discucion'+data.id, JSON.stringify({
						'time': new Date().getTime(),
						'res': data
					}));
				} else {
					document.querySelector('#menu_discucion .cargando').innerHTML = '';
					document.getElementById('dis_content').innerHTML = data.error;

					// Ocultamos formulario para respuestas
					document.getElementById('res_form').style.display = 'none';
				}
			}, 'Json');	
		}
	} else {
		// Si no existe cargamos los datos desde el servidor
		document.querySelector('#menu_discucion .cargando').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';
		ajax('include/server_discucion.php',{
			id: id,
			type: 'dis_mostrar'
		}, function (data){
			if(isEmpty(data.error)){
				// Si el objeto esta vacio
				document.querySelector('#menu_discucion #dis_content').innerHTML = data.dis;
				document.querySelector('#menu_discucion #res_content').innerHTML = data.res;
				document.getElementById('res_form').style.display = 'block';
				document.querySelector('#menu_discucion .cargando').innerHTML = '';

				resaltador();
				// Guardamos en la cache
				localStorage.setItem('cache_discucion'+data.id, JSON.stringify({
					'time': new Date().getTime(),
					'res': data
				}));
			} else {
				document.querySelector('#menu_discucion .cargando').innerHTML = '';
				document.getElementById('dis_content').innerHTML = data.error;

				// Ocultamos formulario para respuestas
				document.getElementById('res_form').style.display = 'none';
			}
		}, 'Json');
	}
}
// Router para el sistema de discuciones
function dis_router(id)
{
		control_form = 0;
		dis_link = 0;

		// Si queremos entrar a la seccion en la que ya estamos regresamos
		if(router_dis == id){
			return
		}

		// Si hay contenido en el textarea de la discusion lo cacheamos
		dis_content = document.getElementById('content_dis').value;
		files_content = document.getElementById('content_dis_results').innerHTML;

		if(id == 1){
			document.getElementById('menu_content').innerHTML = dis_default + dis_nueva + '</div>' + dis_cargar;
		} else if(id == 2){
			document.getElementById('menu_content').innerHTML = dis_default + dis_pop + '</div>'+dis_cargar;
		} else if(id == 3){
			document.getElementById('menu_content').innerHTML = dis_default + dis_no + '</div>'+dis_cargar;
		} else if(id == 4){
			document.getElementById('menu_content').innerHTML = dis_default + dis_propia + '</div>'+dis_cargar;
		}

		// Si habia contenido en el textarea lo cargamos
		if(dis_content.length > 0){
			mostrar_discucion();
			document.getElementById('content_dis').value = dis_content;
			document.getElementById('content_dis_results').innerHTML = files_content;
		}

		// Obtenemos el elemento .controls
		var controls = document.querySelectorAll('.controls');

		for(var i = 0; i < controls.length; i++){
			if(controls[i].getAttribute('data-type') == id){
				addClass(controls[i], 'dis_router');
			} else {
				removeClass(controls[i], 'dis_router');
			}
		}

		router_dis = id;
}
// Publicamos una discucion
function dis_publicar() // OK
{
	// Obtenemos el contenido del formulario
	var contenido = document.getElementById('content_dis').value;
	var link = '';
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Verificamos si utilizamos un enlace en el formulario
	if(dis_link == 1){
		// Obtenemos el contenido del enlace
		link = document.getElementById('link').value;
		// Validamos que se trate de un enlace valido
		if(validateUrl(link) != link)
		{
			error('Ingrese una url valida');
			return
		}
	}

	// Verificamos que no este vacio el contenido
	if(contenido.length == 0 || contenido.length < 20){
		error('Debe ingresar al menos 20 caracteres');
		return
	}

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(contenido.indexOf(codigo[i]) >= 0){
			contenido = contenido.replace(codigo[i], replace[i]);
		}
	}

	// Reemplazamos las url planas
	//contenido = url_replace(contenido);

	// Ocultamos el boton submit
	document.getElementById('submit').style.display = 'none';
	// Mostramos texto cargando
	document.getElementById('form_info').innerHTML = 'Guardando . . .';

	ajax('include/server_discucion.php',{
		contenido: contenido,
		link: link,
		id: id_curso,
		control: control_files,
		type : 'discucion_publicar'
	}, function (data){
		// Manejo grafico
		// Limpiamos el contenido
		document.getElementById('content_dis').value = '';
		// Limpiamos el enlace
		document.getElementById('link').value = '';
		// Mostramos boton submit
		document.getElementById('submit').style.display = 'inline-block';
		// Mostramos mensajes de exito
		document.getElementById('form_info').innerHTML = 'La discusion se creo con exito';
		success('La discusión de creo con exito');
		// Limpamos la zona de archivos
		document.getElementById('content_dis_results').innerHTML = '';

		// Manejo de datos
		// Mostramos las discusiones nuevas
		router_dis = 1;
		// Actualizamos la cache de discusiones nuevas
		dis_nueva = data.dis + dis_nueva;
		// Actualizamos la cache de discusiones propias
		dis_propia = data.dis + dis_propia;
		// Mostramos la discusion nueva
		document.getElementById('discucion_mostrar').innerHTML = dis_nueva;

		// Obtenemos el elemento .controls
		var controls = document.querySelectorAll('.controls');

		for(var i = 0; i < controls.length; i++){
			if(controls[i].getAttribute('data-type') == 1){
				addClass(controls[i], 'dis_router');
			} else {
				removeClass(controls[i], 'dis_router');
			}
		}

		// Limpiamos la informacion de exito
		setTimeout(function(){
			document.getElementById('form_info').innerHTML = '';
		}, 3000);

		// Ocultamos la vista previa del archivo (en caso de haberla)
		back_files();
	}, 'Json');
}
// Voto a favor de la publicacion
function dis_voto_si(id) // OK
{
	// Si el control esta disponible procedemos
	if(control_voto == 0){
		// Actualizamos el control para no recibir peticiones
		control_voto = 1;
		ajax('include/server_discucion.php',{
			id: id,
			type: 'dis_voto_si'
		}, function (data){
			control_voto = 0;
			if(isEmpty(data.error)){
				// Actualizamos los votos de la discusion
				var element = document.querySelectorAll('.dis_voto_'+data.id);
				for(var i = 0; i < element.length; i++){
					element[i].innerHTML = data.votos;
				}

				if(router_dis == 1){
					dis_nueva = document.getElementById('discucion_mostrar').innerHTML;
				} else if(router_dis == 2){
					dis_pop = document.getElementById('discucion_mostrar').innerHTML;
				} else if(router_dis == 3){
					dis_no = document.getElementById('discucion_mostrar').innerHTML;
				} else if(router_dis == 4){
					dis_propia = document.getElementById('discucion_mostrar').innerHTML;
				}

				// Eliminamos la cache de la discucion para que se vuelva a cargar
				localStorage.removeItem('cache_discucion'+data.id);
			} else {
				error(data.error);
				return
			}
		}, 'Json');	
	}
}
// Voto en contra de la publicacion
function dis_voto_no(id)
{
	if(control_voto == 0){
		control_voto = 1;
		ajax('include/server_discucion.php',{
			id: id,
			type: 'dis_voto_no'
		}, function (data){
			control_voto = 0;

			if(isEmpty(data.error)){
				// Actualizamos los votos
				var element = document.querySelectorAll('.dis_voto_'+data.id);
				for(var i = 0; i < element.length; i++){
					element[i].innerHTML = data.votos;
				}

				if(router_dis == 1){
					dis_nueva = document.getElementById('discucion_mostrar').innerHTML;
				} else if(router_dis == 2){
					dis_pop = document.getElementById('discucion_mostrar').innerHTML;
				} else if(router_dis == 3){
					dis_no = document.getElementById('discucion_mostrar').innerHTML;
				} else if(router_dis == 4){
					dis_propia = document.getElementById('discucion_mostrar').innerHTML;
				}

				// Eliminamos la cache de la discucion para que se vuelva a cargar
				localStorage.removeItem('cache_discucion'+data.id);
			} else {
				error(data['error']);
				return
			}
		}, 'Json');	
	}
}
// Mostramos un error en pantalla
function error(text)
{
	document.querySelector('.error').innerHTML = text;
	document.querySelector('.error').style.transform = 'translateY(0)';
	document.querySelector('.error').style.webkitTransform = 'translateY(0)';
	setTimeout(function(){
		document.querySelector('.error').style.transform = 'translateY(-60px)';
		document.querySelector('.error').style.webkitTransform = 'translateY(-60px)';
	}, 3000);
}
// Mostramos informacion sobre un usuario del curso
function files_mostrar(id, tipo)
{
	// Verificamos si el argumento temp esta vacio
	if(tipo == null || tipo == undefined){
		tipo = 'temp';
	}

	document.getElementById('wrapper_4').style.transform = 'translateX(0)';
	document.getElementById('wrapper_4').style.webkitTransform = 'translateX(0)';

	// Mostramos la imagen de cargando
	document.getElementById('wrapper_4').innerHTML = cargando_blue;

	// Verificamos la cache de los temas
	var cache = JSON.parse(localStorage.getItem('cache_files'+id));
	var minutos = 5;

	if(cache){
		document.getElementById('wrapper_4').innerHTML = cache.res;
		resaltador();

		if((new Date().getTime() - (minutos * 60 * 1000)) > cache.time){
			ajax('include/server_files.php',{
				id: id,
				tipo: tipo,
				type: 'file_mostrar'
			}, function (data){
				document.getElementById('wrapper_4').innerHTML = data.datos;

				// Guardamos en la cache
				localStorage.setItem('cache_files'+data.id, JSON.stringify({
					'time': new Date().getTime(),
					'res': data.datos
				}));

				//resaltamos el codigo
				resaltador();
			}, 'Json');	
		}
	} else {
		ajax('include/server_files.php',{
			id: id,
			tipo: tipo,
			type: 'file_mostrar'
		}, function (data){
			document.getElementById('wrapper_4').innerHTML = data.datos;
			// Guardamos en la cache
			localStorage.setItem('cache_files'+data.id, JSON.stringify({
				'time': new Date().getTime(),
				'res': data.datos
			}));
			//resaltamos el codigo
			resaltador();
		}, 'Json');
	}
}
// Configuramos pantalla de inicio
function intro()
{
	var height = (window.screen.height / 2) - 100;
	document.getElementById('intro').style.paddingTop = height+'px';
}
// Mostramos el menu al dar clic
function menu(size)
{
	var win = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	console.log(win);
	var width = win - size;

	if(menu_count == 1){
		// Mostramos el menu y redimencionamos el wrapper
		document.getElementById('menu_principal').style.width = (size - 10)+'px';
		document.getElementById('menu_principal').style.transform = 'translateX('+width+'px)';
		document.getElementById('menu_principal').style.webkitTransform = 'translateX('+width+'px)';

		// Cambiamos el icono del boton
		document.getElementById('mostrar_menu').innerHTML = '<span class="icon-right"></span>';
		menu_count = 0;
	} else {
		document.getElementById('menu_principal').style.transform = 'translateX('+win+'px)';
		document.getElementById('menu_principal').style.webkitTransform = 'translateX('+win+'px)';

		// Cambiamos el icono del boton
		document.getElementById('mostrar_menu').innerHTML = '<span class="icon-left"></span>';
		menu_count = 1;
	}
}
// Mostramos el boton segun el tamaño de la ventana
function menu_button()
{
	var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

	if(width < 900 && width > 600){
		document.getElementById('mostrar_menu').style.display = 'block';
		menu_count = 0;
	} else if(width < 600){
		document.getElementById('mostrar_menu').style.display= 'block';
		document.getElementById('mostrar_menu').innerHTML = '<span class="icon-right"></span>';
		menu_count = 1;
	}
}
// Asignamos el alto al menu principal
function menu_principal()
{
	var header = document.querySelector('header').style.pixelHeight || document.querySelector('header').offsetHeight;
	var win = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	var height = win - header;
	var height_content = win -130;
	var dis = height - 10;

	document.getElementById("menu_principal").style.height = height+'px';
	document.getElementById("menu_principal").style.maxHeight = height+'px';
	document.getElementById("menu_principal").style.background = '#eee';
	
	document.getElementById("menu_discucion").style.height = dis+'px';
	document.getElementById("menu_discucion").style.maxHeight = dis+'px';
	document.getElementById("menu_discucion").style.background = '#fff';
	
	document.getElementById("menu_notas").style.height= dis+'px';
	document.getElementById("menu_notas").style.maxHeight = dis+'px';
	document.getElementById("menu_notas").style.background = '#fff';
	
	document.getElementById('menu_content').style.height = height_content+'px';
}
// Router para el menu lateral
function menu_router(id)
{
	// Si ya estamos en el menu seleccionado no hacemos nada
	if(router_menu == id){
		return false;
	}

	element = document.querySelectorAll('.items');
	control_form = 0;

	for(var i = 0; i < element.length; i++){
		var temp = element[i].getAttribute('data-type');

		if(temp == id){
			addClass(element[i], 'menu_router');
		} else {
			removeClass(element[i], 'menu_router');
		}
	};

	// Si hay contenido en el textarea de las discusiones lo guardamos
	if(router_menu == 2){
		dis_content = document.getElementById('content_dis').value;
		files_content = document.getElementById('content_dis_results').innerHTML;
	}

	// Si hay contenido en el textarea de las notas lo guardamos
	if(router_menu == 3){
		notas_content = document.getElementById('content_notas').value;
	}

	if(id == 1){
		// si tenemos un archivo abierto lo cerramos
		// Opcion documentacion
		router_menu = 1;
		dis_link = 0;

		if(isEmpty(doc_cache)){
			// Si esta vacio
			document.getElementById('menu_content').innerHTML = doc_default;
		} else {
			document.getElementById('menu_content').innerHTML = doc_cache;
		}

		resaltador();
	} else if(id == 2){
		// Opcion discusiones
		router_menu = 2;
		dis_link = 0;

		document.getElementById('menu_content').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';

		// Si el tiempo ha caducado volvemos a cargar las discuciones
		var minutos = 5; // Tiempo limite
		// Verificamos si el tiempo esta en el rango
		if((new Date().getTime() - (minutos * 60 * 1000)) > discucion_tiempo){
			// Si esta fuera de rango volvemos a cargar las discuciones
			ajax('include/server_discucion.php',{
				id_curso: id_curso,
				type: 'discucion_cargar'
			}, function (data){
				dis_nueva = data.dis_nueva;
				dis_pop = data.dis_pop;
				dis_no = data.dis_no;
				dis_propia = data.dis_propia;

				document.getElementById('menu_content').innerHTML = dis_default + dis_nueva +'</div>'+dis_cargar;

				var controls = document.querySelectorAll('.controls');
				for(var i = 0; i < controls.length; i++){
					if(controls[i].getAttribute('data-type') == 1){
						controls[i].style.background = '#6F95cc';
					}
				};

				// Asignamos el tiempo
				discucion_tiempo = new Date().getTime();

				// Si habia contenido en el textarea lo cargamos
				if(dis_content.length > 0){
					mostrar_discucion();
					document.getElementById('content_dis').value = dis_content;
					document.getElementById('content_dis_results').innerHTML = files_content;
				}
			}, 'Json');
		} else {
			// Si el tiempo esta dentro del limite procedemos normal
			if(isEmpty(dis_nueva)){
				// Si no tenemos cacheado las discuciones cargamos desde servidor
				ajax('include/server_discucion.php',{
					id_curso: id_curso,
					type: 'discucion_cargar'
				}, function (data){
					dis_nueva = data.dis_nueva;
					dis_pop = data.dis_pop;
					dis_no = data.dis_no;
					dis_propia = data.dis_propia;

					document.getElementById('menu_content').innerHTML = dis_default + dis_nueva +'</div>'+dis_cargar;

					var controls = document.querySelectorAll('.controls');
					for(var i = 0; i < controls.length; i++){
						if(controls[i].getAttribute('data-type') == 1){
							controls[i].style.background = '#6F95cc';
						}
					};

					// Asignamos el tiempo
					discucion_tiempo = new Date().getTime();
				}, 'Json');
			} else {
				// Si tenemos cacheada la discusion la cargamos
				if(router_dis == 1){
					document.getElementById('menu_content').innerHTML = dis_default + dis_nueva + '</div>'+dis_cargar;
				} else if(router_dis == 2){
					document.getElementById('menu_content').innerHTML = dis_default + dis_pop + '</div>'+dis_cargar;
				} else if(router_dis == 3){
					document.getElementById('menu_content').innerHTML = dis_default + dis_no + '</div>'+dis_cargar;
				} else if(router_dis == 4){
					document.getElementById('menu_content').innerHTML = dis_default + dis_propia + '</div>'+dis_cargar;
				}

				var controls = document.querySelectorAll('.controls');
				for(var i = 0; i < controls.length; i++){
					if(controls[i].getAttribute('data-type') == router_dis){
						controls[i].style.background = '#6F95cc';
					}
				}

				// Si habia contenido en el textarea lo cargamos
				if(dis_content.length > 0){
					mostrar_discucion();
					document.getElementById('content_dis').value = dis_content;
					document.getElementById('content_dis_results').innerHTML = files_content;
				}
			}
		}
	} else if(id == 3){
		// Opcion notas
		// Actualizamos el control del menu
		router_menu = 3;
		dis_link = 0;

		// Mostramos la imagen cargando
		document.getElementById('menu_content').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';

		// Verificamos si la cache de notas esta vacia
		if(isEmpty(nota_cache)){
			// Si esta vacio cargamos notas desde servidor
			ajax('include/server_curso.php',{
				id: id_curso,
				type: 'notas'
			}, function (data){
				// Mostramos el contenido en pantalla
				document.getElementById('menu_content').innerHTML = data.defecto + data.nota + '</div>';
				// Guardamos la cache por defecto
				nota_default = data.defecto;
				// Guardamos la cache de notas cargadas
				nota_cache = data.nota;
			}, 'Json');
		} else {
			// Mostramos el contenido en pantalla
			document.getElementById('menu_content').innerHTML = nota_default+nota_cache+'</div>';
		}

		// Si habia contenido en el textarea lo cargamos
		if(notas_content.length > 0){
			mostrar_notas();
			document.getElementById('content_notas').value = notas_content;
		}
	} else if(id == 4){
		// Opciones buscador
		router_menu = 4;
		dis_link = 0;

		if(isEmpty(find_cache)){
			// Si esta vacio
			document.getElementById('menu_content').innerHTML = find_default+'</div>';
			document.getElementById('q').focus();
			document.getElementById('q').value = find_value;
		} else {
			document.getElementById('menu_content').innerHTML = find_cache;
			document.getElementById('q').focus();
			document.getElementById('q').value = find_value;
			//$('#q').scrollTop($('#q')[0].scrollHeight);
		}
	}
}
// Mostramos informacion sobre un tema de curso
function mostrar(id) // OK
{
	// Mostramos div para tema de un curso
	document.getElementById('wrapper_2').style.transform = 'translateX(0)';
	document.getElementById('wrapper_2').style.webkitTransform = 'translateX(0)';
	document.getElementById('wrapper_2').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';
	// Si en el menu esta seleccionada la documentacion
	if(router_menu == 1){
		// Mostramos imagen cargando
		document.getElementById('menu_content').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';
		doc_cache = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';
	}

	// Verificamos la cache de los temas
	var cache = JSON.parse(localStorage.getItem('cache_tema'+id));

	if(!isEmpty(cache)){
		// Si la cache esta disponible la cargamos
		document.getElementById('wrapper_2').innerHTML = cache.tema;
		if(isEmpty(cache.doc)){
			// Si esta vacio
			if(router_menu == 1){
				document.getElementById('menu_content').innerHTML = 'Tema sin Documentación';
			}
			doc_cache = 'Tema sin documentación';
		} else {
			if(router_menu == 1){
				document.getElementById('menu_content').innerHTML = cache.doc;
			}
			doc_cache = cache.doc;
		}

		// Cambiamos la url
		history.replaceState({},'', curso_url+'/'+cache.url);

		// Cambiamos el contenido del elemento title
		if(control_notificacion > 0){
			// Modificamos el titulo si hay notificaciones
			document.title = '('+control_notificacion+') '+cache.titulo+' | codeando.org';
		} else {
			document.title = cache.titulo+' | codeando.org';
		}

		resaltador();
	} else {
		// Si la cache no esta disponible solicitamos datos al servidor
		ajax('include/server_curso.php',{
			id: id,
			type: 'tema_cargar'
		}, function (data){
			document.getElementById('wrapper_2').innerHTML = data.tema;

			if(isEmpty(data.doc)){
				// Si esta vacio
				if(router_menu == 1){
					document.getElementById('menu_content').innerHTML = 'Tema sin Documentacion';
				}
				doc_cache = 'Tema sin Documentación';
			} else {
				if(router_menu == 1){
					document.getElementById('menu_content').innerHTML = data.doc;
				}
				doc_cache = data.doc;
			}

			// Cambiamos la url
			history.replaceState({},'', curso_url+'/'+data.url);

			// Cambiamos el contenido del elemento title
			if(control_notificacion > 0){
				// Modificamos el titulo si hay notificaciones
				document.title = '('+control_notificacion+') '+data.titulo+' | codeando.org';
			} else {
				document.title = data.titulo+' | codeando.org';
			}

			resaltador();
			// Guardamos en la cache
			localStorage.setItem('cache_tema'+data.id, JSON.stringify(data));
		}, 'Json');
	}
}
// Mostrar opciones para el formulario de discuciones
function mostrar_discucion() // OK
{
	if(control_form == 0){
		// Si hay contenido quitamos el efecto de transition
		if(dis_content.length > 0){
			document.getElementById('content_dis').style.transition = 'none';
			document.getElementById('content_dis').style.webkitTransition = 'none';
		} else {
			// Si no hay le utilizamos un efecto de transition
			document.getElementById('content_dis').style.transition = 'all 0.25s ease';
			document.getElementById('content_dis').style.webkitTransition = 'all 0.25s ease';
		}

		// Mostramos las opciones del toolbox
		document.getElementById('resp_toolbox').style.display = 'block';
		// Mostramos boton submit
		document.getElementById('submit').style.display = 'inline-block';
		document.querySelector('#discucion_form .submit').style.display = 'inline-block';
		// Asignamos altura al textarea
		document.getElementById('content_dis').style.height = '100px';
		document.getElementById('content_dis').style.minHeight = '100px';

		// Modificamos valor para no recibir mas peticiones
		control_form = 1;
		control_files++;

		// Solo activamos el gestor de archivos si no nos visitan desde un movil
		var device = navigator.userAgent;
		var valor = device.match(/Iphone/i)|| device.match(/Ipod/i)|| device.match(/Android/i)|| device.match(/J2ME/i)|| device.match(/BlackBerry/i)|| device.match(/iPhone|iPad|iPod/i)|| device.match(/Opera Mini/i)|| device.match(/IEMobile/i)|| device.match(/Mobile/i)|| device.match(/Windows Phone/i)|| device.match(/windows mobile/i)|| device.match(/windows ce/i)|| device.match(/webOS/i)|| device.match(/palm/i)|| device.match(/bada/i)|| device.match(/series60/i)|| device.match(/nokia/i)|| device.match(/symbian/i)|| device.match(/HTC/i);

		if(!valor){
			// Mostramos el area para arrastrar archivos
			document.getElementById('content_dis_files').style.display = 'block';
			// Activamos arrastrar y soltar para archivos
			files(control_files);
			// Eliminamos los archivos anteriores
			ajax('include/server_files.php',{
				type: 'delete'
			}, function (data){
				// Archivos eliminados
			}, 'Json');
		}
	}
}
// Opciones para el formulario de discuciones
function mostrar_notas()
{
	if(control_form == 0){
		// Si hay contenido quitamos el efecto de transition
		if(notas_content.length > 0){
			document.getElementById('content_notas').style.transition = 'none';
			document.getElementById('content_notas').style.webkitTransition = 'none';
		} else {
			// Si no hay le utilizamos un efecto de transition
			document.getElementById('content_notas').style.transition = 'all 0.25s ease';
			document.getElementById('content_notas').style.webkitTransition = 'all 0.25s ease';
		}

		document.querySelector('#form_notas #resp_toolbox').style.display = 'block';
		document.querySelector('#notas #submit').style.display = 'inline-block';
		document.getElementById('content_notas').style.height = '100px';
		document.getElementById('content_notas').style.minHeight = '100px';

		control_form = 1;
	}
}
// Opciones para el formulario de respuestas
function mostrar_res()
{
	if(control_form_res == 0){
		document.querySelector('#form_res #resp_toolbox').style.display = 'block';
		document.querySelector('#res_form #submit').style.display = 'block';
		document.getElementById('content_res').style.height = '100px';
		document.getElementById('content_res').style.minHeight = '100px';

		// Al ampliar el formulario mandamos el scroll al final para visualizarla correctamente
		setTimeout(function (){
			var height = document.getElementById('menu_discucion').style.pixelHeight || document.getElementById('menu_discucion').offsetHeight;
			document.getElementById('menu_discucion').scrollTop = height;
		}, 400);

		control_form_res = 1;
	}
}
// Funcion encargada de mover el slider de los twitts
function moveSlider(address)
{
	// Funcion que manipula el movimiento del slider
	var limite = document.querySelectorAll(".twitter_container .twitter").length;
	indicador = (address == 'right') ? indicador + 1 : indicador -1;
	indicador = (indicador >= limite) ? 0 : indicador;
	indicador = (indicador < 0) ? limite - 1 : indicador;

	if(indicador == 0){
		var width = document.querySelector('.twitter_container').style.pixelHeight || document.querySelector('.twitter_container').offsetHeight;
		var size = -(indicador * width)+"px";

		document.querySelector(".twitter_container .twitterContainer").style.marginLeft = size;
	} else {
		var width = document.querySelector('.twitter_container').style.pixelHeight || document.querySelector('.twitter_container').offsetHeight;
		var size = -((indicador * width) + 10)+"px";

		document.querySelector(".twitter_container .twitterContainer").style.marginLeft = size;
	}
}
// Ocultamos la notan y mostramos el menu
function nota_back() // OK
{
	var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

	document.getElementById('menu_notas').style.transform = 'translateX('+width+'px)';
	document.getElementById('menu_notas').style.webkitTransform = 'translateX('+width+'px)';

	document.getElementById('nota_content').value = '';
}
// Cancelamos la edicion de las notas
function nota_cancelar(id) // OK
{
	document.querySelector('.nota_edit').style.display = 'none';
	document.getElementById('nota_options').style.display = 'block';
	document.getElementById('nota_content_'+id).style.display = 'block';
}
// Mostramos mensaje para eliminar una nota
function nota_delete() // OK
{
	document.getElementById('nota_options').style.display = 'none';
	document.getElementById('nota_delete').style.display = 'block';
}
// Ocultamos mensaje eliminar nota
function nota_delete_no() // OK
{
	document.getElementById('nota_delete').style.display = 'none';
	document.getElementById('nota_options').style.display = 'block';
}
// Eliminamos una nota
function nota_delete_yes(id) // OK
{
	// Mostramos mensaje de eliminando
	document.getElementById('nota_delete').innerHTML = 'Eliminando nota . . .';

	ajax('include/server_curso.php',{
		id: id,
		type: 'nota_delete'
	}, function (data){
		// Mostramos mensaje de exito
		document.getElementById('nota_content').innerHTML = 'La nota se elimino con exito';
		success('La nota se elimino con exito');
		// Ocultamos la nota
		document.getElementById('nota_'+data.id).style.display = 'none';
		// Actualizamos la cache de notas
		nota_cache = document.getElementById('notas_append').innerHTML;

		// Ocultamos el div de la nota
		setTimeout(function (){
			nota_back();
		}, 3000)
	}, 'Json');
}
// Editamos la nota
function nota_edicion(id)
{
	// Obtenemos el contenido del formulario
	var contenido = document.getElementById('nota_edicion').value;

	// Verificamos que no este vacio
	if(contenido.length == 0){
		return
	}

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(contenido.indexOf(codigo[i]) >= 0){
			contenido = contenido.replace(codigo[i], replace[i]);
		}
	}

	// Reemplazamos las url planas
	//contenido = url_replace(contenido);

	// Ocultamos boton submit
	document.querySelector('.submit').style.display = 'none';
	// Mostramos texto cargando
	document.getElementById('form_nota_edit_info').innerHTML = 'Guardando . . .';

	ajax('include/server_curso.php',{
		id: id,
		contenido: contenido,
		type: 'nota_edicion'
	}, function (data){
		// Actualizamos el contenido de la nota
		document.getElementById('nota_edicion').value = data.contenido_edit;
		// Limpiamos el mensaje informativo
		document.getElementById('form_nota_edit_info').innerHTML = '';
		// Mostramos boton submit
		document.querySelector('.submit').style.display = 'inline-block';

		// Ocultamos el formulario de edicion
		document.querySelector('.nota_edit').style.display = 'none';
		// Mostramos la nota
		document.getElementById('nota_content_'+id).style.display = 'block';
		// Actualizamos el contenido de la nota
		document.getElementById('nota_content_'+id).innerHTML = data.contenido;

		// Actualizamos el titulo de la nota
		var element = document.querySelectorAll('.n_title_'+data.id);
		for(var i = 0; i < element.length; i++){
			element[i].innerHTML = data.titulo;
		}

		// Actualizamos el resumen de la nota
		document.querySelector('.n_content_'+data.id).innerHTML = data.content;
		// Mostramos opciones de la nota
		document.getElementById('nota_options').style.display = 'block';

		success('La nota se edito con exito');

		resaltador();
	}, 'Json');
}
// Cambiamos a modo edicion de una nota
function nota_edit(id)
{
	document.getElementById('nota_content_'+id).style.display = 'none';
	document.getElementById('nota_options').style.display = 'none';
	document.querySelector('.nota_edit').style.display = 'block';
}
// Publicamos una nota
function nota_publicar() // OK
{
	// Obtenemos el valor del formulario
	var contenido = document.getElementById('content_notas').value;
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Verificamos que no este vacio
	if(contenido.length == 0){
		return
	}

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(contenido.indexOf(codigo[i]) >= 0){
			contenido = contenido.replace(codigo[i], replace[i]);
		}
	}

	// Reemplazamos las url planas
	//contenido = url_replace(contenido);

	// Ocultamos boton submit
	document.querySelector('#notas #submit').style.display = 'none';
	// Mostramos texto cargando
	document.getElementById('form_nota_info').innerHTML = 'Guardando . . .';

	ajax('include/server_curso.php',{
		contenido: contenido,
		id: id_curso,
		contenido: contenido,
		type : 'nota_publicar'
	}, function (data){
		// Manejo grafico
		// Limpiamos formulario
		document.getElementById('content_notas').value = '';
		// Mostramos boton submit
		document.querySelector('#notas #submit').style.display = 'inline-block';
		// Mostramos mensaje de exito
		document.getElementById('form_nota_info').innerHTML = 'La nota se guardo con exito';
		success('La nota se guardo con exito');

		// Manejo de datos
		// Actualizamos la cache de notas
		nota_cache = data.nota + nota_cache;
		// Mostramos la nota creada
		document.getElementById('notas_append').innerHTML = nota_cache;

		// Quitamos el mensaje de exito
		setTimeout(function(){
			document.getElementById('form_nota_info').innerHTML = '';
		}, 3000);
	}, 'Json');
}
// Visualizamos una nota
function nota_ver(id) // OK
{
	// Obtenemos el ancho de la pantalla
	var width = window.innerWidth  || document.documentElement.clientWidth|| document.body.clientWidth;

	// Mostramos el div de la nota
	if(width >= 900){
		var win = width - 525;

		document.getElementById('menu_notas').style.transform = 'translateX('+win+'px)';
		document.getElementById('menu_notas').style.webkitTransform = 'translateX('+win+'px)';
	} else if(width > 600 && width < 899){
		var win = width - 395;

		document.getElementById('menu_notas').style.transform = 'translateX('+win+'px)';
		document.getElementById('menu_notas').style.webkitTransform = 'translateX('+win+'px)';
	} else if(width <= 600){
		var win = 0;

		document.getElementById('menu_notas').style.transform = 'translateX('+win+'px)';
		document.getElementById('menu_notas').style.webkitTransform = 'translateX('+win+'px)';
	}

	// Opciones para cargar la discucion
	document.getElementById('nota_content').innerHTML = '';
	// Mostramos la imagen cargando
	document.querySelector('#menu_notas .cargando').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';

	ajax('include/server_curso.php',{
		id: id,
		type: 'nota_ver'
	}, function (data){
		// Mostramos la nota
		document.querySelector('#menu_notas #nota_content').innerHTML = data.nota;
		// Ocultamos la imagen cargando
		document.querySelector('#menu_notas .cargando').innerHTML = '';

		resaltador();
	}, 'Json');
}
// Cargamos notificaciones leidas
function notificacion_cargar() // OK
{
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Ocultamos el boton de submit
	document.querySelector('#not_cargar button').style.display = 'none';
	// Mostramos imagen cargando
	document.querySelector('#not_cargar .cargando').innerHTML = cargando_white;

	ajax('include/server_curso.php',{
		start: not_start,
		id: id_curso,
		type: 'notificacion_cargar'
	}, function (data){
		// Actualizamos el digito para empezar a cargar notificaciones
		not_start = data.start;

		// Quitamos la imagen cargando
		document.querySelector('#not_cargar .cargando').innerHTML = '';

		// Verificamos si hay notificaciones mostradas
		if(control_notificacion == 0){
			// Si no hay mostramos desde servidor
			document.getElementById('alert').innerHTML = data.not;
		} else {
			// Si hay obtenemos las notificaciones anteriores y sumamos desde servidor
			var alert = document.getElementById('alert').innerHTML;
			document.getElementById('alert').innerHTML = alert + data.not;
		}

		// Actualizamos el control de notificaciones
		control_notificacion = data.start;

		// Verificamos si hay error
		if(isEmpty(data.error)){
			// Si no hay mostramos el boton submit
			document.querySelector('#not_cargar button').style.display = 'inline-block';
		} else {
			// Si hay no mostramos el boton
			document.querySelector('#not_cargar button').style.display = 'none';
			document.querySelector('#not_cargar .cargando').innerHTML = 'No hay mas notificaciones';
		}
	}, 'Json');
}
// Mostramos las notificaciones nuevas cada vez que damos clic en alertas
function notificacion_mostrar() // OK
{
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Restauramos valores por defecto
	not_start = 0;

	// Ocultamos el boton submit
	document.querySelector('#not_cargar button').style.display = 'none';
	// Mostramos imagen cargando
	document.querySelector('#not_cargar .cargando').innerHTML = cargando_white;
	// Limpiamos el div de las notificaciones
	document.getElementById('alert').innerHTML = '';

	ajax('include/server_curso.php',{
		id: id_curso,
		type: 'notificacion_mostrar'
	}, function (data){
		// Quitamos la imagen cargando
		document.querySelector('#not_cargar .cargando').innerHTML = '';
		// Mostramos las notificaciones
		document.getElementById('alert').innerHTML = data.not;
		// Mostramos el boton de cargar mas notificaciones
		document.querySelector('#not_cargar button').style.display = 'inline-block';

		// Actualizamos el numero de notificaciones
		control_notificacion = data.num;

		// Modificamos el titulo si hay notificaciones
		document.title = data.titulo;
	}, 'Json');
}
// Mostramos una discucion desde notificaciones
function notificacion_ver(id_not,id) // OK
{
	// Ocultamos el menu de notificaciones
	document.getElementById('alertas').style.transform = 'translateY(-100%)';
	document.getElementById('alertas').style.webkitTransform = 'translateY(-100%)';
	// Regresamos el valor a 0 para procesar nueva solicitud de notificaciones
	alertas = 0;

	// Mostramos discucion
	dis_mostrar(id);

	// Marcamos la notificacion como leida
	ajax('include/server_curso.php',{
		id: id_not,
		type: 'notificacion_leida'
	}, function (data){
		if(data.leida == 'NO'){
			// Modificamos el total de notificaciones
			var not = parseInt(document.getElementById('notificacion').innerHTML) - 1;
			// Verificamos si ya no hay notificaciones
			if(not == 0){
				// Modificamos el titulo si hay notificaciones
				var title = document.title;
				var title = title.replace('(1) ', '');
				document.title = title;

				// Si no hay mas notificaciones
				document.getElementById('notificacion').style.display = 'none';
			} else {
				// Modificamos el titulo si hay notificaciones
				var title = document.title;
				var title = title.replace('('+(not + 1)+') ', '');
				document.title = '('+not+') '+title;

				document.getElementById('notificacion').innerHTML = not;
			}
			// Actualizamos la variable global para notificaciones
			control_notificacion = not;
		}
		return
	}, 'Json');
}
// Mostramos el perfil desde notificaciones
function notificacion_perfil(id_not,user) // OK
{
	// Ocultamos el menu de notificaciones
	document.getElementById('alertas').style.transform = 'translateY(-100%)';
	document.getElementById('alertas').style.webkitTransform = 'translateY(-100%)';
	// Regresamos el valor a 0 para procesar mas notificaciones
	alertas = 0;

	// Mostramos discucion
	user_mostrar(user);

	// Marcamos la notificacion como leida
	ajax('include/server_curso.php',{
		id: id_not,
		type: 'notificacion_leida'
	}, function (data){
		if(data.leida == 'NO'){
			// Modificamos el total de notificaciones
			var not = parseInt(document.getElementById('notificacion').innerHTML) - 1;
			if(not == 0){
				// Modificamos el titulo si hay notificaciones
				var title = document.title;
				var title = title.replace('(1) ', '');
				document.title = title;

				document.getElementById('notificacion').style.display = 'none';
			} else {
				// Modificamos el titulo si hay notificaciones
				var title = document.title;
				var title = title.replace('('+(not + 1)+') ', '');
				document.title = '('+not+') '+title;

				document.getElementById('notificacion').innerHTML = not;
			}
			// Actualizamos la variable global para notificaciones
			control_notificacion = not;
		}
		return
	}, 'Json');
}
// Opciones del menu (Asignamos como seleccionado el primer menu)
function options()
{
	var items = document.querySelectorAll('.items');

	for(var i = 0; i < items.length; i++){
		var id = items[i].getAttribute('data-type');

		if(id == '1'){
			addClass(items[i], 'menu_router');
		}
	};
}
// Mostramos la opcion de eliminar la respuesta
function res_delete(id) // OK
{
	document.getElementById('res_option_'+id).style.display = 'none';
	document.getElementById('res_delete_'+id).style.display = 'block';
}
// Ocultamos la opcion de eliminar la discusion
function res_delete_no(id) // OK
{
	document.getElementById('res_delete_'+id).style.display = 'none';
	document.getElementById('res_option_'+id).style.display = 'block';
}
// Eliminamos la discusion
function res_delete_yes(id_res, id_dis)
{
	ajax('include/server_discucion.php',{
		id_res: id_res,
		id_dis: id_dis,
		type: 'res_delete'
	}, function (data){
		// Ocultamos la respuesta
		document.getElementById('respuestas_'+data.id_res).style.display = 'none';
		// Actualizamos el total de respuestas
		document.querySelector('.dis_res_'+data.id_dis).innerHTML = data.res;
		// Eliminamos la cache de la discucion para que se vuelva a cargar
		localStorage.removeItem('cache_discucion'+data.id_dis);
	}, 'Json');
}
// Mostramos el formulario para editar una respuesta
function res_edit(id) // OK
{
	document.getElementById('respuestas_'+id).style.display = 'none';
	document.getElementById('res_form_'+id).style.display = 'block';
	document.getElementById('res_form').style.display = 'none';
}
// ocultamos el formulario para editar una respuesta
function res_edit_cancelar(id) // OK
{
	document.getElementById('respuestas_'+id).style.display = 'block';
	document.getElementById('res_form_'+id).style.display = 'none';
	document.getElementById('res_form').style.display = 'block';
}
// Editamos la discusion
function res_editar(id, id_dis) // OK
{
	// Obtenemos el contenido de la respuesta
	var contenido = document.getElementById('content_res_edit_'+id).value;

	// Verificamos que no este vacio
	if(contenido.length == 0){
		return
	}

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(contenido.indexOf(codigo[i]) >= 0){
			contenido = contenido.replace(codigo[i], replace[i]);
		}
	}

	// Reemplazamos las url planas
	//contenido = url_replace(contenido);

	// Ocultamos el boton submit
	document.getElementById('res_submit_edit').style.display = 'none';
	// Ocultamos el boton cancelar
	document.getElementById('res_cancelar_edit').style.display = 'none';
	// Mostramos texto guardando
	document.getElementById('res_edit_info').innerHTML = 'Guardando . . .';

	ajax('include/server_discucion.php',{
		contenido: contenido,
		id: id,
		id_dis: id_dis,
		type : 'res_editar'
	}, function (data){
		// Actualizamos el contenido del formulario
		document.getElementById('content_res_edit_'+data.id).value = data.contenido_edit;
		// Ocultamos texto guardando
		document.getElementById('res_edit_info').innerHTML = '';
		// Mostramos boton submit
		document.getElementById('res_submit_edit').style.display = 'inline-block';
		// Mostramos boton cancelar
		document.getElementById('res_cancelar_edit').style.display = 'inline-block';

		// Ocultamos formulario de edicion
		document.getElementById('res_form_'+id).style.display = 'none';
		// Mostramos formulario para respuestas
		document.getElementById('res_form').style.display = 'block';
		// Mostramos la respuesta
		document.getElementById('respuestas_'+data.id).style.display = 'block';
		// Actualizamos el contenido de la respuesta
		document.getElementById('res_content_'+data.id).innerHTML = data.contenido_mostrar;

		success('La respuesta se actualizo con exito');

		// Borramos la cache de la discusion
		localStorage.removeItem('cache_discucion'+data.id_dis);
		resaltador();
	}, 'Json');
}
// Publicamos una respuesta
function res_publicar() // OK
{
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Obtenemos el contenido de la respuesta
	var contenido = document.getElementById('content_res').value;
	// Obtenemos el ID de la discusion
	var id_dis = document.getElementById('res_dis_id').value;

	// Verificamos que no este vacio
	if(contenido.length == 0){
		return
	}

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(contenido.indexOf(codigo[i]) >= 0){
			contenido = contenido.replace(codigo[i], replace[i]);
		}
	}

	// Reemplazamos las url planas
	//contenido = url_replace(contenido);

	// Ocultamos boton submit
	document.querySelector('#res_form #submit').style.display = 'none';
	// Mostramos cargando
	document.getElementById('form_res_info').innerHTML = 'Guardando . . .';

	ajax('include/server_discucion.php',{
		contenido: contenido,
		id_curso: id_curso,
		id_dis: id_dis,
		type : 'res_publicar'
	}, function (data){
		// Manejo grafico
		// Limpiamos el contenido del form
		document.getElementById('content_res').value = '';
		// Mostramos el boton submit
		document.querySelector('#res_form #submit').style.display = 'inline-block';
		// Mostramos mensaje de exito
		document.getElementById('form_res_info').innerHTML = 'La respuesta se guardo con exito';
		success('La respuesta se guardo con exito');

		// Manejo de datos
		// Obtenemos las respuestas anteriores
		var temp = document.getElementById('res_content').innerHTML;
		// Agregamos la respuesta nueva
		document.getElementById('res_content').innerHTML = temp + data.res;
		var element = document.querySelectorAll('.dis_res_'+data.id);
		for(var i = 0; i < element.length; i++){
			element[i].innerHTML = 'Respuestas: '+data.respuestas;
		}

		// Actualizamos la cache
		if(router_dis == 1){
			dis_nueva = document.getElementById('discucion_mostrar').innerHTML;
		} else if(router_dis == 2){
			dis_pop = document.getElementById('discucion_mostrar').innerHTML;
		} else if(router_dis == 3){
			dis_no = document.getElementById('discucion_mostrar').innerHTML;
		} else if(router_dis == 4){
			dis_propia = document.getElementById('discucion_mostrar').innerHTML;
		}

		resaltador();

		// Ocultamos la informacion de exito
		setTimeout(function(){
			document.getElementById('form_res_info').innerHTML = '';
		}, 3000);

		// Eliminamos la cache de la discucion para que se vuelva a cargar
		localStorage.removeItem('cache_discucion'+data.id);
	}, 'Json');
}
// Voto a favor de la respuesta
function res_voto_si(id) // OK
{
	// Verificamos si el control esta disponible
	if(control_voto == 0){
		// Cambiamos el valor del control para no recibir mas peticiones
		control_voto = 1;
		ajax('include/server_discucion.php',{
			id: id,
			type: 'res_voto_si'
		}, function (data){
			// Cambiamos el valor del control para recibir mas peticiones
			control_voto = 0;
			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Actualizamos los votos de la publicacion
				document.querySelector('.res_voto_'+data.id).innerHTML = data.votos;

				// Eliminamos la cache de la discucion para que se vuelva a cargar
				localStorage.removeItem('cache_discucion'+data.id_dis);
			} else {
				error(data.error);
				return
			}
		}, 'Json');	
	}
}
// Voto en contra de la respuesta
function res_voto_no(id) // OK
{
	// Verificamos si el control esta disponible
	if(control_voto == 0){
		// Cambiamos el valor del control para no recibir mas peticiones
		control_voto = 1;
		ajax('include/server_discucion.php',{
			id: id,
			type: 'res_voto_no'
		}, function (data){
			// Cambiamos el valor del control para recibir mas peticiones
			control_voto = 0;
			// Verificamos si hay error
			if(isEmpty(data.error)){
				// Actualizamos los votos de la discusion
				document.querySelector('.res_voto_'+data.id).innerHTML = data.votos;

				// Eliminamos la cache de la discucion para que se vuelva a cargar
				localStorage.removeItem('cache_discucion'+data.id_dis);
			} else {
				error(data.error);
				return
			}
		}, 'Json');	
	}
}
// Router de la plataforma
function router(data)
{
	if(data == 'plataforma'){
		location.href = '/plataforma/';
	}
}
// Mostramos success
function success (text) // OK
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
// Acciones para la caja de herramientas del textarea
function toolbox(id, identificador)
{
	if(id == 1){
		// Añadimos opcion de codigo
		var sel = getCursorSelection(document.getElementById(identificador));
		if(isEmpty(sel)){
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
// Scroll en el area de usuarios
function user_scroll() // OK
{
	document.getElementById('wrapper_3').onscroll = function (){
		var scroll = document.getElementById('wrapper_3').scrollTop;
		// 192 es el alto de los dos div regresar
		var parametro_1 = (document.getElementById('u_content').style.pixelHeight || document.getElementById('u_content').offsetHeight) + 192;
		var parametro_2 = document.getElementById('wrapper_3').style.pixelHeight || document.getElementById('wrapper_3').offsetHeight;
		var scroll_size = parametro_1 - parametro_2;

		if(scroll >= scroll_size){
			// Obtenemos el ID del usuario
			var user = document.getElementById('user_mostrar_id').value;
			var control = JSON.parse(localStorage.getItem('user_mostrar_control_'+user));

			// Si control no existe le asignamos el valor 0 para aceptar peticiones
			if(!control){
				control = 0;
			}

			// Verificamos si el control esta disponible
			if(control == 0){
				// Obtenemos el numero desde el que comenzaremos a cargar mensajes
				var start = JSON.parse(localStorage.getItem('user_mostrar_'+user));
				// Asignamos 1 al control para no aceptar otra peticion
				localStorage.setItem('user_mostrar_control_'+user, JSON.stringify(1));

				// Si start no existe le asignamos 0 para comenzar a cargar mensajes
				if(!start){
					start = 0;
				}

				// Mostramos la imagen cargando
				document.getElementById('user_cargando').innerHTML = '<div style="margin-bottom:5px;">'+cargando_blue+'</div>';

				ajax('include/server_curso.php',{
					start: start,
					user: user,
					type: 'user_cargar'
				}, function (data){
					// Obtenemos los mensaje ya impresos
					var temp = document.getElementById('u_mensajes').innerHTML;
					// Agregamos los mensajes cargados desde servidor
					document.getElementById('u_mensajes').innerHTML = temp + data.resultado;
					// Quitamos la imagen cargando
					document.getElementById('user_cargando').innerHTML = '';
					// Modificamos el valor de start para seguir cargando mensajes
					localStorage.setItem('user_mostrar_'+data.user, JSON.stringify(data.start));

					// Si no hay mas mensajes, damos avizo al usuario
					if(data.control == 1){
						// Mostramos mensaje
						document.getElementById('user_cargando').innerHTML = 'No hay mas mensajes por mostrar!';
					} else if(data.control == 0){
						// Si hay mas mensajes modificamos control a 0 para recibir mas peticiones
						localStorage.setItem('user_mostrar_control_'+data.user, JSON.stringify(0));
					}

					// Destruimos la cache
					localStorage.removeItem('cache_user'+data.user);
					// Volvemos a crear la cache
					var datos = document.getElementById('wrapper_3').innerHTML;
					localStorage.setItem('cache_user'+data.user, JSON.stringify({
						'time': new Date().getTime(),
						'res': datos
					}));
				}, 'Json');
			}
		} else {
			return false;
		}
	};
}
// Enviamos un mensaje en el perfil del usuario
function user_mensaje(user)
{
	// Obtenemos el contenido
	var mensaje = document.getElementById('content_user').value;
	var id_curso = id_curso || localStorage.getItem('id_curso');

	// Comprobamos que el tamaño del contenido
	if(mensaje.length < 20){
		if(mensaje.length == 0){
			return
		} else {
			error('El mensaje debe contener minimo 20 caracteres');
			return
		}
	}

	// Reemplazamos todo
	for(var i=0; i <= codigo.length; i++){
		while(mensaje.indexOf(codigo[i]) >= 0){
			mensaje = mensaje.replace(codigo[i], replace[i]);
		}
	}

	// Ocultamos el boton de envio
	document.getElementById('user_boton').style.display = 'none';
	// Mostramos mensaje de enviando
	document.getElementById('user_info').innerHTML = 'Enviando su mensaje . . .';

	ajax('include/server_curso.php',{
		id_curso: id_curso,
		mensaje: mensaje,
		user: user,
		type: 'user_mensaje'
	}, function (data){
		// Mostramos boton de envio
		document.getElementById('user_boton').style.display = 'inline-block';
		// Limpiamos el textarea
		document.getElementById('content_user').value = '';

		// Verificamos si hay un error
		if(isEmpty(data.error)){
			// Si no hay error mostramos informacion de exito
			document.getElementById('user_info').innerHTML = data.status;
			success(data.status);
		} else {
			// Si hay error lo mostramos
			error(data.error);
		}
		
		// Obtenemos los mensajes publicados
		var temp = document.getElementById('u_mensajes').innerHTML;
		// Insertamos el nuevo mensaje al principio
		document.getElementById('u_mensajes').innerHTML = data.mensaje + temp;

		// Destruimos la cache
		localStorage.removeItem('cache_user'+data.user);
		// Volvemos a crear la cache
		var datos = document.getElementById('wrapper_3').innerHTML;
		localStorage.setItem('cache_user'+data.user, JSON.stringify({
			'time': new Date().getTime(),
			'res': datos
		}));

		// resaltamos el codigo
		resaltador();

		// Limpiamos la informacion
		setTimeout(function (){
			document.getElementById('user_info').innerHTML = '';
		}, 3000);
	}, 'Json');
}
// Mostramos informacion sobre un usuario del curso
function user_mostrar(id) // OK
{
	// Mostramos div para la informacion de perfil
	document.getElementById('wrapper_3').style.transform = 'translateX(0)';
	document.getElementById('wrapper_3').style.webkitTransform = 'translateX(0)';
	document.getElementById('wrapper_3').innerHTML = '<div class="cargando" style="margin-top:100px;">'+cargando_blue+'</div>';

	// Obtenemos la cache del perfil de usuarios
	var cache = JSON.parse(localStorage.getItem('cache_user'+id));
	var minutos = 5;

	// Verificamos la cache del perfil de usuarios
	if(cache){
		// Si existe la cache, la cargamos
		document.getElementById('wrapper_3').innerHTML = cache.res;

		if((new Date().getTime() - (minutos * 60 * 1000)) > cache.time){
			ajax('include/server_curso.php',{
				user: id,
				type: 'user_mostrar'
			}, function (data){
				// Mostramos la informacion
				document.getElementById('wrapper_3').innerHTML = data.datos;
				// Guardamos informacion en la cache
				localStorage.setItem('cache_user'+data.user, JSON.stringify({
					'time': new Date().getTime(),
					'res': data.datos
				}));
				localStorage.setItem('user_mostrar_control_'+data.user, JSON.stringify(0));
				localStorage.setItem('user_mostrar_'+data.user, JSON.stringify(0));
				//resaltamos el codigo
				resaltador();
			}, 'Json');	
		}
	} else {
		// Si no hay cache cargamos informacion del servidor
		ajax('include/server_curso.php',{
			user: id,
			type: 'user_mostrar'
		}, function (data){
			// Mostramos la informacion
			document.getElementById('wrapper_3').innerHTML = data.datos;
			// Guardamos informacion en la cache
			localStorage.setItem('cache_user'+data.user, JSON.stringify({
				'time': new Date().getTime(),
				'res': data.datos
			}));
			//resaltamos el codigo
			resaltador();
		}, 'Json');
	}
	// Limpiamos la informacion sobre el usuario
	setTimeout(function (){
		document.getElementById('user_info').innerHTML = '';
	}, 500);
}
// Obtenemos los hashtag del curso
function twitter_hashtag(hashtag)
{
	ajax('include/server_twitter.php',{
		hashtag: hashtag,
		type: 'twitter_hashtag'
	}, function (data){
		// Creamos mensaje
		var title = "<span class='icon-twitter' style='font-weight:bold;'>Manda un mensaje en twitter con el hashtag <a href='https://twitter.com/intent/tweet?hashtags="+hashtag+"'>#"+hashtag+"</a></span>";
		var temp = document.getElementById('twitter_title').innerHTML;
		document.getElementById('twitter_title').innerHTML = title + temp;

		// Mostramos los hashtag
		var datos = data.statuses;
		var mensajes = '';
		for(i in datos){
			// Obtenemos el texto del twitt
			var texto = datos[i].text;
			// Reemplazamos los enlaces planos por html
			texto = url_replace_twitter(texto);
			// Reemplazamos e color del hashtag
			texto = texto.replace('#'+hashtag, "<span><a href='https://twitter.com/search?q=%23"+hashtag+"&src=savs' target='_blank' class='twitter_hashtag'>#"+hashtag+"</a></span>");
		
			mensajes += "<div class='twitter'><img src='"+datos[i].user.profile_image_url+"'><p>"+datos[i].user.name+" <a href='http://twitter.com/"+datos[i].user.screen_name+"' target='_blank'>@"+datos[i].user.screen_name+"</a>:</p><p>"+texto+"</p></div>";
		}
		// Agregamos los div
		document.getElementById('twitter_contenedor').innerHTML += mensajes;
		// Activamos el rotador de imagenes
		defineSizes();
		var slide = setInterval('moveSlider("right")', 5000);
	}, 'Json');
}
// Redimencionamos el wrapper para la aplicacion
function wrapper()
{
	var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	var height = (window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight) - 50;
	var height_ = (window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight) - 50;
	var width_menu = document.getElementById('menu_principal').style.pixelHeight || document.getElementById('menu_principal').offsetHeight;

	// verificamos la resolucion de la pantalla
	if(width >= 900){
		// si la resoluciones mayor a 800 compartimos pantalla con el menu
		var wrapper = width - 535;
		var menu = width - 525;

		document.getElementById('wrapper').style.width = wrapper+'px';
		document.getElementById('wrapper').style.minWidth = wrapper+'px';
		document.getElementById('wrapper').style.height = height+'px';
		document.getElementById('wrapper').style.maxHeight = height+'px';

		document.getElementById('wrapper_1').style.width = menu+'px';
		document.getElementById('wrapper_1').style.minWidth = menu+'px';
		document.getElementById('wrapper_1').style.height = height_+'px';
		document.getElementById('wrapper_1').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_2').style.width = menu+'px';
		document.getElementById('wrapper_2').style.minWidth = menu+'px';
		document.getElementById('wrapper_2').style.height = height_+'px';
		document.getElementById('wrapper_2').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_3').style.width = menu+'px';
		document.getElementById('wrapper_3').style.minWidth = menu+'px';
		document.getElementById('wrapper_3').style.height = height_+'px';
		document.getElementById('wrapper_3').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_4').style.width = menu+'px';
		document.getElementById('wrapper_4').style.minWidth = menu+'px';
		document.getElementById('wrapper_4').style.height = height_+'px';
		document.getElementById('wrapper_4').style.maxHeight = height_+'px';
		
		document.getElementById('menu_principal').style.width = '515px';
		document.getElementById('menu_principal').style.transform = 'translateX('+menu+'px)';
		document.getElementById('menu_principal').style.webkitTransform = 'translateX('+menu+'px)';
		
		document.getElementById('menu_discucion').style.width = '515px';
		document.getElementById('menu_discucion').style.transform = 'translateX('+width+'px)';
		document.getElementById('menu_discucion').style.webkitTransform = 'translateX('+width+'px)';
		
		document.getElementById('menu_notas').style.width = '515px';
		document.getElementById('menu_notas').style.transform = 'translateX('+width+'px)';
		document.getElementById('menu_notas').style.webkitTransform = 'translateX('+width+'px)';
		
		// Mostramos el header
		document.getElementById('header_1').style.borderRight = '25px solid #fff';
		removeClass(document.getElementById('header_2'), 'header_2');
		removeClass(document.getElementById('header_3'), 'header_2');

		// Ocultamos el boton
		document.getElementById('mostrar_menu').style.display = 'none';
	} else if(width > 600 && width < 899){
		// Si la pantalla es menor a 800 le asignamos un tamaño menor al menu
		var wrapper = width - 395;
		var menu = width - 395;

		document.getElementById('wrapper').style.width = wrapper+'px';
		document.getElementById('wrapper').style.minWidth = wrapper+'px';
		document.getElementById('wrapper').style.height = height+'px';
		document.getElementById('wrapper').style.maxHeight = height+'px';
		
		document.getElementById('wrapper_1').style.width = width+'px';
		document.getElementById('wrapper_1').style.minWidth = width+'px';
		document.getElementById('wrapper_1').style.height = height_+'px';
		document.getElementById('wrapper_1').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_2').style.width = width+'px';
		document.getElementById('wrapper_2').style.minWidth = width+'px';
		document.getElementById('wrapper_2').style.height = height_+'px';
		document.getElementById('wrapper_2').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_3').style.width = width+'px';
		document.getElementById('wrapper_3').style.minWidth = width+'px';
		document.getElementById('wrapper_3').style.height = height_+'px';
		document.getElementById('wrapper_3').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_4').style.width = width+'px';
		document.getElementById('wrapper_4').style.minWidth = width+'px';
		document.getElementById('wrapper_4').style.height = height_+'px';
		document.getElementById('wrapper_4').style.maxHeight = height_+'px';
		
		document.getElementById('menu_principal').style.width = '385px';
		document.getElementById('menu_principal').style.transform = 'translateX('+menu+'px)';
		document.getElementById('menu_principal').style.webkitTransform = 'translateX('+menu+'px)';
		
		document.getElementById('menu_discucion').style.width = '385px';
		document.getElementById('menu_discucion').style.transform = 'translateX('+width+'px)';
		document.getElementById('menu_discucion').style.webkitTransform = 'translateX('+width+'px)';

		document.getElementById('menu_notas').style.width = '385px';
		document.getElementById('menu_notas').style.transform = 'translateX('+width+'px)';
		document.getElementById('menu_notas').style.webkitTransform = 'translateX('+width+'px)';
		
		// Ocultamos el header_2
		document.getElementById('header_1').style.borderRight = '25px solid #1F6177';
		addClass(document.getElementById('header_2'), 'header_2');
		addClass(document.getElementById('header_3'), 'header_2');

		// Mostramos el menu
		document.getElementById('mostrar_menu').style.display = 'block';
	} else if(width <= 600){
		// Si el tamaño de la pantalla es menor a 600 ocultamos el menu
		var wrapper = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

		document.getElementById('menu_principal').style.width = '310px';
		document.getElementById('menu_principal').style.transform = 'translateX('+(width + 20)+'px)';
		document.getElementById('menu_principal').style.webkitTransform = 'translateX('+(width + 20)+'px)';
		
		document.getElementById('menu_discucion').style.width = width+'px';
		document.getElementById('menu_discucion').style.transform = 'translateX('+(width + 20)+'px)';
		document.getElementById('menu_discucion').style.webkitTransform = 'translateX('+(width + 20)+'px)';
		
		document.getElementById('menu_notas').style.width = width+'px';
		document.getElementById('menu_notas').style.transform = 'translateX('+(width + 20)+'px)';
		document.getElementById('menu_notas').style.webkitTransform = 'translateX('+(width + 20)+'px)';
		
		document.getElementById('wrapper_1').style.width = width+'px';
		document.getElementById('wrapper_1').style.minWidth = width+'px';
		document.getElementById('wrapper_1').style.height = height_+'px';
		document.getElementById('wrapper_1').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_2').style.width = width+'px';
		document.getElementById('wrapper_2').style.minWidth = width+'px';
		document.getElementById('wrapper_2').style.height = height_+'px';
		document.getElementById('wrapper_2').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_3').style.width = width+'px';
		document.getElementById('wrapper_3').style.minWidth = width+'px';
		document.getElementById('wrapper_3').style.height = height_+'px';
		document.getElementById('wrapper_3').style.maxHeight = height_+'px';
		
		document.getElementById('wrapper_4').style.width = width+'px';
		document.getElementById('wrapper_4').style.minWidth = width+'px';
		document.getElementById('wrapper_4').style.height = height_+'px';
		document.getElementById('wrapper_4').style.maxHeight = height_+'px';
		
		// Ocultamos el header_2
		document.getElementById('header_1').style.borderRight = '25px solid #1F6177';
		addClass(document.getElementById('header_2'), 'header_2');
		addClass(document.getElementById('header_3'), 'header_2');
	}
}

// Resaltador de sintaxis
function resaltador()
{
	// ########### ZONA EDITABLE ########################################################################################
    var lenguajeEspecifico = ''; //Dejarlo así para que funcione por defecto con la mayoría de lenguajes más usados 
    var skin = 'desert'; //Selección de skin o tema. Ver lista posible más abajo. Por defecto se usa el skin 'default'
    // ########### FIN ZONA EDITABLE ########################################################################################

    getScript("https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js?skin=" + (skin ? skin : "default") + (lenguajeEspecifico ? "?lang=" + lenguajeEspecifico : ""));
    var code = document.querySelectorAll('.code');
    for(var i = 0; i < code.length; i++){
    	// Obtenemos el html de code en cuestion
    	var contenido = code[i].innerHTML;
    	var pre = "<pre class='prettyprint" + (lenguajeEspecifico ? " lang-" + lenguajeEspecifico : "") + "' >";

    	// Agregamos el contenido
    	code[i].innerHTML = pre + contenido + '</pre>';

    }

    // Modificamos todas las etiquetas .tag y .str
    setTimeout(function (){
	    $tag = document.querySelectorAll('.tag');
	    for(var i = 0; i < $tag.length; i++){
	    	var contenido = $tag[i].innerHTML;
	    	// reemplazamos los simbolos < y >
    		contenido = contenido.replace(/&lt;\//g,'<span style="color:#fff;font-weight:normal;">&lt;/</span>');
			contenido = contenido.replace(/&lt;/g,'<span style="color:#fff;font-weight:normal;">&lt;</span>');
			contenido = contenido.replace(/&gt;/g,'<span style="color:#fff;font-weight:normal;">&gt;</span>');

	    	$tag[i].innerHTML = contenido;
	    };

	    $str = document.querySelectorAll('.str');
	    for(var i = 0; i < $str.length; i++){
	    	var contenido = $str[i].innerHTML;
	    	// reemplazamos los simbolos \n
	    	contenido = contenido.replace(/\n/g,'\\n');

	    	$str[i].innerHTML = contenido;
	    };

	    $com = document.querySelectorAll('.com');
	    for(var i = 0; i < $com.length; i++){
	    	var contenido = $com[i].innerHTML;
	    	// reemplazamos los simbolos \n
	    	//contenido = contenido.replace(/\n/g,'\\n');

	    	$com[i].innerHTML = contenido;
	    };
	}, 1000);
}