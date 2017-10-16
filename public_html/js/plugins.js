// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.

// Plugin para fijar el cursor del textarea
$.fn.setCursorPosition = function (pos){
    this.each(function (index, elem){
        if(elem.setSelectionRange){
            elem.setSelectionRange(pos, pos);
        } else if(elem.createTextRange) {
            var range = elem.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    });
    return this;
}

// Plugin para obtener la posicion de un cursor en el textarea
$.fn.getCursorPosition = function (){
    var elem = $(this).get(0);
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

// Plugin para obtener el texto seleccionado de un textarea
$.fn.getCursorSelection = function (){
    var elem = $(this).get(0);
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