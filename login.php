<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Crear una nueva instancia de la clase conexión
$conexion = new Conexion();
$conn = $conexion->getConexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que las claves existen en el array $_POST
    if (isset($_POST['usuario']) && isset($_POST['contraseña'])) {
        // Capturar los datos del formulario
        $usuario = $_POST['usuario'];
        $password = $_POST['contraseña'];

        if ($conn) {
            // Consulta para verificar si el usuario existe en la base de datos
            $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si se encontró el usuario y si la contraseña es correcta
            if ($user) { // Verifica si el usuario existe
                if (password_verify($password, $user['contraseña'])) {
                    // Credenciales correctas, iniciar sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['usuario'] = $user['usuario'];
                    header("Location: index.php"); // Redirigir a la página de inicio o panel
                    exit;
                } else {
                    echo "Usuario o contraseña incorrectos."; // Mensaje si la contraseña es incorrecta
                }
            } else {
                echo "Usuario o contraseña incorrectos."; // Mensaje si el usuario no existe
            }
        } else {
            echo "Error al conectar con la base de datos."; // Mensaje si hay error en la conexión
        }
    } else {
        echo "Por favor, completa todos los campos del formulario."; // Mensaje si faltan campos
    }
}
?>
