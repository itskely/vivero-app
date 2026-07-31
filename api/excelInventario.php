<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/InventarioModel.php";

use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

$inventario = new InventarioModel();

$inventario->setBusqueda($_GET['busqueda'] ?? '');
$inventario->setEtapa($_GET['etapa'] ?? '');
$inventario->setUbicacion($_GET['ubicacion'] ?? '');
$inventario->setUnidad($_GET['unidad'] ?? '');

$inventario->setLimit(1000000);
$inventario->setOffset(0);

$registros = $inventario->getAllCompleto();

$writer = new Writer();
$writer->openToBrowser('inventario.xlsx');

$writer->addRow(Row::fromValues([
    'Etapa',
    'especie',
    'Ubicación',
    'Unidad de medida',
    'lotes',
    'Total'
]));

foreach ($registros as $fila) {

    $writer->addRow(Row::fromValues([
        $fila['etapa'],
        $fila['nombre_comun'],
        $fila['ubicacion'],
        $fila['unidad_medida'],
        $fila['numero_lotes'],
        $fila['total_unidades']
    ]));
}

$writer->close();
