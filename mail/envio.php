<?php

/******************************/
/***						***/
/***	CONFIGURACIÓN		***/
/***						***/
/******************************/
$destinatario = 'AAAA@mataroin365.onmicrosoft.com'; // dirección de destino del email
$usrSMTP = 'AAAA@mataroin365.onmicrosoft.com'; // mail de la cuenta SMTP
$pasSMTP = '****'; // contraseña de la cuenta SMTP
$sk = '***'; // clave privada de Google Recaptcha


/******************************/
/******************************/

$nombre = $_POST['Campo_Nombre'];
$asunto = $_POST['Campo_Asunto'];
$email = $_POST['Campo_Email'];
$mensaje = $_POST['Campo_Mensaje'];

$cuerpo = "<html><head><style>table {font-family:tahoma,sans-serif;} th {text-align:right;}</style></head><body>\n";
$cuerpo .= "<table>\n";
$cuerpo .= "<tr><th>Nombre: </th><td>".$nombre."</td></tr>\n";
$cuerpo .= "<tr><th>Asunto: </th><td>".$asunto."</td></tr>\n";
$cuerpo .= "<tr><th>Email: </th><td>".$email."</td></tr>\n";
$cuerpo .= "<tr><th>Mensaje: </th><td>".$mensaje."</td></tr>\n";

$cuerpo .= "</table>\n";
$cuerpo .= "</body></html>\n";

$cuerpo2 = "Nombre: ".$nombre."\n";
$cuerpo2 .= "Asunto: ".$asunto."\n";
$cuerpo2 .= "Email: ".$email."\n";
$cuerpo2 .= "Mensaje: ".$mensaje."\n";


// recaptcha
if(isset($_POST['g-recaptcha-response'])) {
	$captcha=$_POST['g-recaptcha-response'];
	  
	$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$sk."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
	if($response['success'] == true) {

		require('_class.phpmailer.php');
		require('_class.smtp.php');


		$mail = new phpmailer();
		$mail->IsSMTP();
		$mail->Host			= "smtp.office365.com";
		$mail->SMTPAuth		= true; 
		$mail->SMTPSecure 	= 'tls';
		$mail->Port 		= 587;
		$mail->Username		= $usrSMTP;
		$mail->Password		= $pasSMTP;   

		$mail->SMTPDebug  	= 0;
		$mail->From 	  	= $usrSMTP;
		$mail->FromName   	= "Web ACME";
		$mail->CharSet		= "UTF-8";
		$mail->isHTML(true); 

		$mail->AddAddress($destinatario);
		$mail->addReplyTo($email, $nombre);
		$mail->Subject = $asunto;

		$mail->Body = $cuerpo;
		$mail->AltBody = $cuerpo2;
		if ( $mail->Send() ) { 
			header('Location: ../envio-correcto.html');
		} else {
			header('Location: ../envio-error.html');
		}
	} else { // recaptcha
		header('Location: ../envio-error.html');
	}
} else { // recaptcha
	header('Location: ../envio-error.html');
}
?>