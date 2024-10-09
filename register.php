<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include 'conexion.php'; // Asegúrate de tener este archivo con la configuración correcta de la base de datos

// Crear una nueva instancia de la clase conexión
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Verificar que el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $username = $_POST['usuario'];
    $email = $_POST['email'];
    $password = $_POST['contraseña'];
    $confirm_password = $_POST['confirmar_contraseña'];

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }

    // Validar si se aceptaron los términos y condiciones
    if (!isset($_POST['terms'])) {
        echo "Debes aceptar los términos y condiciones.";
        exit;
    }

    // Encriptar la contraseña para almacenarla de manera segura
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertar los datos del usuario en la base de datos
    if ($conn) {
        $sql = "INSERT INTO usuarios (usuario, email, contraseña, created_at) 
                VALUES (:usuario, :email, :contrasena, GETDATE())"; // Cambié :contraseña a :contrasena
        $stmt = $conn->prepare($sql);

        // Vincular los parámetros
        $stmt->bindParam(':usuario', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contrasena', $hashed_password); // Cambié :contraseña a :contrasena

        // Ejecutar la consulta
        try {
            if ($stmt->execute()) {
                echo "Registro exitoso. Ahora puedes <a href='login.html'>iniciar sesión</a>.";
            } else {
                echo "Error al registrar el usuario.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error al conectar con la base de datos.";
    }
}
?>
