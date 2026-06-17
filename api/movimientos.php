<?php
session_start();
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../models/MovimientoInventarioModel.php";
$movimientoInventario = new MovimientoInventarioModel();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    die(json_encode(
        [
            "error" => "No autenticado"
        ]
    ));
}

$etapa_id = !empty($_GET['etapa_id']) ? $_GET['etapa_id'] : "all";
$year = !empty($_GET['year']) ? $_GET['year'] : date('Y');
$mode = !empty($_GET['chart']) ? $_GET['chart'] : null;

switch ($mode) {
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

        foreach ($movimientos as $value) {
            $anoMes = explode("-", $value['ano_mes']);
            $numeroMes = $anoMes[1];
            $value = $meses[$numeroMes] . " " . $anoMes[0];
            if (!in_array($value, $sanitisedLabels)) {
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
        foreach ($movimientos as $value) {
            $data['series'][$value['tipo_movimiento']][] = (float) $value['total'];
        }

        echo json_encode($data);
        break;
    case 'salidas_destino':
        $mode = $_GET['mode'] ?? 'mes';

        $mes = !empty($_GET['mes'])
            ? (int) $_GET['mes']
            : date('n');
        $cuatrimestre = !empty($_GET['cuatrimestre'])
            ? (int) $_GET['cuatrimestre']
            : 1;

        if ($mode === 'mes') {

            $movimientosDestino = $movimientoInventario
                ->salidasDestinosMes(
                    $year,
                    $mes
                );
        } elseif ($mode === 'cuatrimestre') {

            switch ($cuatrimestre) {

                case 1:
                    $mesInicio = 1;
                    $mesFin = 4;
                    break;

                case 2:
                    $mesInicio = 5;
                    $mesFin = 8;
                    break;

                case 3:
                    $mesInicio = 9;
                    $mesFin = 12;
                    break;

                default:
                    $mesInicio = 1;
                    $mesFin = 4;
            }

            $movimientosDestino = $movimientoInventario
                ->salidasDestinosCuatrimestre(
                    $year,
                    $mesInicio,
                    $mesFin
                );
        } else {

            $movimientosDestino = $movimientoInventario
                ->salidasDestinosAnio(
                    $year
                );
        }
        $years = $movimientoInventario->years();
        $yearArray = [];
        foreach ($years as $value) {
            $yearArray[] = (int) $value['ano'];
        }

        echo json_encode([
            "years" => $yearArray,
            "data" => $movimientosDestino
        ]);
        break;
    case 'semillas_recolectadas':

        $mode = $_GET['mode'] ?? 'mes';

        $mes = !empty($_GET['mes'])
            ? (int) $_GET['mes']
            : date('n');
        $unidad = !empty($_GET['unidad'])
            ? $_GET['unidad']
            : 'gramos';
        $cuatrimestre = !empty($_GET['cuatrimestre'])
            ? (int) $_GET['cuatrimestre']
            : 1;

        if ($mode === 'mes') {

            $semillas = $movimientoInventario
                ->semillasRecolectadasMes(
                    $year,
                    $mes,
                    $unidad
                );
        } elseif ($mode === 'cuatrimestre') {

            switch ($cuatrimestre) {

                case 1:
                    $mesInicio = 1;
                    $mesFin = 4;
                    break;

                case 2:
                    $mesInicio = 5;
                    $mesFin = 8;
                    break;

                case 3:
                    $mesInicio = 9;
                    $mesFin = 12;
                    break;

                default:
                    $mesInicio = 1;
                    $mesFin = 4;
            }

            $semillas = $movimientoInventario
                ->semillasRecolectadasCuatrimestre(
                    $year,
                    $mesInicio,
                    $mesFin,
                    $unidad
                );
        } else {

            $semillas = $movimientoInventario
                ->semillasRecolectadasAnio(
                    $year,
                    $unidad
                );
        }

        $years = $movimientoInventario->years();
        $yearArray = [];
        foreach ($years as $value) {
            $yearArray[] = (int) $value['ano'];
        }

        echo json_encode([
            "years" => $yearArray,
            "data" => $semillas
        ]);

        break;
    case 'plantulas_recolectadas':

        $mode = $_GET['mode'] ?? 'mes';

        $mes = !empty($_GET['mes'])
            ? (int) $_GET['mes']
            : date('n');

        $cuatrimestre = !empty($_GET['cuatrimestre'])
            ? (int) $_GET['cuatrimestre']
            : 1;

        switch ($mode) {

            case 'mes':

                $plantulas = $movimientoInventario
                    ->plantulasRecolectadasMes($year, $mes);
                break;

            case 'cuatrimestre':

                switch ($cuatrimestre) {
                    case 1:
                        $inicio = 1;
                        $fin = 4;
                        break;
                    case 2:
                        $inicio = 5;
                        $fin = 8;
                        break;
                    case 3:
                        $inicio = 9;
                        $fin = 12;
                        break;
                    default:
                        $inicio = 1;
                        $fin = 4;
                }

                $plantulas = $movimientoInventario
                    ->plantulasRecolectadasCuatrimestre($year, $inicio, $fin);

                break;

            default:
                $plantulas = $movimientoInventario
                    ->plantulasRecolectadasAnio($year);
                break;
        }

        $years = $movimientoInventario->years();
        $yearArray = [];
        foreach ($years as $value) {
            $yearArray[] = (int) $value['ano'];
        }

        echo json_encode([
            'years' => $yearArray,
            'data'  => $plantulas,
        ]);

        break;
}
