<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Si el usuario no ha iniciado sesión, redirigirlo al login
    header("Location: login.html");
    exit; // Terminar la ejecución del script
}

// Si el usuario ha iniciado sesión, se incluye el archivo de consultas
include("consultas.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/encabezado.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Datos</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand navbar-tittle" href="#">Gestión de Datos</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup">
            <div class="navbar-nav d-flex justify-content-center">
                <a class="nav-item nav-link active text-center" href="?">Home</a>
                <!-- Menú dinámico de tablas -->
                <?php foreach ($tablas as $tabla): ?>
                    <a class="nav-item nav-link text-center" href="?tabla=<?php echo $tabla['TABLE_NAME']; ?>">
                        <?php echo $tabla['TABLE_NAME']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</nav>

</body>

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

            // Crear campos de entrada para cada columna, incluyendo el ID
            foreach (array_keys($datosTabla[0]) as $columna) {
                echo "<input type='hidden' name='columnas[]' value='$columna'>";
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
