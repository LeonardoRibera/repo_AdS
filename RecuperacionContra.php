<?php
session_start();
include 'conexion.php'; // Asegúrate de que este archivo contiene la clase de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gmail'])) {
    $gmail = $_POST['gmail'];

    // Crear conexión a la base de datos
    $conexion = new Conexion();
    $conn = $conexion->getConexion();

    // Verificar si el correo existe
    $sql = "SELECT * FROM usuarios WHERE gmail = :gmail";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':gmail', $gmail);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generar un token único
        $token = bin2hex(random_bytes(50));

        // Guardar el token en la base de datos
        $sql = "UPDATE usuarios SET token = :token WHERE gmail = :gmail";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':gmail', $gmail); // Cambié 'email' a 'gmail' para que coincida
        $stmt->execute();

        // Enviar el correo
        $to = $gmail;
        $subject = "Recuperación de Contraseña";
        $message = "Haz clic en el siguiente enlace para recuperar tu contraseña: ";
        $message .= "http://localhost/repo_AdS/reset_password.php?token=" . $token; // Cambia a la ruta correcta
        $headers = "From: nachoarancibia06@gmail.com"; // Cambia a tu correo de Gmail

        if (mail($to, $subject, $message, $headers)) {
            echo "Se ha enviado un enlace de recuperación a tu correo.";
        } else {
            echo "Error al enviar el correo.";
        }
    } else {
        echo "El correo electrónico no está registrado.";
    }
} else {
    echo "Método no permitido.";
}
?>
