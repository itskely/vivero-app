<?php
session_start();
include __DIR__ . "/../config/database.php";

# Prueba de devuelta json
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    die(json_encode(
        [
            "error" => "No autenticado"
        ]
    ));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(
        [
            "error" => "Método no permitido"
        ]
    ));
}

$planta_id = $_POST['planta_id'] ?? null;
$etapa_id = $_POST['etapa_id'] ?? null;
$ubicacion_origen_id = $_POST['ubicacion_origen_id'] ?? null;
$ubicacion_destino_id = $_POST['ubicacion_destino_id'] ?? null;
$usuario_id = $_SESSION['usuario']['id'] ?? null;
$motivo = $_POST['motivo'] ?? "";

$db = new Database();
$conn = $db->connect();

// Call SP
$stmt = $conn->prepare("CALL sp_unificar_inventario(:planta_id, :etapa_id, :ubicacion_origen_id, :ubicacion_destino_id, :usuario_id, :motivo)");
$stmt->bindParam(":planta_id", $planta_id);
$stmt->bindParam(":etapa_id", $etapa_id);
$stmt->bindParam(":ubicacion_origen_id", $ubicacion_origen_id);
$stmt->bindParam(":ubicacion_destino_id", $ubicacion_destino_id);
$stmt->bindParam(":motivo", $motivo);
$stmt->bindParam(":usuario_id", $usuario_id);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(
    [
        "success" => true,
        "message" => $resultado["resultado"],
    ],
    JSON_NUMERIC_CHECK
);
