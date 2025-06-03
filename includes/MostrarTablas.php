<?php
// Encabezado de la tabla con los nombres de las columnas
for ($i = 0; $i < count(array_keys($datosTabla[0])); $i++) {
    if ($nombreTabla == "Productos") {
        switch (array_keys($datosTabla[0])[$i]) {
            case "cod_prov":
                echo "<th class='table_head'>" . "Proveedor" . "</th>";
                break;
            case "cod_alm":
                echo "<th class='table_head'>" . "Almacen" . "</th>";
                break;
            case "cod_dist":
                echo "<th class='table_head'>" . "Distribuidora" . "</th>";
                break;
            default:
                echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
                break;
        }
    } else if ($nombreTabla == "Pedidos" || $nombreTabla == "Movimientos") {
        switch (array_keys($datosTabla[0])[$i]) {
            case "cod_prov":
                echo "<th class='table_head'>" . "Proveedor" . "</th>";
                break;
            case "cod_prod":
                echo "<th class='table_head'>" . "Producto" . "</th>";
                break;
            default:
                echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
                break;
        }
    } else if ($nombreTabla == "Compras") {
        switch (array_keys($datosTabla[0])[$i]) {
            case "cod_dist":
                echo "<th class='table_head'>" . "Distribuidora" . "</th>";
                break;
            case "cod_prod":
                echo "<th class='table_head'>" . "Producto" . "</th>";
                break;
            case "cod_cli":
                echo "<th class='table_head'>" . "DNI_Clientes" . "</th>";
                break;
            case "cod_emp":
                echo "<th class='table_head'>" . "Empleados" . "</th>";
                break;
            default:
                echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
                break;
        }
    } else {
        echo "<th class='table_head'>" . array_keys($datosTabla[0])[$i] . "</th>";
    }
}
echo "<th class='table_head'></th>"; // Columna para las acciones
echo "</tr></thead><tbody>";
// Filas con los datos de cada registro
for ($j = 0; $j < count($datosTabla); $j++) {
    echo "<tr>";

    if ($nombreTabla == "Productos") {
        for ($k = 0; $k < count($datosTabla[$j]); $k++) {
            // agregar nombres proveedores
            if ($k == 6) {
                $cod_prod = $datosTabla[$j]['cod_prod'];

                // Consulta única para obtener nombres de proveedor, almacén y distribuidora en función de cod_prod
                $consulta = $con->getConexion()->prepare("
                            SELECT 
                                p.nombre AS nombre_proveedor,
                                a.nombre AS nombre_almacen,
                                d.nombre AS nombre_distribuidora
                            FROM 
                                Productos m
                            LEFT JOIN Proveedores p ON m.cod_prov = p.cod_prov
                            LEFT JOIN Almacen a ON m.cod_alm = a.cod_alm
                            LEFT JOIN Distribuidora d ON m.cod_dist = d.cod_dist
                            WHERE 
                                m.cod_prod = :cod_prod
                        ");
                $consulta->bindParam(':cod_prod', $cod_prod);
                $consulta->execute();
                $resultados = $consulta->fetch(PDO::FETCH_ASSOC);

                // Mostrar resultados en la tabla
                echo "<td class='table_body'>" . ($resultados['nombre_proveedor'] ?? 'Proveedor no encontrado') . "</td>";
                echo "<td class='table_body'>" . ($resultados['nombre_almacen'] ?? 'Almacén no encontrado') . "</td>";
                echo "<td class='table_body'>" . ($resultados['nombre_distribuidora'] ?? 'Distribuidora no encontrada') . "</td>";
            } else if ($k < 6) {
                echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
            }
        }
    } else if ($nombreTabla == "Movimientos") {
        for ($k = 0; $k < count($datosTabla[$j]); $k++) {
            if ($k == 1) {
                // Consulta única para obtener el nombre del producto en función de cod_mov
                $nombre_productosQuery = $con->getConexion()->prepare("
                            SELECT p.nombre 
                            FROM Productos p
                            JOIN Movimientos m ON p.cod_prod = m.cod_prod
                            WHERE m.cod_mov = :cod_mov
                        ");
                $nombre_productosQuery->bindParam(':cod_mov', $datosTabla[$j]['cod_mov']);
                $nombre_productosQuery->execute();
                $nombre_productos = $nombre_productosQuery->fetchColumn();

                // Mostrar el nombre del producto o un mensaje si no se encuentra
                echo "<td class='table_body'>" . ($nombre_productos ?? '---') . "</td>";
            } else {
                echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
            }
        }
    } else if ($nombreTabla == "Pedidos") {
        for ($k = 0; $k < count($datosTabla[$j]); $k++) {
            if ($k == 1) {
                // Consulta única para obtener el nombre del proveedor y del producto en función de cod_ped
                $consulta = $con->getConexion()->prepare("
                            SELECT prov.nombre AS nombre_proveedor, prod.nombre AS nombre_producto
                            FROM Pedidos ped
                            LEFT JOIN Proveedores prov ON ped.cod_prov = prov.cod_prov
                            LEFT JOIN Productos prod ON ped.cod_prod = prod.cod_prod
                            WHERE ped.cod_ped = :cod_ped
                        ");
                $consulta->bindParam(':cod_ped', $datosTabla[$j]['cod_ped']);
                $consulta->execute();
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                // Mostrar el nombre del proveedor o un mensaje si no se encuentra
                echo "<td class='table_body'>" . ($resultado['nombre_proveedor'] ?? '---') . "</td>";
                // Mostrar el nombre del producto o un mensaje si no se encuentra
                echo "<td class='table_body'>" . ($resultado['nombre_producto'] ?? '---') . "</td>";
            } else if ($k != 2) {
                echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
            }
        }
    } else if ($nombreTabla == "Compras") {
        for ($k = 0; $k < count($datosTabla[$j]); $k++) {
            if ($k == 1) {
                // Consulta para obtener el nombre del distribuidor en función de cod_dist
                $consulta = $con->getConexion()->prepare("
                            SELECT nombre AS nombre_distribuidor
                            FROM Distribuidora
                            WHERE cod_dist = :cod_dist
                        ");
                $consulta->bindParam(':cod_dist', $datosTabla[$j]['cod_dist']);
                $consulta->execute();
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                // Mostrar el nombre del distribuidor o un mensaje si no se encuentra
                echo "<td class='table_body'>" . ($resultado['nombre_distribuidor'] ?? '---') . "</td>";
            } else if ($k == 2) {
                // Consulta para obtener el nombre del producto en función de cod_prod
                $consulta = $con->getConexion()->prepare("
                            SELECT nombre AS nombre_producto
                            FROM Productos
                            WHERE cod_prod = :cod_prod
                        ");
                $consulta->bindParam(':cod_prod', $datosTabla[$j]['cod_prod']);
                $consulta->execute();
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                // Mostrar el nombre del producto o un mensaje si no se encuentra
                echo "<td class='table_body'>" . ($resultado['nombre_producto'] ?? '---') . "</td>";
            } else if ($k == 4) {
                // Consulta para obtener el DNI del cliente en función de cod_cli
                $consulta = $con->getConexion()->prepare("
                            SELECT DNI AS DNI_cliente
                            FROM Clientes
                            WHERE cod_cli = :cod_cli
                        ");
                $consulta->bindParam(':cod_cli', $datosTabla[$j]['cod_cli']);
                $consulta->execute();
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                // Mostrar el nombre del cliente o un mensaje si no se encuentra
                echo "<td class='table_body'>" . ($resultado['DNI_cliente'] ?? '---') . "</td>";
            } else if ($k == 5) {
                // Consulta para obtener el nombre del empleado en función de cod_emp
                $consulta1 = $con->getConexion()->prepare("
                            SELECT nombre AS nombre_empleado
                            FROM Empleados
                            WHERE cod_emp = :cod_emp
                        ");
                $consulta2 = $con->getConexion()->prepare("
                        SELECT apellido AS apellido_empleado
                        FROM Empleados
                        WHERE cod_emp = :cod_emp
                        ");
                $consulta1->bindParam(':cod_emp', $datosTabla[$j]['cod_emp']);
                $consulta1->execute();
                $resultado1 = $consulta1->fetch(PDO::FETCH_ASSOC);

                $consulta2->bindParam(':cod_emp', $datosTabla[$j]['cod_emp']);
                $consulta2->execute();
                $resultado2 = $consulta2->fetch(PDO::FETCH_ASSOC);

                // Mostrar el nombre del Empleado o un mensaje si no se encuentra
                echo "<td class='table_body'>" . ($resultado1['nombre_empleado'] ?? '---') . ", " . ($resultado2['apellido_empleado'] ?? '---') . "</td>";
            } else {
                echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
            }
        }
    } else {
        for ($k = 0; $k < count($datosTabla[$j]); $k++) {
            echo "<td class='table_body'>" . $datosTabla[$j][array_keys($datosTabla[0])[$k]] . "</td>";
        }
    }

    // Obtener el primer campo como clave para eliminar
    $id = $datosTabla[$j][array_key_first($datosTabla[$j])]; // Obtener el primer campo de la fila
    echo "<td class='table_body delete'><button id='abrirModal2' class='btn btn-primary'>modificar registro</button>
                <a href='?tabla=$nombreTabla&eliminar=true&id=$id' id='eliminar' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este registro?\");'>Eliminar</a>
            </td>";
    echo "</tr>";
}
echo "</tbody></table>";
