/************************************************
Archivo javascript para el inicio de la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.com@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Verificamos la version de internet explorer
var nav = navigator.appName;
var version = 10;
var cargando_blue = '<div class="loader loader-blue"></div>';
var cargando_white = '<div class="loader loader-white"></div>';
var url = 'include/server.php';

if(nav == "Microsoft Internet Explorer"){
    // Convertimos en minusculas la cadena que devuelve userAgent
    var ie = navigator.userAgent.toLowerCase();
    // Extraemos de la cadena la version de IE
    version = parseInt(ie.split('msie')[1]);

    // Dependiendo de la version mostramos un resultado
    if(version <= 9){
    	cargando_blue = '<div class="cargando"><img src="/img/cargando.gif"></div>';
    	cargando_white = '<div class="cargando"><img src="/img/cargando.gif"></div>';
    }
}

docReady(function (){
	// Procesamos el formulario de contacto
	form_contacto();
	// Mostramos efecto parallax para el index
	efecto_parallax();
	// Imprimimos la imagen del contador
	img_count();
	// Procesamos formulario de contacto
	form_login();
	// Procesamos el formulario de recover
	form_recover();
	// Procesamos el formulario de registro
	form_register();
	// Mostramos el formulario de login
	login_show();
	img_tema();
	// Mostramos el menu movil
	mostrar_menu();
	// Buscador
	buscador();

	/*
	if(document.getElementById('presentacion')){
		var i = 0;
		var img = ['/img/background2.jpg',
			'/img/background3.png',
			'/img/background.jpg'];

		setInterval(function (){
			if(i == 3){
				i = 0;
			}
			document.getElementById('presentacion').style.background = 'url('+img[i]+') 100% 110% no-repeat fixed';

			i++;
		}, 5000);
	}
	*/
});

window.onload = function (){
	resaltador();
}

window.onresize = function (){
	img_tema();
}

// Buscador
var buscador = function ()
{
	document.getElementById('q').onkeyup = function (e)
	{
		var key = e.which || window.event.keyCode;
		if(key == 13){
			var q = document.getElementById('q').value;

			if(q.length > 0){
				location.href = "/buscador/?cx=partner-pub-0593566584451788%3A7918232415&cof=FORID%3A10&ie=UTF-8&q="+q+"&sa=Buscar&siteurl=codeando.org%2Fbuscador%2F&ref=&ss=";
			}
		}
	}
}

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

// Formulario de contacto
function form_contacto()
{
	var formulario = document.getElementById('form_contacto') || 'form';
	formulario.onsubmit = function (e){
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		var name = document.getElementById('name').value;
		var email = document.getElementById('email').value;
		var asunto = document.getElementById('asunto').value;
		var contenido = document.getElementById('contenido').value;

		if(check(name)){ return }

		if(!correo(email)){
			var text = 'Ingrese un email valido';
	
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('error');
				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = text;
					temp[i].style.marginTop = '0px';
				}
			} else {
				document.querySelector('.error').innerHTML = text;
				document.getElementById('.error').style.transform = 'translateY(0)';
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

			return
		}

		if(check(asunto)){ return }

		if(contenido.length < 30){
			var text = 'Ingrese un comentario con mas de 30 caracteres';
			
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

			return
		}

		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '/img/cargando.gif';
			}
		} else {
			document.querySelector('.cargando').innerHTML = '/img/cargando.gif';
		}

		document.getElementById('submit_contacto').style.display = 'none';

		ajax(url, {
			name: name,
			email: email,
			asunto: asunto,
			contenido: contenido,
			type: 'form_contacto'
		}, function (data){
			document.getElementById('submit_contacto').style.display = 'block';

			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = '';
				}
			} else {
				document.querySelector('.cargando').innerHTML = '';
			}

			name = document.getElementById('name').value = '';
			email = document.getElementById('email').value = '';
			asunto = document.getElementById('asunto').value = '';
			contenido = document.getElementById('contenido').value = '';

			var text = 'Su comentario se envio con exito';
			
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
				}
			}, 3000);

			return
		}, 'Json');
	}
	formulario.onkeyup = function (e)
	{
		var ev = (e) ? e : event;
   		var key = (ev.which) ? ev.which : window.event.keyCode;
		
		if(key == 13){
			return false;
		}
	}
}

// Procesamos el formulario de login
var form_login = function()
{
	var formulario = document.getElementById('form_login') || 'form';
	formulario.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		// Obtenemos los valores del formulario
		var user = document.getElementById('user').value;
		var pass = document.getElementById('pass').value;

		// Verificamos los datos obtenidos
		if(check(user)){ return }
		if(check(pass)){ return }

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_login');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando_login').innerHTML = '<img src="/img/cargando.gif">';
		}

		document.getElementById('submit_login').style.display = 'none';

		ajax(url, {
			user: user,
			pass: pass,
			type: 'form_login'
		}, function (data){
			// Verificamos si no hubo un error al enviar el formulario
			if(isEmpty(data.error)){
				// Si no hubo error
				document.getElementById('user').disabled = true;
				document.getElementById('pass').disabled = true;

				// Almacenamos el id de usuario
				localStorage.setItem('id_user', data.id_user);

				var text = "Inicio de sesion con exito . . .";
				success(text);

				// Mostramos las diferentes opciones al inicio de session
				document.getElementById('login_button').style.display = 'none';

				if(document.getElementById('login_init')){
					document.getElementById('login_init').style.display = 'inline-block';
				}

				// En la pagina de cursos mostramos mensaje de bienvenida
				if(document.getElementById('results')){
					document.getElementById('results').innerHTML = "<h3 class='titulo'>"+data.result+"<h3>";
				}

				// Mostramos el boton de ingreso
				if(document.querySelectorAll('.boton_none')){
					var boton = document.querySelectorAll('.boton_none');

					for(var i = 0; i < boton.length; i++){
						boton[i].style.display = 'inline-block';
					}
				}

				// Ocultamos el boton de informacion
				if(document.querySelectorAll('.boton_informacion')){
					var boton = document.querySelectorAll('.boton_informacion');

					for(var i = 0; i < boton.length; i++){
						boton[i].style.display = 'none';
					}
				}

				// Mostramos boton al admin
				document.getElementById('button_admin').style.display = 'inline-block';

				document.getElementById('login_box').style.display = 'none';
			} else {
				// Si hubo error
				// Mostramos el boton de envio y ocultamos la imagen de cargando
				document.getElementById('submit_login').style.display = 'inline-block';
				document.getElementById('pass').value = '';
				document.getElementById('pass').focus();

				if(version <= 8){
					// Soporte a navegadores antiguos
					var temp = getElementsByClassName('cargando_login');

					for(var i = 0; i < temp.length; i++){
						temp[i].innerHTML = '';
					}
				} else {
					document.querySelector('.cargando_login').innerHTML = '';
				}

				error(data.error);
			}

			return
		}, 'Json');
	}
}

// Procesamos el formulario de registro
var form_register = function()
{
	var formulario = document.getElementById('form_register') || 'form';
	formulario.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		// Obtenemos los valores del formulario
		var username = document.getElementById('register_username').value;
		var pass1 = document.getElementById('register_pass1').value;
		var pass2 = document.getElementById('register_pass2').value;
		var email = document.getElementById('register_email').value;

		// Verificamos los datos obtenidos
		if(check(username)){ return }
		if(username.length < 8){
			error('El username debe tener al menos 8 caracteres');
			return
		}
		if(check(pass1)){ return }
		if(check(pass2)){ return }
		if(pass1.length < 8){
			error('La contraseña debe tener al menos 8 caracteres');
			return
		}
		if(pass1 != pass2){
			error('Las contraseñas no coinciden, intente nuevamente');
			return
		}
		if(!correo(email)){
			error('Ingrese un email valido, intente nuevamente');
			return
		}

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_register');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando_register').innerHTML = '<img src="/img/cargando.gif">';
		}

		// Ocultamos el boton submit
		document.getElementById('submit_register').style.display = 'none';
		// Ocultamos el boton cancelar
		document.getElementById('register_cancel').style.display = 'none';

		ajax(url, {
			user: username,
			pass: pass1,
			email: email,
			type: 'form_register'
		}, function (data){
			// Verificamos si no hubo un error al enviar el formulario
			if(isEmpty(data.error)){
				// Si no hubo error
				document.getElementById('register_username').disabled = true;
				document.getElementById('register_pass1').disabled = true;
				document.getElementById('register_pass2').disabled = true;
				document.getElementById('register_email').disabled = true;

				var text = "Revice su bandeja de email";
				success(text);

				var msg = "<p>Se le envio un email con el enlace para autorizar su registro,<br><strong>no olvide revisar su bandeja de correo no deseado</strong></p>";
				document.getElementById('register_msg').innerHTML = msg;

				setTimeout(function (){
					// Ocultamos el formulario de register y mostramos login
					document.getElementById('register').style.display = 'none';
					document.getElementById('form').style.display = 'block';
				}, 2000);
			} else {
				// Si hubo error
				// Mostramos el boton de envio y ocultamos la imagen de cargando
				document.getElementById('submit_register').style.display = 'inline-block';
				document.getElementById('register_pass1').value = '';
				document.getElementById('register_pass2').value = '';

				error(data.error);
			}
			// Mostramos el boton cancel
			document.getElementById('register_cancel').style.display = 'inline-block';

			// Quitamos la imagen cargando
			if(version <= 8){
				// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando_register');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = '';
				}
			} else {
				document.querySelector('.cargando_register').innerHTML = '';
			}

			return
		}, 'Json');
	}
}

// Procesamos el formulario de recover
var form_recover = function()
{
	var formulario = document.getElementById('form_recover') || 'form';
	formulario.onsubmit = function (e){
		// Evitamos el comportamiento por defecto del formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			window.event.returnValue = false;
		} else {
			e.preventDefault();
		}

		// Obtenemos los valores del formulario
		var email = document.getElementById('recover_email').value;

		// Verificamos los datos obtenidos
		if(!correo(email)){
			error('Ingrese un email valido');
			return
		}

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_recover');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando.gif">';
			}
		} else {
			document.querySelector('.cargando_recover').innerHTML = '<img src="/img/cargando.gif">';
		}

		// Ocultamos el boton submit
		document.getElementById('submit_recover').style.display = 'none';
		// Ocultamos el boton cancelar
		document.getElementById('recover_cancel').style.display = 'none';

		ajax(url, {
			email: email,
			type: 'form_recover'
		}, function (data){
			// Verificamos si no hubo un error al enviar el formulario
			if(isEmpty(data.error)){
				// Si no hubo error
				document.getElementById('recover_email').disabled = true;

				success(data.status);

				var msg = "<p>Sus datos de acceso fueron enviados a su email,<br><strong>no olvide revisar su bandeja de correo no deseado</strong></p>";
				document.getElementById('recover_msg').innerHTML = msg;

				setTimeout(function (){
					// Ocultamos el formulario de recover y mostramos login
					document.getElementById('recover').style.display = 'none';
					document.getElementById('form').style.display = 'block';

					// Hacemos focus al input de username
					document.getElementById('user').value = '';
					document.getElementById('user').focus();
				}, 3000);
			} else {
				// Si hubo error
				// Mostramos el boton de envio y ocultamos la imagen de cargando
				document.getElementById('submit_recover').style.display = 'inline-block';
				document.getElementById('recover_email').value = '';
				document.getElementById('recover_email').focus();

				error(data.error);
			}
			// Mostramos el boton cancelar
			document.getElementById('recover_cancel').style.display = 'none';

			// Quitamos la imagen cargando
			if(version <= 8){
			// Soporte a navegadores antiguos
				var temp = getElementsByClassName('cargando_recover');

				for(var i = 0; i < temp.length; i++){
					temp[i].innerHTML = '';
				}
			} else {
				document.querySelector('.cargando_recover').innerHTML = '';
			}

			return
		}, 'Json');
	}
}

// Mostramos formulario para registrarse
function login_recover()
{
	// Limpiamos el formulario de login
	document.getElementById('user').value = '';
	document.getElementById('pass').value = '';

	// Ocultamos el formulario
	document.getElementById('form').style.display = 'none';
	document.getElementById('recover').style.display = 'block';
}

// Ocultamos formulario para registrarse
function login_recover_cancel()
{
	// Ocultamos el formulario
	document.getElementById('recover').style.display = 'none';
	document.getElementById('form').style.display = 'block';
}

// Mostramos formulario para registrarse
function login_register()
{
	// Limpiamos el formulario de login
	document.getElementById('user').value = '';
	document.getElementById('pass').value = '';

	// Ocultamos el formulario
	document.getElementById('form').style.display = 'none';
	document.getElementById('register').style.display = 'block';
}

// Ocultamos formulario para registrarse
function login_register_cancel()
{
	// Limpiamos el formulario de registro
	document.getElementById('register_username').value = '';
	document.getElementById('register_pass1').value = '';
	document.getElementById('register_pass2').value = '';
	document.getElementById('register_email').value = '';

	// Ocultamos el formulario
	document.getElementById('register').style.display = 'none';
	document.getElementById('form').style.display = 'block';
}

// Mostramos el formulario de login
function login_show()
{
	// Mostramos el formulario
	if(document.getElementById('login_button')){
		var show = document.getElementById('login_button').onclick = function (){
			document.getElementById('login_box').style.display = 'block';
		}
	}

	// Ocultamos el formulario
	var hide = document.getElementById('login_close').onclick = function (){
		document.getElementById('login_box').style.display = 'none';	
	}
}

// Router de la plataforma
function router(data, id_curso)
{
	if(data == 'plataforma'){
		localStorage.setItem('id_curso', id_curso);
		location.href = '/plataforma/';
	} else if(data == 'cursos'){
		location.href = '/cursos/';
	}
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

// Suscribir al curso
function suscribir(id)
{
	ajax('include/server.php',{
		id: id,
		type: 'suscribir'
	}, function (data){
		// Ocultamos boton de suscripcion
		document.getElementById('boton_suscribir_'+data.id).style.display = 'none';
		// Mostramos boton de ingreso al curso
		document.getElementById('boton_ingreso_'+data.id).style.display = 'inline-block';

		// Aumentamos el contador de los usuarios suscritos
		if(document.getElementById('user_suscription'+data.id)){
			document.getElementById('user_suscription'+data.id).innerHTML = data.count;
		}

		// Mostramos mensaje
		success(data.status);
	}, 'Json');
}

// Efecto parallax pagina de inicio
function efecto_parallax()
{
	window.onscroll = function() {
	    var parallax = document.querySelectorAll('.parallax');

	    for(var i = 0; i < parallax.length; i++){       
	     	var obj = parallax[i]; // assigning the object
	                    
			// Scroll the background at var speed
			// the yPos is a negative value because we're scrolling it UP!
			var scroll = (document.documentElement.scrollTop || document.body.scrollTop);
			var yPos = -(scroll / 2);					
			//var yPos = -(window.scrollTop() / 10); 
			
			// Put together our final background position
			var coords = '50% '+ yPos + 'px';

			// Move the background
			obj.style.backgroundPosition = coords;
		
		}; // window scroll Ends

 	};
}

// Mostramos imagen del contador
function img_count()
{
	document.getElementById('img_count').innerHTML = '<img style="opacity:0;filter: alpha(opacity=0);" src="/estadisticas/counter.php?ref=' + escape(document.referrer) + '">';
}

// Mostramos tamaño imagen de fondo temas
function img_tema()
{
	// Si esta disponible el div informacion actuamos
	if(document.getElementById('informacion')){
		// Obtenemos el alto del navegador
		var height = document.getElementsByTagName('header')[0].style.pixelHeight || document.getElementsByTagName('header')[0].offsetHeight;
		// Obtenemos el ancho del navegador
		var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
		// Obtenemos el alto del header
		var header = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

		// Tamaño para la imagen
		var total =  header - height - 90;

		// Aplicamos el tamaño
		document.getElementById('informacion').style.height = total+'px';

		// Modificamos el tamaño del titulo segun el tamaño de la imagen
		if(width > 750){
			document.getElementsByTagName('h1')[0].style.fontSize = (total / 6)+'px';
		} else {
			document.getElementsByTagName('h1')[0].style.fontSize = '2.5em';
		}
	}
}

var mostrar_menu = function ()
{
	// Mostramos el menu
	if(document.getElementById('display-menu')){
		// document.getElementById('display-menu').style.display = 'none';
		// Ocultamos el icono
		document.getElementById('display-menu').onclick = function (){
			// document.getElementById('display-menu').style.display = 'none';

			// Mostramos el menu
			var height = document.getElementById('menu-contenido').style.pixelHeight || document.getElementById('menu-contenido').offsetHeight
			
			document.getElementById('menu-contenido').style.opacity = '1';
			document.getElementById('menu-contenido').style.transform = 'translateY(0px)';
			document.getElementById('menu-contenido').style.webkitTransform = 'translateY(0px)';
		}
	}

	// Cerramos el menu
	if(document.getElementById('menu_cerrar')){
		document.getElementById('menu_cerrar').onclick = function (){
			// Ocultamos el menu
			var height = document.getElementById('menu-contenido').style.pixelHeight || document.getElementById('menu-contenido').offsetHeight
			
			document.getElementById('menu-contenido').style.transform = 'translateY(-'+height+'px)';
			document.getElementById('menu-contenido').style.webkitTransform = 'translateY(-'+height+'px)';
			document.getElementById('menu-contenido').style.opacity = '0';

			// Mostramos el icono
			// document.getElementById('display-menu').style.display = 'block';
		}
	}

	// Ocultamos el menu
	var height = document.getElementById('menu-contenido').style.pixelHeight || document.getElementById('menu-contenido').offsetHeight
	
	document.getElementById('menu-contenido').style.transform = 'translateY(-'+height+'px)';
	document.getElementById('menu-contenido').style.webkitTransform = 'translateY(-'+height+'px)';
}

// Resaltador de sintaxis
function resaltador()
{
	// ########### ZONA EDITABLE ########################################################################################
    var lenguajeEspecifico = ''; //Dejarlo así para que funcione por defecto con la mayoría de lenguajes más usados 
    var skin = 'desert'; //Selección de skin o tema. Ver lista posible más abajo. Por defecto se usa el skin 'default'
    // ########### FIN ZONA EDITABLE ########################################################################################

    getScript("/js/resaltador.js?skin=" + (skin ? skin : "default") + (lenguajeEspecifico ? "?lang=" + lenguajeEspecifico : ""));
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