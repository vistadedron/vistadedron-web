<?php
include "../functions.php";
?>
<?php include "../includes/header.php"; ?>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>
    <main class="content">
       <div class="login-card">
            <h1 class="card-title">Registro de Usuario</h1>
            <form action="/registro/procesar_registro.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" required>
                </div>
                <div class="form-group">
                    <label for="email">📧 Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">🔒 Contraseña</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="telefono">📱 Teléfono</label>
                    <input type="text" name="telefono" id="telefono">
                </div>
                <div class="form-group">
                    <label for="web">🌐 Web personal</label>
                    <input type="url" name="web" id="web">
                </div>
                <div class="form-group">
                    <label for="profesional">👤 Tipo de usuario</label>
                    <select name="profesional" id="profesional">
                        <option value="0">Particular</option>
                        <option value="1">Profesional</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Registrarse</button>
                <p class="help-text">¿Ya tienes cuenta? <a href="/backoffice/login">Inicia sesión aquí</a></p>
            </form>
        </div>
    </main>
</div>

<?php include "../includes/footer.php"; ?>
