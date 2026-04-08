<?php
include __DIR__ . "/../models/LoteModel.php";
$lote = new LoteModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_GET['id'] ?? null;
$delete_id = $_GET['delete_id'] ?? null;
$planta_id = $_POST['planta_id'] ?? null;
$codigo_lote = $_POST['codigo_lote'] ?? null;
$unidad_medida = $_POST['unidad_medida'] ?? null;
$cantidad = $_POST['cantidad'] ?? null;
$etapa_id = $_POST['etapa_id'] ?? null;
$ubicacion_id = $_POST['ubicacion_id'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

if ($method === "POST") {
    if ($planta_id && $codigo_lote && $unidad_medida && $cantidad && $etapa_id && $ubicacion_id) {
        $lote->setId($id);
        $lote->setPlantaId($planta_id);
        $lote->setCodigoLote($codigo_lote);
        $lote->setUnidadMedida($unidad_medida);
        $lote->setCantidad($cantidad);
        $lote->setEtapaId($etapa_id);
        $lote->setUbicacionId($ubicacion_id);
        $lote->setObservaciones($observaciones);

        if (empty($id)) {
            $fueCreado = $lote->crear();
            if ($fueCreado) {
                $_SESSION["success"] = "Lote creado con éxito";
            } else {
                $_SESSION["error"] = "Error al crear el lote";
            }
        } else {
            $editingLote = $lote->getOne();

            $fueEditado = $lote->update();
            if ($fueEditado) {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Lote actualizado con éxito";
                header("Location: $newUrl");
            } else {
                $_SESSION["error"] = "Error al actualizar el lote";
            }
        }
    } else {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}

$oneLote = null;
if ($id) {
    $lote->setId($id);
    $oneLote = $lote->getOne();
}

if ($delete_id) {
    $lote->setId($delete_id);
    $fueEliminado = $lote->delete();
    if ($fueEliminado) {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Lote eliminado con éxito";
        header("Location: $newUrl");
    } else {
        $_SESSION["error"] = "Error al eliminar el lote";
    }
}


// Parametros de busqueda y paginación
$busqueda = $_GET['busqueda'] ?? null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$lote->setBusqueda($busqueda);
$totalRegistros = $lote->getCount()['total'];

$limit = 20;
$offset = ($page - 1) * $limit;

$totalPaginas =  ceil($totalRegistros / $limit);

$lote->setLimit($limit);
$lote->setOffset($offset);
$allLotes = $lote->getAll();
