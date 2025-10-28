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


foreach ($contacts as $contact) {

  $token = $objEncrypt::secure_encrypt($contact["CELULAR"]);
  // echo $cel_encypt;
  // die();

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.botmaker.com/v2.0/notifications',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
      "campaign": null,
      "channelId": "samycosmetics-whatsapp-573176379508",
      "name": "Enviar Notificacion",
      "intentIdOrName": "notificar_pedido3",
      "contacts": [
        {
          "contactId":  "' . $contact["CELULAR"] . '",
          "variables": {
            "Nombre_contacto": "' . $contact["CONTACTO"] . '",
            "Numero_pedido": "pedido # ' . $contact["PEDIDO"] . '",
            "Fecha_Pedido": "' . $contact["RecordDate"] . ' a nombre",
            "Nombre_Cliente": "' . $contact["CLIENTE"] . '",
            "Clave": "https://app.samycosmetics.com/order-status/?token=' . $token . '"
          }
          
        }
      ]
    }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'access-token: eyJhbGciOiJIUzUxMiJ9.eyJidXNpbmVzc0lkIjoic2FteWNvc21ldGljcyIsIm5hbWUiOiJBbmRyw6lzIEZlbGlwZSBVcmliZSBTZXJyYW5vIiwiYXBpIjp0cnVlLCJpZCI6IlpYTjd3bk9USjBUYjlUdzllaWowOEVWVEowNzMiLCJleHAiOjE4OTA1Njk4MDYsImp0aSI6IlpYTjd3bk9USjBUYjlUdzllaWowOEVWVEowNzMifQ.jDaNBZhqzPytLVQ1AM3yf97mKq366k1shYuY9--Z-87qHJ54y1Wt3AvbiG_8oXYaqh8mVg5_3xZnjgddg7FSYg'
    ),
  ));

  $response = curl_exec($curl);

  // $error = curl_error($curl);
  // echo "Error en cURL: " . $error;

  if (!$response) {
    echo "Enviado a " . $contact["CELULAR"] . PHP_EOL;
    $notification->updateOrder($contact['PEDIDO']);
  }

  // echo 'Here';

  curl_close($curl);
}