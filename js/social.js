/************************************************
Archivo que carga las librerias js de sociales

Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
************************************************/

(function() {
	var js,
		s = document.getElementsByTagName("script")[0],
		add = function(url,id){
		if(document.getElementById(id)){ return; }
			js = document.createElement("script");
			js.async = true;
			js.src = url;
			s.parentNode.insertBefore(js, s);
		};

	add("//apis.google.com/js/plusone.js","plus");
	add("//platform.twitter.com/widgets.js","twitter-wjs");
	add("//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js","adsense");
})();