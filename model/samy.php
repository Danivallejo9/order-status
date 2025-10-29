	<?php

	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(E_ALL);

	require_once('../config/global.php');
	require_once('../config/database.php');
	class Samy
	{

		private $data;
		protected $samyDB;
		protected $gbiDB;
		protected $key;

		function __construct($data)
		{
			$objDB = new Database();
			$this->samyDB = $objDB->pdo_erp();
			$this->gbiDB = $objDB->pdo_gbi();
			$this->key = "m3m0c0d3";
			$this->data = $data;
		}

		public function setData($data)
		{
			$this->data = $data;
		}

		public function getData()
		{
			return $this->data;
		}

		// function getStatusOrder() //Pedido OK
		// {

		// 	$sql = "SELECT 
		// 						STRING_AGG(
		// 								UPPER(LEFT(Word, 1)) + LOWER(SUBSTRING(Word, 2, LEN(Word))),
		// 								' '
		// 						) AS name,
		// 						samy.PEDIDO.PEDIDO AS n_order,
		// 						CONCAT(
		// 								UPPER(LEFT(samy.PEDIDO.U_ESTADO, 1)),
		// 								LOWER(SUBSTRING(samy.PEDIDO.U_ESTADO, 2, LEN(samy.PEDIDO.U_ESTADO)))
		// 						) AS status,    
		// 						CONVERT(date, samy.PEDIDO.FECHA_PEDIDO) AS release_date,
		// 						CONVERT(date, samy.PEDIDO.FECHA_ORDEN) AS release_order,
		// 						CONVERT(date,samy.PEDIDO.FECHA_PROX_EMBARQU) AS deliver_start,
		// 						CONVERT (date,Despachos.DocumentosRemesas.REMESAFECHAENTREGA) AS deliver_end,
		// 						Despachos.DocumentosRemesas.REMESA  AS remittance,
		// 						Despachos.DocumentosRemesas.ESTADO AS status_remittance
		// 				FROM samy.CLIENTE
		// 				LEFT JOIN samy.PEDIDO ON samy.PEDIDO.CLIENTE = samy.CLIENTE.CLIENTE 
		// 				LEFT JOIN Despachos.DocumentosRemesas ON samy.PEDIDO.PEDIDO = Despachos.DocumentosRemesas.DOCUMENTOORIGEN
		// 				CROSS APPLY (
		// 						SELECT value AS Word
		// 						FROM STRING_SPLIT(samy.CLIENTE.NOMBRE, ' ')
		// 				) partes
		// 				WHERE samy.PEDIDO.PEDIDO = '{$this->data}'
		// 				GROUP BY 
		// 					samy.PEDIDO.PEDIDO, 
		// 					samy.PEDIDO.U_ESTADO, 
		// 					samy.PEDIDO.FECHA_PEDIDO, 
		// 					Despachos.DocumentosRemesas.REMESA, 
		// 					Despachos.DocumentosRemesas.ESTADO, 
		// 					samy.PEDIDO.FECHA_ORDEN, 
		// 					samy.PEDIDO.FECHA_PROX_EMBARQU, 
		// 					Despachos.DocumentosRemesas.REMESAFECHAENTREGA";

		// 	$stm = $this->samyDB->query($sql);
		// 	$response = [];

		// 	while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
		// 		$response[] = $row;
		// 	}
		// 	return json_encode($response);
		// }

		function getStatusOrder() //Pedido OK
		{

			$sql = "SELECT 
						STRING_AGG(
							CAST(UPPER(LEFT(Word, 1)) + LOWER(SUBSTRING(Word, 2, LEN(Word))) AS NVARCHAR(MAX)),
							' '
						) AS name,
						P.PEDIDO_SIESA AS n_order,
						'' AS status,
						CONVERT(date, P.FECHA_PEDIDO) AS release_date,
						CONVERT(date, P.FECHA_CREACION) AS release_order, --CONVERT(date, samy.PEDIDO.FECHA_ORDEN) AS release_order,
						CONVERT(date, P.FECHA_PROX_EMBARQU) AS deliver_start,
						CONVERT(date, DR.RemesaFechaEntrega) AS deliver_end,
						DR.Remesa AS remittance, 
						DR.Estado AS status_remittance 
					FROM UnoEE.dbo.VWS_GBICLIENTES C
					LEFT JOIN UnoEE.dbo.VWS_PEDIDOS P ON P.CLIENTE_SUC = C.CLIENTE
					LEFT JOIN UnoEE.dbo.TIC_DOCUMENTOSREMESAS DR ON DR.PedidoId = P.PEDIDO_SIESA 
					CROSS APPLY (
						SELECT value AS Word
						FROM STRING_SPLIT(C.NOMBRE_CLIENTE, ' ')
					) partes
						WHERE 
							P.PEDIDO_SIESA = '{$this->data}'
						GROUP BY
						P.PEDIDO_SIESA,
						P.FECHA_PEDIDO,
						P.FECHA_CREACION,
						DR.RemesaFechaEntrega,
						DR.Remesa,
						DR.Estado";

			$stm = $this->samyDB->query($sql);
			$response = [];

			while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
				$response[] = $row;
			}
			return json_encode($response);
		}

		// function getOrderDetails() //Pedido OK
		// {
		// 	$sql = "SELECT  
		// 							samy.PEDIDO.NOMBRE_CLIENTE AS NAME, 
		// 							samy.PEDIDO_LINEA.PEDIDO_LINEA AS LINE_ITEM,
		// 							samy.PEDIDO_LINEA.ARTICULO AS ITEM,
		// 							CONCAT(
		// 									UPPER(LEFT(CAST(samy.ARTICULO.DESCRIPCION AS VARCHAR(MAX)), 1)),
		// 									LOWER(SUBSTRING(CAST(samy.ARTICULO.DESCRIPCION AS VARCHAR(MAX)), 2, LEN(CAST(samy.ARTICULO.DESCRIPCION AS VARCHAR(MAX)))))
		// 							) AS DESCRIPTION,
		// 							samy.ARTICULO.CODIGO_BARRAS_VENT AS BAR_CODE, 
		// 							samy.PEDIDO_LINEA.CANTIDAD_PEDIDA AS AMOUNT
		// 					FROM samy.PEDIDO_LINEA
		// 					INNER JOIN samy.PEDIDO  ON samy.PEDIDO.PEDIDO = samy.PEDIDO_LINEA.PEDIDO
		// 					INNER JOIN samy.ARTICULO ON samy.ARTICULO.ARTICULO = samy.PEDIDO_LINEA.ARTICULO  
		// 					WHERE PEDIDO_LINEA.PEDIDO = '{$this->data}'
		// 					ORDER BY LINE_ITEM ASC";

		// 	$stm = $this->samyDB->query($sql);
		// 	$response = [];

		// 	while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
		// 		$response[] = $row;
		// 	}
		// 	return json_encode($response);
		// }

		function getOrderDetails() //Pedido OK
		{
			$sql = "SELECT 
						P.CLIENTE AS NAME,
						PD.ID_LINEA AS LINE_ITEM,
						PD.ARTICULO AS ITEM,
						CONCAT(
							UPPER(LEFT(CAST(A.DESCRIPCION AS VARCHAR(MAX)), 1)),
							LOWER(SUBSTRING(CAST(A.DESCRIPCION AS VARCHAR(MAX)), 2, LEN(CAST(A.DESCRIPCION AS VARCHAR(MAX)))))
						) AS DESCRIPTION,
						A.COD_BARRAS AS BAR_CODE,
						PD.CANTIDAD_PEDIDA AS AMOUNT
					FROM UnoEE.dbo.VWS_PEDIDOSDETALLES PD
					INNER JOIN UnoEE.dbo.VWS_PEDIDOS P ON P.PEDIDO_SIESA = PD.PEDIDO
					INNER JOIN UnoEE.dbo.VWS_GBIARTICULOS A ON PD.ARTICULO = A.ARTICULO
					WHERE 
						PD.PEDIDO = '{$this->data}'
					ORDER BY LINE_ITEM ASC";

			$stm = $this->samyDB->query($sql);
			$response = [];

			while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
				$response[] = $row;
			}
			return json_encode($response);
		}

		// function  dataWallet() //Cartera OK
		// {
		// 	$sql = "SELECT                        
		// 						C.TIPO AS Fac_rec,
		// 						C.DOCUMENTO AS Consecutivo,
		// 						C.TIPO_CREDITO AS tipocred,
		// 						CONVERT (VARCHAR (16), C.FECHA, 111) AS FEmision,
		// 						CONVERT (
		// 								VARCHAR (16),
		// 								C.FECHA_VENCE,
		// 								111
		// 						) AS FVence,
		// 						C.CONDICION_PAGO AS cond_pago,
		// 						C.DIAS_VENCIDO AS DAtraso,
		// 						C.SALDO AS saldo,
		// 						C.MONTO AS Valor,
		// 						C.LIMITE_CREDITO AS limite,
		// 						C.VALOR_PAGO AS valorpago,
		// 						CONVERT (VARCHAR (16), C.FECHAPAGO, 111) AS Fpago,
		// 						C.CREDITO AS credito,
		// 						C.CONDICION_PAGO AS Plazo
		// 						FROM samy.GBI_CARTERA AS C
		// 				WHERE
		// 						C.CLIENTE = '{$this->data}'";

		// 	$stm = $this->samyDB->query($sql);
		// 	$response = [];

		// 	while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
		// 		$response[] = $row;
		// 	}
		// 	return json_encode($response);
		// }

		function  dataWallet() //Cartera OK
		{
			$sql = "SELECT 
						TIPO_DOC  AS Fac_rec,
						DOCUMENTO AS Consecutivo,
						'' AS tipocred, --PREGUNTAR
						CONVERT (VARCHAR (16), FECHA, 111) AS FEmision,
						CONVERT (VARCHAR (16), FECHA_VENCE, 111) AS FVence,
						COND_PAGO AS cond_pago,
						DIAS_VENCIDO AS DAtraso,
						SALDO AS saldo,
						'' AS Valor,
						LIMITE_CREDITO AS limite,
						VLR_DOCTO AS valorpago,
						CONVERT (VARCHAR (16), FECHA_VENCE, 111) AS Fpago,
						'' AS credito,
						COND_PAGO AS Plazo --ESTABA IGUAL
					FROM UnoEE.dbo.VWS_GBICARTERA
						WHERE
							CLIENTE = '{$this->data}'";

			$stm = $this->samyDB->query($sql);
			$response = [];

			while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
				$response[] = $row;
			}
			return json_encode($response);
		}

		// function  quantityorders() //Pedido OK
		// {
		// 	// $objdecrypt = new Samy($this->data);
		// 	// $data = $objdecrypt->decrypt();

		// 	// $sql = "SELECT 
		// 	// 								STRING_AGG(
		// 	// 										UPPER(LEFT(partes.Word, 1)) + LOWER(SUBSTRING(partes.Word, 2, LEN(partes.Word))),
		// 	// 										' '
		// 	// 								) AS name,  
		// 	// 								samy.PEDIDO.PEDIDO AS n_order,
		// 	// 								CONCAT(
		// 	// 						UPPER(FORMAT(samy.PEDIDO.FECHA_PEDIDO, 'MMMM', 'es-ES')), 
		// 	// 						' ', 
		// 	// 						DAY(samy.PEDIDO.FECHA_PEDIDO)) AS date_release,
		// 	// 								samy.PEDIDO.U_ESTADO AS status,
		// 	// 								Despachos.DocumentosRemesas.REMESA  AS remittance,
		// 	// 								CONVERT (date,Despachos.DocumentosRemesas.REMESAFECHAENTREGA) AS deliver
		// 	// 						FROM samy.CLIENTE
		// 	// 						LEFT JOIN samy.PEDIDO ON samy.PEDIDO.CLIENTE = samy.CLIENTE.CLIENTE 
		// 	// 						LEFT JOIN Despachos.DocumentosRemesas ON samy.PEDIDO.PEDIDO = Despachos.DocumentosRemesas.DOCUMENTOORIGEN
		// 	// 						CROSS APPLY (
		// 	// 								SELECT value AS Word
		// 	// 								FROM STRING_SPLIT(samy.CLIENTE.NOMBRE, ' ')
		// 	// 						) partes
		// 	// 						WHERE samy.CLIENTE.CLIENTE = '{$this->data}'
		// 	// 						AND samy.PEDIDO.PEDIDO IN (
		// 	// 								SELECT PEDIDO
		// 	// 								FROM samy.PEDIDO
		// 	// 								WHERE CLIENTE = samy.CLIENTE.CLIENTE
		// 	// 								AND FECHA_PEDIDO >= DATEADD(MONTH, -2, GETDATE()) 
		// 	// 						)
		// 	// 						GROUP BY samy.PEDIDO.PEDIDO,samy.PEDIDO.FECHA_PEDIDO,samy.PEDIDO.U_ESTADO, Despachos.DocumentosRemesas.REMESA,Despachos.DocumentosRemesas.REMESAFECHAENTREGA
		// 	// 						ORDER BY samy.PEDIDO.FECHA_PEDIDO ASC";
		// 	$sql = "SELECT 
		// 						c.NOMBRE as name,
		// 						p.PEDIDO as n_order,
		// 						CONCAT(
		// 							UPPER(FORMAT(p.FECHA_PEDIDO, 'MMMM', 'es-ES')),
		// 							' ', 
		// 							DAY(p.FECHA_PEDIDO)
		// 						) AS date_release,
		// 						p.U_ESTADO as status,
		// 						dr.REMESA as remittance,
		// 						CONVERT (date, dr.REMESAFECHAENTREGA) AS deliver
		// 					FROM
		// 						samy.CLIENTE c
		// 					INNER JOIN samy.PEDIDO p 
		// 						ON
		// 						c.CLIENTE = p.CLIENTE
		// 						AND c.CLIENTE = :client
		// 						AND p.FECHA_PEDIDO >= DATEADD(MONTH, -2, GETDATE())
		// 					LEFT JOIN Despachos.DocumentosRemesas dr 
		// 						ON
		// 						p.PEDIDO = dr.DOCUMENTOORIGEN
		// 					ORDER BY
		// 						p.FECHA_PEDIDO ASC";

		// 	$stmt = $this->samyDB->prepare($sql);
		// 	$stmt->bindValue(':client', $this->data, PDO::PARAM_STR);
		// 	$stmt->execute();
		// 	return $stmt->fetchAll(PDO::FETCH_ASSOC);
		// 	// $stmt = $this->samyDB->query($sql);
		// 	// $response = [];

		// 	// while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
		// 	// 	$response[] = $row;
		// 	// }

		// 	// return $response;
		// 	// return json_encode($response);
		// }

		function  quantityorders() //Pedido OK
		{
			$sql = "SELECT 
						C.NOMBRE_CLIENTE as name,
						P.PEDIDO_SIESA as n_order,
						CONCAT(UPPER(FORMAT(P.FECHA_PEDIDO, 'MMMM', 'es-ES')),' ', DAY(p.FECHA_PEDIDO)) AS date_release,
						'' AS status,
						DR.Remesa AS remittance, --dr.REMESA as remittance,
						CONVERT (date, DR.RemesaFechaEntrega) AS deliver --CONVERT (date, dr.REMESAFECHAENTREGA) AS deliver
					FROM UnoEE.dbo.VWS_GBICLIENTES C
					INNER JOIN UnoEE.dbo.VWS_PEDIDOS P ON P.CLIENTE_SUC = C.CLIENTE
					AND C.CLIENTE = :client
					AND P.FECHA_PEDIDO >= DATEADD(MONTH, -2, GETDATE())
					LEFT JOIN UnoEE.dbo.TIC_DOCUMENTOSREMESAS DR ON DR.PedidoId = P.PEDIDO_SIESA
					ORDER BY
						P.FECHA_PEDIDO ASC";

			$stmt = $this->samyDB->prepare($sql);
			$stmt->bindValue(':client', $this->data, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		// function getClients() //Contacto_cliente OK
		// {
		// 	$sql = "SELECT 		
		// 						DISTINCT (cc.CLIENTE),
		// 						C.ALIAS 
		// 					FROM samy.CONTACTO_CLIENTE cc 
		// 					INNER JOIN samy.CLIENTE C
		// 						ON C.CLIENTE = cc.CLIENTE 
		// 					WHERE cc.CELULAR = :celular							
		// 					GROUP BY cc.CLIENTE,
		// 					cc.CELULAR,
		// 					C.ALIAS 
		// 	";

		// 	$stmt = $this->samyDB->prepare($sql);
		// 	$stmt->bindValue(':celular', $this->data, PDO::PARAM_STR);
		// 	$stmt->execute();
		// 	return $stmt->fetchAll(PDO::FETCH_ASSOC);
		// }

		function getClients() //Contacto_cliente OK
		{
			$sql = "SELECT 
						DISTINCT (CC.documento_cliente_c) AS CLIENTE,
						C.NOMBRE_CLIENTE AS ALIAS
					FROM SAMY_GBI.dbo.gbi_contactoscliente_cstm CC	
					INNER JOIN UnoEE.dbo.VWS_GBICLIENTES C
						ON C.CLIENTE = CC.documento_cliente_c
					WHERE CC.celular_c = :celular
					GROUP BY CC.documento_cliente_c,
					CC.celular_c,
					C.NOMBRE_CLIENTE";

			$stmt = $this->samyDB->prepare($sql);
			$stmt->bindValue(':celular', $this->data, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		// function testInfoWallet() //Cartera OK
		// {
		// 	$sql = "SELECT
		// 		EC.CLIENTE as num_documento,
		// 		EC.LIMITE_CREDITO as limite,
		// 		EC.COND_PAGO,
		// 		EC.SALDOPENDIENTE as deuda,
		// 		EC.PORAPLICAR as por_aplicar,
		// 		EC.CUPO_DISPONIBLE as cupo_disp,
		// 		CLI.NOMBRE as nombre, 
		// 		ISNULL(
		// 			(SELECT
		// 				MAX(C.DIAS_VENCIDO)
		// 			FROM
		// 				COSMETICOS_SAMY.samy.GBI_CARTERA C
		// 			WHERE
		// 				C.CLIENTE = EC.CLIENTE			
		// 			), 0) as dia_atraso,
		// 			(SELECT
		// 					SUM (CAST(TOTAL_PEDIDO AS FLOAT)) AS valor_pedidos
		// 			FROM COSMETICOS_SAMY.samy.GBI_PEDIDOS
		// 			WHERE CLIENTE = EC.CLIENTE  AND ESTADO = 'N') as total_pedido
		// 	FROM
		// 		COSMETICOS_SAMY.samy.GBI_ESTADOCLIENTE EC
		// 	INNER JOIN COSMETICOS_SAMY.samy.CLIENTE CLI
		// 		ON CLI.CLIENTE = EC.CLIENTE
		// 		AND EC.CLIENTE = '$this->data'
		// 	";

		// 	$stmt = $this->samyDB->query($sql);
		// 	return $stmt->fetch(PDO::FETCH_ASSOC);
		// }

		function testInfoWallet() //Cartera OK
		{
			$sql = "SELECT
						EC.CLIENTE AS num_documento,
						EC.LIMITE_CREDITO AS limite,
						EC.COND_PAGO,
						C.SALDO AS deuda, 
						EC.PORAPLICAR AS por_aplicar,
						C.CUPO_DISPONIBLE AS cupo_disp,
						C.NOMBRE_CLIENTE AS nombre,
						ISNULL(
						(SELECT
							MAX(C.DIAS_VENCIDO)
						FROM
							UnoEE.dbo.VWS_GBICARTERA C
						WHERE
							C.CLIENTE = EC.CLIENTE			
						), 0) as dia_atraso,
						(SELECT SUM (CAST(P.TOTAL_PEDIDO AS FLOAT)) AS valor_pedidos
						FROM UnoEE.dbo.VWS_PEDIDOS P
						WHERE P.CLIENTE_SUC = EC.CLIENTE ) AS total_pedido
					FROM UnoEE.dbo.VWS_GBIESTADOCLIENTE EC
					INNER JOIN UnoEE.dbo.VWS_GBICARTERA C ON EC.CLIENTE = C.CLIENTE
					AND EC.CLIENTE = '$this->data'";

			$stmt = $this->samyDB->query($sql);
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		// function infoWallet()
		// {
		// 	// $objdecrypt = new Samy($this->data);
		// 	// $data = $objdecrypt->decrypt();


		// 	$sql_1 = "SELECT
		// 			gbi_clientes_cstm.codigo_cliente_c AS num_documento,
		// 			gbi_clientes_cstm.nombre_c AS nombre,
		// 			gbi_clientes_cstm.deuda_total_c AS deuda,
		// 			gbi_clientes_cstm.cupo_credito_c AS limite,
		// 			gbi_clientes_cstm.cupo_disponible_c AS cupo_disp,
		// 			gbi_clientes_cstm.condiciones_pago_c AS cond_pago,
		// 			gbi_clientes_cstm.saldo_por_aplicar_c AS por_aplicar,
		// 			(
		// 			SELECT TOP 1
		// 					C.DIAS_VENCIDO AS dia_atraso
		// 			FROM
		// 					COSMETICOS_SAMY.samy.GBI_CARTERA AS C
		// 			WHERE
		// 					C.CLIENTE COLLATE Latin1_General_CI_AI = gbi_clientes_cstm.codigo_cliente_c
		// 			) as dia_atraso
		// 		FROM
		// 				gbi_clientes
		// 		INNER JOIN gbi_clientes_cstm ON
		// 				gbi_clientes.id = gbi_clientes_cstm.id_c
		// 		WHERE
		// 			gbi_clientes_cstm.codigo_cliente_c = '{$this->data}'
		// 	";

		// 	$stm_1 = $this->gbiDB->query($sql_1);
		// 	$response_1 = [];

		// 	while ($row_1 = $stm_1->fetch(PDO::FETCH_ASSOC)) {

		// 		$sql_2 = "SELECT SUM (CAST(TOTAL_PEDIDO AS FLOAT)) AS total_pedidos FROM samy.GBI_PEDIDOS WHERE CLIENTE = '{$this->data}' AND ESTADO = 'N'";
		// 		$stm_2 = $this->samyDB->query($sql_2);
		// 		$response_2 = [];

		// 		while ($row_2 = $stm_2->fetch(PDO::FETCH_ASSOC)) {
		// 			$response_2[] = $row_2;
		// 		}

		// 		$row_1['orders'] = $response_2;
		// 		$response_1[] = $row_1;
		// 	}

		// 	$response = [
		// 		'account' => $response_1
		// 	];

		// 	return json_encode($response);
		// }

		function infoWallet()
		{
			// $objdecrypt = new Samy($this->data);
			// $data = $objdecrypt->decrypt();


			$sql_1 = "SELECT
					gbi_clientes_cstm.codigo_cliente_c AS num_documento,
					gbi_clientes_cstm.nombre_c AS nombre,
					gbi_clientes_cstm.deuda_total_c AS deuda,
					gbi_clientes_cstm.cupo_credito_c AS limite,
					gbi_clientes_cstm.cupo_disponible_c AS cupo_disp,
					gbi_clientes_cstm.condiciones_pago_c AS cond_pago,
					gbi_clientes_cstm.saldo_por_aplicar_c AS por_aplicar,
					(
					SELECT TOP 1
							C.DIAS_VENCIDO AS dia_atraso
					FROM
							UnoEE.dbo.VWS_GBICARTERA AS C
					WHERE
							C.CLIENTE COLLATE Latin1_General_CI_AI = gbi_clientes_cstm.codigo_cliente_c
					) as dia_atraso
				FROM
						gbi_clientes
				INNER JOIN gbi_clientes_cstm ON
						gbi_clientes.id = gbi_clientes_cstm.id_c
				WHERE
					gbi_clientes_cstm.codigo_cliente_c = '{$this->data}'
			";

			$stm_1 = $this->gbiDB->query($sql_1);
			$response_1 = [];

			while ($row_1 = $stm_1->fetch(PDO::FETCH_ASSOC)) {

				$sql_2 = "SELECT SUM (CAST(TOTAL_PEDIDO AS FLOAT)) AS total_pedidos FROM samy.GBI_PEDIDOS WHERE CLIENTE = '{$this->data}' AND ESTADO = 'N'";
				$stm_2 = $this->samyDB->query($sql_2);
				$response_2 = [];

				while ($row_2 = $stm_2->fetch(PDO::FETCH_ASSOC)) {
					$response_2[] = $row_2;
				}

				$row_1['orders'] = $response_2;
				$response_1[] = $row_1;
			}

			$response = [
				'account' => $response_1
			];

			return json_encode($response);
		}

		function actualizarDeudaCupoCliente() //No se usa
		{

			$idClient = $this->data;
			$objCartera = new Samy($idClient);
			//ID, LIMITE --CRM
			$dato = $objCartera->selectRelDiasAtraFact($idClient);
			if (isset($idClient)) {
				//SALDO, FACTURA --CRM
				$datosDias = $objCartera->selectSumaDiasAtraFact($dato['id']);
				$total = array();
				if (count($datosDias) > 0) {
					foreach ($datosDias as $rowD) {
						array_push($total, round($rowD["valor"]));
					}
				}

				$total = array_sum($total);
				//CUPO --ERP
				$getCupoErp = $objCartera->erpGetCupoCliente($idClient);
				$limite = (($dato['limite'] != $getCupoErp->LIMITE_CREDITO) ? $getCupoErp->LIMITE_CREDITO : $dato['limite']);
				$cupo = intval($limite) - $total;
				$dataUpdate = array(
					"limite" => $limite,
					"cupo" => $cupo,
					"total" => $total,
					"cliente" => $dato["id"]
				);

				return $objCartera->updateCupoClienteCstm($dataUpdate);
			} else {
				echo  "No hay registros para actualizar  <br>";
			}
		}

		function selectRelDiasAtraFact($cliente) //No se usa
		{
			try {
				$sql = "SELECT DISTINCT
						gbi_clientes_gbi_diasatrafact_1_c.gbi_clientes_gbi_diasatrafact_1gbi_clientes_ida AS id,
						gbi_clientes_cstm.cupo_credito_c AS limite
						FROM
						gbi_clientes_gbi_diasatrafact_1_c
						INNER JOIN gbi_clientes_cstm ON gbi_clientes_gbi_diasatrafact_1_c.gbi_clientes_gbi_diasatrafact_1gbi_clientes_ida = gbi_clientes_cstm.id_c
						WHERE gbi_clientes_gbi_diasatrafact_1_c.deleted = 0
						AND gbi_clientes_cstm.codigo_cliente_c = '{$cliente}'
					";
				$query = $this->gbiDB->prepare($sql);
				$query->execute();
				return $query->fetch();
			} catch (Exception $e) {
				return	$e->getMessage();
			}
		}

		function selectSumaDiasAtraFact($idCliente)
		{
			try {
				$sql = "SELECT DISTINCT
					gbi_diasatrafact.name,
					gbi_diasatrafact_cstm.saldo_c AS valor
				FROM
					gbi_diasatrafact
				INNER JOIN gbi_diasatrafact_cstm ON gbi_diasatrafact.id = gbi_diasatrafact_cstm.id_c
				INNER JOIN gbi_clientes_gbi_diasatrafact_1_c ON gbi_diasatrafact.id = gbi_clientes_gbi_diasatrafact_1_c.gbi_clientes_gbi_diasatrafact_1gbi_diasatrafact_idb
				WHERE gbi_clientes_gbi_diasatrafact_1_c.gbi_clientes_gbi_diasatrafact_1gbi_clientes_ida = :cliente
				AND gbi_diasatrafact_cstm.fac_rec_c = 'FAC'
				AND gbi_diasatrafact_cstm.saldo_c <> '.00000000'
				AND gbi_diasatrafact.deleted = '0'
				AND (YEAR(gbi_diasatrafact_cstm.fecha_emision_c) >= 2022)
				";
				$query = $this->gbiDB->prepare($sql);
				$query->bindParam(':cliente', $idCliente);
				$query->execute();
				return $query->fetchAll();
			} catch (Exception $e) {
				return	$e->getMessage();
			}
		}

		// function erpGetCupoCliente($cliente)
		// {
		// 	try {
		// 		$stm = "SELECT
		// 			CLIENTE,
		// 			CASE
		// 				WHEN LIMITE_CREDITO IS NULL THEN 0.0
		// 				ELSE LIMITE_CREDITO
		// 			END AS LIMITE_CREDITO,
		// 			SALDO_LOCAL
		// 		FROM
		// 			COSMETICOS_SAMY.samy.GBI_CLIENTES
		// 		WHERE
		// 			CLIENTE = :cliente";

		// 		$query = $this->samyDB->prepare($stm);
		// 		$query->bindParam(':cliente', $cliente, PDO::PARAM_STR);
		// 		$query->execute();
		// 		$result = $query->fetch(PDO::FETCH_OBJ);

		// 		$query = null;

		// 		return $result;
		// 	} catch (Exception $e) {
		// 		return	$e->getMessage();
		// 	}
		// }

		function erpGetCupoCliente($cliente)
		{
			try {
				$stm = "SELECT
							CLIENTE,
							CASE
								WHEN LIMITE_CREDITO IS NULL THEN 0.0
								ELSE LIMITE_CREDITO
							END AS LIMITE_CREDITO,
							SALDO AS SALDO_LOCAL --PREGUNTAR
						FROM
							UnoEE.dbo.VWS_GBICARTERA
						WHERE
							CLIENTE = :cliente";

				$query = $this->samyDB->prepare($stm);
				$query->bindParam(':cliente', $cliente, PDO::PARAM_STR);
				$query->execute();
				$result = $query->fetch(PDO::FETCH_OBJ);

				$query = null;

				return $result;
			} catch (Exception $e) {
				return	$e->getMessage();
			}
		}

		function updateCupoClienteCstm($data)
		{
			try {
				$sql = "UPDATE gbi_clientes_cstm SET cupo_credito_c = :limite, cupo_disponible_c = :cupo, deuda_total_c = :total WHERE id_c = :cliente";
				$query = $this->gbiDB->prepare($sql);
				$query->bindParam(":limite", $data["limite"]);
				$query->bindParam(":cupo", $data["cupo"]);
				$query->bindParam(":total", $data["total"]);
				$query->bindParam(":cliente", $data["cliente"]);
				return $query->execute();
			} catch (Exception $e) {
				return	$e->getMessage();
			}
		}

		function encrypt()
		{
			$numIteraciones = rand(1, 9);
			$result = '';
			$string = $this->data;
			for ($i = 0; $i < (strlen($string) + $numIteraciones); $i++) {
				if ($i > strlen($string) - 1) {
					$newRandom = rand(0, 9);
					$result .= $newRandom;
				} else {
					$char = substr($string, $i, 1);
					$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
					$char = chr(ord($char) + ord($keychar));
					$result .= $char;
				}
			}
			$result .= $numIteraciones;
			return base64_encode($result);
		}

		function decrypt()
		{
			$lastChar = 0;
			$result = '';
			$string = base64_decode($this->data);
			$lastChar = substr($string, -1);
			for ($i = 0; $i < strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
				$char = chr(ord($char) - ord($keychar));
				$result .= $char;
			}
			return substr($result, 0, (strlen($result) - ($lastChar + 1)));
		}
	}
