<?php

// echo phpinfo();

use model\Encrypt;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "notification.model.php";
require_once('../model/Encrypt.php');

$notification = new notificationModel();
$contacts = $notification->getContacts();

$objEncrypt = new Encrypt();


function logMessage($message, $file = 'notificaciones.log')
{
  $logDir = __DIR__ . '/logs';
  $logPath = $logDir . '/' . $file;

  if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
  }

  $timestamp = date('[Y-m-d H:i:s]');
  error_log("$timestamp $message" . PHP_EOL, 3, $logPath);
}

foreach ($contacts as $contact) {

  if (empty($contact['CELULAR'])) {
    logMessage("Saltado (sin celular) pedido {$contact['PEDIDO']} - Cliente: {$contact['CLIENTE']}");
    continue;
  }

  $token = $objEncrypt::secure_encrypt($contact["CELULAR"]);

  $payload = [
    "campaign" => null,
    "channelId" => "samycosmetics-whatsapp-573176379508",
    "name" => "Enviar Notificacion",
    "intentIdOrName" => "notificar_pedido3",
    "contacts" => [[
      "contactId" => $contact["CELULAR"],
      "variables" => [
        "Nombre_contacto" => $contact["CONTACTO"],
        "Numero_pedido" => "pedido # " . $contact["PEDIDO"],
        "Fecha_Pedido" => $contact["RecordDate"] . " a nombre",
        "Nombre_Cliente" => $contact["CLIENTE"],
        "Clave" => "https://app.samycosmetics.com/order-status/?token=" . $token
      ]
    ]]
  ];

  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.botmaker.com/v2.0/notifications',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
      'Content-Type: application/json',
      'access-token: eyJhbGciOiJIUzUxMiJ9.eyJidXNpbmVzc0lkIjoic2FteWNvc21ldGljcyIsIm5hbWUiOiJBbmRyw6lzIEZlbGlwZSBVcmliZSBTZXJyYW5vIiwiYXBpIjp0cnVlLCJpZCI6IlpYTjd3bk9USjBUYjlUdzllaWowOEVWVEowNzMiLCJleHAiOjE4OTA1Njk4MDYsImp0aSI6IlpYTjd3bk9USjBUYjlUdzllaWowOEVWVEowNzMifQ.jDaNBZhqzPytLVQ1AM3yf97mKq366k1shYuY9--Z-87qHJ54y1Wt3AvbiG_8oXYaqh8mVg5_3xZnjgddg7FSYg'
    ],
    CURLOPT_TIMEOUT => 0
  ]);

  $response = curl_exec($curl);
  $curlErrNo = curl_errno($curl);
  $curlErrMsg = curl_error($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  curl_close($curl);

  if ($curlErrNo) {
    logMessage("Error cURL pedido {$contact['PEDIDO']} ({$contact['CELULAR']}): $curlErrMsg");
    continue;
  }

  $decoded = json_decode($response, true);
  $isSuccess = false;

  if (is_array($decoded) && isset($decoded['success'])) {
    $isSuccess = (bool) $decoded['success'];
  } else {
    $isSuccess = ($httpCode >= 200 && $httpCode < 300);
  }

  if ($isSuccess) {
    $notification->updateOrder($contact['PEDIDO']);
    echo "Enviado y confirmado a {$contact['CELULAR']} (pedido {$contact['PEDIDO']})" . PHP_EOL;
  } else {
    logMessage("Falló envío pedido {$contact['PEDIDO']} - HTTP {$httpCode} - Respuesta: " . substr($response, 0, 300));
  }
}
