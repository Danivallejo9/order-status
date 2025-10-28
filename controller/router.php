<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once('../model/samy.php');
require_once('../model/Encrypt.php');

use model\Encrypt;

$objJSON  = file_get_contents('php://input');
$data = json_decode($objJSON, true);
$objEncrypt = new Encrypt();

switch ($data['case']) {
  case 'getStatusOrder':
    $objSamy = new Samy($data['id']);
    $response = $objSamy->getStatusOrder();
    // $response = $objSamy->getStatusOrder();
    echo $response;
    break;

  case 'quantityorders':
    if ($data['clientSelected']) {
      $idClient = $objEncrypt::decrypParams($data);
      $objSamy = new Samy($idClient);
    } else {
      $objSamy = new Samy($data['id']);
    }

    getOrders($objSamy);
    break;

  case 'getOrderDetails':
    $objSamy = new Samy($data['order']);
    $response = $objSamy->getOrderDetails();
    echo $response;
    break;

  case 'infoWallet':
    $objSamy = new Samy($data['id']);

    echo json_encode($objSamy->testInfoWallet());
    // $clientUpdate = $objSamy->actualizarDeudaCupoCliente();
    // $response = $objSamy->infoWallet();
    // echo $response;
    break;

  case 'dataWallet':
    $objSamy = new Samy($data['id']);
    $response = $objSamy->dataWallet();
    echo $response;
    break;

  case 'getClients':
    $objSamy = new Samy(decrypParams($data));
    $quantityClient = $objSamy->getClients();
    if (count($quantityClient) > 1) {
      $clients = [];
      foreach ($quantityClient as $value) {
        $clients[] = ["encryptData" => $objEncrypt::encrypt($value['CLIENTE']), "alias" => $value['ALIAS']];
      }

      echo json_encode(["hasManyClients" => true, "clients" => $clients]);
    } else {
      $objSamy->setData($quantityClient[0]['CLIENTE']);
      getOrders($objSamy);
    }
}

function decrypParams($data)
{
  $secret_key = "cda4a3b9-b921-4824-87a0-689c3a9609e9";
  $method = "AES-256-CBC";
  $iv = base64_decode($data['iv']);
  return openssl_decrypt($data['token'], $method, $secret_key, 0, $iv);
}

function getOrders($obj)
{
  $data = $obj->quantityorders();
  $wallet = $obj->testInfoWallet();

  if (!count($data)) {
    $response['status'] = 404;
    $response['message'] = "No tenemos pedidos de los últimos dos meses para mostrar";
    $response['id'] = $obj->getData();
    echo json_encode($response);
    return false;
  }
  // $response['hasManyClients'] = false;
  echo json_encode(['orders' => $data, "wallet" => $wallet]);
}
