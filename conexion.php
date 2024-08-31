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

}


?>
