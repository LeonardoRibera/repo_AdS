<?php
include ("conexion.php");

// Obtener la lista de tablas de la base de datos
$pps = $con->getConexion()->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
$pps->execute();
$tablas = $pps->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link active" href="?">Home<span class="sr-only"></span></a>
            <!-- Aquí se muestran los nombres de las tablas como enlaces en el menú -->
            <?php foreach ($tablas as $tabla): ?>
                <a class="nav-item nav-link" href="?tabla=<?php echo $tabla['TABLE_NAME']; ?>">
                    <?php echo $tabla['TABLE_NAME']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<div class="container">
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
            echo "</tr></thead><tbody>";

            // Filas con los datos de cada registro
            foreach ($datosTabla as $fila) {
                echo "<tr>";
                foreach ($fila as $valor) {
                    echo "<td>$valor</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            // Si no hay datos en la tabla
            echo "<p>No hay datos en la tabla $nombreTabla.</p>";
        }
    } else {
        // Si no se selecciona ninguna tabla, mostrar contenido en blanco
        echo "<h2>Bienvenido a la página principal</h2>";
        echo "<p>Seleccione una tabla del menú para ver sus datos.</p>";
    }
    ?>
</div>

</body>
</html>
