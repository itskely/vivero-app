<?php
include __DIR__ . "/../models/MovimientoInventarioModel.php";
include __DIR__ . "/../models/InventarioModel.php";
include __DIR__ . "/../models/DestinosModel.php";
$movimiento = new MovimientoInventarioModel();
$inventario = new InventarioModel();
$destino = new DestinosModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$lote_id = $_POST['lote_id'] ?? null;
$inventario_id = $_POST['inventario_id'] ?? null;
$cantidad_salida = $_POST['cantidad_salida'] ?? null;
$destino_id = $_POST['destino_id'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;
$tipo_movimiento = $_POST['tipo_movimiento'] ?? null;

if ($method === "POST") {
    if ($lote_id && $inventario_id && $cantidad_salida && $destino_id && $tipo_movimiento) {
        $inventario->setId($inventario_id);
        $invLote = $inventario->getOne();

        if (!$invLote) {
            $_SESSION["error"] = "Inventario no encontrado";
        } else {
            if ($cantidad_salida > $invLote['cantidad_actual'] || $invLote['cantidad_actual'] === 0) {
                $_SESSION["error"] = "Cantidad de salida mayor a la cantidad en inventario";
            } else {
                $movimiento->setLoteId($lote_id);
                $movimiento->setEtapaId($invLote['etapa_id']);
                $movimiento->setUbicacionId($invLote['ubicacion_id']);
                $movimiento->setTipoMovimiento($tipo_movimiento);
                $movimiento->setCantidad($cantidad_salida);
                $movimiento->setDestinoId($destino_id);
                $movimiento->setMotivo($observaciones);

                $fueCreado = $movimiento->store();
                if ($fueCreado) {
                    $_SESSION["success"] = "Movimiento creado con éxito";
                } else {
                    $_SESSION["error"] = "Error al crear el movimiento";
                }
            }
        }
    } else {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}


$allDestinos = $destino->getAll();
$misSalidas = $movimiento->getSalidasUsuario();
$totalSalidas = $movimiento->getTotalSalidasHoyUsuario();
