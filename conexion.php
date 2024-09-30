<?php
require 'settings.php';

class conexion {
    private $conector = null;

    public function getConexion()
    {
        // Conectar a la base de datos
        $this->conector = new PDO("sqlsrv:server=".SERVIDOR.";database=".DATABASE, USUARIO, PASSWORD);
        return $this->conector;
    }
}

$con = new conexion();

if ($con->getConexion() != null) {
    // Obtener la lista de tablas de la base de datos
    $pps = $con->getConexion()->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
    $pps->execute();
    
    // Obtener todas las tablas en un array
    $tablas = $pps->fetchAll(PDO::FETCH_ASSOC);
    
    // // Iterar sobre cada tabla y generar una tabla HTML para mostrar sus datos
    // foreach ($tablas as $tabla) {
    //     $nombreTabla = $tabla['TABLE_NAME'];
        
    //     // Consultar todos los datos de la tabla actual
    //     $pps = $con->getConexion()->prepare("SELECT * FROM " . $nombreTabla);
    //     $pps->execute();
    //     $datosTabla = $pps->fetchAll(PDO::FETCH_ASSOC);

    //     // Mostrar el nombre de la tabla
    //     echo "<h2>Tabla: $nombreTabla</h2>";

    //     if (count($datosTabla) > 0) {
    //         // Comenzar la tabla HTML
    //         echo "<table border='1' cellpadding='5' cellspacing='0'>";

    //         // Encabezado de la tabla con los nombres de las columnas
    //         echo "<tr>";
    //         foreach (array_keys($datosTabla[0]) as $columna) {
    //             echo "<th>$columna</th>";
    //         }
    //         echo "</tr>";

    //         // Filas con los datos de cada registro de la tabla
    //         foreach ($datosTabla as $fila) {
    //             echo "<tr>";
    //             foreach ($fila as $valor) {
    //                 echo "<td>$valor</td>";
    //             }
    //             echo "</tr>";
    //         }

    //         // Cerrar la tabla
    //         echo "</table>";
    //     } else {
    //         // Si no hay datos en la tabla
    //         echo "<p>No hay datos en la tabla $nombreTabla.</p>";
    //     }

    //     echo "<br>"; // Espacio entre tablas
    // }
} else {
    echo "Error al conectarse a la base de datos";
}
?>
