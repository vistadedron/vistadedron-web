<?php
include "../functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    $conn = conectarBD();
    $stmt = $conn->prepare("SELECT id, email, contrasena_hash, verificado, deshabilitado FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {
        if (!$usuario['verificado']) {
            $error = "Tu cuenta aún no ha sido verificada. Revisa tu correo electrónico.";
        } elseif ($usuario['deshabilitado']) {
            $error = "Tu cuenta ha sido deshabilitada. Contacta con soporte.";
        } elseif (password_verify($password, $usuario['contrasena_hash'])) {
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            header("Location: /backoffice");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<?php include "../includes/header.php"; ?>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>
    <main class="content">
        <div class="login-card">
            <h1 class="card-title">Acceso al Backoffice</h1>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">📧 Email</label>
                    <input type="email" name="email" id="email" placeholder="tucorreo@ejemplo.com" required>
                </div>

                <div class="form-group">
                    <label for="password">🔒 Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary">Entrar</button>

                <p class="help-text">¿No tienes cuenta? <a href="/registro">Regístrate aquí</a></p>
            </form>
        </div>
    </main>
</div>

<?php include "../includes/footer.php"; ?>
