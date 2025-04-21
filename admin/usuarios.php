<?php
session_start();

// Incluir archivo de funciones
include '../functions.php';

// Comprobar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Conectar a la base de datos
$conexion = conectarBD();

// Obtener los usuarios
$sql = "SELECT * FROM usuarios";
$resultado = $conexion->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $accion = $_POST['accion'];

    // Cambiar el estado de verificación o deshabilitación
    if ($accion == 'verificar') {
        $sql = "UPDATE usuarios SET verificado = 1 WHERE id = $usuario_id";
    } elseif ($accion == 'deshabilitar') {
        $sql = "UPDATE usuarios SET deshabilitado = 1 WHERE id = $usuario_id";
    }

    if ($conexion->query($sql) === TRUE) {
        $mensaje = "Estado actualizado correctamente.";
    } else {
        $mensaje = "Error al actualizar el estado: " . $conexion->error;
    }
}
?>

<!-- HTML para mostrar los usuarios -->
<h1>Gestión de Usuarios</h1>
<?php if (isset($mensaje)) echo "<p>$mensaje</p>"; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Verificado</th>
            <th>Deshabilitado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($usuario = $resultado->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $usuario['id']; ?></td>
            <td><?php echo $usuario['nombre']; ?></td>
            <td><?php echo $usuario['email']; ?></td>
            <td><?php echo $usuario['verificado'] ? 'Sí' : 'No'; ?></td>
            <td><?php echo $usuario['deshabilitado'] ? 'Sí' : 'No'; ?></td>
            <td>
                <?php if (!$usuario['verificado']) { ?>
                <form action="usuarios.php" method="POST">
                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                    <button type="submit" name="accion" value="verificar">Verificar</button>
                </form>
                <?php } ?>
                <?php if (!$usuario['deshabilitado']) { ?>
                <form action="usuarios.php" method="POST">
                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                    <button type="submit" name="accion" value="deshabilitar">Deshabilitar</button>
                </form>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
