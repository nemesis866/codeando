/************************************************
Archivo contenedor de funciones

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

/*******************************************************************
Funciones javascript
*******************************************************************/

// Funcion que comprueba que un string contenga el formato de un email
function correo (email){
	if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)){
		return true; // Si es un email
	} else {
		return false; // Si no es un email
	}
}

// Funcion que reemplaza los enlaces planos por enlaces html
function url_replace(text)
{
	// Reemplazamos url que comiencen con http://, https://, ftp://, file://
	var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gi;
	text = text.replace(exp, "<a href=\"$1\" target=\"_blank\" rel=\"nofollow\">$1</a>");

	return text;
}

// Funcion que reemplaza los enlaces planos por enlaces html para twitter
function url_replace_twitter(text)
{
	// Reemplazamos url que comiencen con http://, https://, ftp://, file://
	var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gi;
	text = text.replace(exp, "<a href=\"$1\" target=\"_blank\" rel=\"nofollow\" class=\"twitter_hashtag\">$1</a>");

	return text;
}

// Funcion encargada de validar la URL
function validateUrl(url){
	// Expresiones regulares para la validacion
	var b = RegExp("((?:(https?|ftp|itms)://)?(?:(?:[^\\s\\!\"\\#\\$\\%\\&\\'\\(\\)\\*\\+\\,\\.\\/\\:\\;\\<\\=\\>\\?\\@\\\\[\\]\\^\\_`\\{\\|\\}\\~]+\\.)+(?:aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|local|example|invalid|test|\u0645\u0635\u0631|\u0440\u0444|\u0627\u0644\u0633\u0639\u0648\u062f\u064a\u0629|\u0627\u0645\u0627\u0631\u0627\u062a|xn--wgbh1c|xn--p1ai|xn--mgberp4a5d4ar|xn--mgbaam7a8h|\u4e2d\u56fd|\u4e2d\u570b|\u9999\u6e2f|\u0627\u0644\u0627\u0631\u062f\u0646|\u0641\u0644\u0633\u0637\u064a\u0646|\u0642\u0637\u0631|\u0dbd\u0d82\u0d9a\u0dcf|\u0b87\u0bb2\u0b99\u0bcd\u0b95\u0bc8|\u53f0\u7063|\u53f0\u6e7e|\u0e44\u0e17\u0e22|\u062a\u0648\u0646\u0633|xn--fiqs8S|xn--fiqz9S|xn--j6w193g|xn--mgbayh7gpa|xn--ygbi2ammx|xn--wgbl6a|xn--fzc2c9e2c|xn--xkc2al3hye2a|xn--kpry57d|xn--kprw13d|xn--o3cw4h|xn--pgbs0dh|\u0625\u062e\u062a\u0628\u0627\u0631|\u0622\u0632\u0645\u0627\u06cc\u0634\u06cc|\u6d4b\u8bd5|\u6e2c\u8a66|\u0438\u0441\u043f\u044b\u0442\u0430\u043d\u0438\u0435|\u092a\u0930\u0940\u0915\u094d\u0937\u093e|\u03b4\u03bf\u03ba\u03b9\u03bc\u03ae|\ud14c\uc2a4\ud2b8|\u05d8\u05e2\u05e1\u05d8|\u30c6\u30b9\u30c8|\u0baa\u0bb0\u0bbf\u0b9f\u0bcd\u0b9a\u0bc8|xn--kgbechtv|xn--hgbk6aj7f53bba|xn--0zwm56d|xn--g6w251d|xn--80akhbyknj4f|xn--11b5bs3a9aj6g|xn--jxalpdlp|xn--9t4b11yi5a|xn--deba0ad|xn--zckzah|xn--hlcj6aya9esc7a|[a-z]{2})(?::[0-9]+)?|(?:[0-9]{1,3}\\.){3}(?:[0-9]{1,3}))(?:\\/?[\\S]+)?)", "gi");
	
	matches = url.match(b);
	if (!matches) return null;
	return matches
}

// Funcion que verifica si una clase existe en un elemento html
function thereClass(elem, cls)
{
    return elem.className.match(new RegExp("(\\s|^)"+cls+"(\\s|$)"));
}

//Función para agregar una clase, si no existe la clase enviada - agrega la clase.
function addClass(elem, cls)
{
    if(!thereClass(elem, cls)){
        elem.className += " "+cls;
    }
}

// Función para Eliminar una clase
function removeClass(elem, cls)
{
    if(thereClass(elem, cls)){
        var exp = new RegExp("(\\s|^)"+cls);

        elem.className = elem.className.replace(exp,"");
    }
}

// Funcion que verifica si un enlace esta vacio
function isEmpty(obj) {
    // null and undefined are "empty"
    if (obj == null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}

// Funcion similar a getScript() de jquery
function getScript(url)
{
    var g = document.createElement('script'), // create a script tag
        s = document.getElementsByTagName('script')[0]; // find the first script tag in the document
    g.src = url; // set the source of the script to your script
    s.parentNode.insertBefore(g, s); // append the script to the DOM
}

// Funcion para traer datos en formato json asincronos
// https://github.com/jfriend00/docReady/blob/master/docready.js
function ajax(url, datos, callback, type)
{
	// Enviamos peticion ajax
	var xmlhttp;
	var type = type.toLowerCase();

	if(window.XMLHttpRequest){
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function(e){
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
			var data;

			// Obtenemos los datos asincronos
			if(type == 'json'){
                if(!isEmpty(xmlhttp.responseText)){
                    data = JSON.parse(xmlhttp.responseText);
                } else {
                    data = xmlhttp.responseText;
                }
	    	} else if(type == 'text'){
	    		data = xmlhttp.responseText;
	    	} else {
	    		data = xmlhttp.responseXML;
	    	}

	    	// Ejecutamos la funcion callback
	    	callback(data);
	    }
	}

	// Ordenamos los datos
	var params = [];
    for (var key in datos) {
        if (datos.hasOwnProperty(key)) {
            params.push(encodeURIComponent(key) + '=' + encodeURIComponent(datos[key]));
        }
    }
    params =  params.join('&');

	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(params);
}

// Funcion equivalente para document.ready() de jquery
(function(funcName, baseObj)
{
    // The public function name defaults to window.docReady
    // but you can pass in your own object and own function name and those will be used
    // if you want to put them in a different namespace
    funcName = funcName || "docReady";
    baseObj = baseObj || window;
    var readyList = [];
    var readyFired = false;
    var readyEventHandlersInstalled = false;
    
    // call this when the document is ready
    // this function protects itself against being called more than once
    function ready() {
        if (!readyFired) {
            // this must be set to true before we start calling callbacks
            readyFired = true;
            for (var i = 0; i < readyList.length; i++) {
                // if a callback here happens to add new ready handlers,
                // the docReady() function will see that it already fired
                // and will schedule the callback to run right after
                // this event loop finishes so all handlers will still execute
                // in order and no new ones will be added to the readyList
                // while we are processing the list
                readyList[i].fn.call(window, readyList[i].ctx);
            }
            // allow any closures held by these functions to free
            readyList = [];
        }
    }
    
    function readyStateChange() {
        if ( document.readyState === "complete" ) {
            ready();
        }
    }
    
    // This is the one public interface
    // docReady(fn, context);
    // the context argument is optional - if present, it will be passed
    // as an argument to the callback
    baseObj[funcName] = function(callback, context) {
        // if ready has already fired, then just schedule the callback
        // to fire asynchronously, but right away
        if (readyFired) {
            setTimeout(function() {callback(context);}, 1);
            return;
        } else {
            // add the function and context to the list
            readyList.push({fn: callback, ctx: context});
        }
        // if document already ready to go, schedule the ready function to run
        if (document.readyState === "complete") {
            setTimeout(ready, 1);
        } else if (!readyEventHandlersInstalled) {
            // otherwise if we don't have event handlers installed, install them
            if (document.addEventListener) {
                // first choice is DOMContentLoaded event
                document.addEventListener("DOMContentLoaded", ready, false);
                // backup is window load event
                window.addEventListener("load", ready, false);
            } else {
                // must be IE
                document.attachEvent("onreadystatechange", readyStateChange);
                window.attachEvent("onload", ready);
            }
            readyEventHandlersInstalled = true;
        }
    }
})("docReady", window);

// Funcion que hace lo mismo que get() de jquery
function httpGet(theUrl)
{
    var xmlHttp = null;

    if(window.XMLHttpRequest){
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlHttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlHttp.open( "GET", theUrl, false );
    xmlHttp.send( null );

    return xmlHttp.responseText;
}

// Funcion para fijar el cursor del textarea
function setCursorPosition (element, pos){
    if(element.setSelectionRange){
        element.setSelectionRange(pos, pos);
    } else if(element.createTextRange) {
        var range = element.createTextRange();
        range.collapse(true);
        range.moveEnd('character', pos);
        range.moveStart('character', pos);
        range.select();
    }

    return element;
}

// Funcion para obtener la posicion de un cursor en el textarea
function getCursorPosition (elem){
    var pos = 0;
    if('selectionStart' in elem){
        pos = elem.selectionStart;
    } else if('selection' in document){
        elem.focus();
        var sel = document.selection.createRange();
        var selLength = document.selection.createRange().text.length;
        sel.moveStart('character', -elem.value.length);
        pos = sel.text.length - selLength;
    }
    return pos;
}

// Funcion para obtener el texto seleccionado de un textarea
function getCursorSelection (elem){
    if('selectionStart' in elem){
        var startPos = elem.selectionStart;
        var endPos = elem.selectionEnd;
        return elem.value.substr(startPos, endPos - startPos);
    } else if('selection' in document){
        elem.focus();
        var selection = document.selection.createRange();
        return selection.text;
    }
}

// Seleccionar clases en navegadores antiguos
function getElementsByClassName(cls){
    // Si soporta getElementsByClassName
    if(document.querySelectorAll){
        return document.querySelectorAll(cls);
    } else if(document.getElementsByClassName){
        return document.getElementsByClassName(cls);
    } else {
        // Para navegadores antiguos
        var elements = document.all;
        var result = [];
        cls = cls.replace(/\-/g, "\\-");
        var reg = new RegExp("(^|\\s)" + cls + "(\\s|$)");

        for(var i = 0; i < elements.length; i++){
            if(reg.test(oElements[i].className)){
                result.push(oElements[i]);
            }
        }

        return result;
    }
}

// Funcion para insertar un elemento delante en una lista
function insertAfter(newElement,targetElement) {
    //target is what you want it to go after. Look for this elements parent.
    var parent = targetElement.parentNode;

    //if the parents lastchild is the targetElement...
    if(parent.lastchild == targetElement) {
        //add the newElement after the target element.
        parent.appendChild(newElement);
        } else {
        // else the target has siblings, insert the new element between the target and it's next sibling.
        parent.insertBefore(newElement, targetElement.nextSibling);
        }
}

/********************************************************
Funciones complementarias de otras funciones
********************************************************/

// Retorna la version de IE del navegador
var ie = function (){
    // Obtenemos el nombre del navegador
    var nav = navigator.appName;

    // Si el navegador es IE retornamos su version
    if(nav == "Microsoft Internet Explorer"){
        // Convertimos en minusculas la cadena que devuelve userAgent
        var ie = navigator.userAgent.toLowerCase();
        // Extraemos de la cadena la version de IE
        return parseInt(ie.split('msie')[1]);
    } else {
        // Si el navegador no es IE retornamos valor por defecto
        return 10;
    }
}