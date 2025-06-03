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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/menu.js"></script>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/encabezado.css">
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Mayorista</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand navbar-tittle" href="#">Gestor Mayorista</a>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup">
                <div class="navbar-nav d-flex justify-content-center">
                    <a class="nav-item nav-link active text-center" href="?">Inicio</a>
                    <!-- Menú dinámico de tablas -->
                    <?php for ($i = 0; $i < count($tablas) - 14; $i++): ?>
                        <?php
                        $tablaActual = strtolower($tablas[$i]['TABLE_NAME']);
                        if ($tablaActual === 'movimientos' || $tablaActual === 'empleados') continue;
                        ?>
                        <a class="nav-item nav-link text-center" href="?tabla=<?php echo $tablas[$i]['TABLE_NAME']; ?>">
                            <?php echo $tablas[$i]['TABLE_NAME']; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
            <div><button id="LogOut" class="LogOut" onclick="logout()"><i class="fas fa-sign-out-alt"></i></button></div>
        </div>
    </nav>



    <div class="container mt-4">
        <?php
        // Configuración de paginación
        $regPorPagina = 10; // Registros por página
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($paginaActual - 1) * $regPorPagina;

        // Comprobar si se ha seleccionado una tabla
        if (isset($_GET['tabla'])) {
            $nombreTabla = $_GET['tabla'];

            // Consultar el total de registros para la tabla seleccionada
            $totalRegistrosQuery = $con->getConexion()->prepare("SELECT COUNT(*) FROM " . $nombreTabla);
            $totalRegistrosQuery->execute();
            $totalRegistros = $totalRegistrosQuery->fetchColumn();

            // Calcular el total de páginas
            $totalPaginas = ceil($totalRegistros / $regPorPagina);

            // Consultar los datos de la tabla seleccionada con paginación
            $pps = $con->getConexion()->prepare("SELECT * FROM " . $nombreTabla . " ORDER BY (SELECT NULL) OFFSET :offset ROWS FETCH NEXT :regPorPagina ROWS ONLY");
            $pps->bindParam(':offset', $offset, PDO::PARAM_INT);
            $pps->bindParam(':regPorPagina', $regPorPagina, PDO::PARAM_INT);
            $pps->execute();
            $datosTabla = $pps->fetchAll(PDO::FETCH_ASSOC);

            // Mostrar el nombre de la tabla / Boton de Ingresar nuevo registro
            echo "<h2 class='h2-tittle'>$nombreTabla</h2> <button id='abrirModal' class='btn btn-primary'>Ingresar nuevo registro</button>";

            if (count($datosTabla) > 0) {
                // Mostrar la tabla HTML con los datos
                echo "<table class='table table-bordered'>";
                echo "<thead><tr>";

                // Mostrar la tabla en un php aparte
                include("includes/MostrarTablas.php");

                // Mostrar el paginador en un php aparte
                include("includes/Paginador.php");

                include("includes/FormInsertarDatos.php");
            } else if (!empty($nombreTabla)) {
                // código para manejar la tabla seleccionada
            } else {
                echo "<p>Por favor, selecciona una tabla para gestionar.</p>";
            }
        } else {
            echo "<p>Bienvenido al Gestor de Base de Datos. </p>";
            echo "<p>Por favor, selecciona una tabla para gestionar.</p>";
        }
        include("modificarTablas.php");
        ?>
    </div>
</body>

</html>