<?php
// Ubicación: src/controllers/AdminChartController.php
header('Content-Type: application/json');
session_start();

// CAMBIO: Ruta relativa corregida
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['labels' => [], 'data' => []]);
    exit();
}

$compania_id = $_SESSION['compania_id'] ?? 1;

$sql = "SELECT 
            t.fecha, 
            SUM(v.monto_total) as total_diario
        FROM FactVentas v
        JOIN DimTiempo t ON v.fecha_id = t.fecha_id
        WHERE v.compania_id = ?
        GROUP BY t.fecha
        ORDER BY t.fecha ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $compania_id);
$stmt->execute();
$resultado = $stmt->get_result();

$labels = []; 
$data = []; 

while($fila = $resultado->fetch_assoc()) {
    $labels[] = $fila['fecha'];
    $data[] = $fila['total_diario'];
}

echo json_encode(['labels' => $labels, 'data' => $data]);
$conn->close();
?>