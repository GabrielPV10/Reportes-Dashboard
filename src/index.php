<?php
// Este archivo sirve como puente.
// Cuando entras a localhost, Nginx carga este archivo primero.
// Este archivo te redirige a la carpeta pública donde está el login.
header('Location: public/index.php');
exit();
?>