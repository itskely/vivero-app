<?php

# Prueba de devuelta json

header('Content-Type: application/json');
echo json_encode([
    [
        "id" => 1,
        "lote" => "AGU-2024-001",
        "especie" => "Aguacate Hass",
        "cantidad" => 430,
        "unidad_medida" => "unidades",
        "etapa" => "Listo para Venta"
    ],
    [
        "id" => 2,
        "lote" => "AGU-2024-002",
        "especie" => "Aguacate Hass",
        "cantidad" => 450,
        "unidad_medida" => "unidades",
        "etapa" => "Germinación"
    ],
    [
        "id" => 3,
        "lote" => "AGU-2024-003",
        "especie" => "Aguacate Hass",
        "cantidad" => 450,
        "unidad_medida" => "unidades",
        "etapa" => "Germinación",
    ],
    [
        "id" => 4,
        "lote" => "AGU-2024-004",
        "especie" => "Aguacate Hass",
        "cantidad" => 450,
        "unidad_medida" => "unidades",
        "etapa" => "Germinación"
    ],
    [
        "id" => 5,
        "lote" => "AGU-2024-005",
        "especie" => "Aguacate Hass",
        "cantidad" => 450,
        "unidad_medida" => "unidades",
        "etapa" => "Germinación"
    ],
    [
        "id" => 6,
        "lote" => "AGU-2024-006",
        "especie" => "Aguacate Hass",
        "cantidad" => 450,
        "unidad_medida" => "unidades",
        "etapa" => "Germinación"
    ],
]);