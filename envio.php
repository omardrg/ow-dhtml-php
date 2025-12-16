<?php
/******************************/
/***						***/
/***	CONFIGURACIÓN		***/
/***						***/
/******************************/
$destinatario = 'aaa@aaa'; // dirección de destino del email
$usrSMTP = 'bbb@gmail.com'; // mail de la cuenta SMTP
$pasSMTP = 'ccc'; // contraseña de la cuenta SMTP
$sk = 'ddd'; // clave privada de Google Recaptcha

$nombre 		= htmlspecialchars($_POST['Campo_Nombre'], ENT_QUOTES, 'UTF-8');
$asunto 		= htmlspecialchars($_POST['Campo_Asunto'], ENT_QUOTES, 'UTF-8');
$email 			= htmlspecialchars($_POST['Campo_Email'], ENT_QUOTES, 'UTF-8');

/******************************/
/******************************/

$cuerpo = "<html><head></head><body><style>table {font-family:tahoma,sans-serif;} th {text-align:right;}</style>\n";
$cuerpo .= "<table>\n";
foreach($_POST as $nombre_campo => $valor){
	if ( is_array($valor) ) {
		$safeValor = htmlspecialchars(implode(",", $valor), ENT_QUOTES, 'UTF-8');
        $safeCampo = htmlspecialchars($nombre_campo, ENT_QUOTES, 'UTF-8');
        $cuerpo .= "<tr><th>{$safeCampo}: </th><td>{$safeValor}</td></tr>\n";
	} else {
		if ($nombre_campo != 'g-recaptcha-response') {
            $safeValor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            $safeCampo = htmlspecialchars($nombre_campo, ENT_QUOTES, 'UTF-8');
            $cuerpo .= "<tr><th>{$safeCampo}: </th><td>{$safeValor}</td></tr>\n";
        }
	}
}

$cuerpo .= "</table>\n";
$cuerpo .= "</body></html>\n";

$cuerpo2 = "Asunto: ".$asunto."\n";
foreach($_POST as $nombre_campo => $valor){
	if ( is_array($valor) ) {
		$safeValor = htmlspecialchars(implode(",", $valor), ENT_QUOTES, 'UTF-8');
        $safeCampo = htmlspecialchars($nombre_campo, ENT_QUOTES, 'UTF-8');
        $cuerpo2 .= "{$safeCampo}: {$safeValor}\n";
	} else {
		if ($nombre_campo != 'g-recaptcha-response') {
            $safeValor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
            $safeCampo = htmlspecialchars($nombre_campo, ENT_QUOTES, 'UTF-8');
            $cuerpo2 .= "{$safeCampo}: {$safeValor}\n";
        }
	}
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// recaptcha
if(isset($_POST['g-recaptcha-response'])) {
	$captcha=$_POST['g-recaptcha-response'];

	//$urlRecaptcha = "https://www.google.com/recaptcha/api/siteverify?secret=".$sk."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR'];
	//$response=json_decode(file_get_contents($urlRecaptcha), true);
	
	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "https://www.google.com/recaptcha/api/siteverify",
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => [
			'secret'   => $sk,
			'response' => $captcha,
			'remoteip' => $_SERVER['REMOTE_ADDR']
		],
		CURLOPT_RETURNTRANSFER => true
	]);
	
	$responseData = curl_exec($curl);
	curl_close($curl);

	$response = json_decode($responseData, true);

	if($response['success'] == true) {

		require('PHPMailer/src/Exception.php');
		require('PHPMailer/src/PHPMailer.php');
		require('PHPMailer/src/SMTP.php');

		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
			//Server settings
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp.gmail.com';                   	//Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = $usrSMTP;                     			//SMTP username
			$mail->Password   = $pasSMTP;                              	//SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;			//Enable implicit TLS encryption
			$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
			$mail->SMTPDebug  = 0;                      				//Enable verbose debug output; set value to 2
			$mail->setLanguage('es', 'assets/PHPMailer/language/');

			//Recipients
			$mail->setFrom($usrSMTP);
			$mail->addAddress($destinatario);     					//Add a recipient
			$mail->addReplyTo($email, $nombre);

			// Attachment
			$allowedTypes = ['image/jpeg','image/png','application/pdf'];
			foreach($_FILES as $nombre_campo => $valor){
				if ($valor['error'] == UPLOAD_ERR_OK && is_uploaded_file($valor['tmp_name'])) {
					if (in_array(mime_content_type($valor['tmp_name']), $allowedTypes)) {
						$mail->addAttachment($valor['tmp_name'], basename($valor['name']));
					}
				}
			}

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