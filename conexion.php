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
} else {
    echo "Error al conectarse a la base de datos";
}
?>
