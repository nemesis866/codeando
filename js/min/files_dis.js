function files_dis(e,t){document.getElementById("content_dis_edit_files").ondragover=function(e){return e.dataTransfer.dropEffect="move",this.style.border="1px dashed #000",!1},document.getElementById("content_dis_edit_files").ondragleave=function(e){this.style.border="1px solid #Ff8124",this.style.borderBottom="3px solid #cc7013"},document.getElementById("form_discucion_edit").ondragover=function(e){return!1},document.getElementById("form_discucion_edit").ondrop=function(e){e.preventDefault(),e.stopPropagation()},document.getElementById("content_dis_edit_files").ondrop=function(n){n.preventDefault(),n.stopPropagation();for(var r=Array("<",">","){","(",")","'","\t","javascript:","array:","text/html"),i=Array("[-","-]",") {","&-","-&",'"',"&nbsp;&nbsp;&nbsp;&nbsp;","javascript :","array :","text/htm"),o=n.dataTransfer.files.length,s=0;s<o;s++){var d=n.dataTransfer.files[s].name,a=n.dataTransfer.files[s].size/1e3;if(a<=10){var c=d.split("."),l=c.length-1,u=c[l];if(document.getElementById("content_dis_edit_cargando").innerHTML='<img src="/img/cargando1.gif">',"html"==u||"js"==u||"css"==u||"php"==u||"json"==u||"c"==u||"cpp"==u||"h"==u||"cc"==u||"java"==u||"ino"==u){var f=n.dataTransfer.files[s],m=new FileReader,_="UTF-8";"c"!=u&&"cpp"!=u||(_="ISO-8859-1"),m.readAsText(f,_),m.addEventListener("progress",function(e){e.lengthComputable},!1),m.addEventListener("load",function(n){var o=n.target.result;console.log(o),o=o.replace(/</g,"&lt;"),o=o.replace(/>/g,"&gt;");for(var s=0;s<=r.length;s++)for(;o.indexOf(r[s])>=0;)o=o.replace(r[s],i[s]);ajax("include/server_files.php",{ext:u,name:d,size:a,contenido:o,id:t,control:e,type:"file_temp_dis",tipo:"DIS"},function(e){document.getElementById("content_dis_edit_cargando").innerHTML="",isEmpty(e.error)?(document.getElementById("content_dis_edit_results").innerHTML+='<div id="file_'+e.id+'" class="files icon-'+u+'" onclick="javascript:files_mostrar('+e.id+",'file')\">"+d+" ("+a+'Kb)<span onclick="javascript:files_delete('+e.id+')">X</span></div>',document.getElementById("dis_content_"+t).innerHTML+='<div id="file_'+e.id+'" class="files icon-'+u+'" onclick="javascript:files_mostrar('+e.id+",'file')\">"+d+" ("+a+'Kb)<span onclick="javascript:files_delete('+e.id+')">X</span></div>',sessionStorage.removeItem("cache_discucion"+e.id_dis),success(e.status)):error(e.error)},"Json")},!1),m.addEventListener("error",function(e){"NotReadableError"==e.target.error.name&&(document.querySelector(".error").innerHTML="Error al subir el archivo intente nuevamente",document.querySelector(".error").style.transform="translateY(0)",setTimeout(function(){document.querySelector(".error").style.transform="translateY(-60px)"},3e3)),document.getElementById("content_dis_edit_cargando").innerHTML=""},!1)}else document.querySelector(".error").innerHTML="Extensión no valida",document.querySelector(".error").style.transform="translateY(0)",setTimeout(function(){document.querySelector(".error").style.transform="translateY(-60px)"},3e3),document.getElementById("content_dis_edit_cargando").innerHTML=""}else document.querySelector(".error").innerHTML="Archivo mayor a 10 Kb",document.querySelector(".error").style.transform="translateY(0)",setTimeout(function(){document.querySelector(".error").style.transform="translateY(-60px)"},3e3)}return this.style.border="1px solid #009957",!1}}function files_delete_dis(e){ajax("include/server_files.php",{id:e,type:"file_delete_dis"},function(e){document.getElementById("file_"+e.id).style.display="none",back_files(),success(e.status)},"Json")}function files_delete_edit(e,t){ajax("include/server_files.php",{id:e,type:"file_delete_edit"},function(e){for(var n=document.querySelectorAll(".file_"+e.id),r=0;r<n.length;r++)n[r].style.display="none";sessionStorage.removeItem("cache_discucion"+t),back_files(),success(e.status)},"Json")}