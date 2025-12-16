$(document).ready(function(){
	
	// icono menu
	$('#nav-icon3').click(function(){
		$(this).toggleClass('open');
		$('ul.menu-principal').toggleClass('abierto');
	});
	
	// lightbox imágenes
	$('figcaption a').fancybox({
		slideShow : {
			autoStart : true,
			speed     : 500
		}
	});
	
	// aviso cookies https://www.w3schools.com/js/js_cookies.asp
	var cookies = document.cookie.split("; "); // leo todas las cookies y las separo en un array
	
	if ( $.inArray('aviso_cookies=1', cookies) < 0 ) { // si no encuentro la cookie 'aviso_cookies=1'
		$('#aviso_cookies').css('display', 'block'); // muestro el aviso
	}
	
	$('#aviso_cookies_cerrar').click(function(){ // al clicar en el icono de cerrar
		document.cookie = "aviso_cookies=1; path=/; Secure; SameSite=Strict"; // genero la cookie, sin fecha de expiración (se borrará al finalizar la navegación) y sirve para todo el dominio
		$('#aviso_cookies').css('display', 'none'); // Esconde el aviso de cookies
	});
	
	// formulario
	$('form').validate();
});