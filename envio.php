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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// recaptcha
if(isset($_POST['g-recaptcha-response'])) {
	$captcha=$_POST['g-recaptcha-response'];

	$urlRecaptcha = "https://www.google.com/recaptcha/api/siteverify?secret=".$sk."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR'];
	$response=json_decode(file_get_contents($urlRecaptcha), true);
	if($response['success'] == true) {

		require('PHPMailer/src/Exception.php');
		require('PHPMailer/src/PHPMailer.php');
		require('PHPMailer/src/SMTP.php');

		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
			//Server settings
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp.office365.com';                   //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = $usuario;                     			//SMTP username
			$mail->Password   = $password;                              //SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;			//Enable implicit TLS encryption
			$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
			$mail->SMTPDebug  = 0;                      				//Enable verbose debug output; set value to 2
			$mail->setLanguage('ca', 'PHPMailer/language');

			//Recipients
			$mail->setFrom($usuario);
			$mail->addAddress($destinatario);     					//Add a recipient
			$mail->addReplyTo($email, $nombre);

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = $asunto;
			$mail->Body    = $cuerpo;
			$mail->AltBody = $cuerpo2;
			$mail->CharSet = "UTF-8";

			$mail->send();
			header('Location: envio-correcto.html');
		} catch (Exception $e) {
			echo "{$mail->ErrorInfo}";
			header('Location: envio-error.html');
		}

	} else { // recaptcha
		header('Location: envio-error.html');
	}
} else { // recaptcha
	header('Location: envio-error.html');
}

?>