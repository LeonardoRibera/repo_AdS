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
    foreach ($columnas as $columna) {
        if (isset($_POST[$columna])) {
            $valores[] = $_POST[$columna]; // Asigna todos los valores
        }
    }

    // Preparar la consulta de inserción
    $placeholders = rtrim(str_repeat('?, ', count($valores)), ', ');
    $sql = "INSERT INTO $nombreTabla (" . implode(',', $columnas) . ") VALUES ($placeholders)";
    $pps = $con->getConexion()->prepare($sql);
    
    // Ejecutar la consulta con los valores
    try {
        $pps->execute($valores); // Usar el arreglo de valores
        echo "<p>Registro insertado correctamente.</p>";
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
        } catch (PDOException $e) {
            echo "<p>Error al eliminar el registro: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>No se encontró el registro para eliminar.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Datos</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Gestión de Datos</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link active" href="?">Home</a>
            <!-- Menú dinámico de tablas -->
            <?php foreach ($tablas as $tabla): ?>
                <a class="nav-item nav-link" href="?tabla=<?php echo $tabla['TABLE_NAME']; ?>">
                    <?php echo $tabla['TABLE_NAME']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php
    // Comprobar si se ha seleccionado una tabla
    if (isset($_GET['tabla'])) {
        $nombreTabla = $_GET['tabla'];

        // Consultar los datos de la tabla seleccionada
        $pps = $con->getConexion()->prepare("SELECT * FROM " . $nombreTabla);
        $pps->execute();
        $datosTabla = $pps->fetchAll(PDO::FETCH_ASSOC);

        // Mostrar el nombre de la tabla
        echo "<h2>Tabla: $nombreTabla</h2>";

        if (count($datosTabla) > 0) {
            // Mostrar la tabla HTML con los datos
            echo "<table class='table table-bordered'>";
            echo "<thead><tr>";

            // Encabezado de la tabla con los nombres de las columnas
            foreach (array_keys($datosTabla[0]) as $columna) {
                echo "<th>$columna</th>";
            }
            echo "<th>Acciones</th>"; // Columna para las acciones
            echo "</tr></thead><tbody>";

            // Filas con los datos de cada registro
            foreach ($datosTabla as $fila) {
                echo "<tr>";
                foreach ($fila as $valor) {
                    echo "<td>$valor</td>";
                }
                
                // Obtener el primer campo como clave para eliminar
                $id = $fila[array_key_first($fila)]; // Obtener el primer campo de la fila
                echo "<td>
                    <a href='?tabla=$nombreTabla&eliminar=true&id=$id' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este registro?\");'>Eliminar</a>
                </td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Mostrar el formulario para insertar datos
            echo "<h3>Insertar nuevo registro</h3>";
            echo "<form method='POST' action=''>";
            echo "<input type='hidden' name='tabla' value='$nombreTabla'>";
            echo "<input type='hidden' name='columnas[]' value='" . implode("', '", array_keys($datosTabla[0])) . "'>";

            // Crear campos de entrada para cada columna, incluyendo el ID
            foreach (array_keys($datosTabla[0]) as $columna) {
                echo "<div class='mb-3'>";
                echo "<label for='$columna' class='form-label'>$columna</label>";
                echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                echo "</div>";
            }

            echo "<button type='submit' class='btn btn-primary'>Insertar</button>";
            echo "</form>";
        } else {
            // Si no hay datos en la tabla
            echo "<p>No hay datos en la tabla $nombreTabla.</p>";
        }
    } else {
        // Si no se seleccionó ninguna tabla
        echo "<p>Por favor, selecciona una tabla para gestionar.</p>";
    }
    ?>
</div>
</body>
</html>
