<?php
require 'libs/PHPMailer/src/Exception.php';
require 'libs/PHPMailer/src/PHPMailer.php';
require 'libs/PHPMailer/src/SMTP.php';

// Carga automática de librerías instaladas con Composer
require 'vendor/autoload.php';  

// Importamos las clases principales de PHPMailer que vamos a usar
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Creamos un nuevo objeto PHPMailer y le decimos que puede lanzar excepciones
$mail = new PHPMailer(true);

try {
    // ---------- CONFIGURACIÓN DEL SERVIDOR SMTP ----------
    $mail->isSMTP();                          // Decimos que vamos a usar SMTP (protocolo para enviar correos)
    $mail->Host       = 'smtp.gmail.com';     // Servidor SMTP de Gmail
    $mail->SMTPAuth   = true;                 // Activamos la autenticación SMTP
    $mail->Username   = 'tu_correo@gmail.com';// Tu cuenta de Gmail
    $mail->Password   = 'contraseña_de_app';  // Contraseña de aplicación de Gmail (NO tu contraseña normal)
    $mail->SMTPSecure = 'tls';                // Tipo de encriptación: TLS (puede ser 'ssl' en algunos casos)
    $mail->Port       = 587;                  // Puerto de Gmail para TLS (465 si fuera SSL)

    // ---------- REMITENTE Y DESTINATARIOS ----------
    $mail->setFrom('empresasistmayorista@gmail.com', 'Mi Sistema');  // Dirección y nombre que aparecerán como remitente
    $mail->addAddress('leoribera17@gmail.com', 'Empleado');    // Dirección y nombre del destinatario del correo
    // Podés agregar más destinatarios con más llamadas a addAddress()
    // También existen: addCC() para copia, addBCC() para copia oculta

    // ---------- CONTENIDO DEL MENSAJE ----------
    $mail->isHTML(true);                                   // Indicamos que el cuerpo del correo está en HTML
    $mail->Subject = 'Notificación de Stock';              // Asunto del correo
    $mail->Body    = '<b>El stock llegó a 0</b>. Por favor reponer el producto.'; // Cuerpo en HTML
    // Si querés también podés agregar texto alternativo sin HTML:
    // $mail->AltBody = 'El stock llegó a 0. Por favor reponer el producto.';

    // ---------- ENVÍO ----------
    $mail->send();                                         // Enviamos el correo
    echo 'Mensaje enviado correctamente';                  // Mensaje si todo salió bien
} catch (Exception $e) {
    // Si ocurre un error al enviar, se captura aquí
    echo "Error al enviar: {$mail->ErrorInfo}";
}
