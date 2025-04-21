<?php
// municipio_dinamico_realtime.php
// Última modificación: 2025-04-17 22:50

include_once("../functions.php");
$conn = conectarBD();
$municipio = mysqli_real_escape_string($conn, $_GET['municipio'] ?? 'Cardedeu');

// Verificamos si ya está en la base de datos
$sql_check = "SELECT * FROM municipios WHERE nombre = '$municipio'";
$res = $conn->query($sql_check);

if ($res && $res->num_rows > 0) {
    $data = $res->fetch_assoc();
    $descripcion = $data['descripcion'];
    $lat = $data['latitud'];
    $lon = $data['longitud'];
    $altitud = $data['altitud'];
    $poblacion = $data['habitantes'];
    $puntos = json_decode($data['puntos_interes'], true) ?? [];
} else {
    // === FUNCIONES API ===
    function curlGet($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "VistaDeDronBot/1.0");
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    function obtenerDescripcionWikipedia($municipio) {
        $url = "https://es.wikipedia.org/api/rest_v1/page/summary/" . urlencode($municipio);
        $data = json_decode(curlGet($url), true);
        return $data['extract'] ?? null;
    }

    function obtenerDatosOSM($municipio) {
        $q = urlencode($municipio . ", España");
        $url = "https://nominatim.openstreetmap.org/search?q=$q&format=json&limit=1";
        $data = json_decode(curlGet($url), true);
        if (!empty($data[0])) {
            return [
                "lat" => (float)$data[0]['lat'],
                "lon" => (float)$data[0]['lon'],
                "altitud" => 0  // opcional: se puede calcular con otro servicio si se desea
            ];
        }
        return ["lat" => 0, "lon" => 0, "altitud" => 0];
    }

    function obtenerPOIs($lat, $lon) {
        $query = '[out:json][timeout:25];
        (
          node["tourism"](around:1500,' . $lat . ',' . $lon . ');
          node["historic"](around:1500,' . $lat . ',' . $lon . ');
          node["amenity"="place_of_worship"](around:1500,' . $lat . ',' . $lon . ');
        );
        out body; >; out skel qt;';
        $url = "https://overpass-api.de/api/interpreter";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "data=" . urlencode($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "VistaDeDronBot/1.0");
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);
        $lugares = [];

        if (!empty($data['elements'])) {
            foreach ($data['elements'] as $el) {
                if (!empty($el['tags']['name'])) {
                    $lugares[] = $el['tags']['name'];
                }
            }
        }

        return array_unique($lugares);
    }

    function obtenerPoblacionSimulada($municipio) {
        return rand(500, 100000); // Simulado, puede conectarse al INE
    }

    // === RECOGER DATOS ===
    $descripcion = obtenerDescripcionWikipedia($municipio);
    $geo = obtenerDatosOSM($municipio);
    $lat = isset($geo['lat']) && is_numeric($geo['lat']) ? $geo['lat'] : 0;
    $lon = isset($geo['lon']) && is_numeric($geo['lon']) ? $geo['lon'] : 0;
    $altitud = isset($geo['altitud']) && is_numeric($geo['altitud']) ? $geo['altitud'] : 0;
    $poblacion = obtenerPoblacionSimulada($municipio);
    if (!is_numeric($poblacion)) { $poblacion = 0; }
    $puntos = obtenerPOIs($lat, $lon);

    $json_puntos = json_encode($puntos, JSON_UNESCAPED_UNICODE);

    // Insertar a la base de datos
    $stmt = $conn->prepare("INSERT INTO municipios (nombre, descripcion, latitud, longitud, altitud, habitantes, puntos_interes)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdddis", $municipio, $descripcion, $lat, $lon, $altitud, $poblacion, $json_puntos);
    $stmt->execute();
    $stmt->close();
}
?>

<?php include_once("../includes/header.php"); ?>

<div class="layout">
    <main class="content">
        <div class="form-card">
            <h1 class="card-title">📍 <?= htmlspecialchars($municipio) ?></h1>

            <?php if ($descripcion): ?>
                <p><strong>Descripción:</strong> <?= htmlspecialchars($descripcion) ?></p>
            <?php endif; ?>

            <p><strong>Habitantes:</strong> <?= number_format($poblacion ?? 0, 0, ',', '.') ?></p>
            <p><strong>Altitud:</strong> <?= $altitud ?> m</p>
            <p><strong>Coordenadas:</strong> <?= $lat ?>, <?= $lon ?></p>

            <h3 style="margin-top: 1.5rem;">🎯 Puntos de interés</h3>
            <ul>
                <?php foreach ($puntos as $lugar): ?>
                    <li><?= htmlspecialchars($lugar) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
</div>

<?php include_once("../includes/footer.php"); ?>
