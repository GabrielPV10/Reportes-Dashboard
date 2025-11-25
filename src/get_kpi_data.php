<?php //Ya quedo 6
// Especificamos que la respuesta será en formato JSON
header('Content-Type: application/json');

session_start();
require_once 'conectar.php';

// Verificamos si hay un usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si no, devolvemos ceros para no dar error
    echo json_encode(['total_ventas' => '$0.00', 'clientes_unicos' => 0, 'productos_vendidos' => 0]);
    exit();
}

// Obtenemos el ID de la compañía del usuario en sesión
$compania_id = $_SESSION['compania_id'] ?? 1;

// --- LA CONSULTA MÁGICA PARA LOS KPIs ---
// Hacemos todos los cálculos en una sola consulta eficiente
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

// Formateamos los datos para que se vean bien
$respuesta = [
    'total_ventas' => '$' . number_format($resultado['total_ventas'], 2),
    'clientes_unicos' => $resultado['clientes_unicos'],
    'productos_vendidos' => $resultado['productos_vendidos']
];

echo json_encode($respuesta);

$conn->close();
?>