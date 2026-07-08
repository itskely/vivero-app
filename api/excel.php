<?php

// llamar al autoload de composer

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/MovimientoInventarioModel.php";

use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

$movimientoInventario = new MovimientoInventarioModel();

$busqueda = $_GET['busqueda'] ?? null;

$movimientoInventario->setBusqueda($busqueda);
$movimientoInventario->setTipo($_GET['tipo'] ?? '');
$movimientoInventario->setEtapa($_GET['etapa'] ?? '');
$movimientoInventario->setFechaInicio($_GET['fecha_inicio'] ?? '');
$movimientoInventario->setFechaFin($_GET['fecha_fin'] ?? '');
$movimientoInventario->setSalidasVivero($_GET['salidas_vivero'] ?? '');
$movimientoInventario->setLimit(1000000);
$movimientoInventario->setOffset(0);
$allMovimientosInventario = $movimientoInventario->getAll();

$writer = new Writer();
$writer->openToBrowser('inventario.xlsx');

$cabecera = Row::fromValues([
    'Lote',
    'Tipo de movimiento',
    'Tipo de material',
    'Cantidad',
    'Etapa',
    'Ubicación',
    'Origen',
    'Destino',
    'Motivo',
    'Fecha'
]);
$writer->addRow($cabecera);

foreach ($allMovimientosInventario as $movimientoInventario) {
    $fila = Row::fromValues([
        $movimientoInventario['lote_id'],
        $movimientoInventario['tipo_movimiento'],
        $movimientoInventario['tipo_material'] ?: 'No registrado',
        $movimientoInventario['cantidad'] . ' ' . $movimientoInventario['unidad_medida'],
        $movimientoInventario['nombre_etapa'],
        $movimientoInventario['nombre_ubicacion'],
        $movimientoInventario['nombre_origen'],
        $movimientoInventario['nombre_destino'] ?: '-',
        $movimientoInventario['motivo'],
        $movimientoInventario['fecha']

    ]);
    $writer->addRow($fila);
}

$writer->close();
