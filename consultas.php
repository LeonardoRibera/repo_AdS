<?php
include("conexion.php");

// Obtener la lista de tablas de la base de datos
$pps = $con->getConexion()->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
$pps->execute();
$tablas = $pps->fetchAll(PDO::FETCH_ASSOC);

// Procesar la inserción de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tabla'])) {
    $nombreTabla = $_POST['tabla'];
    $columnas = $_POST['columnas'];

    // Inicializar un arreglo para los valores
    $valores = [];
    $finalColumnas = [];

    foreach ($columnas as $columna) {
        if (isset($_POST[$columna])) {
            $valores[] = $_POST[$columna]; // Asigna todos los valores de los inputs normales
            $finalColumnas[] = $columna;
        }
    }

    // Verificar si el campo 'estado' (opciones) existe y agregarlo
    if (isset($_POST['opciones'])) {
        $valores[] = $_POST['opciones'];

        switch ($_POST['opciones']) {
            case 'En stock':
            case 'En espera':
                $finalColumnas[] = 'estado';
                break;
            
            case 'Pendiente':
            case 'Completada':
                $finalColumnas[] = 'estado_transaccion';
                break;
    
            default:
                echo "<p>Error: Valor no reconocido en el campo de opciones.</p>";
                break;
        }
    }

    // Preparar la consulta de inserción con los campos finales
    $placeholders = rtrim(str_repeat('?, ', count($valores)), ', ');
    $sql = "INSERT INTO $nombreTabla (" . implode(',', $finalColumnas) . ") VALUES ($placeholders)";
    $pps = $con->getConexion()->prepare($sql);
    
    // Ejecutar la consulta con los valores
    try {
        $pps->execute($valores); // Usar el arreglo de valores
        echo "<p>Registro insertado correctamente.</p>";
        header("Location: ?tabla=$nombreTabla"); // Redireccionar a la misma página para ver la tabla actualizada
        exit;
    } catch (PDOException $e) {
        echo "<p>Error al insertar el registro: " . $e->getMessage() . "</p>";
    }
}

// Procesar la eliminación de un registro
if (isset($_GET['eliminar']) && isset($_GET['tabla']) && isset($_GET['id'])) {
    $nombreTabla = $_GET['tabla'];
    $id = $_GET['id'];

    // Obtener el nombre de la clave primaria
    $pps = $con->getConexion()->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = (SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = ? AND CONSTRAINT_TYPE = 'PRIMARY KEY')");
    $pps->execute([$nombreTabla, $nombreTabla]);
    $clavePrimaria = $pps->fetchColumn();

    // Verificar si el registro existe
    $checkSql = "SELECT COUNT(*) FROM $nombreTabla WHERE $clavePrimaria = ?";
    $checkPps = $con->getConexion()->prepare($checkSql);
    $checkPps->execute([$id]);
    $exists = $checkPps->fetchColumn();

    if ($exists > 0) {
        // Eliminar el registro usando la clave primaria
        $sql = "DELETE FROM $nombreTabla WHERE $clavePrimaria = ?";
        try {
            $pps = $con->getConexion()->prepare($sql);
            $pps->execute([$id]);
            header("Location: ?tabla=$nombreTabla"); // Redireccionar para ver la tabla actualizada
            exit;
        } catch (PDOException $e) {
            echo "<p>Error al eliminar el registro: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>No se encontró el registro para eliminar.</p>";
    }
}
?>
