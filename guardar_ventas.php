<?php
require_once("database/connection.php");
$db = new Database;
$con = $db->conectar();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recibir datos del formulario
        $id_documento     = trim($_POST['docu_vendedor']);
        $nombre_vendedor  = trim($_POST['nom_vendedor']);
        $id_comprador     = trim($_POST['docu_comprador']);
        $nombre_comprador = trim($_POST['nom_comprador']);
        $telefono         = trim($_POST['telefono']);
        $nombre_material  = trim($_POST['material']);
        $cantidad         = trim($_POST['cantidad']);
        $valor_unitario   = trim($_POST['valor_unitario']);

        // Calcular valor total automáticamente
        $valor_total = $cantidad * $valor_unitario;

        // Insertar vendedor
        $insertVendedor = $con->prepare("INSERT INTO vendedor (id_documento, nombre_vendedor) 
                                         VALUES (?, ?)");
        $insertVendedor->execute([$id_documento, $nombre_vendedor]);

        // Insertar comprador
        $insertComprador = $con->prepare("INSERT INTO comprador (id_comprador, nombre_comprador, telefono) 
                                          VALUES (?, ?, ?)");
        $insertComprador->execute([$id_comprador, $nombre_comprador, $telefono]);

        // Insertar venta
        $insertVenta = $con->prepare("INSERT INTO venta 
            (fecha_venta, nombre_material, cantidad, valor_unitario, valor_total, id_documento, id_comprador)
            VALUES (NOW(), ?, ?, ?, ?, ?, ?)");
        
        $insertVenta->execute([$nombre_material, $cantidad, $valor_unitario, $valor_total, $id_documento, $id_comprador]);

        echo json_encode(['message' => 'Venta registrada correctamente.']);
    } else {
        echo json_encode(['error' => 'Método no permitido.']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
