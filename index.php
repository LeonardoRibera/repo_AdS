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
        // preparo la consulta que quiero realizar.
        $consulta = $conn->prepare("SELECT * FROM Almacen");
        // ejecuta, nada mas.
        $consulta -> execute();
        // conecta la consulta con la base de datos
        $datos = $consulta -> fetchAll(PDO::FETCH_ASSOC);
        // va mostrando de forma ordenada la consulta zz
        var_dump($datos);
    ?>
    
</body>
</html>