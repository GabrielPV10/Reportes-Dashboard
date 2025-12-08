<?php
// Ubicación: src/includes/session.php

// Asegura que no haya espacios antes de <?php para evitar error de Headers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario está logueado y tiene el rol correcto.
 * @param int|null $rol_requerido (Opcional) 1 para Admin, 2 para Analista
 */
function verificarSesion($rol_requerido = null) {
    // 1. Verificar si hay usuario
    if (!isset($_SESSION['usuario_id'])) {
        // Redirigir al login (ajustamos la ruta asumiendo que se llama desde views)
        header('Location: ../../public/index.php');
        exit();
    }

    // 2. Verificar rol (si se pide uno específico)
    if ($rol_requerido !== null && $_SESSION['rol_id'] != $rol_requerido) {
        // Si no tiene permiso, lo mandamos fuera o a una página de error
        header('Location: ../../public/index.php?error=acceso_denegado');
        exit();
    }
    
    // 3. Verificar compania_id (Bug Crítico resuelto)
    if (!isset($_SESSION['compania_id'])) {
        // Si por error del sistema no tiene compañía, logout forzoso
        session_destroy();
        header('Location: ../../public/index.php?error=error_compania');
        exit();
    }
}
?>