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
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/encabezado.css">
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Datos</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand navbar-tittle" href="#">Gestión de Datos</a>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup">
            <div class="navbar-nav d-flex justify-content-center">
                <a class="nav-item nav-link active text-center" href="?">Home</a>
                <!-- Menú dinámico de tablas -->
                <?php for ($i = 0; $i < count($tablas)-1; $i++): ?>
                    <a class="nav-item nav-link text-center" href="?tabla=<?php echo $tablas[$i]['TABLE_NAME']; ?>">
                        <?php echo $tablas[$i]['TABLE_NAME']; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <div ><button id="LogOut" class="LogOut" onclick="logout()"><i class="fas fa-sign-out-alt"></i></button></div>
    </div>
</nav>

<script>
    function logout() {
        window.location.href = 'logout.php'; // Redirige a logout.php
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Obtener el modal
        var modal = document.getElementById("modalFormulario");

        // Obtener el botón que abre el modal
        var btnAbrir = document.getElementById("abrirModal");

        // Verificar si el botón existe
        if (btnAbrir) {
            // Abrir el modal cuando se hace clic en el botón
            btnAbrir.onclick = function() {
                modal.style.display = "block";
            };
        } else {
            console.error("El botón 'abrirModal' no se encontró.");
        }

        // Obtener el botón de cerrar (la "X")
        var btnCerrar = document.getElementsByClassName("close")[0];

        // Cerrar el modal cuando se hace clic en la "X"
        if (btnCerrar) {
            btnCerrar.onclick = function() {
                modal.style.display = "none";
            };
        }

        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    });
</script>

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

    // Mostrar el nombre de la tabla
    echo "<h2 class='h2-tittle'>$nombreTabla</h2> <button id='abrirModal' class='btn btn-primary'>Ingresar nueva columna</button>";

    if (count($datosTabla) > 0) {
        // Mostrar la tabla HTML con los datos
        echo "<table class='table table-bordered'>";
        echo "<thead><tr>";

        // Encabezado de la tabla con los nombres de las columnas
        for ($i = 0; $i < count(array_keys($datosTabla[0])); $i++) {
            if($nombreTabla=="Productos" ){
                switch(array_keys($datosTabla[0])[$i] ){
                    case "cod_prov":
                        echo "<th class='table_head'>" . "nom_proveedores" . "</th>";
                        break;
                    case "cod_alm":
                        echo "<th class='table_head'>" . "nom_almacen" . "</th>";
                        break;
                    case "cod_dist":
                        echo "<th class='table_head'>" . "nom_distribuidora" . "</th>";
                        break;
                    default:
                            echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
                        break;
                }
            } else if($nombreTabla=="Pedidos" || $nombreTabla=="Movimientos"){
                switch(array_keys($datosTabla[0])[$i] ){
                    case "cod_prov":
                        echo "<th class='table_head'>" . "nom_proveedores" . "</th>";
                        break;
                    case "cod_prod":
                        echo "<th class='table_head'>" . "nom_productos" . "</th>";
                        break;
                    default:
                            echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
                        break;
                }
            } else {
                echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
            }
        }
        echo "<th class='table_head'></th>"; // Columna para las acciones
        echo "</tr></thead><tbody>";

        // Filas con los datos de cada registro
        for ($j = 0; $j < count($datosTabla); $j++) {
            echo "<tr>";

            if ($nombreTabla == "Productos"){
                for ($k = 0; $k < count($datosTabla[$j]); $k++) {
                    // agregar nombres proveedores
                    if ($k==6){
                        $col_nom_provQuery = $con->getConexion()->prepare("SELECT cod_prod FROM Productos WHERE cod_prov = ".$datosTabla[$j]['cod_prov']);
                        $col_nom_provQuery->execute();
                        $col_nom_prov = $col_nom_provQuery->fetchColumn();

                        // Consultar el total de registros para la tabla seleccionada
                        $nombre_proveedorQuery = $con->getConexion()->prepare("SELECT p.nombre FROM Proveedores p JOIN Productos m ON p.cod_prov = m.cod_prov WHERE m.cod_prov = ".$col_nom_prov);
                        $nombre_proveedorQuery->execute();
                        $nombre_proveedor = $nombre_proveedorQuery->fetchColumn();

                        echo "<td class='table_body'>" . $nombre_proveedor . "</td>";

                        $col_nom_almQuery = $con->getConexion()->prepare("SELECT cod_alm FROM Productos WHERE cod_prov = ".$datosTabla[$j]['cod_prov']);
                        $col_nom_almQuery->execute();
                        $col_nom_alm = $col_nom_almQuery->fetchColumn();

                        // Consultar el total de registros para la tabla seleccionada
                        $nombre_almacenQuery = $con->getConexion()->prepare("SELECT a.nombre FROM Almacen a JOIN Productos m ON a.cod_alm = m.cod_alm WHERE m.cod_alm = ".$col_nom_alm);
                        $nombre_almacenQuery->execute();
                        $nombre_almacen = $nombre_almacenQuery->fetchColumn();

                        echo "<td class='table_body'>" . $nombre_almacen . "</td>";
                        
                        $col_nom_distQuery = $con->getConexion()->prepare("SELECT cod_dist FROM Productos WHERE cod_prov = ".$datosTabla[$j]['cod_prov']);
                        $col_nom_distQuery->execute();
                        $col_nom_dist = $col_nom_distQuery->fetchColumn();

                        // Consultar el total de registros para la tabla seleccionada
                        $nombre_distribuidoraQuery = $con->getConexion()->prepare("SELECT d.nombre FROM Distribuidora d JOIN Productos m ON d.cod_dist = m.cod_dist WHERE m.cod_dist = ".$col_nom_dist);
                        $nombre_distribuidoraQuery->execute();
                        $nombre_distribuidora = $nombre_distribuidoraQuery->fetchColumn();

                        echo "<td class='table_body'>" . $nombre_distribuidora . "</td>";

                    } else if($k<6) {
                        echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
                    }
                }
            } else if ($nombreTabla == "Movimientos"){
                for ($k = 0; $k < count($datosTabla[$j]); $k++){
                    if ($k==1){
                        $col_nom_prodQuery = $con->getConexion()->prepare("SELECT cod_prod FROM Movimientos WHERE cod_mov = ".$datosTabla[$j]['cod_mov']);
                        $col_nom_prodQuery->execute();
                        $col_nom_prod = $col_nom_prodQuery->fetchColumn();

                        // Consultar el total de registros para la tabla seleccionada
                        $nombre_productosQuery = $con->getConexion()->prepare("SELECT p.nombre FROM Productos p JOIN Movimientos m ON p.cod_prod = m.cod_prod WHERE m.cod_prod = ".$col_nom_prod);
                        $nombre_productosQuery->execute();
                        $nombre_productos = $nombre_productosQuery->fetchColumn();

                        echo "<td class='table_body'>" . $nombre_productos . "</td>";
                    }
                    else {
                        echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
                    }
                }
            } else if ($nombreTabla == "Pedidos"){
                for ($k = 0; $k < count($datosTabla[$j]); $k++){
                    if ($k==1){
                        $col_nom_provQuery = $con->getConexion()->prepare("SELECT cod_prov FROM Pedidos WHERE cod_ped = ".$datosTabla[$j]['cod_ped']);
                        $col_nom_provQuery->execute();
                        $col_nom_prov = $col_nom_provQuery->fetchColumn();

                        // Consultar el total de registros para la tabla seleccionada
                        $nombre_proveedoresQuery = $con->getConexion()->prepare("SELECT p.nombre FROM Proveedores p JOIN Pedidos m ON p.cod_prov = m.cod_prov WHERE m.cod_prov = ".$col_nom_prov);
                        $nombre_proveedoresQuery->execute();
                        $nombre_proveedores = $nombre_proveedoresQuery->fetchColumn();

                        echo "<td class='table_body'>" . $nombre_proveedores . "</td>";

                        $col_nom_prodQuery = $con->getConexion()->prepare("SELECT cod_prod FROM Pedidos WHERE cod_ped = ".$datosTabla[$j]['cod_ped']);
                        $col_nom_prodQuery->execute();
                        $col_nom_prod = $col_nom_prodQuery->fetchColumn();

                        // Consultar el total de registros para la tabla seleccionada
                        $nombre_productosQuery = $con->getConexion()->prepare("SELECT p.nombre FROM Productos p JOIN Pedidos m ON p.cod_prod = m.cod_prod WHERE m.cod_prod = ".$col_nom_prod);
                        $nombre_productosQuery->execute();
                        $nombre_productos = $nombre_productosQuery->fetchColumn();

                        echo "<td class='table_body'>" . $nombre_productos . "</td>";
                    } else if ($k!=2) {
                        echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
                    }
                }
            }
            
            else {
                for ($k = 0; $k < count($datosTabla[$j]); $k++) {
                    echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
                }
            }
            
            // Obtener el primer campo como clave para eliminar
            $id = $datosTabla[$j][array_key_first($datosTabla[$j])]; // Obtener el primer campo de la fila
            echo "<td class='table_body delete'>
                <a href='?tabla=$nombreTabla&eliminar=true&id=$id' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este registro?\");'>Eliminar</a>
            </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        
        // Enlaces de paginación
        echo "<nav aria-label='Page navigation'>";
        echo "<ul class='pagination'>";

        // Enlace para la página anterior
        if ($paginaActual > 1) {
            echo "<li class='page-item'><a class='page-link boton-pag' href='?tabla=$nombreTabla&pagina=" . ($paginaActual - 1) . "'> <- </a></li>";
        }

        // Enlaces numéricos para las páginas
        for ($p = 1; $p <= $totalPaginas; $p++) {
            if ($p == $paginaActual) {
                echo "<li class='page-item active'><a class='page-link boton-pag custom-color' href='#'>$p</a></li>";
            } else {
                echo "<li class='page-item'><a class='page-link boton-pag' href='?tabla=$nombreTabla&pagina=$p'>$p</a></li>";
            }
        }

        // Enlace para la página siguiente
        if ($paginaActual < $totalPaginas) {
            echo "<li class='page-item'><a class='page-link boton-pag' href='?tabla=$nombreTabla&pagina=" . ($paginaActual + 1) . "'> -> </a></li>";
        }

        echo "</ul></nav>";

        echo "<div id='modalFormulario' class='modal'>";
        echo "<div class='modal-content'><span class='close'>&times;</span>";

        // Mostrar el formulario para insertar datos
        echo "<form method='POST' action=''>";
        echo "<h3>Insertar nuevo registro</h3>";
        echo "<input type='hidden' name='tabla' value='$nombreTabla'>";

        // Abrir un contenedor con un grid para dividir en dos columnas
        echo "<div class='row'>";

        // Obtener el número total de columnas
        $totalColumnas = count(array_keys($datosTabla[0]));
        $mitad = ceil($totalColumnas / 2);

        // Primera mitad de los inputs (columna izquierda)
        echo "<div class='col-md-6'>"; // Primera columna
        for ($m = 1; $m < $mitad; $m++) {
            $columna = array_keys($datosTabla[0])[$m];
            echo "<input type='hidden' name='columnas[]' value='$columna'>";
            echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
            echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
            echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
            echo "</div>";
        }
        echo "</div>"; // Cerrar la primera columna

        // Segunda mitad de los inputs (columna derecha)
        echo "<div class='col-md-6'>"; // Segunda columna
        for ($n = $mitad; $n < $totalColumnas; $n++) {
            $columna = array_keys($datosTabla[0])[$n];
            echo "<input type='hidden' name='columnas[]' value='$columna'>";
            echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
            echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
            echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
            echo "</div>";
        }
        echo "</div>"; // Cerrar la segunda columna

        // Cerrar el contenedor del grid
        echo "</div>"; 

        // Botón de envío
        echo "<button type='submit' class='btn btn-primary'>Insertar</button><br><br>";
        echo "</form>";
        echo "</div></div>";

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
