<?php

$page = new PageModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_GET['id'] ?? null;
$delete_id = $_GET['delete_id'] ?? null;
$name = $_POST['name'] ?? null;
$route = $_POST['route'] ?? null;
$ord = $_POST['ord'] ?? null;
$icon = $_POST['icon'] ?? null;
$description = $_POST['description'] ?? null;
$is_home = $_POST['is_home'] ?? false;

if ($method === "POST") {
    if ($name && $route && $ord && $icon && $description) {
        $page->setId($id);
        $page->setName($name);
        $page->setRoute($route);
        $page->setOrd($ord);
        $page->setIcon($icon);
        $page->setIsHome($is_home ? 1 : 0);
        $page->setDescription($description);

        if (empty($id)) {
            $fueCreado = $page->crear();
            if ($fueCreado) {
                $_SESSION["success"] = "Página creada con éxito";
            } else {
                $_SESSION["error"] = "Error al crear la página";
            }
        } else {
            $fueEditado = $page->update();
            if ($fueEditado) {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Página actualizada con éxito";
                header("Location: $newUrl");
            } else {
                $_SESSION["error"] = "Error al actualizar la página";
            }
        }
    } else {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}

$onePage = null;
if ($id) {
    $page->setId($id);
    $onePage = $page->getOne();
}

if ($delete_id) {
    $page->setId($delete_id);
    $fueEliminado = $page->delete();
    if ($fueEliminado) {
        $_SESSION["success"] = "Página eliminada con éxito";
    } else {
        $_SESSION["error"] = "Error al eliminar la página";
    }
}

$allPages = $page->getAll();
