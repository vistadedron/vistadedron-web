<?php
// Incluir la función de conexión a la base de datos
include('../functions.php');

// Verificar si el usuario está logueado
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
$conn = conectarBD();

// Obtener la información del usuario logueado
$userId = $_SESSION['id_usuario'];
$query = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Si el usuario existe
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "No se encontró el usuario.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../styles/main.css">
</head>
<body>
    <header>
        <h1>Bienvenido a tu perfil</h1>
    </header>

    <main>
        <section>
            <h2>Información de usuario</h2>
            <p><strong>Nombre:</strong> <?php echo $user['nombre']; ?></p>
            <p><strong>Apellidos:</strong> <?php echo $user['apellidos']; ?></p>
            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
            <p><strong>Fecha de registro:</strong> <?php echo $user['fecha_registro']; ?></p>
            <p><strong>Estado:</strong> <?php echo $user['verificado'] ? 'Verificado' : 'No verificado'; ?></p>
            <p><strong>Deshabilitado:</strong> <?php echo $user['deshabilitado'] ? 'Sí' : 'No'; ?></p>
        </section>

        <section>
            <a href="editar_perfil.php">Editar perfil</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 VistaDeDron.es - Todos los derechos reservados</p>
    </footer>
</body>
</html>
