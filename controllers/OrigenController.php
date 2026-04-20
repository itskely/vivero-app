<?php
include __DIR__ . "/../models/OrigenModel.php";
$origen = new OrigenModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_POST['id'] ?? ($_GET['id'] ?? null);
$delete_id = $_GET['delete_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;





if ($method === "POST")
{
    if ($nombre && $descripcion && $tipo)
    {


        $origen->setId($id);
        $origen->setNombre($nombre);
        $origen->setDescripcion($descripcion);
        $origen->setTipo($tipo);

        if (empty($id))
        {
            $fueCreado = $origen->crear();
            if ($fueCreado)
            {
                $_SESSION["success"] = "Origen creado con éxito";
            } else
            {
                $_SESSION["error"] = "Error al crear el Origen";
            }
        } else
        {

            $fueEditado = $origen->update();
            if ($fueEditado)
            {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Origen actualizado con éxito";
                header("Location: $newUrl");
            } else
            {
                $_SESSION["error"] = "Error al actualizar el Origen";
            }
        }
    } else
    {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}
$oneOrigen = null;
if ($id)
{
    $origen->setId($id);
    $oneOrigen = $origen->getOne();
}


if ($delete_id)
{
    $origen->setId($delete_id);
    $fueEliminado = $origen->delete();
    if ($fueEliminado)
    {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Origen eliminado con éxito";
        header("Location: $newUrl");
    } else
    {
        $_SESSION["error"] = "Error al eliminar el Origen";
    }
}
$allOrigen = $origen->getAll();
$tipo = [
    "Interno",
    "Compra",
    "Donaciones",
    "Externo"
];
