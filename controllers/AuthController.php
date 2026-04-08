<?php
session_start();
include "../config/database.php";
include "../models/UserModel.php";
include "../models/PageModel.php";

$user = new UserModel();
$page = new PageModel();

$method = $_SERVER['REQUEST_METHOD'];
$cedula = $_POST['cedula'] ?? null;
$password = $_POST['password'] ?? null;

if ($method === "POST") {
    if ($cedula && $password) {
        $user->setCedula($cedula);
        $user->setPassword($password);
        $usuario = $user->getDocumento();
        if ($usuario) {
            if (password_verify($password, $usuario['password'])) {
                $_SESSION['usuario'] = $usuario;
                $pageToAccess = $page->getIsHomePage();
                $messages = [
                    "success" => "Inicio de sesión exitoso",
                    "usuario" => $usuario,
                    "pageToAccess" => $pageToAccess
                ];
                header('Content-Type: application/json');
                echo json_encode($messages);
                exit;
            } else {
                // Devolvemos JSON DE error para leer en JS
                $messages = [
                    "error" => "Contraseña incorrecta"
                ];
                header('Content-Type: application/json');
                echo json_encode($messages);
                exit;
            }
        } else {
            // Devolvemos JSON DE error para leer en JS
            $messages = [
                "error" => "Usuario no encontrado"
            ];
            header('Content-Type: application/json');
            echo json_encode($messages);
            exit;
        }
    } else {
        // Devolvemos JSON DE error para leer en JS
        $messages = [
            "error" => "todos los campos son requeridos"
        ];
        header('Content-Type: application/json');
        echo json_encode($messages);
        exit;
    }
}
