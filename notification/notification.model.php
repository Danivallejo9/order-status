<?php

require_once __DIR__ . '/../config/global.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Log.php';
require_once __DIR__ . '/../model/updateChatBot.php';

class notificationModel
{

  protected $connection;
  private $log;

  public function __construct()
  {
    $this->log = new Log();
    $samyDB = new Database();
    $this->connection = $samyDB->pdo_erp();
  }


  public function getContacts()
  {
    $sql = "SELECT 
              CONTACTO, 
              CELULAR, 
              PEDIDO, 
              RecordDate, 
              CLIENTE, 
              NIT
            FROM SAMY_TI.dbo.V_TIC_GBIPEDIDOCHATBOT
            WHERE CAST(RecordDate AS DATE) = CAST(GETDATE() AS DATE)
              AND (U_ENVIADOWAT IS NULL OR U_ENVIADOWAT <> 1)"; //WHERE PEDIDO = '1517340558568'"; 


    $stm = $this->connection->query($sql);
    $response = [];

    while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
      $response[] = $row;
    }
    return $response;
  }

  // public function updateOrder($order)
  // {
  //   $query = "UPDATE COSMETICOS_SAMY.samy.PEDIDO
  //   SET U_ENVIADOWAT = 1
  //   WHERE PEDIDO = :id
  //   ";

  //   $stmt = $this->connection->prepare($query);
  //   $stmt->bindParam(':id', $order, PDO::PARAM_STR);

  //   try {
  //     $stmt->execute();
  //   } catch (PDOException $e) {
  //     $this->log->registrar_error('Error actualizando pedido: ', $e->getMessage());
  //   }
  // }

  public function updateOrder($pedido)
  {
      if (empty($pedido)) {
          $this->log->registrar_error("updateOrder: pedido vacío");
          return false;
      }

      try {
          $sql = "SELECT TIPO_DOC, CONSEC_DOC
                  FROM SAMY_TI.dbo.V_TIC_GBIPEDIDOCHATBOT
                  WHERE PEDIDO = :pedido";
          $stmt = $this->connection->prepare($sql);
          $stmt->bindParam(':pedido', $pedido);
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          if (!$row) {
              $this->log->registrar_error("updateOrder: no se encontró registro para PEDIDO {$pedido}");
              return false;
          }

          $tipo = isset($row['TIPO_DOC']) ? $row['TIPO_DOC'] : (isset($row['TIPO_DOC']) ? $row['TIPO_DOC'] : null);
          $consec = isset($row['CONSEC_DOC']) ? $row['CONSEC_DOC'] : null;

          if (empty($tipo) || empty($consec)) {
              $this->log->registrar_error("updateOrder: faltan TIPO_DOC o CONSEC_DOC para PEDIDO {$pedido}");
              return false;
          }

          $updater = new UpdateChatBot();
          $res = $updater->updateOrder($tipo, $consec);

          if (empty($res['success']) || $res['success'] !== true) {
              $this->log->registrar_error("updateOrder: fallo al actualizar en SIESA para PEDIDO {$pedido}: " . (isset($res['error']) ? $res['error'] : 'unknown'));
              return false;
          }

          $this->log->registrar_info("updateOrder: PEDIDO {$pedido} actualizado correctamente en SIESA.");
          return true;

      } catch (PDOException $e) {
          $this->log->registrar_error("updateOrder PDOException: " . $e->getMessage());
          return false;
      } catch (Exception $e) {
          $this->log->registrar_error("updateOrder Exception: " . $e->getMessage());
          return false;
      }
  }

}
