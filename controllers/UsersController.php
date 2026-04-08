<?php

include __DIR__ . "/../models/UserModel.php";
include __DIR__ . "/../models/RoleModel.php";
$user = new UserModel();
$role = new RoleModel();

$method = $_SERVER['REQUEST_METHOD'];
$params = $_GET;
$id = $_GET['id'] ?? null;
$delete_id = $_GET['delete_id'] ?? null;
$nombre_completo = $_POST['nombre_completo'] ?? null;
$cedula = $_POST['cedula'] ?? null;
$password = $_POST['password'] ?? null;
$id_rol = $_POST['id_rol'] ?? null;

if ($method === "POST") {
    if ($nombre_completo && $cedula && $id_rol) {
        $user->setId($id);
        $user->setNombreCompleto($nombre_completo);
        $user->setCedula($cedula);
        $user->setPassword($password);
        $user->setRol($id_rol);

        if (empty($id)) {
            if (empty($password)) {
                $_SESSION["error"] = "La contraseña es requerida para crear un nuevo usuario";
            } else {
                $user->setPassword(password_hash($password, PASSWORD_BCRYPT));

                $fueCreado = $user->crear();
                if ($fueCreado) {
                    $_SESSION["success"] = "Usuario creado con éxito";
                } else {
                    $_SESSION["error"] = "Error al crear el usuario";
                }
            }
        } else {
            if (!empty($password)) {
                $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
            } else {
                // Si no se proporciona una nueva contraseña, mantenemos la existente
                $existingUser = $user->getOne();
                $user->setPassword($existingUser['password']);
            }

            $fueEditado = $user->update();
            if ($fueEditado) {
                unset($params['id']);

                // 3. Reconstruir la URL
                $newQuery = http_build_query($params);
                $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
                $_SESSION["success"] = "Usuario actualizado con éxito";
                header("Location: $newUrl");
            } else {
                $_SESSION["error"] = "Error al actualizar el usuario";
            }
        }
    } else {
        $_SESSION["error"] = "Todos los campos son requeridos";
    }
}

$oneUser = null;
if ($id) {
    $user->setId($id);
    $oneUser = $user->getOne();
}

if ($delete_id) {
    $user->setId($delete_id);
    $fueEliminado = $user->delete();
    if ($fueEliminado) {
        unset($params['delete_id']);

        // 3. Reconstruir la URL
        $newQuery = http_build_query($params);
        $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQuery;
        $_SESSION["success"] = "Usuario eliminado con éxito";
        header("Location: $newUrl");
    } else {
        $_SESSION["error"] = "Error al eliminar el usuario";
    }
}

$allUsers = $user->getAll();
$allRoles = $role->getAll();
