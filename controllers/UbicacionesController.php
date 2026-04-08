<?php
include __DIR__ . "/../models/UbicacionesModel.php";
$ubicaciones = new UbicacionesModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_POST['id'] ?? ($_GET['id'] ?? null);
$delete_id = $_GET['delete_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;





if ($method === "POST") {
    if ($nombre  && $descripcion) {


        $ubicaciones->setId($id);
        $ubicaciones->setNombre($nombre);
        $ubicaciones->setDescripcion($descripcion);

        if (empty($id)) {
            $fueCreado = $ubicaciones->crear();
            if ($fueCreado) {
                $_SESSION["success"] = "Ubicación creada con éxito";
            } else {
                $_SESSION["error"] = "Error al crear la Ubicación";
            }
        } else {

            $fueEditado = $ubicaciones->update();
            if ($fueEditado) {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Ubicación actualizada con éxito";
                header("Location: $newUrl");
            } else {
                $_SESSION["error"] = "Error al actualizar la ubicación";
            }
        }
    } else {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}
$oneUbicacion = null;
if ($id) {
    $ubicaciones->setId($id);
    $oneUbicacion = $ubicaciones->getOne();
}


if ($delete_id) {
    $ubicaciones->setId($delete_id);
    $fueEliminado = $ubicaciones->delete();
    if ($fueEliminado) {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Ubicación eliminada con éxito";
        header("Location: $newUrl");
    } else {
        $_SESSION["error"] = "Error al eliminar la Ubicación";
    }
}
$allUbicaciones = $ubicaciones->getAll();
