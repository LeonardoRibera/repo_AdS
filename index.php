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

    window.addEventListener("load", function() {
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
    function handleSelectChange() {
        const select = document.getElementById('opciones_movimientos');
        const selectedValue = select.value;

        if (selectedValue == 'Entrada') {
            // Acciones para 'Entrada'
            document.getElementById('cal1').required = true;
            document.getElementById('cal1').disabled = false;
            document.getElementById('cal2').disabled = true;
        } else if (selectedValue == 'Salida') {
            // Acciones para 'Salida'
            document.getElementById('cal2').required = true;
            document.getElementById('cal2').disabled = false;
            document.getElementById('cal1').disabled = true;
        }
    }

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
    echo "<h2 class='h2-tittle'>$nombreTabla</h2> <button id='abrirModal' class='btn btn-primary'>Ingresar nuevo registro</button>";

    if (count($datosTabla) > 0) {
        // Mostrar la tabla HTML con los datos
        echo "<table class='table table-bordered'>";
        echo "<thead><tr>";

        // Encabezado de la tabla con los nombres de las columnas
        for ($i = 0; $i < count(array_keys($datosTabla[0])); $i++) {
            if($nombreTabla=="Productos" ){
                switch(array_keys($datosTabla[0])[$i] ){
                    case "cod_prov":
                        echo "<th class='table_head'>" . "Proveedor" . "</th>";
                        break;
                    case "cod_alm":
                        echo "<th class='table_head'>" . "Almacen" . "</th>";
                        break;
                    case "cod_dist":
                        echo "<th class='table_head'>" . "Distribuidora" . "</th>";
                        break;
                    default:
                            echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
                        break;
                }
            } else if($nombreTabla=="Pedidos" || $nombreTabla=="Movimientos"){
                switch(array_keys($datosTabla[0])[$i] ){
                    case "cod_prov":
                        echo "<th class='table_head'>" . "Proveedor" . "</th>";
                        break;
                    case "cod_prod":
                        echo "<th class='table_head'>" . "Producto" . "</th>";
                        break;
                    default:
                            echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
                        break;
                }
            } else if($nombreTabla=="Compras" ){
                switch(array_keys($datosTabla[0])[$i] ){
                    case "cod_prod":
                        echo "<th class='table_head'>" . "Producto" . "</th>";
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
                    if ($k == 6) {
                        $cod_prod = $datosTabla[$j]['cod_prod'];
                    
                        // Consulta única para obtener nombres de proveedor, almacén y distribuidora en función de cod_prod
                        $consulta = $con->getConexion()->prepare("
                            SELECT 
                                p.nombre AS nombre_proveedor,
                                a.nombre AS nombre_almacen,
                                d.nombre AS nombre_distribuidora
                            FROM 
                                Productos m
                            LEFT JOIN Proveedores p ON m.cod_prov = p.cod_prov
                            LEFT JOIN Almacen a ON m.cod_alm = a.cod_alm
                            LEFT JOIN Distribuidora d ON m.cod_dist = d.cod_dist
                            WHERE 
                                m.cod_prod = :cod_prod
                        ");
                        $consulta->bindParam(':cod_prod', $cod_prod);
                        $consulta->execute();
                        $resultados = $consulta->fetch(PDO::FETCH_ASSOC);
                    
                        // Mostrar resultados en la tabla
                        echo "<td class='table_body'>" . ($resultados['nombre_proveedor'] ?? 'Proveedor no encontrado') . "</td>";
                        echo "<td class='table_body'>" . ($resultados['nombre_almacen'] ?? 'Almacén no encontrado') . "</td>";
                        echo "<td class='table_body'>" . ($resultados['nombre_distribuidora'] ?? 'Distribuidora no encontrada') . "</td>";
                    }
                    else if($k<6) {
                        echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
                    }
                }
            } else if ($nombreTabla == "Movimientos"){
                for ($k = 0; $k < count($datosTabla[$j]); $k++){
                    if ($k == 1) {
                        // Consulta única para obtener el nombre del producto en función de cod_mov
                        $nombre_productosQuery = $con->getConexion()->prepare("
                            SELECT p.nombre 
                            FROM Productos p
                            JOIN Movimientos m ON p.cod_prod = m.cod_prod
                            WHERE m.cod_mov = :cod_mov
                        ");
                        $nombre_productosQuery->bindParam(':cod_mov', $datosTabla[$j]['cod_mov']);
                        $nombre_productosQuery->execute();
                        $nombre_productos = $nombre_productosQuery->fetchColumn();
                    
                        // Mostrar el nombre del producto o un mensaje si no se encuentra
                        echo "<td class='table_body'>" . ($nombre_productos ?? '---') . "</td>";
                    }
                    
                    else {
                        echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
                    }
                }
            } else if ($nombreTabla == "Pedidos"){
                for ($k = 0; $k < count($datosTabla[$j]); $k++){
                    if ($k == 1) {
                        // Consulta única para obtener el nombre del proveedor y del producto en función de cod_ped
                        $consulta = $con->getConexion()->prepare("
                            SELECT prov.nombre AS nombre_proveedor, prod.nombre AS nombre_producto
                            FROM Pedidos ped
                            LEFT JOIN Proveedores prov ON ped.cod_prov = prov.cod_prov
                            LEFT JOIN Productos prod ON ped.cod_prod = prod.cod_prod
                            WHERE ped.cod_ped = :cod_ped
                        ");
                        $consulta->bindParam(':cod_ped', $datosTabla[$j]['cod_ped']);
                        $consulta->execute();
                        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                    
                        // Mostrar el nombre del proveedor o un mensaje si no se encuentra
                        echo "<td class='table_body'>" . ($resultado['nombre_proveedor'] ?? '---') . "</td>";
                        // Mostrar el nombre del producto o un mensaje si no se encuentra
                        echo "<td class='table_body'>" . ($resultado['nombre_producto'] ?? '---') . "</td>";
                    }
                     else if ($k!=2) {
                        echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
                    }
                }
            } else if ($nombreTabla == "Compras"){
                for ($k = 0; $k < count($datosTabla[$j]); $k++){
                    if ($k == 0) {
                        // Consulta para obtener el nombre del distribuidor en función de cod_dist
                        $consulta = $con->getConexion()->prepare("
                            SELECT nombre AS nombre_distribuidor
                            FROM Distribuidora
                            WHERE cod_dist = :cod_dist
                        ");
                        $consulta->bindParam(':cod_dist', $datosTabla[$j]['cod_dist']);
                        $consulta->execute();
                        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                        
                        // Mostrar el nombre del distribuidor o un mensaje si no se encuentra
                        echo "<td class='table_body'>" . ($resultado['nombre_distribuidor'] ?? '---') . "</td>";
                    } else if ($k == 1) {
                        // Consulta para obtener el nombre del producto en función de cod_prod
                        $consulta = $con->getConexion()->prepare("
                            SELECT nombre AS nombre_producto
                            FROM Productos
                            WHERE cod_prod = :cod_prod
                        ");
                        $consulta->bindParam(':cod_prod', $datosTabla[$j]['cod_prod']);
                        $consulta->execute();
                        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                        
                        // Mostrar el nombre del producto o un mensaje si no se encuentra
                        echo "<td class='table_body'>" . ($resultado['nombre_producto'] ?? '---') . "</td>";
                    } else {
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
                <a href='?tabla=$nombreTabla&eliminar=true&id=$id' id='eliminar' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este registro?\");'>Eliminar</a>
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

        // Obtiene los productos y los almacena en un array
        $productosQuery = $con->getConexion()->prepare("SELECT cod_prod, nombre FROM Productos");
        $productosQuery->execute();
        $productos = $productosQuery->fetchAll(PDO::FETCH_ASSOC);    
        
        $proveedoresQuery = $con->getConexion()->prepare("SELECT cod_prov, nombre FROM Proveedores");
        $proveedoresQuery->execute();
        $proveedores = $proveedoresQuery->fetchAll(PDO::FETCH_ASSOC);    
        
        $almacenesQuery = $con->getConexion()->prepare("SELECT cod_alm, nombre FROM Almacen");
        $almacenesQuery->execute();
        $almacenes = $almacenesQuery->fetchAll(PDO::FETCH_ASSOC);    
        
        $distribuidoraQuery = $con->getConexion()->prepare("SELECT cod_dist, nombre FROM Distribuidora");
        $distribuidoraQuery->execute();
        $distribuidoras = $distribuidoraQuery->fetchAll(PDO::FETCH_ASSOC);   

        if ($nombreTabla == "Pedidos"){
            for ($m = 1; $m < $mitad; $m++) {
                $columna = array_keys($datosTabla[0])[$m];
                // Generar el select
                if ($columna == "cod_prov") {
                    //proveedor
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>proveedor</label>
                          <select id='opciones_proveedor' name='opciones_proveedor' class='form-select'>";
                
                    // Generar opciones del select
                    foreach ($proveedores as $proveedor) {
                        $cod_prov = $proveedor['cod_prov'];
                        $nombre_proveedor = $proveedor['nombre'];
                        echo "<option value='$cod_prov'" . ($EstadoActual == $cod_prov ? 'selected' : '') . ">$nombre_proveedor</option>";
                    }
                    echo "</select></div>";
                } else {
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='$columna' class='form-label'>$columna</label>";
                    echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                    echo "</div>";
                }
            }
        } else if ($nombreTabla == "Movimientos") {
            for ($m = 1; $m < $mitad; $m++) {
                $columna = array_keys($datosTabla[0])[$m];
                
                
                // Generar el select
                if ($columna == "cod_prod") {
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>producto</label>
                          <select id='opciones_productos' name='opciones_productos' class='form-select'>";
                
                    // Generar opciones del select
                    foreach ($productos as $producto) {
                        $cod_prod = $producto['cod_prod'];
                        $nombre_produc = $producto['nombre'];
                        echo "<option value='$cod_prod'" . ($EstadoActual == $cod_prod ? 'selected' : '') . ">$nombre_produc</option>";
                    }
                    echo "</select></div>";
                } else if ($columna == "tipo_movimiento"){
                    $estadoActual = $datosTabla[0][$columna]; // Obtiene el estado actual de la primera fila de $datosTabla
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>tipo_movimiento</label>
                          <select id='opciones_movimientos' name='opciones_movimientos' class='form-select' onchange='handleSelectChange()'>";
                    echo "<option value='Salida' " . ($estadoActual == 'Salida' ? 'selected' : '') . ">Salida</option>";
                    echo "<option value='Entrada' " . ($estadoActual == 'Entrada' ? 'selected' : '') . ">Entrada</option>";
                    echo "</select>";
                    echo "</div>";
                } else {
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='$columna' class='form-label'>$columna</label>";
                    echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                    echo "</div>";
                }
            }
        } else if ($nombreTabla == 'Productos'){
            for ($m = 1; $m < $mitad; $m++) {
                $columna = array_keys($datosTabla[0])[$m];
                if ($columna == 'fecha_pedido'){
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='$columna' class='form-label'>$columna</label>";
                    echo "<input type='date' class='form-control' id='$columna' name='$columna' placeholder='../../..' required>";
                    echo "</div>";
                } else {
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='$columna' class='form-label'>$columna</label>";
                    echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                    echo "</div>";
                }
            }
        } else if ($nombreTabla == 'Compras'){
            for ($m = 0; $m < $mitad; $m++) {
                $columna = array_keys($datosTabla[0])[$m];
                if ($columna == "cod_dist") {
                    //distribuidora
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>distribuidora</label>
                          <select id='opciones_distribuidora' name='opciones_distribuidora' class='form-select'>";

                    // Generar opciones del select
                    foreach ($distribuidoras as $distribuidora) {
                        $cod_dist = $distribuidora['cod_dist'];
                        $nombre_distribuidora = $distribuidora['nombre'];
                        echo "<option value='$cod_dist'" . ($EstadoActual == $cod_dist ? 'selected' : '') . ">$nombre_distribuidora</option>";
                    }
                    
                    echo "</select></div>";
                }else if ($columna == "cod_prod") {
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>producto</label>
                          <select id='opciones_productos' name='opciones_productos' class='form-select'>";
                
                    // Generar opciones del select
                    foreach ($productos as $producto) {
                        $cod_prod = $producto['cod_prod'];
                        $nombre_produc = $producto['nombre'];
                        echo "<option value='$cod_prod'" . ($EstadoActual == $cod_prod ? 'selected' : '') . ">$nombre_produc</option>";
                    }
                    echo "</select></div>";
                }else {
                    $columna = array_keys($datosTabla[0])[$m];
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
                    echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
                    echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                    echo "</div>";
                }
            }
        }
         else {
            for ($m = 1; $m < $mitad; $m++) {
                $columna = array_keys($datosTabla[0])[$m];
                echo "<input type='hidden' name='columnas[]' value='$columna'>";
                echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
                echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
                echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                echo "</div>";
            }
        }

        echo "</div>"; // Cerrar la primera columna
        
        // Segunda mitad de los inputs (columna derecha)
        echo "<div class='col-md-6'>"; // Segunda columna
        if ($nombreTabla == "Clientes"){
            for ($n = $mitad; $n < $totalColumnas; $n++) {
                $columna = array_keys($datosTabla[0])[$n];
                // Generar el select
                if ($columna == "estado") {
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>estado</label>
                          <select id='opciones_estado_cliente' name='opciones_estado_cliente' class='form-select'>";
                    echo "<option value='Activo' " . ($estadoActual == 'Activo' ? 'selected' : '') . ">Activo</option>";
                    echo "<option value='Inactivo' " . ($estadoActual == 'Inactivo' ? 'selected' : '') . ">Inactivo</option>";
                    echo "</select>";
                    echo "</div>";
                } else {
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='$columna' class='form-label'>$columna</label>";
                    echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                    echo "</div>";
                }
            }
        } else if ($nombreTabla == "Pedidos"){
            for ($n = $mitad; $n < $totalColumnas; $n++) {
                $columna = array_keys($datosTabla[0])[$n];
                // Generar el select
                if ($columna == "cod_prod") {
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>producto</label>
                          <select id='opciones_productos' name='opciones_productos' class='form-select'>";
                
                    // Generar opciones del select
                    foreach ($productos as $producto) {
                        $cod_prod = $producto['cod_prod'];
                        $nombre_produc = $producto['nombre'];
                        echo "<option value='$cod_prod'" . ($EstadoActual == $cod_prod ? 'selected' : '') . ">$nombre_produc</option>";
                    }
                    echo "</select></div>";
                } else {
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='$columna' class='form-label'>$columna</label>";
                    echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                    echo "</div>";
                }
            }
        }
        
        else if ($nombreTabla == "Productos"){
            for ($n = $mitad; $n < $totalColumnas; $n++) {
                $columna = array_keys($datosTabla[0])[$n];
                if ($columna == "estado"){

                    $estadoActual = $datosTabla[0][$columna]; // Obtiene el estado actual de la primera fila de $datosTabla
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>estado</label>
                          <select id='opciones_estados' name='opciones_estados' class='form-select'>";
                    echo "<option value='En stock' " . ($estadoActual == 'En stock' ? 'selected' : '') . ">En stock</option>";
                    echo "<option value='En espera' " . ($estadoActual == 'En espera' ? 'selected' : '') . ">En espera</option>";
                    echo "</select>";
                    echo "</div>";
                } else if ($columna == "cod_prov") {
                    //proveedor
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>proveedor</label>
                          <select id='opciones_proveedor' name='opciones_proveedor' class='form-select'>";
                
                    // Generar opciones del select
                    foreach ($proveedores as $proveedor) {
                        $cod_prov = $proveedor['cod_prov'];
                        $nombre_proveedor = $proveedor['nombre'];
                        echo "<option value='$cod_prov'" . ($EstadoActual == $cod_prov ? 'selected' : '') . ">$nombre_proveedor</option>";
                    }
                    echo "</select></div>";
                } else if ($columna == "cod_alm") {
                    //almacen
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>almacen</label>
                          <select id='opciones_almacen' name='opciones_almacen' class='form-select'>";
                
                    // Generar opciones del select
                    foreach ($almacenes as $almacen) {
                        $cod_alm = $almacen['cod_alm'];
                        $nombre_almacen = $almacen['nombre'];
                        echo "<option value='$cod_alm'" . ($EstadoActual == $cod_alm ? 'selected' : '') . ">$nombre_almacen</option>";
                    }

                    echo "</select></div>";
                } else if ($columna == "cod_dist") {
                    //distribuidora
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>distribuidora</label>
                          <select id='opciones_distribuidora' name='opciones_distribuidora' class='form-select'>";

                    // Generar opciones del select
                    foreach ($distribuidoras as $distribuidora) {
                        $cod_dist = $distribuidora['cod_dist'];
                        $nombre_distribuidora = $distribuidora['nombre'];
                        echo "<option value='$cod_dist'" . ($EstadoActual == $cod_dist ? 'selected' : '') . ">$nombre_distribuidora</option>";
                    }
                    
                    echo "</select></div>";
                } else {
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
                    echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
                    echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                    echo "</div>";
                }
            }
        } 
        else if ($nombreTabla == "Movimientos"){
            for ($n = $mitad; $n < $totalColumnas; $n++){
                $columna = array_keys($datosTabla[0])[$n];
                if ($columna == "estado_transaccion"){
                    $EstadoActual = $datosTabla[0][$columna]; // Obtiene el estado actual de la primera fila de $datosTabla
                    echo "<div class='mb-3'>";
                    echo "<label for='opciones' class='form-label'>estado_transaccion</label>
                          <select id='opciones_movimiento' name='opciones_movimiento' class='form-select'>";
                    echo "<option value='Pendiente' " . ($EstadoActual == 'Pendiente' ? 'selected' : '') . ">Pendiente</option>";
                    echo "<option value='Completado' " . ($EstadoActual == 'Completado' ? 'selected' : '') . ">Completado</option>";
                    echo "</select>";
                    echo "</div>";
                } else if($columna == "fecha_entrada"){
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='$columna' class='form-label'>$columna</label>";
                    echo "<input type='date' class='form-control' id='cal1' name='$columna' placeholder='../../..' required>";
                    echo "</div>";
                }
                else if($columna == "fecha_salida"){
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>";
                    echo "<label for='$columna' class='form-label'>$columna</label>";
                    echo "<input type='date' class='form-control' id='cal2' name='$columna' placeholder='../../..' disabled>";
                    echo "</div>";
                }
                else {
                    echo "<input type='hidden' name='columnas[]' value='$columna'>";
                    echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
                    echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
                    echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                    echo "</div>";
                }
            }
        } else {
            for ($n = $mitad; $n < $totalColumnas; $n++) {
                $columna = array_keys($datosTabla[0])[$n];
                echo "<input type='hidden' name='columnas[]' value='$columna'>";
                echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
                echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
                echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
                echo "</div>";
            }
        }
        echo "</div>"; // Cerrar la segunda columna

        // Cerrar el contenedor del grid
        echo "</div>"; 

        // Botón de envío
        echo "<button type='submit' class='btn btn-primary'>Insertar</button><br><br>";
        echo "</form>";
        echo "</div></div>";

    } else if (isset($nombreTabla)) {
        
            // Configuración de paginación
        $regPorPagina = 10; // Registros por página
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($paginaActual - 1) * $regPorPagina;

        // Obtiene los productos y los almacena en un array
        $productosQuery = $con->getConexion()->prepare("SELECT cod_prod, nombre FROM Productos");
        $productosQuery->execute();
        $productos = $productosQuery->fetchAll(PDO::FETCH_ASSOC);    
        
        $proveedoresQuery = $con->getConexion()->prepare("SELECT cod_prov, nombre FROM Proveedores");
        $proveedoresQuery->execute();
        $proveedores = $proveedoresQuery->fetchAll(PDO::FETCH_ASSOC);    
        
        $almacenesQuery = $con->getConexion()->prepare("SELECT cod_alm, nombre FROM Almacen");
        $almacenesQuery->execute();
        $almacenes = $almacenesQuery->fetchAll(PDO::FETCH_ASSOC);    
        
        $distribuidoraQuery = $con->getConexion()->prepare("SELECT cod_dist, nombre FROM Distribuidora");
        $distribuidoraQuery->execute();
        $distribuidoras = $distribuidoraQuery->fetchAll(PDO::FETCH_ASSOC);   

        // Preparar la consulta para obtener los nombres de columnas en SQL Server
        $query = $con->getConexion()->prepare("
            SELECT c.name 
            FROM sys.columns c
            INNER JOIN sys.tables t ON c.object_id = t.object_id
            WHERE t.name = :nombreTabla
        ");
        $query->bindParam(':nombreTabla', $nombreTabla, PDO::PARAM_STR);
        $query->execute();
    
        // Obtener los resultados como un array
        $nombresColumnas = $query->fetchAll(PDO::FETCH_COLUMN);
    
        // Obtener el total de registros de la tabla para la paginación
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
    
        // Determinar si hay datos en la tabla
        $hayDatos = count($datosTabla) > 0;
    
        // Definir el número total de columnas y la mitad
        $totalColumnas = count($nombresColumnas) ; // Número total de columnas según la tabla
        $mitad = ceil($totalColumnas / 2); // Calcular la mitad de las columnas (redondeando hacia arriba si es impar)
    
        echo "<div id='modalFormulario' class='modal'>";
        echo "<div class='modal-content col-md-6'><span class='close'>&times;</span>";
        echo "<h3>Insertar nuevo registro</h3>";
        echo "<form method='POST' action=''>";
        echo "<div class='row'>";
        echo "<div class='col-md-6'>";
        // Primera columna de campos (hasta la mitad)
        for ($n = 1; $n < $mitad; $n++) {
            $columna = $nombresColumnas[$n];
            $valor = $hayDatos && isset($datosTabla[0][$columna]) ? $datosTabla[0][$columna] : '';
        
            echo "<input type='hidden' name='columnas[]' value='$columna'>";
            echo "<div class='mb-3'>";
            echo "<label for='$columna' class='form-label'>$columna</label>";
        
            if ($nombreTabla == "Pedidos" && $columna == "cod_prov") {
                $EstadoActual = ($hayDatos && isset($datosTabla[0][$columna])) ? $datosTabla[0][$columna] : '';
                echo "<select id='opciones_proveedor' name='opciones_proveedor' class='form-select'>";
            
                foreach ($proveedores as $proveedor) {
                    $cod_prov = $proveedor['cod_prov'];
                    $nombre_proveedor = $proveedor['nombre'];
                    echo "<option value='$cod_prov'" . ($EstadoActual == $cod_prov ? 'selected' : '') . ">$nombre_proveedor</option>";
                }
                echo "</select>";
            } else {
                echo "<input type='text' class='form-control' id='$columna' name='$columna' value='$valor' required>";
            }

            echo "</div>";
        }

        echo "</div><div class='col-md-6'>";
        for ($n = $mitad; $n < $totalColumnas; $n++) {
            $columna = $nombresColumnas[$n];
            $valor = $hayDatos && isset($datosTabla[0][$columna]) ? $datosTabla[0][$columna] : '1';
            echo "<input type='hidden' name='columnas[]' value='$columna'>";
            echo "<div class='mb-3'>";
            echo "<label for='$columna' class='form-label'>$columna</label>";
        
            if ($nombreTabla == "Pedidos" && $columna == "cod_prod") {
                $EstadoActual = ($hayDatos && isset($datosTabla[0][$columna])) ? $datosTabla[0][$columna] : '1';
                echo "<select id='opciones_productos' name='opciones_productos' class='form-select'>";
            
                foreach ($productos as $producto) {
                    $cod_prod = $producto['cod_prod'];
                    $nombre_produc = $producto['nombre'];
                    echo "<option value='$cod_prod'" . ($EstadoActual == $cod_prod ? ' selected' : '') . ">$nombre_produc</option>";
                }
                echo "</select>";
            } else {
                echo "<input type='text' class='form-control' id='$columna' name='$columna' value='$valor' required>";
            }
            echo "</div>";
        }

        echo "</div>"; // Cerrar la segunda columna
        echo "</div>"; // Cerrar la fila
        echo "<button type='submit' class='btn btn-primary'>Insertar</button><br><br>";
        echo "</form>";

                echo "</div>"; // Cerrar el contenedor del modal
                echo "</div>"; // Cerrar el contenedor modal
            } else if (!isset($nombreTabla)){
            // Si no se seleccionó ninguna tabla
            echo "<p>Por favor, selecciona una tabla para gestionar.</p>";
            }
        }

    ?>
</div>
</body>
</html>
