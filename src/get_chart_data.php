<?php // Ya quedo 8
// Especificamos que la respuesta será en formato JSON
header('Content-Type: application/json');

// --- INICIO DE LA SOLUCIÓN ---
// 1. Reanudamos la sesión para saber quién está pidiendo los datos
session_start();
// --- FIN DE LA SOLUCIÓN ---

require_once 'conectar.php';

// Verificamos si hay un usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si no hay nadie, devolvemos datos vacíos para no dar error
    echo json_encode(['labels' => [], 'data' => []]);
    exit();
}

// Obtenemos el ID de la compañía del usuario en sesión
$compania_id = $_SESSION['compania_id'] ?? 1; // Usamos 1 como default

// 2. La consulta SQL ahora filtra por el compania_id
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