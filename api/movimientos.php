<?php
session_start();
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../models/MovimientoInventarioModel.php";
$movimientoInventario = new MovimientoInventarioModel();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario']))
{
    die(json_encode(
        [
            "error" => "No autenticado"
        ]
    ));
}

$etapa_id = !empty($_GET['etapa_id']) ? $_GET['etapa_id'] : "all";
$year = !empty($_GET['year']) ? $_GET['year'] : date('Y');
$mode = !empty($_GET['chart']) ? $_GET['chart'] : null;

switch ($mode)
{
    case 'movimientos_mes':
        $movimientos = $movimientoInventario->movimientosMes($etapa_id, $year);
        $years = $movimientoInventario->years();

        $meses = [
            1 => 'ene',
            2 => 'feb',
            3 => 'mar',
            4 => 'abr',
            5 => 'may',
            6 => 'jun',
            7 => 'jul',
            8 => 'ago',
            9 => 'sep',
            10 => 'oct',
            11 => 'nov',
            12 => 'dic'
        ];

        $sanitisedLabels = [];

        foreach ($movimientos as $value)
        {
            $anoMes = explode("-", $value['ano_mes']);
            $numeroMes = $anoMes[1];
            $value = $meses[$numeroMes] . " " . $anoMes[0];
            if (!in_array($value, $sanitisedLabels))
            {
                $sanitisedLabels[] = $value;
            }
        }

        $data = [
            "filter_etapa" => $etapa_id,
            "filter_year" => $year,
            "years" => $years,
            "labels" => $sanitisedLabels,
            "series" => [
                "entrada" => [],
                "salida" => []
            ]
        ];
        foreach ($movimientos as $value)
        {
            $data['series'][$value['tipo_movimiento']][] = (float) $value['total'];
        }

        echo json_encode($data);
        break;
    case 'destinos_lotes':
        $movimientosDestino = $movimientoInventario->movimientosDestino();
        echo json_encode([
            "data" => $movimientosDestino
        ]);
        break;
    default:
        echo json_encode(
            [
                "error" => "Modo no reconocido"
            ]
        );
        break;
}
