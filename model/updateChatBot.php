<?php
// model/updateChatBot.php

require_once __DIR__ . '/../config/global.php';
require_once __DIR__ . '/../model/Log.php';

class UpdateChatBot
{
    private $baseUrl;
    private $idSistema;
    private $idDocumento;
    private $nombreDocumento;
    private $conniKey;
    private $conniToken;
    private $log;

    public function __construct()
    {
        $this->log = new Log();

        $this->baseUrl = defined('SIESA_BASE_URL') ? SIESA_BASE_URL : 'https://pedidos.samycosmetics.com:82';
        $this->idSistema = defined('SIESA_ID_SISTEMA') ? SIESA_ID_SISTEMA : '2';
        $this->idDocumento = defined('SIESA_ID_DOCUMENTO') ? SIESA_ID_DOCUMENTO : '224082';
        $this->nombreDocumento = defined('SIESA_NOMBRE_DOCUMENTO') ? SIESA_NOMBRE_DOCUMENTO : 'ENTIDAD_CHATBOT';
        $this->conniKey = defined('CONNI_KEY') ? CONNI_KEY : 'f7f33c2b13e8107bcb6220a8a0fa5eef';
        $this->conniToken = defined('CONNI_TOKEN') ? CONNI_TOKEN : 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6ImYwMzVmZjMyLTAxYTUtNGMwMi1iOWJhLWM0OWFhOWYzMzdhOSIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcHJpbWFyeXNpZCI6ImJhODM2MmRjLWFiOTctNDRlNy05ODc0LTA3ZDYyNDRjMGQzNyJ9.sJRVYdDBaeWowJ7pUy0zbeOkNv2RhkES2HO92yjZfms';
    }

    public function updateOrder($tipoDocto, $consecDocto)
    {
        if (empty($tipoDocto) || empty($consecDocto)) {
            return array('success' => false, 'error' => 'Faltan TIPO_DOCT o CONSEC_DOC');
        }

        $url = rtrim($this->baseUrl, '/') . '/v3.1/conectoresimportar' .
               '?idCompania=8490&idSistema=' . urlencode($this->idSistema) .
               '&idDocumento=' . urlencode($this->idDocumento) .
               '&nombreDocumento=' . urlencode($this->nombreDocumento);

        $payload = array(
            "Entidades dinámicas" => array(
                array(
                    "Tipo de documento" => (string)$tipoDocto,
                    "Consecutivo de documento" => (string)$consecDocto,
                    "Información númerica para la entidad" => 1
                )
            )
        );

        $jsonBody = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );
        if ($this->conniKey !== '')   $headers[] = 'ConniKey: ' . $this->conniKey;
        if ($this->conniToken !== '') $headers[] = 'ConniToken: ' . $this->conniToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Escribir debug en logs/debug_siesa.log (asegurando carpeta)
        $debugText = date('c') . " - TIPO: $tipoDocto, CONSEC: $consecDocto\nREQUEST: " . print_r($payload, true) . "\nRESPONSE: " . print_r($response, true) . "\n\n";
        // usa el helper de Log para escribir en un archivo debug
        $this->log->writeDebug('debug_siesa.log', $debugText);

        if ($curlErr) {
            // pasar parámetros para evitar warnings y tener contexto
            $this->log->registrar_error("UpdateChatBot CURL error: {$curlErr}", [
                'tipoDocto' => $tipoDocto,
                'consecDocto' => $consecDocto,
                'curl_error' => $curlErr
            ]);
            return array('success' => false, 'error' => $curlErr);
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return array('success' => true, 'data' => isset($decoded) ? $decoded : $response);
        }

        $msg = isset($decoded['message']) ? $decoded['message'] : ($response ? $response : "HTTP {$httpCode}");
        $this->log->registrar_error("UpdateChatBot HTTP {$httpCode} - {$msg}", [
            'tipoDocto' => $tipoDocto,
            'consecDocto' => $consecDocto,
            'http_code' => $httpCode,
            'response' => $decoded ? $decoded : $response
        ]);
        return array('success' => false, 'error' => $msg, 'http_code' => $httpCode);
    }
}
