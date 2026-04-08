<?php

include __DIR__ . "/../models/RoleModel.php";
$role = new RoleModel();
$page = new PageModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_GET['id'] ?? null;
$delete_id = $_GET['delete_id'] ?? null;
$name = $_POST['name'] ?? null;

// Datos recividos por el modal de permisos
$pages = isset($_POST['pages']) && is_array($_POST['pages']) ? $_POST['pages'] : [];
$role_id = $_POST['role_id'] ?? null;

if ($method === "POST") {

    if ($role_id && !empty($pages)) {
        $role->delPageRole($role_id);
        foreach ($pages as $page_id) {
            $role->insPageRole($page_id, $role_id);
        }
    } else {
        if ($name) {
            $role->setId($id);
            $role->setName($name);

            if (empty($id)) {
                $fueCreado = $role->crear();
                if ($fueCreado) {
                    $_SESSION["success"] = "Rol creado con éxito";
                } else {
                    $_SESSION["error"] = "Error al crear el rol";
                }
            } else {
                $fueEditado = $role->update();
                if ($fueEditado) {
                    unset($params['id']);

                    // 3. Reconstruir la URL
                    $newQuery = http_build_query($params);
                    $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                    $_SESSION["success"] = "Rol actualizado con éxito";
                    header("Location: $newUrl");
                } else {
                    $_SESSION["error"] = "Error al actualizar el rol";
                }
            }
        } else {
            $_SESSION["error"] = "Todos los campos son requeridos";
        }
    }
}

$oneRole = null;
if ($id) {
    $role->setId($id);
    $oneRole = $role->getOne();
}

if ($delete_id) {
    $role->setId($delete_id);
    $fueEliminado = $role->delete();
    if ($fueEliminado) {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Rol eliminado con éxito";
        header("Location: $newUrl");
    } else {
        $_SESSION["error"] = "Error al eliminar el rol";
    }
}

$allRoles = $role->getAll();
$allPages = $page->getAll();
