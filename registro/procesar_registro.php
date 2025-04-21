<?php
include "../functions.php";

$conn = conectarBD();

// Obtener datos del formulario
$nombre = $_POST['nombre'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$profesional = isset($_POST['profesional']) ? 1 : 0;
$ip_registro = $_SERVER['REMOTE_ADDR'];
$fecha_registro = date('Y-m-d H:i:s');
$codigoVerificacion = bin2hex(random_bytes(16));

// Verificar si el email ya está registrado
$sql_check = "SELECT id FROM usuarios WHERE email = '" . $conn->real_escape_string($email) . "'";
$result = $conn->query($sql_check);

if ($result && $result->num_rows > 0) {
    echo "<p style='color:red;'>El email ya está registrado. Por favor, utiliza otro.</p>";
    exit;
}

// Encriptar la contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar el usuario en la base de datos
$sql = "INSERT INTO usuarios (nombre, apellidos, email, contrasena_hash, profesional, ip_registro, fecha_registro, verificado, deshabilitado, codigo_verificacion)
        VALUES ('$nombre', '$apellidos', '$email', '$password_hash', $profesional, '$ip_registro', '$fecha_registro', 0, 0, '$codigoVerificacion')";

if ($conn->query($sql) === TRUE) {
    enviarEmailConfirmacion($email, $nombre, $codigoVerificacion);
    echo "<p>Registro exitoso. Revisa tu correo electrónico para confirmar tu cuenta.</p>";
} else {
    echo "Error al registrar el usuario: " . $conn->error;
}

$conn->close();
?>
