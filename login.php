<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usuario']) && isset($_POST['contraseña'])) {
        $usuario = $_POST['usuario'];
        $password = $_POST['contraseña'];

        $conexion = new Conexion();
        $conn = $conexion->getConexion();

        if ($conn) {
            $sql = "SELECT * FROM usuarios WHERE usuario = :usuario OR gmail = :gmail";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':gmail', $usuario);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar las credenciales
            if ($user && password_verify($password, $user['contraseña'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['usuario'] = $user['usuario'];
                header("Location: index.php");
                exit;
            } else {
                // Redirigir a login.php con un mensaje de alerta
                header("Location: login.html?error=error-usuario");
                exit;
            }
        }
    }
}
