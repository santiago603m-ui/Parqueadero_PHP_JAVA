<?php
date_default_timezone_set('America/Bogota');

define('API_BASE_URL', 'http://127.0.0.1:8080/api');

function apiRequest(string $endpoint, string $metodo = 'GET', array $datos = []): array {
    $url = API_BASE_URL . $endpoint;
    $ch  = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => $metodo,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
    CURLOPT_TIMEOUT        => 15,         
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4, 
]);

    if (!empty($datos)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
    }

    $respuesta  = curl_exec($ch);
    $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError  = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['error' => 'No se pudo conectar con el servidor Java: ' . $curlError, 'code' => 0];
    }

    $json = json_decode($respuesta, true);
    return $json ?? ['error' => 'Respuesta inválida del servidor', 'code' => $httpCode];
}
?>
