<script>
    window.addEventListener("load", function() {
        // Obtener el modal
        var modal = document.getElementById("modalFormulario");

        // Obtener el botón que abre el modal
        var btnAbrir = document.getElementById("abrirModal");
        // Obtener el botón que abre el modal
        var btnAbrir = document.getElementById("abrirModal2");
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
<?php

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

$empleadoQuery = $con->getConexion()->prepare("SELECT cod_emp, nombre, apellido FROM Empleados");
$empleadoQuery->execute();
$empleados = $empleadoQuery->fetchAll(PDO::FETCH_ASSOC);

$clienteQuery = $con->getConexion()->prepare("SELECT cod_cli, DNI FROM Clientes");
$clienteQuery->execute();
$clientes = $clienteQuery->fetchAll(PDO::FETCH_ASSOC);

if ($nombreTabla == "Pedidos") {
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
        } else if ($columna == "tipo_movimiento") {
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
} else if ($nombreTabla == 'Productos') {
    for ($m = 1; $m < $mitad; $m++) {
        $columna = array_keys($datosTabla[0])[$m];
        if ($columna == 'fecha_pedido') {
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
} else if ($nombreTabla == 'Compras') {
    for ($m = 1; $m < $mitad; $m++) {
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
        } else if ($columna == "cod_prod") {
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
            $columna = array_keys($datosTabla[0])[$m];
            echo "<input type='hidden' name='columnas[]' value='$columna'>";
            echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
            echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
            echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
            echo "</div>";
        }
    }
} else {
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
if ($nombreTabla == "Clientes") {
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
} else if ($nombreTabla == "Pedidos") {
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
} else if ($nombreTabla == "Productos") {
    for ($n = $mitad; $n < $totalColumnas; $n++) {
        $columna = array_keys($datosTabla[0])[$n];
        if ($columna == "estado") {

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
} else if ($nombreTabla == "Movimientos") {
    for ($n = $mitad; $n < $totalColumnas; $n++) {
        $columna = array_keys($datosTabla[0])[$n];
        if ($columna == "estado_transaccion") {
            $EstadoActual = $datosTabla[0][$columna]; // Obtiene el estado actual de la primera fila de $datosTabla
            echo "<div class='mb-3'>";
            echo "<label for='opciones' class='form-label'>estado_transaccion</label>
                          <select id='opciones_movimiento' name='opciones_movimiento' class='form-select'>";
            echo "<option value='Pendiente' " . ($EstadoActual == 'Pendiente' ? 'selected' : '') . ">Pendiente</option>";
            echo "<option value='Completado' " . ($EstadoActual == 'Completado' ? 'selected' : '') . ">Completado</option>";
            echo "</select>";
            echo "</div>";
        } else if ($columna == "fecha_entrada") {
            echo "<input type='hidden' name='columnas[]' value='$columna'>";
            echo "<div class='mb-3'>";
            echo "<label for='$columna' class='form-label'>$columna</label>";
            echo "<input type='date' class='form-control' id='cal1' name='$columna' placeholder='../../..' required>";
            echo "</div>";
        } else if ($columna == "fecha_salida") {
            echo "<input type='hidden' name='columnas[]' value='$columna'>";
            echo "<div class='mb-3'>";
            echo "<label for='$columna' class='form-label'>$columna</label>";
            echo "<input type='date' class='form-control' id='cal2' name='$columna' placeholder='../../..' disabled>";
            echo "</div>";
        } else {
            echo "<input type='hidden' name='columnas[]' value='$columna'>";
            echo "<div class='mb-3'>"; // Cambiar a una clase 'mb-3' para separar los campos
            echo "<label for='$columna' class='form-label'>$columna</label>"; // Usar 'form-label' para el título arriba del input
            echo "<input type='text' class='form-control' id='$columna' name='$columna' required>";
            echo "</div>";
        }
    }
} else if ($nombreTabla == "Compras") {
    for ($n = $mitad; $n < $totalColumnas; $n++) {
        $columna = array_keys($datosTabla[0])[$n];
        if ($columna == "cod_cli") {
            //clientes
            $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
            echo "<div class='mb-3'>";
            echo "<label for='opciones' class='form-label'>DNI del cliente</label>
                            <select id='opciones_clientes' name='opciones_clientes' class='form-select'>";

            // Generar opciones del select
            foreach ($clientes as $cliente) {
                $cod_cli = $cliente['cod_cli'];
                $DNI_cliente = $cliente['DNI'];
                echo "<option value='$cod_cli'" . ($EstadoActual == $cod_cli ? 'selected' : '') . ">$DNI_cliente</option>";
            }

            echo "</select></div>";
        } else if ($columna == "cod_emp") {
            $columna = array_keys($datosTabla[0])[$n];


            if ($columna == "cod_emp") {
                //empleados
                $EstadoActual = $datosTabla[0][$columna]; // Obtiene el valor actual de 'cod_prod'
                echo "<div class='mb-3'>";
                echo "<label for='opciones' class='form-label'>empleados</label>
                            <select id='opciones_empleados' name='opciones_empleados' class='form-select'>";

                // Generar opciones del select
                foreach ($empleados as $empleado) {
                    $cod_emp = $empleado['cod_emp'];
                    $nombre_empleado = $empleado['nombre'];
                    $apellido_empleado = $empleado['apellido'];
                    echo "<option value='$cod_emp'" . ($EstadoActual == $cod_emp ? 'selected' : '') . ">$nombre_empleado, $apellido_empleado</option>";
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
?>