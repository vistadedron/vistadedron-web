<?php
include "../functions.php";
$mysqli = conectarBD();

$codigo = $_GET['codigo'] ?? '';

if (!empty($codigo)) {
  $stmt = $mysqli->prepare("UPDATE usuarios SET verificado = 1 WHERE codigo_verificacion = ?");
  $stmt->bind_param("s", $codigo);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    echo "<p>Usuario verificado correctamente.</p>";
  } else {
    echo "<p>Error: Código inválido o ya verificado.</p>";
  }

  $stmt->close();
}

$mysqli->close();
