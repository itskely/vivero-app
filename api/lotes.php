<?php
session_start();
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../models/LoteModel.php";
include __DIR__ . "/../models/PlantaModel.php";
$lote = new LoteModel();
$planta = new PlantaModel();

# Prueba de devuelta json
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']))
{
    die(json_encode(
        [
            "error" => "No autenticado"
        ]
    ));
}

// Parametros de busqueda y paginación
$busqueda = $_GET['busqueda'] ?? null;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$lote->setBusqueda($busqueda);
$totalRegistros = $lote->getCount()['total'];

$limit = 20;
$offset = ($page - 1) * $limit;

$totalPaginas = ceil($totalRegistros / $limit);

$lote->setLimit($limit);
$lote->setOffset($offset);
$allLotes = $lote->getAll();
$plantaIds = array_unique(array_column($allLotes, 'planta_id'));
$allPlantas = count($plantaIds) > 0 ? $planta->getPlantasInIds($plantaIds) : [];

$plantasMap = [];
foreach ($allPlantas as $p)
{
    $plantasMap[$p['id']] = $p;
}

foreach ($allLotes as &$lote)
{
    $lote['planta'] = $plantasMap[$lote['planta_id']] ?? null;
}

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