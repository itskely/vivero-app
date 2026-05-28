<?php
session_start();
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../models/InventarioModel.php";
include __DIR__ . "/../models/PlantaModel.php";
$inventario = new InventarioModel();
$planta = new PlantaModel();

# Prueba de devuelta json
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    die(json_encode(
        [
            "error" => "No autenticado"
        ]
    ));
}

// Parametros de busqueda y paginación
$busqueda = $_GET['busqueda'] ?? null;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$inventario->setBusqueda($busqueda);
$totalRegistros = $inventario->getAllCount()['total'];

$limit = 20;
$offset = ($page - 1) * $limit;

$totalPaginas = ceil($totalRegistros / $limit);

$inventario->setLimit($limit);
$inventario->setOffset($offset);
$allLotes = $inventario->getAll();

echo json_encode(
    [
        "data" => $allLotes,
        "pagination" => [
            "total" => $totalRegistros,
            "current_page" => $page,
            "per_page" => $limit,
            "total_pages" => $totalPaginas
        ]
    ]
);
