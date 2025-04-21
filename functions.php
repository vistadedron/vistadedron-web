<?php

// Mostrar todos los errores y advertencias
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Función para conectar a la base de datos
function conectarBD() {
    $server = 'localhost';
    $usuario = 'vistadedron_user';
    $password = 'C0BmV[4BRo&~';
    $bd = 'vistadedron_web';

    $conexion = new mysqli($server, $usuario, $password, $bd);


    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }
    mysqli_set_charset($conexion, "utf8");
    return $conexion;
}

// Función para enviar un correo normal (para confirmación de registro)
function enviarCorreoNormal($email, $nombre) {
    $subject = "Bienvenido a Vistadedron";
    $message = "
    <html>
    <head>
    <title>Bienvenido a Vistadedron</title>
    </head>
    <body>
    <p>Hola $nombre,</p>
    <p>Gracias por registrarte en Vistadedron. Tu cuenta ha sido creada correctamente. Por favor, verifica tu correo electrónico para completar el proceso.</p>
    <p>Saludos,</p>
    <p>El equipo de Vistadedron</p>
    </body>
    </html>
    ";

    // Establecer cabeceras para el envío de un correo HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: hola@vistadedron.es' . "\r\n";

    return mail($email, $subject, $message, $headers);
}

// Función para enviar el correo de verificación (para confirmar la cuenta)
function enviarEmailConfirmacion($email, $nombre, $codigoVerificacion) {
    $asunto = "Confirma tu registro en VistaDeDron.es";
    $headers = "From: VistaDeDron <hola@vistadedron.es>\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";

    $mensaje = "
    <html>
    <head><title>Confirmación de registro</title></head>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Hola $nombre,</h2>
        <p>Gracias por registrarte en <strong>VistaDeDron.es</strong>.</p>
        <p>Por favor, confirma tu dirección de correo haciendo clic en el siguiente enlace:</p>
        <p><a href='https://www.vistadedron.es/registro/confirmacion_usuario.php?codigo=$codigoVerificacion'>Confirmar mi cuenta</a></p>
        <br>
        <p>¡Nos vemos desde el aire!</p>
    </body>
    </html>";

    return mail($email, $asunto, $mensaje, $headers);
}
?>
