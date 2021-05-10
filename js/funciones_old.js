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
	
	// aviso cookies
	if ( sessionStorage.controlcookie == 0 || sessionStorage.controlcookie == undefined ){ 
		$('#aviso_cookies').css('display', 'block');
	}
	$('#aviso_cookies_cerrar').click(function(){
		// si variable no existe se crea (al clicar en cerrar)
		// sessionStorage para que se borren los datos al cerrar sesión.
		// localStorage para mantener los datos al cerrar sesión
		sessionStorage.controlcookie = (localStorage.controlcookie || 0);
		
		sessionStorage.controlcookie++; // incrementamos cuenta de la cookie
		$('#aviso_cookies').css('display', 'none'); // Esconde el aviso de cookies
	});
	
	// formulario
	$('form').validate();
});