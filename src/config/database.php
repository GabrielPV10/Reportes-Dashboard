<?php
// --- Datos de tu docker-compose.yml ---
$host = 'db';        // Nombre del servicio de la base de datos en Docker
$user = 'root';   // El nuevo usuario de la aplicación
$pass = '12345678'; // La contraseña para el 'usuario'
$db   = 'reporte_db';   // El nombre de la base de datos

// Crear la conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar si la conexión falló
if ($conn->connect_error) {
    // Detener la ejecución y mostrar el error
    die("Conexión fallida: " . $conn->connect_error);
}
?>