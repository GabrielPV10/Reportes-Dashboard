<?php //Ya quedo 1
session_start();
require_once 'conectar.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date; // Volvemos a necesitar esta herramienta

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    die("Acceso no autorizado.");
}

if (isset($_FILES['archivo_excel']) && $_FILES['archivo_excel']['error'] == 0) {
    
    $archivo_tmp = $_FILES['archivo_excel']['tmp_name'];

    try {
        $conn->begin_transaction();
        $spreadsheet = IOFactory::load($archivo_tmp);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $filas_nuevas = 0;
        $filas_omitidas = 0;

        foreach ($worksheet->getRowIterator() as $fila) {
            if ($fila->getRowIndex() == 1) { continue; }

            $celdas = $fila->getCellIterator();
            $celdas->setIterateOnlyExistingCells(false);
            $datosFila = [];
            foreach ($celdas as $celda) { $datosFila[] = $celda->getValue(); }

            $fecha_excel = $datosFila[0]; $nombre_cliente = $datosFila[1]; $nombre_producto = $datosFila[2];
            $categoria = $datosFila[3]; $cantidad = (int)$datosFila[4]; $precio_unitario = (float)$datosFila[5];
            $monto = (float)$datosFila[6]; $tienda = $datosFila[7]; $ciudad = $datosFila[8];
            $compania_id = $_SESSION['compania_id'] ?? 1;

            // --- INICIO DE LA SOLUCIÓN FINAL PARA FECHAS ---
            // 1. Verificamos si el dato de la fecha es numérico (formato Excel)
            if (is_numeric($fecha_excel)) {
                // Si es número, usamos la herramienta de la librería para convertirlo
                $fecha_php = Date::excelToDateTimeObject($fecha_excel);
            } else {
                // Si es texto, usamos la herramienta flexible de PHP
                $fecha_php = new DateTime($fecha_excel);
            }
            // --- FIN DE LA SOLUCIÓN FINAL PARA FECHAS ---

            // El resto del código para procesar dimensiones es el mismo...
            $fecha_sql = $fecha_php->format('Y-m-d');
            $stmt = $conn->prepare("SELECT fecha_id FROM DimTiempo WHERE fecha = ?"); $stmt->bind_param("s", $fecha_sql); $stmt->execute(); $res = $stmt->get_result();
            if ($res->num_rows > 0) { $fecha_id = $res->fetch_assoc()['fecha_id']; } else {
                $anio = (int)$fecha_php->format('Y'); $mes = (int)$fecha_php->format('m'); $nombre_mes = "Mes"; $dia = (int)$fecha_php->format('d'); $nombre_dia = "Dia"; $trimestre = 1;
                $stmt = $conn->prepare("INSERT INTO DimTiempo (fecha, anio, mes, nombre_mes, dia, nombre_dia, trimestre) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("siisisi", $fecha_sql, $anio, $mes, $nombre_mes, $dia, $nombre_dia, $trimestre); $stmt->execute(); $fecha_id = $stmt->insert_id;
            }
            
            $stmt = $conn->prepare("SELECT producto_id FROM DimProducto WHERE nombre_producto = ? AND compania_id = ?"); $stmt->bind_param("si", $nombre_producto, $compania_id); $stmt->execute(); $res = $stmt->get_result();
            if ($res->num_rows > 0) { $producto_id = $res->fetch_assoc()['producto_id']; } else {
                $stmt = $conn->prepare("INSERT INTO DimProducto (nombre_producto, categoria, compania_id) VALUES (?, ?, ?)"); $stmt->bind_param("ssi", $nombre_producto, $categoria, $compania_id); $stmt->execute(); $producto_id = $stmt->insert_id;
            }
            
            $stmt = $conn->prepare("SELECT cliente_id FROM DimCliente WHERE nombre_cliente = ? AND compania_id = ?"); $stmt->bind_param("si", $nombre_cliente, $compania_id); $stmt->execute(); $res = $stmt->get_result();
            if ($res->num_rows > 0) { $cliente_id = $res->fetch_assoc()['cliente_id']; } else {
                $stmt = $conn->prepare("INSERT INTO DimCliente (nombre_cliente, compania_id) VALUES (?, ?)"); $stmt->bind_param("si", $nombre_cliente, $compania_id); $stmt->execute(); $cliente_id = $stmt->insert_id;
            }
            
            $stmt = $conn->prepare("SELECT ubicacion_id FROM DimUbicacion WHERE tienda = ? AND ciudad = ?"); $stmt->bind_param("ss", $tienda, $ciudad); $stmt->execute(); $res = $stmt->get_result();
            if ($res->num_rows > 0) { $ubicacion_id = $res->fetch_assoc()['ubicacion_id']; } else {
                $pais = "México"; $stmt = $conn->prepare("INSERT INTO DimUbicacion (tienda, ciudad, pais) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $tienda, $ciudad, $pais); $stmt->execute(); $ubicacion_id = $stmt->insert_id;
            }
            
            $stmt_check = $conn->prepare("SELECT venta_id FROM FactVentas WHERE fecha_id = ? AND producto_id = ? AND cliente_id = ? AND cantidad_vendida = ? AND monto_total = ?");
            $stmt_check->bind_param("iiidd", $fecha_id, $producto_id, $cliente_id, $cantidad, $monto);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result();

            if ($res_check->num_rows === 0) {
                $stmt = $conn->prepare("INSERT INTO FactVentas (fecha_id, producto_id, cliente_id, ubicacion_id, compania_id, cantidad_vendida, precio_unitario, monto_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiiiddd", $fecha_id, $producto_id, $cliente_id, $ubicacion_id, $compania_id, $cantidad, $precio_unitario, $monto);
                $stmt->execute();
                $filas_nuevas++;
            } else {
                $filas_omitidas++;
            }
        }

        $conn->commit();
        echo "<h1>Proceso Completado</h1>";
        echo "<p><b>Filas nuevas insertadas:</b> {$filas_nuevas}</p>";
        echo "<p><b>Filas duplicadas omitidas:</b> {$filas_omitidas}</p>";
        echo '<a href="dashboard_admin.php">Volver al Dashboard</a>';

    } catch (Exception $e) {
        $conn->rollback();
        die('Error al procesar el archivo: ' . $e->getMessage());
    }
} else {
    echo "<h1>Error</h1> <p>No se subió ningún archivo o hubo un error en la subida.</p>";
}
$conn->close();
?>