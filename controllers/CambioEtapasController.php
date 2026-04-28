<?php
include __DIR__ . "/../models/CambioEtapaModel.php";
include __DIR__ . "/../models/EtapasModel.php";
include __DIR__ . "/../models/UbicacionesModel.php";
include __DIR__ . "/../models/InventarioModel.php";
$cambioEtapa = new CambioEtapaModel();
$etapa = new EtapasModel();
$ubicacion = new UbicacionesModel();
$inventario = new InventarioModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_GET['id'] ?? null;
$lote_id = $_POST['lote_id'] ?? null;
$inventario_id = $_POST['inventario_id'] ?? null;
$etapa_destino_id = $_POST['etapa_destino_id'] ?? null;
$ubi_destino_id = $_POST['ubi_destino_id'] ?? null;
$cantidad_salida = $_POST['cantidad_salida'] ?? null;
$cantidad_entrada = $_POST['cantidad_entrada'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;
$unidad_medida = $_POST['unidad_medida'] ?? null;

if ($method === "POST")
{
    if ($lote_id && $inventario_id && $etapa_destino_id && $ubi_destino_id && $cantidad_salida && $cantidad_entrada)
    {
        $inventario->setId($inventario_id);
        $invLote = $inventario->getOne();

        if (!$invLote)
        {
            $_SESSION["error"] = "Inventario no encontrado";
        } else
        {
            if ($cantidad_salida > $invLote['cantidad_actual'] || $invLote['cantidad_actual'] === 0)
            {
                $_SESSION["error"] = "Cantidad de salida mayor a la cantidad en inventario";
            } else
            {
                $cambioEtapa->setId($id);
                $cambioEtapa->setLoteId($lote_id);
                $cambioEtapa->setEtapaOrigenId($invLote['etapa_id']);
                $cambioEtapa->setUbiOrigenId($invLote['ubicacion_id']);
                $cambioEtapa->setEtapaDestinoId($etapa_destino_id);
                $cambioEtapa->setUbiDestinoId($ubi_destino_id);
                $cambioEtapa->setCantidadSalida($cantidad_salida);
                $cambioEtapa->setCantidadEntrada($cantidad_entrada);
                $cambioEtapa->setObservaciones($observaciones);
                $cambioEtapa->setUnidadMedida($unidad_medida);

                $fueCreado = $cambioEtapa->crear();
                if ($fueCreado)
                {
                    $_SESSION["success"] = "Movimiento creado con éxito";
                } else
                {
                    $_SESSION["error"] = "Error al crear el movimiento";
                }
            }
        }
    } else
    {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}


// Parametros de busqueda y paginación
$busqueda = $_GET['busqueda'] ?? null;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$cambioEtapa->setBusqueda($busqueda);
$totalRegistros = $cambioEtapa->getCount()['total'];

$limit = 20;
$offset = ($page - 1) * $limit;

$totalPaginas = ceil($totalRegistros / $limit);

$cambioEtapa->setLimit($limit);
$cambioEtapa->setOffset($offset);
$allCambioEtapas = $cambioEtapa->getAll();

$allEtapas = $etapa->getAll();
$allUbicaciones = $ubicacion->getAll();
