function testAPI(page) {
	// conectamos con el servidor Programacion azteca
	FB.api('/me', function (response) {
		version = ie();

		// Ocultamos el boton de login
		document.getElementById('button_fb').style.display = 'none';

		// Mostramos la imagen de cargando mientras procesamos el formulario
		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('cargando_fb');

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = '<img src="/img/cargando1.gif">';
			}
		} else {
			document.querySelector('.cargando_fb').innerHTML = '<img src="/img/cargando1.gif">';
		}

		if(response.email == null){
			if(document.getElementById('results')){
				document.getElementById('results').innerHTML = '';
			}
			// Ocultamos el boton de login
			document.getElementById('button_fb').style.display = 'block';
		} else {
			//document.getElementById('LoginButton').style.display = 'none';
			//document.getElementById('results').innerHTML = '<img src="/img/cargando1.gif">';
			ajax('process_facebook.php', {
				first_name : response.first_name,
				last_name  : response.last_name,
				uid        : response.id,
				gender     : response.gender,
				email      : response.email
			}, function (data) {
				// Almacenamos el id del usuario
				localStorage.setItem('id_user', data.id_user);

				// Quitamos la imagen cargando
				if(version <= 8){
					// Soporte a navegadores antiguos
					var temp = getElementsByClassName('cargando_fb');

					for(var i = 0; i < temp.length; i++){
						temp[i].innerHTML = '<img src="/img/cargando.gif">';
					}
				} else {
					document.querySelector('.cargando_fb').innerHTML = '<img src="/img/cargando.gif">';
				}

				// Mostramos el boton de ingreso
				if(document.querySelectorAll('.boton_none')){
					var boton = document.querySelectorAll('.boton_none');

					for(var i = 0; i < boton.length; i++){
						boton[i].style.display = 'inline-block';
					}
				}

				// Mostramos boton al admin
				document.getElementById('button_admin').style.display = 'inline-block';

				// Mostramos las diferentes opciones al inicio de session
				document.getElementById('login_button').style.display = 'none';

				if(document.getElementById('login_init')){
					document.getElementById('login_init').style.display = 'inline-block';
				}

				if(document.getElementById('results')){
					document.getElementById('results').innerHTML = "<h3 class='titulo'>"+data.result+"</h3>";
				}

				document.getElementById('login_box').style.display = 'none';
			}, 'Json');
		}
	});
}

function logout(){
	var url = '?logout=1';
	location.href = url;
}