<?php
include __DIR__ . "/../models/MovimientoInventarioModel.php";
include __DIR__ . "/../models/EtapasModel.php";
include __DIR__ . "/../models/UbicacionesModel.php";
include __DIR__ . "/../models/InventarioModel.php";
$movimientoInventario = new MovimientoInventarioModel();
$etapa = new EtapasModel();
$allEtapas = $etapa->getAll();
$ubicacion = new UbicacionesModel();
$inventario = new InventarioModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_GET['id'] ?? null;
$anulacion_id = $_POST['anulacion_id'] ?? null;
$lote_id = $_POST['lote_id'] ?? null;
$inventario_id = $_POST['inventario_id'] ?? null;
$cantidad_real = $_POST['cantidad_real'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;


if ($method === "POST") {
    if ($anulacion_id) {
        try {
            $movimientoInventario->setId($anulacion_id);
            $movimiento = $movimientoInventario->getOne();
            if ($movimiento && $movimiento["estado"] === "anulado") {
                $_SESSION["error"] = "El movimiento ya se encuentra anulado";
            } else {
                $fueAnulado = $movimientoInventario->anular();
                if ($fueAnulado) {
                    $_SESSION["success"] = "Movimiento anulado con éxito";
                }
            }
        } catch (Exception $e) {
            $_SESSION["error"] = $e->getMessage();
        }
    } elseif ($lote_id && $inventario_id && $cantidad_real && $observaciones) {
        $inventario->setId($inventario_id);
        $invLote = $inventario->getOne();

        if (!$invLote) {
            $_SESSION["error"] = "Inventario no encontrado";
        } else {
            $movimientoInventario->setId($id);
            $movimientoInventario->setLoteId($lote_id);
            $movimientoInventario->setEtapaId($invLote['etapa_id']);
            $movimientoInventario->setUbicacionId($invLote['ubicacion_id']);
            $movimientoInventario->setCantidad($cantidad_real);
            $movimientoInventario->setMotivo($observaciones);

            $fueCreado = $movimientoInventario->crear();
            if ($fueCreado) {
                $_SESSION["success"] = "Ajuste creado con éxito";
            } else {
                $_SESSION["error"] = "Error al crear el ajuste";
            }
        }
    } else {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}


// Parametros de busqueda y paginación
$busqueda = $_GET['busqueda'] ?? null;
$tipo = $_GET['tipo'] ?? null;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$movimientoInventario->setBusqueda($busqueda);
$totalRegistros = $movimientoInventario->getCount()['total'];

$limit = 20;
$offset = ($page - 1) * $limit;

$totalPaginas = ceil($totalRegistros / $limit);

$movimientoInventario->setLimit($limit);
$movimientoInventario->setOffset($offset);
$allMovimientosInventario = $movimientoInventario->getAll();
