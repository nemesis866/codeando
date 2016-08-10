var $cap;
var active,copy;
var identificator = [];
var borrar = [];
borrar.push({id:0});
var url = 'include/server_admin.php';

docReady(function (){
	innitComponents();
});

function innitComponents()
{
	//$cap = $('.item');
	$cap = document.querySelectorAll('.item');

	identificator = [];

	$.each($cap, function (i){
		this.ondragstart = inicia;
		this.ondragleave = fin;
		this.ondragover = encima;
		this.ondrop = suelta;

		identificator.push({id: parseInt($(this).attr('id'))});
	});

	for(i in identificator){
		for(j in borrar){
			if(identificator[i]['id'] == borrar[j]['id']){
				identificator.splice(i,1);
			}
		}
	}
}

function inicia(e)
{
	// Cuando empecemos a arrastrar el objeto colocamos un cursor nuevo
	e.dataTransfer.effectAllowed = 'move';
	// Obtenemos el objeto que arrastramos
	var self = $(this);
	var id = self.attr('id');
	// Obtenemos el indice del objeto que arrastramos
	active = self.index();

	e.dataTransfer.setData('i', active);
	e.dataTransfer.setData('id', id);
	// Creamos un objeto extendido del objeto actual
	copy = self.clone();

}
function suelta(e)
{
	e.preventDefault();
	e.stopPropagation();

	var self = $(this);
	var sIndex = self.index();
	var prev = parseInt(e.dataTransfer.getData('i'));
	var prev_id = e.dataTransfer.getData('id');
	var id = self.attr('id');

	// Si soltamos en el mismo lugar no pasa nada
	if(sIndex === prev){
		return false;
	}

	self.removeClass('capitulo_hover');

	// Removemos el objeto del que venimos
	//$cap.eq(prev).remove();
	$cap[prev].remove();

	if(prev < sIndex){
		// Si soltamos en un elemento anterior al actual
		copy.insertAfter($cap[sIndex]);
	} else if(prev > sIndex){
		// Si soltamos en un elemento despues del actual
		copy.insertBefore($cap[sIndex]);
	}

	// Volvemos a añadir los eventos al nuevo objeto creado
	innitComponents();

	// Declaramos un arreglo temporal
	var temp_identificador = [];

	// Cambiamos los identificadores
	for(var i in identificator){
		$('.number_cap_'+parseInt(identificator[i]['id'])).html('Capitulo '+(parseInt(i) + 1)+': ');
		// Agregamos informacion al arreglo temporal
		var temp = {
			id: i,
			identificador: identificator[i]['id'],
		}
		temp_identificador.push(temp);
	}

	// Ordenamos los resultados en la base de datos
	ajax(url,{
		object: JSON.stringify(temp_identificador), // Formateamos para envio a php
		type: 'orden_capitulos'
	}, function (data){
		success(data.status);
	}, 'Json');
}
function encima(e)
{
	e.dataTransfer.dropEffect = 'move';

	var self = $(this);

	if(self.index() === active){
		return false;
	}

	self.addClass('capitulo_hover')

	return false;
}
function fin(e)
{
	var self = $(this);
	self.removeClass('capitulo_hover');
}

// Funciones independientes
function cap_delete_yes(id,id_curso)
{
	// Elimina un capitulo de la DB y el contenido
	document.getElementById('cap_delete_'+id).innerHTML = '<div class="cargando_line"><img src="/img/cargando.gif"></div>';

	// Agregamos el id al array borrar
	borrar.push({id: id});

	// Volvemos a añadir los eventos al objeto eliminado
	innitComponents();

	// Cambiamos los identificadores
	for(i in identificator){
		var version = ie();

		if(version <= 8){
			// Soporte a navegadores antiguos
			var temp = getElementsByClassName('number_cap_'+parseInt(identificator[i]['id']));

			for(var i = 0; i < temp.length; i++){
				temp[i].innerHTML = 'Capitulo '+(parseInt(i)+ 1)+': ';
			}
		} else {
			document.querySelector('.number_cap_'+parseInt(identificator[i]['id'])).innerHTML = 'Capitulo '+(parseInt(i)+ 1)+': ';
		}
	}

	// Eliminamos de la base de datos
	ajax(url,{
		id: id,
		id_curso: id_curso,
		type: 'cap_delete'
	}, function (data){
		// Ocultamos el item
		document.getElementById(id).style.display = 'none';
		// Mostramos mensaje
		success(data.status);
	}, 'Json');
}
function reinicio()
{
	innitComponents();
}