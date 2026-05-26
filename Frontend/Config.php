<?php
define('API_BASE_URL', 'http://localhost:8080/parqueadero-api/api');

function apiRequest(string $endpoint, string $metodo = 'GET', array $datos = []): array {
    $url = API_BASE_URL . $endpoint;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $metodo,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 5,
    ]);
    if (!empty($datos)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
    }
    $respuesta = curl_exec($ch);
    curl_close($ch);
    return json_decode($respuesta, true) ?: [];
}
?>