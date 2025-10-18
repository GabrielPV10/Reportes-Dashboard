<?php //ya quedo
session_start(); // Reanuda la sesión actual

// Destruye todas las variables de la sesión
$_SESSION = array();

// Finalmente, destruye la sesión.
session_destroy();

// Redirige al usuario a la página de inicio de sesión
header("location: index.php");
exit;
?>