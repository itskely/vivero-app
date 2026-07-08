<?php
include __DIR__ . "/../models/InventarioModel.php";
include __DIR__ . "/../models/EtapasModel.php";
include __DIR__ . "/../models/UbicacionesModel.php";
$inventario = new InventarioModel();
$etapasModel = new EtapasModel();
$ubicacionesModel = new UbicacionesModel();

// Parametros de busqueda y paginación
$busqueda = $_GET['busqueda'] ?? null;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$inventario->setBusqueda($busqueda);
$totalRegistros = $inventario->getCount()['total'];

$limit = 20;
$offset = ($page - 1) * $limit;

$totalPaginas = ceil($totalRegistros / $limit);

$inventario->setLimit($limit);
$inventario->setOffset($offset);
$allEtapas = $etapasModel->getAll();
$allUbicaciones = $ubicacionesModel->getAll();
$allInventarios = $inventario->getAllCompleto();
