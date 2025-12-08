<?php
require_once '../includes/session.php'; // Usamos el helper
verificarSesion(1); // Solo admin

require_once '../config/database.php';
require '../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date; 

if (isset($_FILES['archivo_excel']) && $_FILES['archivo_excel']['error'] == 0) {
    
    // VALIDACIÓN 1: Extensión del archivo
    $allowed = ['xlsx', 'xls'];
    $filename = $_FILES['archivo_excel']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), $allowed)) {
        die("Error: Solo se permiten archivos Excel (.xlsx, .xls)");
    }

    $archivo_tmp = $_FILES['archivo_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($archivo_tmp);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Iniciamos transacción
        $conn->begin_transaction();
        
        $filas_nuevas = 0;
        $filas_omitidas = 0;
        $fila_actual = 0;

        foreach ($worksheet->getRowIterator() as $fila) {
            $fila_actual++;
            if ($fila->getRowIndex() == 1) { continue; } // Saltar cabecera

            $celdas = $fila->getCellIterator();
            $celdas->setIterateOnlyExistingCells(false);
            $datosFila = [];
            foreach ($celdas as $celda) { $datosFila[] = $celda->getValue(); }

            // VALIDACIÓN 2: Estructura de columnas (Esperamos 9 columnas: índice 0 al 8)
            if (count($datosFila) < 9) {
                throw new Exception("La fila $fila_actual no tiene suficientes columnas.");
            }

            // Mapeo de datos
            $fecha_excel = $datosFila[0]; 
            $nombre_cliente = $datosFila[1]; 
            $nombre_producto = $datosFila[2];
            $categoria = $datosFila[3]; 
            $cantidad = $datosFila[4]; 
            $precio_unitario = $datosFila[5];
            $monto = $datosFila[6]; 
            $tienda = $datosFila[7]; 
            $ciudad = $datosFila[8];
            
            // VALIDACIÓN 3: Datos numéricos críticos
            if (!is_numeric($cantidad) || !is_numeric($monto)) {
                 // Opción A: Lanzar error y cancelar todo
                 throw new Exception("Datos inválidos en fila $fila_actual: Cantidad o Monto no son números.");
                 // Opción B (alternativa): continue; // Para saltar solo esta fila
            }

            // Convertir a tipos correctos
            $cantidad = (int)$cantidad;
            $precio_unitario = (float)$precio_unitario;
            $monto = (float)$monto;
            $compania_id = $_SESSION['compania_id'];

            // --- MANEJO DE FECHA ROBUSTO ---
            if (is_numeric($fecha_excel)) {
                $fecha_php = Date::excelToDateTimeObject($fecha_excel);
            } else {
                // Intentar parsear texto
                try {
                    $fecha_php = new DateTime($fecha_excel);
                } catch (Exception $e) {
                     throw new Exception("Formato de fecha inválido en fila $fila_actual");
                }
            }
            $fecha_sql = $fecha_php->format('Y-m-d');

            // ... (AQUÍ SIGUE TU LÓGICA DE DIMENSIONES Y FACT TABLE IGUAL QUE ANTES) ...
            // Solo asegurate de usar prepared statements en todo como ya tenías.
            // Copia aquí el resto de la lógica de inserción de dimensiones...
            
            // Ejemplo rápido de dimensión tiempo (resumido):
            $stmt = $conn->prepare("SELECT fecha_id FROM DimTiempo WHERE fecha = ?"); 
            $stmt->bind_param("s", $fecha_sql); 
            $stmt->execute(); 
            $res = $stmt->get_result();
            if ($res->num_rows > 0) { 
                $fecha_id = $res->fetch_assoc()['fecha_id']; 
            } else {
                $anio = (int)$fecha_php->format('Y'); $mes = (int)$fecha_php->format('m');
                // ... rellenar datos ...
                $stmt = $conn->prepare("INSERT INTO DimTiempo (fecha, anio, mes) VALUES (?, ?, ?)");
                // Nota: Ajusta los parámetros según tu tabla real
                $stmt->bind_param("sii", $fecha_sql, $anio, $mes); 
                $stmt->execute(); 
                $fecha_id = $stmt->insert_id;
            }

            // ... Repetir para Producto, Cliente, Ubicación ...
            
            // Inserción en FactVentas
             $stmt = $conn->prepare("INSERT INTO FactVentas (fecha_id, producto_id, cliente_id, ubicacion_id, compania_id, cantidad_vendida, precio_unitario, monto_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
             // Asegúrate de tener las variables $producto_id, $cliente_id, $ubicacion_id definidas antes
             // $stmt->bind_param(...)
             // $stmt->execute();
             $filas_nuevas++;
        }

        $conn->commit();
        echo "<h1>Éxito</h1><p>Se procesaron $filas_nuevas filas.</p>";
        echo '<a href="../views/dashboard/admin.php">Volver</a>';

    } catch (Exception $e) {
        $conn->rollback();
        die("<h1>Error Crítico</h1><p>" . $e->getMessage() . "</p><a href='../views/dashboard/admin.php'>Volver</a>");
    }
} else {
    header('Location: ../views/dashboard/admin.php?error=no_archivo');
}
?>