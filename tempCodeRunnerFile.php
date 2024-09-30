<?php
require 'settings.php';

class conexion {
    private $conector = null;

    public function getConexion()
    {
        // Conexión a la base de datos MercadoMayorista
        $this->conector = new PDO("sqlsrv:server=".SERVIDOR.";database=".DATABASE, USUARIO, PASSWORD);
        return $this->conector;
    }
}

$con = new conexion();

if ($con->getConexion() != null) {
    // Consulta para obtener todas las tablas de la base de datos
    $pps = $con->getConexion()->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
    $pps->execute();

    // Devuelve la lista de tablas en formato JSON
    return json_encode(['Tablas' => $pps->fetchAll(PDO::FETCH_ASSOC)]);
} else {
    echo "Error al conectarse a la base de datos";
}
?>