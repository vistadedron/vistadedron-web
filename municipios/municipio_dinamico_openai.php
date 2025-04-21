<?php
// municipio_dinamico_openai.php
// Última modificación: 2025-04-17 23:10

include_once("../functions.php");
$conn = conectarBD();
$municipio = mysqli_real_escape_string($conn, $_GET['municipio'] ?? 'Cardedeu');

// Verificar si ya está cacheado
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
    function obtenerInfoChatGPT($municipio) {
        $apikey = 'TU_API_KEY_AQUI';
        $url = 'https://api.openai.com/v1/chat/completions';

        $prompt = "Dame una breve descripción, la altitud, la población aproximada, las coordenadas GPS aproximadas y 5 puntos turísticos del municipio de $municipio en España. Devuelve todo en formato JSON con estas claves: descripcion, altitud, poblacion, coordenadas (lat, lon), puntos_interes (array de strings).";

        $data = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ]
        ];

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apikey
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        $content = $result['choices'][0]['message']['content'] ?? '{}';

        return json_decode($content, true);
    }

    $info = obtenerInfoChatGPT($municipio);

    $descripcion = $info['descripcion'] ?? 'Descripción no disponible';
    $altitud = is_numeric($info['altitud']) ? $info['altitud'] : 0;
    $poblacion = is_numeric($info['poblacion']) ? $info['poblacion'] : 0;
    $lat = isset($info['coordenadas']['lat']) ? $info['coordenadas']['lat'] : 0;
    $lon = isset($info['coordenadas']['lon']) ? $info['coordenadas']['lon'] : 0;
    $puntos = $info['puntos_interes'] ?? [];

    $json_puntos = json_encode($puntos, JSON_UNESCAPED_UNICODE);

    // Guardar en la BBDD
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
