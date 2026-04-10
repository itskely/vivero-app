<?php
include __DIR__ . "/../models/PlantaModel.php";
$planta = new PlantaModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_GET['id'] ?? null;
$delete_id = $_GET['delete_id'] ?? null;
$nombre_cientifico = $_POST['nombre_cientifico'] ?? null;
$nombre_comun = $_POST['nombre_comun'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$imagen = $_FILES["imagen"] ?? null;


function uploadFile($file)
{
    $path = __DIR__ . "/../assets/uploads/";
    if (!is_dir($path))
    {
        if (!mkdir($path, 0777, true))
        {
            echo "No se pudo crear el directorio";
            return null;
        }
    }
    $filePath = $path . $file["name"];
    return move_uploaded_file($file["tmp_name"], $filePath) ? $file["name"] : null;
}

if ($method === "POST")
{
    if ($nombre_cientifico && $nombre_comun && $descripcion && $imagen)
    {
        $imageName = uploadFile($imagen);

        $planta->setId($id);
        $planta->setName($nombre_cientifico);
        $planta->setNombreComun($nombre_comun);
        $planta->setImagen($imageName);
        $planta->setDescripcion($descripcion);

        if (empty($id))
        {
            $fueCreado = $planta->crear();
            if ($fueCreado)
            {
                $_SESSION["success"] = "Planta creada con éxito";
            } else
            {
                $_SESSION["error"] = "Error al crear la planta";
            }
        } else
        {
            $editingPlanta = $planta->getOne();
            $planta->setImagen($imageName ?? $editingPlanta['imagen']);

            $fueEditado = $planta->update();
            if ($fueEditado)
            {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Planta actualizada con éxito";
                header("Location: $newUrl");
            } else
            {
                $_SESSION["error"] = "Error al actualizar la planta";
            }
        }
    } else
    {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}

$onePlanta = null;
if ($id)
{
    $planta->setId($id);
    $onePlanta = $planta->getOne();
}

if ($delete_id)
{
    $planta->setId($delete_id);
    $fueEliminado = $planta->delete();
    if ($fueEliminado)
    {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Planta eliminada con éxito";
        header("Location: $newUrl");
    } else
    {
        $_SESSION["error"] = "Error al eliminar la planta";
    }
}


// Parametros de busqueda y paginación
$busqueda = $_GET['busqueda'] ?? null;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$planta->setBusqueda($busqueda);
$totalRegistros = $planta->getCount()['total'];

$limit = 20;
$offset = ($page - 1) * $limit;

$totalPaginas = ceil($totalRegistros / $limit);

$planta->setLimit($limit);
$planta->setOffset($offset);
$allPlants = $planta->getAll();
