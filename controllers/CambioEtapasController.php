<?php
include __DIR__ . "/../models/CambioEtapaModel.php";
include __DIR__ . "/../models/EtapasModel.php";
include __DIR__ . "/../models/UbicacionesModel.php";
$cambioEtapa = new CambioEtapaModel();
$etapa = new EtapasModel();
$ubicacion = new UbicacionesModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_GET['id'] ?? null;
$delete_id = $_GET['delete_id'] ?? null;
$lote_id = $_POST['lote_id'] ?? null;
$etapa_origen_id = $_POST['etapa_origen_id'] ?? null;
$ubi_origen_id = $_POST['ubi_origen_id'] ?? null;
$etapa_destino_id = $_POST['etapa_destino_id'] ?? null;
$ubi_destino_id = $_POST['ubi_destino_id'] ?? null;
$cantidad_a_mover = $_POST['cantidad_a_mover'] ?? null;
$cantidad_que_sobrevive = $_POST['cantidad_que_sobrevive'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

if ($method === "POST")
{
    if ($lote_id && $etapa_origen_id && $ubi_origen_id && $etapa_destino_id && $ubi_destino_id && $cantidad_a_mover && $cantidad_que_sobrevive)
    {
        $cambioEtapa->setId($id);
        $cambioEtapa->setLoteId($lote_id);
        $cambioEtapa->setEtapaOrigenId($etapa_origen_id);
        $cambioEtapa->setUbiOrigenId($ubi_origen_id);
        $cambioEtapa->setEtapaDestinoId($etapa_destino_id);
        $cambioEtapa->setUbiDestinoId($ubi_destino_id);
        $cambioEtapa->setCantidadAMover($cantidad_a_mover);
        $cambioEtapa->setCantidadQueSobrevive($cantidad_que_sobrevive);
        $cambioEtapa->setObservaciones($observaciones);

        if (empty($id))
        {
            $fueCreado = $cambioEtapa->crear();
            if ($fueCreado)
            {
                $_SESSION["success"] = "Movimiento creado con éxito";
            } else
            {
                $_SESSION["error"] = "Error al crear el movimiento";
            }
        } else
        {
            $editingCambioEtapa = $cambioEtapa->getOne();

            $fueEditado = $cambioEtapa->update();
            if ($fueEditado)
            {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Lote actualizado con éxito";
                header("Location: $newUrl");
            } else
            {
                $_SESSION["error"] = "Error al actualizar el lote";
            }
        }
    } else
    {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}

$oneCambioEtapa = null;
if ($id)
{
    $cambioEtapa->setId($id);
    $oneCambioEtapa = $cambioEtapa->getOne();
}

if ($delete_id)
{
    $cambioEtapa->setId($delete_id);
    $fueEliminado = $cambioEtapa->delete();
    if ($fueEliminado)
    {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Lote eliminado con éxito";
        header("Location: $newUrl");
    } else
    {
        $_SESSION["error"] = "Error al eliminar el lote";
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