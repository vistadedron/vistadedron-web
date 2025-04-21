<?php
// includes/upload_form.php
// Última modif: 2025-04-21 11:00


// Conexión
require_once __DIR__ . '/../functions.php';
$conn = conectarBD();

// Recogemos lista de drones
$drones = [];
if ($res = $conn->query("SELECT id, modelo FROM drones")) {
    while ($row = $res->fetch_assoc()) {
        $drones[] = $row;
    }
}

// Municipio (slug) desde GET o variable
$municipio_slug = $_GET['municipio'] ?? '';
?>

<?php if (empty($_SESSION['user_id'])): ?>
  <!-- Aviso si no está logado -->
  <div class="upload-notice" style="padding:1rem; background:#fdecec; border:1px solid #f5c6cb; border-radius:6px; text-align:center; margin:1rem 0;">
    ⚠️ Debes <a href="/registro" style="color:#d93025; text-decoration:underline;">iniciar sesión</a> para subir imágenes.
  </div>
<?php else: ?>
  <!-- Botón disparador -->
  <button class="btn btn-primary" id="open-upload-btn" style="margin:1rem 0;">
    📤 Sube tus imágenes
  </button>

  <!-- Overlay de subida -->
  <div id="upload-overlay" class="upload-overlay" style="
      display:none;
      position:fixed;
      top:0; left:0;
      width:100%; height:100%;
      background:rgba(0,0,0,0.7);
      justify-content:center;
      align-items:center;
      z-index:20000;
  ">
    <div class="upload-container" style="
        background:#fff;
        padding:2rem;
        border-radius:8px;
        width:90%;
        max-width:500px;
        position:relative;
    ">
      <h2 style="margin-top:0; text-align:center;">Sube tus imágenes</h2>
      <form id="upload-form" action="/upload_image.php" method="POST" enctype="multipart/form-data">
        <!-- Zona drag & drop -->
        <div id="drag-drop-area" style="
            border:2px dashed #0077cc;
            padding:1.5rem;
            text-align:center;
            cursor:pointer;
            margin-bottom:1rem;
        ">
          Arrastra tus imágenes aquí<br>o haz clic para seleccionarlas
        </div>
        <input type="file" name="images[]" id="image-input" multiple accept="image/*" style="display:none;">

        <!-- Descripción -->
        <div class="form-group" style="margin-bottom:1rem;">
          <label for="description">Descripción</label>
          <textarea name="description" id="description" rows="3" required
            style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px;"></textarea>
        </div>

        <!-- Selección de dron -->
        <div class="form-group" style="margin-bottom:1rem;">
          <label for="drone">Selecciona el dron</label>
          <select name="drone_id" id="drone" required
            style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px;">
            <option value="">-- Elige un dron --</option>
            <?php foreach ($drones as $dr): ?>
              <option value="<?= $dr['id'] ?>"><?= htmlspecialchars($dr['modelo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Hidden: usuario y municipio -->
        <input type="hidden" name="usuario_id" value="<?= intval($_SESSION['user_id']) ?>">
        <input type="hidden" name="municipio_slug" value="<?= htmlspecialchars($municipio_slug) ?>">

        <!-- Botones -->
        <div style="text-align:right; gap:0.5rem; display:flex; justify-content:flex-end;">
          <button type="button" id="cancel-upload-btn" class="btn btn-secondary">Cancelar</button>
          <button type="submit" class="btn btn-primary">Subir</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function() {
    const openBtn    = document.getElementById('open-upload-btn');
    const overlay    = document.getElementById('upload-overlay');
    const cancelBtn  = document.getElementById('cancel-upload-btn');
    const dragArea   = document.getElementById('drag-drop-area');
    const fileInput  = document.getElementById('image-input');
    const form       = document.getElementById('upload-form');

    // Abrir overlay
    openBtn.addEventListener('click', () => {
      overlay.style.display = 'flex';
    });

    // Cerrar overlay
    cancelBtn.addEventListener('click', () => {
      overlay.style.display = 'none';
      form.reset();
    });

    // Clic en zona drag-drop abre selector
    dragArea.addEventListener('click', () => fileInput.click());

    // Drag & Drop
    ['dragenter','dragover'].forEach(evt =>
      dragArea.addEventListener(evt, e => {
        e.preventDefault();
        dragArea.classList.add('dragover');
      })
    );
    ['dragleave','drop'].forEach(evt =>
      dragArea.addEventListener(evt, e => {
        e.preventDefault();
        dragArea.classList.remove('dragover');
      })
    );
    // Soltar ficheros
    dragArea.addEventListener('drop', e => {
      fileInput.files = e.dataTransfer.files;
    });
  });
  </script>
<?php endif; ?>
