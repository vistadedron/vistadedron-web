<?php
include "../functions.php";
session_start();
// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /backoffice/login');
    exit;
}
?>
<?php include "../includes/header.php"; ?>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>
    <main class="content">
        <div class="dash-card">
            <h1 class="card-title">Panel de Control</h1>
            <p>Bienvenido al Backoffice de Vista de Dron. Desde aquí puedes gestionar tu cuenta y el contenido del sitio.</p>

            <div class="dashboard-actions">
                <a href="/backoffice/perfil" class="btn btn-secondary">Mi Perfil</a>
                <a href="/admin/usuarios" class="btn btn-secondary">Gestión de Usuarios</a>
                <a href="/registro/crear" class="btn btn-primary">Crear nuevo Usuario</a>
            </div>
        </div>
    </main>
</div>

<?php include "../includes/footer.php"; ?>
