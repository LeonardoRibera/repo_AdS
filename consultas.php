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
    $arrayProductosQuery = $con->getConexion()->prepare("SELECT cod_prod, nombre FROM Productos");
    $arrayProductosQuery->execute();
    $arrayProductos = $arrayProductosQuery->fetchAll(PDO::FETCH_ASSOC);
    // Crear un array solo con los cod_prod para in_array
    $codigosProductos = array_column($arrayProductos, 'cod_prod');

    $arrayProveedoresQuery = $con->getConexion()->prepare("SELECT cod_prov, nombre FROM Proveedores");
    $arrayProveedoresQuery->execute();
    $arrayProveedores = $arrayProveedoresQuery->fetchAll(PDO::FETCH_ASSOC);
    // Crear un array solo con los cod_prov para in_array
    $codigosProveedores = array_column($arrayProveedores, 'cod_prov');

    $arrayAlmacenesQuery = $con->getConexion()->prepare("SELECT cod_alm, nombre FROM Almacen");
    $arrayAlmacenesQuery->execute();
    $arrayAlmacenes = $arrayAlmacenesQuery->fetchAll(PDO::FETCH_ASSOC);
    // Crear un array solo con los cod_alm para in_array
    $codigosAlmacenes = array_column($arrayAlmacenes, 'cod_alm');

    $arrayDistribuidorasQuery = $con->getConexion()->prepare("SELECT cod_dist, nombre FROM Distribuidora");
    $arrayDistribuidorasQuery->execute();
    $arrayDistribuidoras = $arrayDistribuidorasQuery->fetchAll(PDO::FETCH_ASSOC);
    // Crear un array solo con los cod_dist para in_array
    $codigosDistribuidoras = array_column($arrayDistribuidoras, 'cod_dist');

    $arrayClientesQuery = $con->getConexion()->prepare("SELECT cod_cli, DNI FROM Clientes");
    $arrayClientesQuery->execute();
    $arrayClientes = $arrayClientesQuery->fetchAll(PDO::FETCH_ASSOC);
    // Crear un array solo con los cod_cli para in_array
    $codigosClientes = array_column($arrayClientes, 'cod_cli');

    $arrayEmpleadosQuery = $con->getConexion()->prepare("SELECT cod_emp, nombre, apellido FROM Empleados");
    $arrayEmpleadosQuery->execute();
    $arrayEmpleados = $arrayEmpleadosQuery->fetchAll(PDO::FETCH_ASSOC);
    // Crear un array solo con los cod_emp para in_array
    $codigosEmpleados = array_column($arrayEmpleados, 'cod_emp');

    // Procesar la selección del formulario
    if (isset($_POST['opciones_productos'])) {
        switch ($_POST['opciones_productos']) {
            case in_array($_POST['opciones_productos'], $codigosProductos):
                $valores[] = $_POST['opciones_productos'];
                $finalColumnas[] = 'cod_prod';
                break;
        }
    }

    if (isset($_POST['opciones_proveedor'])) {
        switch ($_POST['opciones_proveedor']) {
            case in_array($_POST['opciones_proveedor'], $codigosProveedores):
                $valores[] = $_POST['opciones_proveedor'];
                $finalColumnas[] = 'cod_prov';
                break;
        }
    }

    if (isset($_POST['opciones_almacen'])) {
        switch ($_POST['opciones_almacen']) {
            case in_array($_POST['opciones_almacen'], $codigosAlmacenes):
                $valores[] = $_POST['opciones_almacen'];
                $finalColumnas[] = 'cod_alm';
                break;
        }
    }

    if (isset($_POST['opciones_distribuidora'])) {
        switch ($_POST['opciones_distribuidora']) {
            case in_array($_POST['opciones_distribuidora'], $codigosDistribuidoras):
                $valores[] = $_POST['opciones_distribuidora'];
                $finalColumnas[] = 'cod_dist';
                break;
        }
    }
    if (isset($_POST['opciones_clientes'])) {
        switch ($_POST['opciones_clientes']) {
            case in_array($_POST['opciones_clientes'], $codigosClientes):
                $valores[] = $_POST['opciones_clientes'];
                $finalColumnas[] = 'cod_cli';
                break;
        }
    }
    if (isset($_POST['opciones_empleados'])) {
        switch ($_POST['opciones_empleados']) {
            case in_array($_POST['opciones_empleados'], $codigosEmpleados):
                $valores[] = $_POST['opciones_empleados'];
                $finalColumnas[] = 'cod_emp';
                break;
        }
    }
    if (isset($_POST['opciones_movimientos'])) {
        switch ($_POST['opciones_movimientos']) {
            case 'Salida':
            case 'Entrada':
                $valores[] = $_POST['opciones_movimientos'];
                $finalColumnas[] = 'tipo_movimiento';
                break;
        }
    }
    if (isset($_POST['opciones_estados'])) {
        switch ($_POST['opciones_estados']) {
            case 'En stock':
            case 'En espera':
                $valores[] = $_POST['opciones_estados'];
                $finalColumnas[] = 'estado';
                break;
        }
    }
    if (isset($_POST['opciones_movimiento'])) {
        switch ($_POST['opciones_movimiento']) {
            case 'Pendiente':
            case 'Completado':
                $valores[] = $_POST['opciones_movimiento'];
                $finalColumnas[] = 'estado_transaccion';
                break;
        }
    }
    if (isset($_POST['opciones_estado_cliente'])) {
        switch ($_POST['opciones_estado_cliente']) {
            case 'Activo':
            case 'Inactivo':
                $valores[] = $_POST['opciones_estado_cliente'];
                $finalColumnas[] = 'estado';
                break;
        }
    }
    if (isset($_POST['opciones'])) {
        $valores[] = $_POST['opciones'];
    }

    if (isset($_POST['modificar'])) {
        // Obtener el nombre de la clave primaria
        $pps = $con->getConexion()->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = (
            SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = ? AND CONSTRAINT_TYPE = 'PRIMARY KEY'
        )"
        );
        $pps->execute([$nombreTabla, $nombreTabla]);
        $clavePrimaria = $pps->fetchColumn();

        // Preparar el SET de la consulta
        $camposSet = implode(' = ?, ', $finalColumnas) . ' = ?';

        // Agarro la id del registro a modificar
        $id = $_POST['id'];

        // Agregar el $id al final del array de valores, para el WHERE
        $valoresConId = $valores; // Copio el array
        $valoresConId[] = $id;     // Agrego el id al final

        // Preparo la consulta UPDATE con un placeholder para el WHERE
        $sql = "UPDATE $nombreTabla SET $camposSet WHERE $clavePrimaria = ?";

        $pps = $con->getConexion()->prepare($sql);

        try {
            // Ejecutar la consulta con los valores + id
            $pps->execute($valoresConId);

            echo "<p>Registro modificado correctamente.</p>";
            header("Location: ?tabla=$nombreTabla");
            exit;
        } catch (PDOException $e) {
            echo "<p>Error al modificar el registro: " . $e->getMessage() . "</p>";
        }
    } else {
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
