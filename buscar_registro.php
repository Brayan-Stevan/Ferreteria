<?php
session_start();
require_once("database/connection.php");


$db = new Database();
$con = $db->conectar();

$mysqli = new mysqli("localhost", "root", "", "ferrmundial");

$por_pagina = 17;

if (isset($_GET['pagina'])) {
    $pagina = $_GET['pagina'];
} else {
    $pagina = 1;
}

$empieza = ($pagina - 1) * $por_pagina;

$salida = "";
$query = $query = "SELECT * FROM venta
    INNER JOIN vendedor 
    ON venta.id_documento = vendedor.id_documento
    INNER JOIN comprador 
    ON venta.id_comprador = comprador.id_comprador
    ORDER BY venta.id_venta 
    LIMIT $empieza, $por_pagina;";


if (isset($_POST['consulta'])) {
    $q = $mysqli->real_escape_string($_POST['consulta']);
    $query = "SELECT * FROM venta
    INNER JOIN vendedor 
    ON venta.id_documento = vendedor.id_documento
    INNER JOIN comprador 
    ON venta.id_comprador = comprador.id_comprador
    WHERE venta.fecha_venta LIKE '%".$q."%' 
    OR venta.nombre_material LIKE '%".$q."%' 
    OR venta.cantidad LIKE '%".$q."%' 
    OR venta.valor_unitario LIKE '%".$q."%' 
    OR venta.valor_total LIKE '%".$q."%' 
    OR vendedor.id_documento LIKE '%".$q."%' 
    OR vendedor.nombre_vendedor LIKE '%".$q."%' 
    OR comprador.id_comprador LIKE '%".$q."%' 
    OR comprador.nombre_comprador LIKE '%".$q."%' 
    OR comprador.telefono LIKE '%".$q."%'";
              
}

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $salida .= "<br>
    <div class='container'>
        <table class='table table tablas table-hover table-sm table-striped'>
            <thead class='table-dark'>
                <tr>
                    <th>Fecha De Venta</th>
                    <th>Material</th>
                    <th>Cantidad</th>
                    <th>Valor Unitario</th>
                    <th>Valor Total</th>
                    <th>Nombre De Comprador</th>
                    <th>Documento De Comprador</th>
                    <th>Nombre De Vendedor</th>
                </tr>
            </thead>
            <tbody>";

    while ($fila = $result->fetch_assoc()) {
    $salida .= "<tr>
        <td>".$fila['fecha_venta']."</td>
        <td>".$fila['nombre_material']."</td>
        <td>".$fila['cantidad']."</td>
        <td>".$fila['valor_unitario']."</td>
        <td>".$fila['valor_total']."</td>
        <td>".$fila['nombre_comprador']."</td>
        <td>".$fila['id_comprador']."</td>
        <td>".$fila['nombre_vendedor']."</td>
    </tr>";
}

    $salida .= "</tbody></table>";
} else {
    $salida .= "No Se Encuentran Datos";
}

echo $salida;
$mysqli->close();
?>

<?php
// Paginación
$sql = $con->prepare("SELECT COUNT(*) 
                      FROM venta
                      INNER JOIN vendedor 
                        ON venta.id_documento = vendedor.id_documento
                      INNER JOIN comprador 
                        ON venta.id_comprador = comprador.id_comprador");
$sql->execute();
$resul = $sql->fetchColumn();
$total_paginas = ceil($resul / $por_pagina);

if ($total_paginas == 0) {
    echo "<center>Lista Vacía</center>";
} else {
    echo "<center><a href='search_user.php?pagina=1'><i class='fa fa-arrow-left'></i></a>";
    for ($i = 1; $i <= $total_paginas; $i++) {
        echo "<a href='search_user.php?pagina=".$i."'> ".$i." </a>";
    }
    echo "<a href='search_user.php?pagina=$total_paginas'><i class='fa fa-arrow-right'></i></a></center>";
}
?>