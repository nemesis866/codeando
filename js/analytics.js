/************************************************
Archivo con el codigo de seguimiento de analytics

Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
************************************************/

/* ------------------------------------------------------
Codigo antiguo
---------------------------------------------------------

window.___gcfg = {lang: 'es'};
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo $this->_analytics; ?>']);
_gaq.push(['_trackPageview']);
*/
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', '<?php echo $this->_analytics; ?>', 'auto');
ga('send', 'pageview');