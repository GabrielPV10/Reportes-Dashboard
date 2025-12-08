<?php
// Ubicación: src/controllers/KpiController.php
header('Content-Type: application/json');
session_start();

// CAMBIO: Ruta relativa corregida
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['total_ventas' => '$0.00', 'clientes_unicos' => 0, 'productos_vendidos' => 0]);
    exit();
}

$compania_id = $_SESSION['compania_id'] ?? 1;

$sql = "SELECT 
            COALESCE(SUM(monto_total), 0) as total_ventas,
            COALESCE(COUNT(DISTINCT cliente_id), 0) as clientes_unicos,
            COALESCE(SUM(cantidad_vendida), 0) as productos_vendidos
        FROM FactVentas
        WHERE compania_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $compania_id);
$stmt->execute();
$resultado = $stmt->get_result()->fetch_assoc();

$respuesta = [
    'total_ventas' => '$' . number_format($resultado['total_ventas'], 2),
    'clientes_unicos' => $resultado['clientes_unicos'],
    'productos_vendidos' => $resultado['productos_vendidos']
];

echo json_encode($respuesta);
$conn->close();
?>