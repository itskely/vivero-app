<?php

# Prueba de devuelta json

header('Content-Type: application/json');
echo json_encode(
    [
        [
            "id" => 1,
            "etapa" => "Semillas",
            "ubicacion" => "Tingua",
            "cantidad" => 200,
            "unidad_medida" => "unidades",
            "etapa_id" => 2,
            "ubicacion_id" => 2
        ],
        [
            "id" => 2,
            "etapa" => "Germinacion",
            "ubicacion" => "Tingua",
            "cantidad" => 100,
            "unidad_medida" => "unidades",
            "etapa_id" => 3,
            "ubicacion_id" => 3
        ],
        [
            "id" => 3,
            "etapa" => "Germinacion",
            "ubicacion" => "Arrieros",
            "cantidad" => 450,
            "unidad_medida" => "unidades",
            "etapa_id" => 2,
            "ubicacion_id" => 2
        ],
    ]
);