<?php

class CConexion {
   public static function ConexionBD(){
        $host = 'localhost';
        $dbname = 'MercadoMayorista';
        $username = 'sa';
        $password = '1234';
        $puerto = 1433;

        try{
            $conn = new PDO ("sqlsrv:server=$host,$puerto;database=$dbname",$username,$password);
            echo "Se conecto correctamente";
        }
        catch(PDOException $exp){
            echo ("No se logró conectar correctamente con la base de datos: $dbname, error: $exp");
        }

        return $conn;
    }
}



?>
