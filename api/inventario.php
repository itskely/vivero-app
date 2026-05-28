<?php
session_start();
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../models/InventarioModel.php";
include __DIR__ . "/../models/EtapasModel.php";
include __DIR__ . "/../models/UbicacionesModel.php";
$inventario = new InventarioModel();
$etapa = new EtapasModel();
$ubicacion = new UbicacionesModel();

# Prueba de devuelta json
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    die(json_encode(
        [
            "error" => "No autenticado"
        ]
    ));
}

$lote_id = $_GET['lote_id'] ?? null;

$inventario->setLoteId($lote_id);
$inventarios = $inventario->getByLoteId();

$etapaIds = array_values(array_unique(array_column($inventarios, 'etapa_id')));
$ubicacionIds = array_values(array_unique(array_column($inventarios, 'ubicacion_id')));
$allEtapas = count($etapaIds) > 0 ? $etapa->getEtapasInIds($etapaIds) : [];
$allUbicaciones = count($ubicacionIds) > 0 ? $ubicacion->getUbicacionesInIds($ubicacionIds) : [];

$etapasMap = [];
foreach ($allEtapas as $e) {
    $etapasMap[$e['id']] = $e;
}

$ubicacionesMap = [];
foreach ($allUbicaciones as $u) {
    $ubicacionesMap[$u['id']] = $u;
}

foreach ($inventarios as &$inventario) {
    $inventario['etapa'] = $etapasMap[$inventario['etapa_id']] ?? null;
    $inventario['ubicacion'] = $ubicacionesMap[$inventario['ubicacion_id']] ?? null;
}

echo json_encode(
    $inventarios
);
