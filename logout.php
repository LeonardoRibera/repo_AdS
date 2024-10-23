<?php
session_start(); // Inicia la sesión
session_destroy(); // Destruye la sesión
// Opcional: Para eliminar la cookie de sesión
setcookie(session_name(), '', time() - 3600); // Establece la cookie con un tiempo pasado
header("Location: login.html"); // Redirige a la página de inicio de sesión
exit(); // Asegura que no se ejecute más código
?>
