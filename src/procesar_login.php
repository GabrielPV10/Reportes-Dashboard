<?php // YT auqedo
// ¡Paso 1 crucial! Iniciar la sesión.
session_start();

require_once 'conectar.php';

$email = $_POST['email'];
$password = $_POST['password'];

// Preparamos la consulta para obtener todos los datos que necesitaremos
$sql = "SELECT usuario_id, nombre_completo, password_hash, rol_id FROM Usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    // Verificar la contraseña hasheada
    if (password_verify($password, $usuario['password_hash'])) {
        // La contraseña es correcta. ¡Guardamos los datos en la sesión!
        $_SESSION['usuario_id'] = $usuario['usuario_id'];
        $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
        $_SESSION['rol_id'] = $usuario['rol_id'];

        // --- LA LÓGICA DE REDIRECCIÓN ---
        if ($usuario['rol_id'] == 1) {
            // Si el rol es 1 (Administrador), lo mandamos a su dashboard
            header('Location: dashboard_admin.php');
            exit(); // Es importante terminar el script después de redirigir
        } elseif ($usuario['rol_id'] == 2) {
            // Si el rol es 2 (Analista), lo mandamos a su dashboard
            header('Location: dashboard_analista.php');
            exit();
        } else {
            // Por si hay algún otro rol no definido
            echo "<h1>Rol no reconocido.</h1>";
        }

    } else {
        echo "<h1>Error</h1><p>La contraseña es incorrecta.</p>";
        echo '<a href="index.php">Intentar de nuevo</a>';
    }
} else {
    echo "<h1>Error</h1><p>No se encontró un usuario con ese correo electrónico.</p>";
    echo '<a href="index.php">Intentar de nuevo</a>';
}

$stmt->close();
$conn->close();
?>