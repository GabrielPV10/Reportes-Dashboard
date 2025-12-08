<?php
// Ubicación: src/controllers/ReportController.php
header('Content-Type: application/json');
session_start();

// CAMBIO: Salimos de "controllers" y entramos a "config"
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$compania_id = $_SESSION['compania_id'] ?? 1;
$reporte = $_GET['reporte'] ?? 'ventas_por_categoria';

$sql = "";

switch ($reporte) {
    case 'ventas_por_ciudad':
        $sql = "SELECT u.ciudad, SUM(v.monto_total) as total FROM FactVentas v JOIN DimUbicacion u ON v.ubicacion_id = u.ubicacion_id WHERE v.compania_id = ? GROUP BY u.ciudad ORDER BY total DESC";
        break;

    // --- REPORTES DE TIEMPO MEJORADOS ---
    case 'ventas_diarias':
        $sql = "SELECT t.fecha, SUM(v.monto_total) as total FROM FactVentas v JOIN DimTiempo t ON v.fecha_id = t.fecha_id WHERE v.compania_id = ? GROUP BY t.fecha ORDER BY t.fecha ASC";
        break;
    case 'ventas_semanales':
        $sql = "SELECT CONCAT(YEAR(t.fecha), '-W', WEEK(t.fecha, 1)) as semana, SUM(v.monto_total) as total FROM FactVentas v JOIN DimTiempo t ON v.fecha_id = t.fecha_id WHERE v.compania_id = ? GROUP BY semana ORDER BY semana ASC";
        break;
    case 'ventas_mensuales':
        $sql = "SELECT DATE_FORMAT(t.fecha, '%Y-%m') as mes, SUM(v.monto_total) as total FROM FactVentas v JOIN DimTiempo t ON v.fecha_id = t.fecha_id WHERE v.compania_id = ? GROUP BY mes ORDER BY mes ASC";
        break;
        
    // --- NUEVOS REPORTES RECOMENDADOS ---
    case 'top_clientes_ventas':
        $sql = "SELECT c.nombre_cliente, SUM(v.monto_total) as total FROM FactVentas v JOIN DimCliente c ON v.cliente_id = c.cliente_id WHERE v.compania_id = ? GROUP BY c.nombre_cliente ORDER BY total DESC LIMIT 5";
        break;
    case 'top_productos_ventas':
        $sql = "SELECT p.nombre_producto, SUM(v.monto_total) as total FROM FactVentas v JOIN DimProducto p ON v.producto_id = p.producto_id WHERE v.compania_id = ? GROUP BY p.nombre_producto ORDER BY total DESC LIMIT 5";
        break;
    case 'top_productos_unidades':
        $sql = "SELECT p.nombre_producto, SUM(v.cantidad_vendida) as total FROM FactVentas v JOIN DimProducto p ON v.producto_id = p.producto_id WHERE v.compania_id = ? GROUP BY p.nombre_producto ORDER BY total DESC LIMIT 5";
        break;

    case 'ventas_por_categoria':
    default:
        $sql = "SELECT p.categoria, SUM(v.monto_total) as total FROM FactVentas v JOIN DimProducto p ON v.producto_id = p.producto_id WHERE v.compania_id = ? GROUP BY p.categoria ORDER BY total DESC";
        break;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $compania_id);
$stmt->execute();
$resultado = $stmt->get_result();

$labels = [];
$data = [];
while($fila = $resultado->fetch_assoc()) {
    $labels[] = array_values($fila)[0];
    $data[] = array_values($fila)[1];
}

echo json_encode(['labels' => $labels, 'data' => $data]);
$conn->close();
?>