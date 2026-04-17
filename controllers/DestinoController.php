<?php
include __DIR__ . "/../models/DestinosModel.php";
$destinos = new DestinosModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_POST['id'] ?? ($_GET['id'] ?? null);
$delete_id = $_GET['delete_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;





if ($method === "POST")
{
    if ($nombre && $descripcion)
    {


        $destinos->setId($id);
        $destinos->setNombre($nombre);
        $destinos->setDescripcion($descripcion);

        if (empty($id))
        {
            $fueCreado = $destinos->crear();
            if ($fueCreado)
            {
                $_SESSION["success"] = "Destino creado con éxito";
            } else
            {
                $_SESSION["error"] = "Error al crear el Destino";
            }
        } else
        {

            $fueEditado = $destinos->update();
            if ($fueEditado)
            {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Destino actualizado con éxito";
                header("Location: $newUrl");
            } else
            {
                $_SESSION["error"] = "Error al actualizar el Destino";
            }
        }
    } else
    {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}
$oneDestino = null;
if ($id)
{
    $destinos->setId($id);
    $oneDestino = $destinos->getOne();
}


if ($delete_id)
{
    $destinos->setId($delete_id);
    $fueEliminado = $destinos->delete();
    if ($fueEliminado)
    {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Destino eliminado con éxito";
        header("Location: $newUrl");
    } else
    {
        $_SESSION["error"] = "Error al eliminar el Destino";
    }
}
$allDestinos = $destinos->getAll();
