<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>


    <?php
        include_once("conexion.php");
        // guardo en $conn el return de la funcion "ConexionBD" de la clase "CConexion"
        $conn = CConexion::ConexionBD();
        // Llama la clase y hace la funcion para obtener la consulta y mostrarlo con tablas
        CConexion::Cosultar($conn,'Almacen');
        CConexion::Cosultar($conn,'Clientes');
        CConexion::Cosultar($conn,'Distribuidora');
        CConexion::Cosultar($conn,'Empleados');
        CConexion::Cosultar($conn,'Localidades');
        CConexion::Cosultar($conn,'Pedidos');
        CConexion::Cosultar($conn,'Productos');
        CConexion::Cosultar($conn,'proveedores');
    ?>
    
    
</body>
</html>
