/************************************************
Archivo javascript para estadisticas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

var cargando = "<div class='cargando'><img src='/img/cargando.gif'><div>";
var url = '/estadisticas/include/server_stats.php';

function stats()
{
	document.getElementById('resumen').innerHTML = cargando;
	document.getElementById('horas').innerHTML = cargando;
	document.getElementById('mes').innerHTML = cargando;

	ajax(url,{
		type: 'stats_resumen'
	}, function (data){
		document.getElementById('resumen').innerHTML = data.resumen;
		document.getElementById('horas').innerHTML = data.horas;
		document.getElementById('mes').innerHTML = data.mes;
	}, 'Json');

	// Cargamos estadisticas cada minuto
	setInterval(function (){
		ajax(url,{
			type: 'stats_resumen'
		}, function (data){
			document.getElementById('resumen').innerHTML = data.resumen;
			document.getElementById('horas').innerHTML = data.horas;
			document.getElementById('mes').innerHTML = data.mes;
		}, 'Json');
	}, 60000);
}
function visitors()
{
	document.getElementById('referencia').innerHTML = cargando;
	document.getElementById('pagina').innerHTML = cargando;
	document.getElementById('keyword').innerHTML = cargando;
	document.getElementById('lenguaje').innerHTML = cargando;

	ajax(url,{
		type: 'stats_visitas'
	}, function (data){
		document.getElementById('referencia').innerHTML = data.referencia;
		document.getElementById('pagina').innerHTML = data.pagina;
		document.getElementById('keyword').innerHTML = data.keyword;
		document.getElementById('lenguaje').innerHTML = data.lenguaje;
	}, 'Json');

	// Cargamos estadisticas cada minuto
	setInterval(function (){
		ajax(url,{
			type: 'stats_visitas'
		}, function (data){
			document.getElementById('referencia').innerHTML = data.referencia;
			document.getElementById('pagina').innerHTML = data.pagina;
			document.getElementById('keyword').innerHTML = data.keyword;
			document.getElementById('lenguaje').innerHTML = data.lenguaje;
		}, 'Json');
	}, 60000);
}
function history()
{
	var show_month = document.getElementById('show_month').value;
	var show_year = document.getElementById('show_year').value;

	document.getElementById('historial').innerHTML = cargando;
	document.getElementById('historial_1').innerHTML = cargando;
	document.getElementById('historial_2').innerHTML = cargando;

	ajax(url,{
		show_month: show_month,
		show_year: show_year,
		type: 'stats_historial',
	}, function (data){
		document.getElementById('historial').innerHTML = data.historial;
		document.getElementById('historial_1').innerHTML = data.historial_1;
		document.getElementById('historial_2').innerHTML = data.historial_2;
	}, 'Json');
}
function show_month(month, year)
{
	document.getElementById('historial_2').innerHTML = cargando;

	ajax(url,{
		show_month: month,
		show_year: year,
		type: 'show_month',
	}, function (data){
		document.getElementById('historial_2').innerHTML = data.resultado;
	}, 'Json');
}
function show_year(month, year)
{
	document.getElementById('historial_1').innerHTML = cargando;
	document.getElementById('historial_2').innerHTML = cargando;

	ajax(url,{
		show_month: month,
		show_year: year,
		type: 'show_year',
	}, function (data){
		document.getElementById('historial_1').innerHTML = data.resultado_1;
		document.getElementById('historial_2').innerHTML = data.resultado_2;
	}, 'Json');
}
function cerrar()
{
	window.close();
}