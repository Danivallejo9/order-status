<?php

use model\Encrypt;

require_once('../config/global.php');
require_once('../config/database.php');
require_once('../model/Encrypt.php');

$db = new Database();

if (isset($_GET['nit'])) {
  $parametro    = $_GET['nit'];

  // $sql = "SELECT
  //           samy.CLIENTE.NOMBRE AS nombre,
  //           samy.CONTACTO_CLIENTE.NOMBRE AS nombre_contacto,
  //           samy.CONTACTO_CLIENTE.APELLIDOS AS apellido_contacto,
  //           samy.CARGOS.DESCRIPCION AS cargo,
  //           samy.CLIENTE.CONDICION_PAGO as condicion_pago,
  //           samy.CLIENTE.LIMITE_CREDITO as cupo,
  //           samy.CARGOS.CODIGO AS codigo_cargo,
  //           samy.COBRADOR.NOMBRE AS nombre_cobrador,
  //           samy.VENDEDOR.NOMBRE AS nombre_vendedor,
  //           samy.VENDEDOR.TELEFONO AS cel_vendedor,
  //           samy.VENDEDOR.U_JEFE_ZONA AS jefe_zona,
  //           samy.VENDEDOR.U_CEL_JEFEZONA AS cel_jefe,
  //           samy.COBRADOR.U_CORREO AS correo,
  //           samy.CLIENTE.CLIENTE AS documento,
  //           samy.PEDIDO.PEDIDO AS n_pedido,
  //           Despachos.DocumentosRemesas.FECHA_DOCUMENTO AS fecha_liberacion
  //         FROM
  //           samy.CLIENTE
  //         LEFT JOIN samy.PEDIDO ON
  //           samy.PEDIDO.CLIENTE = samy.CLIENTE.CLIENTE
  //         LEFT JOIN Despachos.DocumentosRemesas ON
  //           samy.PEDIDO.PEDIDO = Despachos.DocumentosRemesas.DOCUMENTOORIGEN
  //         LEFT JOIN samy.COBRADOR ON
  //           samy.COBRADOR.COBRADOR  = samy.CLIENTE.COBRADOR 
  //         LEFT JOIN samy.VENDEDOR ON
  //           samy.VENDEDOR.VENDEDOR  = samy.CLIENTE.VENDEDOR 
  //         LEFT JOIN samy.CONTACTO_CLIENTE ON
  //           samy.CONTACTO_CLIENTE.CLIENTE = samy.CLIENTE.CLIENTE
  //         LEFT JOIN samy.CARGOS ON
  //           samy.CARGOS.CODIGO = samy.CONTACTO_CLIENTE.CARGO
  //         WHERE
  //           samy.CONTACTO_CLIENTE.CELULAR = '$parametro'
  //           AND samy.PEDIDO.PEDIDO = (
  //           SELECT
  //             MAX(PEDIDO)
  //           FROM
  //             samy.PEDIDO
  //           WHERE
  //             CLIENTE = samy.CLIENTE.CLIENTE
  //             )";

    $sql = "SELECT 
              C.NOMBRE_CLIENTE AS nombre, 
              CC.nombre_c AS nombre_contacto, 
              CC.apellidos_c AS apellido_contacto, 
              CARG.DESCRIPCION AS cargo, 
              C.COND_PAGO AS condicion_pago,
              CA.LIMITE_CREDITO AS cupo,
              CARG.ID AS codigo_cargo, 
              CO.NOMBRE AS Nombre_Cobrador,
              V.NOMBRE AS nombre_vendedor,
              V.CELULAR_VENDEDOR AS cel_vendedor,
              V.NOMBRE_JEFE AS jefe_zona,
              V.CEL_JEFEZONA AS cel_jefe,
              CO.CORREO AS correo,
              C.CLIENTE AS documento,
              P.PEDIDO_SIESA AS n_pedido,
              DR.FechaDocumento AS fecha_liberacion 
            FROM UnoEE.dbo.VWS_GBICLIENTES C
            LEFT JOIN UnoEE.dbo.VWS_GBICARTERA CA ON CA.CLIENTE = C.CLIENTE 
            LEFT JOIN UnoEE.dbo.VWS_GBICOBRADOR CO ON CO.CODIGO_COBRADOR = C.COD_COBRADOR
            LEFT JOIN UnoEE.dbo.VWS_GBIVENDEDORES V ON V.VENDEDOR = C.COD_VENDEDOR 
            LEFT JOIN UnoEE.dbo.VWS_PEDIDOS P ON P.CLIENTE_SUC = C.CLIENTE
            LEFT JOIN SAMY_GBI.dbo.gbi_contactoscliente_cstm CC ON CC.documento_cliente_c = C.CLIENTE
            LEFT JOIN UnoEE.dbo.TIC_DOCUMENTOSREMESAS DR ON DR.PedidoId = P.PEDIDO_SIESA
            LEFT JOIN SAMY_GBI.dbo.cargos CARG ON CARG.ID = CC.cargo_c  
            WHERE 
              CC.celular_c = '$parametro'
              AND P.PEDIDO_SIESA = (
                SELECT MAX(P2.PEDIDO_SIESA)
                FROM UnoEE.dbo.VWS_PEDIDOS P2
                WHERE P2.CLIENTE_SUC = C.CLIENTE
              )";

  $stm = $db->pdo_erp()->query($sql);
  $response = [];

  $secret_key = "cda4a3b9-b921-4824-87a0-689c3a9609e9";

  $dataDB = $stm->fetch(PDO::FETCH_ASSOC);

  $objEncrypt = new Encrypt();
  $getParams = $objEncrypt::encrypt($_GET['nit']);
  // Método de encriptación AES
  // $method = "AES-256-CBC";
  // $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
  // $params = openssl_encrypt($stmt['documento'], $method, $secret_key, 0, $iv);
  // $ivBase64 = base64_encode($iv);
  // $getParams = http_build_query(array('token' => $params, 'iv' => $ivBase64));
  $response = array_merge($dataDB, ["encrypt" => $getParams]);

  // echo json_encode($stm->fetch(PDO::FETCH_ASSOC));
  echo json_encode($response);
} else {
  $valor = "El parámetro no funciona en la URL";

  $file = fopen('log.txt', 'a+');
  fwrite($file, date('Y-m-d, H:i:s') . "No llega valor : " .  $valor . "\n");
  fclose($file);
}
