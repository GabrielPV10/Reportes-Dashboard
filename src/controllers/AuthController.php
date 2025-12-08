<?php
session_start();
require_once '../config/database.php';

// Limpieza básica de datos de entrada
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

if (!$email || !$password) {
    header('Location: ../public/index.php?error=campos_vacios');
    exit();
}

// MEJORA: Incluimos compania_id en la consulta
$sql = "SELECT usuario_id, nombre_completo, password_hash, rol_id, compania_id FROM Usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($password, $usuario['password_hash'])) {
        // Regenerar ID de sesión para prevenir Session Fixation (Seguridad extra)
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['usuario_id'];
        $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
        $_SESSION['rol_id'] = $usuario['rol_id'];
        // CORRECCIÓN CRÍTICA: Guardamos la compañía
        $_SESSION['compania_id'] = $usuario['compania_id']; 

        // Redirección
        if ($usuario['rol_id'] == 1) {
            header('Location: ../views/dashboard/admin.php');
        } elseif ($usuario['rol_id'] == 2) {
            header('Location: ../views/dashboard/analyst.php');
        } else {
            // Rol desconocido
            session_destroy();
            header('Location: ../public/index.php?error=rol_desconocido');
        }
        exit();

    } else {
        header('Location: ../public/index.php?error=credenciales_incorrectas');
    }
} else {
    header('Location: ../public/index.php?error=usuario_no_encontrado');
}

$stmt->close();
$conn->close();
?>