<?php
// Última modificación: 2025-04-21 11:10
include "../functions.php";
$conn = conectarBD();

$slug = mysqli_real_escape_string($conn, $_GET['municipio'] ?? '');
$query = "SELECT m.*, p.nombre AS provincia, c.nombre AS comunidad
          FROM municipios m
          JOIN provincias p ON m.cod_provincia = p.codigo_ine
          JOIN comunidades c ON m.cod_comunidad = c.codigo_ine
          WHERE m.slug = '$slug' LIMIT 1";
$result = mysqli_query($conn, $query);
$municipio = mysqli_fetch_assoc($result);

if (!$municipio) {
    echo "<h1>Municipio no encontrado</h1>";
    exit;
}

include "../includes/header.php";
?>

<div class="layout municipio-layout" style="gap: 2rem; padding: 2rem;">
  <!-- Ficha del municipio -->
  <div class="municipio-info" style="flex: 1;">
    <h1><?= htmlspecialchars($municipio['nombre']) ?></h1>
    <p>
      <strong>Provincia:</strong>
      <a href="/provincias/<?= $municipio['cod_provincia'] ?>">
        <?= htmlspecialchars($municipio['provincia']) ?>
      </a>
    </p>
    <p>
      <strong>Comunidad:</strong>
      <a href="/comunidades/<?= $municipio['cod_comunidad'] ?>">
        <?= htmlspecialchars($municipio['comunidad']) ?>
      </a>
    </p>
    <p><strong>Habitantes:</strong> <?= number_format($municipio['habitantes']) ?></p>
    <p><strong>Altitud:</strong> <?= $municipio['altitud'] ?> m</p>
    <p class="descripcion"><?= nl2br(htmlspecialchars($municipio['descripcion'])) ?></p>

    <?php if (!empty($municipio['puntos_interes'])): ?>
      <h3>📍 Puntos de Interés</h3>
      <div class="puntos-interes">
        <?php
        $puntos = explode(",", $municipio['puntos_interes']);
        foreach ($puntos as $p) {
            echo '<span class="punto-tag">' . htmlspecialchars(trim($p)) . '</span>';
        }
        ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Mapa -->
  <div class="municipio-mapa" style="flex: 1;">
    <div id="mapa" style="height: 400px;"></div>
  </div>
</div>

<!-- Sección de subida de imágenes -->
<div class="upload-section">
  <?php include __DIR__ . '/../includes/upload_form.php'; ?>
</div>

<!-- Inicialización del mapa (vuelve a pintarlo una vez cargado todo) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const lat = <?= $municipio['latitud'] ?>;
  const lon = <?= $municipio['longitud'] ?>;

  const map = L.map('mapa').setView([lat, lon], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  L.marker([lat, lon])
    .addTo(map)
    .bindPopup("<?= htmlspecialchars($municipio['nombre']) ?>");
});
</script>

<?php include "../includes/footer.php"; ?>
