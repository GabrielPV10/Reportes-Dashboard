<?php //Ya quedo 5
header('Content-Type: application/json');
session_start();
require_once 'conectar.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['labels' => [], 'data' => []]);
    exit();
}

$compania_id = $_SESSION['compania_id'] ?? 1;

// Consulta para obtener el total de ventas agrupado por fecha
$sql = "SELECT 
            t.fecha, 
            SUM(v.monto_total) as total_diario
        FROM FactVentas v
        JOIN DimTiempo t ON v.fecha_id = t.fecha_id
        WHERE v.compania_id = ?
        GROUP BY t.fecha
        ORDER BY t.fecha ASC"; // Ordenamos por fecha para el gráfico de línea

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $compania_id);
$stmt->execute();
$resultado = $stmt->get_result();

$labels = []; // Fechas (eje X)
$data = [];   // Total de ventas (eje Y)

while($fila = $resultado->fetch_assoc()) {
    $labels[] = $fila['fecha'];
    $data[] = $fila['total_diario'];
}

echo json_encode(['labels' => $labels, 'data' => $data]);

$conn->close();
?>