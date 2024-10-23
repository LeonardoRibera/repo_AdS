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
    $username = $_POST['usuario'] ?? '';
    $gmail = $_POST['gmail'] ?? '';
    $password = $_POST['contraseña'] ?? '';
    $confirm_password = $_POST['confirmar_contraseña'] ?? '';

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        header("Location: register.html?error=contraseñas");
        exit;
    }

    // Validar si se aceptaron los términos y condiciones
    if (!isset($_POST['terms'])) {
        exit;
    }

    // Verificar si el correo electrónico ya está registrado
    $checkEmailSql = "SELECT COUNT(*) FROM usuarios WHERE gmail = :gmail";
    $checkEmailStmt = $conn->prepare($checkEmailSql);
    $checkEmailStmt->bindParam(':gmail', $gmail);

    // Depuración: muestra el correo electrónico que se está verificando
    $MensajeError = ""; // Inicializar variable
    if ($checkEmailStmt->execute()) {
        $count = $checkEmailStmt->fetchColumn();
        if ($count > 0) {
            header("Location: register.html?error=error-email");
            exit;
        }
    } else {
        echo "Error al verificar el correo electrónico.";
        exit;
    }

    // Encriptar la contraseña para almacenarla de manera segura
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Depuración: muestra los datos que se insertarán
    echo "Registrando usuario: $username, gmail: $gmail, Contraseña: $hashed_password <br>";

    // Insertar los datos del usuario en la base de datos
    $sql = "INSERT INTO usuarios (usuario, gmail, contraseña, created_at) 
            VALUES (:usuario, :gmail, :contrasena, GETDATE())"; // Usar GETDATE() para SQL Server
    $stmt = $conn->prepare($sql);

    // Vincular los parámetros
    $stmt->bindParam(':usuario', $username);
    $stmt->bindParam(':gmail', $gmail);
    $stmt->bindParam(':contrasena', $hashed_password);

    // Ejecutar la consulta
    try {
        if ($stmt->execute()) {
            header('Location: login.html');
            exit();
        } else {
            echo "Error al registrar el usuario.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Error al verificar el correo electrónico.";
    exit;
}
?>
