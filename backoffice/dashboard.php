<?php
session_start();

// Incluir archivo de funciones
include '../functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vista de Dron</title>
    <link rel="stylesheet" href="../styles/main.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Bienvenido al Panel de Administración</h1>
        <nav>
            <ul>
                <li><a href="perfil.php">Mi Perfil</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
        <section>
            <h2>Resumen de actividad</h2>
            <!-- Aquí puedes poner información dinámica, como estadísticas o contenido reciente -->
        </section>
    </div>
</body>
</html>
