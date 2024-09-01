<?php

class CConexion {
   public static function ConexionBD(){
    // guardo info del SQLserver en variables para usarlo en la conexion del SQL
        $host = 'localhost';
        $dbname = 'MercadoMayorista';
        $username = 'sa';
        $password = '1234';
        $puerto = 1433;

        try{
        // guardo en $conn todo lo que agarre de la BD
            $conn = new PDO ("sqlsrv:server=$host,$puerto;database=$dbname",$username,$password);
            echo "Se conecto correctamente\n";
        }
        catch(PDOException $exp){
        // en caso de que no pueda encontrar tira el error
            echo ("No se logró conectar correctamente con la base de datos: $dbname, error: $exp");
        }
        
        return $conn;
    }

    public static function Cosultar($conn,$tabla_nombre){
        // preparo la consulta que quiero realizar.
        $consulta = $conn->prepare("SELECT * FROM $tabla_nombre");
        // ejecuta, nada mas.
        $consulta -> execute();
        // conecta la consulta con la base de datos
        $datos = $consulta -> fetchAll(PDO::FETCH_ASSOC);

        // Comenzar la tabla
        echo "<br><table border='1'>";
        // Crear la fila de encabezados (keys del primer elemento del array)
        echo "<tr>";
        echo "$tabla_nombre";
            foreach (array_keys($datos[0]) as $key) {
                echo "<th>{$key}</th>";
            }
        echo "</tr>";
        // Iterar sobre los datos para crear las filas de la tabla
        foreach ($datos as $fila) {
            echo "<tr>";
            foreach ($fila as $valor) {
                echo "<td>{$valor}</td>";
            }
            echo "</tr>";
        }
        // Cerrar la tabla
        echo "</table><br>";
    }
}


?>
