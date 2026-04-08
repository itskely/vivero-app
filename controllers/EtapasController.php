<?php
include __DIR__ . "/../models/EtapasModel.php";
$etapas = new EtapasModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_POST['id'] ?? ($_GET['id'] ?? null);
$delete_id = $_GET['delete_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;





if ($method === "POST") {
    if ($nombre  && $descripcion) {


        $etapas->setId($id);
        $etapas->setNombre($nombre);
        $etapas->setDescripcion($descripcion);

        if (empty($id)) {
            $fueCreado = $etapas->crear();
            if ($fueCreado) {
                $_SESSION["success"] = "Etapa creada con éxito";
            } else {
                $_SESSION["error"] = "Error al crear la Etapa";
            }
        } else {

            $fueEditado = $etapas->update();
            if ($fueEditado) {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Etapa actualizada con éxito";
                header("Location: $newUrl");
            } else {
                $_SESSION["error"] = "Error al actualizar la etapa";
            }
        }
    } else {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}
$oneEtapa = null;
if ($id) {
    $etapas->setId($id);
    $oneEtapa = $etapas->getOne();
}


if ($delete_id) {
    $etapas->setId($delete_id);
    $fueEliminado = $etapas->delete();
    if ($fueEliminado) {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Etapa eliminada con éxito";
        header("Location: $newUrl");
    } else {
        $_SESSION["error"] = "Error al eliminar la Etapa";
    }
}
$allEtapas = $etapas->getAll();
