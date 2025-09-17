<?php
require_once("database/connection.php");
$db = new Database;
$con = $db->conectar();

$tipo      = $_FILES['csv_file']['type'];
$tamano    = $_FILES['csv_file']['size'];
$archivotmp = $_FILES['csv_file']['tmp_name'];
$lineas    = file($archivotmp);

$i = 0;
$insertados = 0;

foreach ($lineas as $linea) {
    if ($i != 0) { // saltar encabezado
        $datos = explode(";", $linea);

        $fecha_venta     = !empty($datos[0]) ? trim(str_replace('"', '', $datos[0])) : '';
        $nombre_material = !empty($datos[1]) ? trim(str_replace('"', '', $datos[1])) : '';
        $cantidad        = !empty($datos[2]) ? trim(str_replace('"', '', $datos[2])) : '';
        $valor_unitario  = !empty($datos[3]) ? trim(str_replace('"', '', $datos[3])) : '';
        $doc_vendedor    = !empty($datos[4]) ? trim(str_replace('"', '', $datos[4])) : '';
        $nombre_vendedor = !empty($datos[5]) ? trim(str_replace('"', '', $datos[5])) : '';
        $doc_comprador   = !empty($datos[6]) ? trim(str_replace('"', '', $datos[6])) : '';
        $nombre_comprador= !empty($datos[7]) ? trim(str_replace('"', '', $datos[7])) : '';
        $telefono        = !empty($datos[8]) ? trim(str_replace('"', '', $datos[8])) : '';

        $valor_total = $cantidad * $valor_unitario;

        // ===== VENDEDOR =====
        if (!empty($doc_vendedor)) {
            $checkVend = $con->prepare("SELECT id_documento FROM vendedor WHERE id_documento= '" . $doc_vendedor . "'");
            $checkVend->execute();

            if ($checkVend->rowCount() == 0) {
                $insertVend = $con->prepare("INSERT INTO vendedor (id_documento, nombre_vendedor) 
                VALUES ('$doc_vendedor','$nombre_vendedor')");
                $insertVend->execute();
            } else {
                $updateVend = $con->prepare("UPDATE vendedor SET nombre_vendedor = '" . $nombre_vendedor . "' WHERE id_documento = '" . $doc_vendedor . "'");
                $updateVend->execute();

            }
        }

        // ===== COMPRADOR =====
        if (!empty($doc_comprador)) {
            $checkComp = $con->prepare("SELECT id_comprador FROM comprador WHERE id_comprador= '" . $doc_comprador . "' ");
            $checkComp->execute();

            if ($checkComp->rowCount() == 0) {
                $insertComp = $con->prepare("INSERT INTO comprador (id_comprador, nombre_comprador, telefono) 
                VALUES ('$doc_comprador','$nombre_comprador', '$telefono')");
                $insertComp->execute();
            } else {
                $updateComp = $con->prepare("UPDATE comprador SET nombre_comprador = '" . $nombre_comprador . "', telefono = '" . $telefono . "' WHERE id_comprador = '" . $doc_comprador . "'");
                $updateComp->execute();
            }
        }

        // ===== VENTA =====
        if (!empty($doc_vendedor) && !empty($doc_comprador)) {
            $insertVenta = $con->prepare("INSERT INTO venta (fecha_venta, nombre_material, cantidad, valor_unitario, valor_total, id_documento, id_comprador)
            VALUES ('$fecha_venta','$nombre_material', '$cantidad', '$valor_unitario','$valor_total', '$doc_vendedor', '$doc_comprador')");
            $insertVenta->execute();
            $insertados++;
        }
    }
    $i++;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Proceso De Archivo SCV</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <link rel="stylesheet" href="../../controller/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<div style="margin:20px; padding:15px; border-radius:5px; background:#d4edda; color:#155724; border:1px solid #c3e6cb;">
    Archivo procesado correctamente.<br>
    Registros insertados: <?php echo $insertados; ?>
</div>
<a href="index.php" class="btn btn-secondary">Atrás</a>
</body>
</html>
