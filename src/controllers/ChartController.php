<?php
// Ubicación: src/controllers/ChartController.php
header('Content-Type: application/json');

session_start();

// CORRECCIÓN: Ruta relativa hacia config
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['labels' => [], 'data' => []]);
    exit();
}

$compania_id = $_SESSION['compania_id'] ?? 1;

$sql = "SELECT 
            p.categoria, 
            SUM(v.monto_total) as total_ventas
        FROM FactVentas v
        JOIN DimProducto p ON v.producto_id = p.producto_id
        WHERE v.compania_id = ? 
        GROUP BY p.categoria
        ORDER BY total_ventas DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $compania_id);
$stmt->execute();
$resultado = $stmt->get_result();

$labels = [];
$data = [];

while($fila = $resultado->fetch_assoc()) {
    $labels[] = $fila['categoria'];
    $data[] = $fila['total_ventas'];
}

$respuesta = [
    'labels' => $labels,
    'data' => $data
];

echo json_encode($respuesta);

$conn->close();
?>