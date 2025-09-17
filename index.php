<?php
session_start();
require_once("database/connection.php");
$db = new Database;
$con = $db->conectar();
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
    <title>Reporte Usuarios</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <link rel="stylesheet" href="../../controller/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Invocar a JQuery para que funcione el archivo JS -->
    <!-- Bootstrap 5 -->

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="registros.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">

  <div class="row">
    <!-- Registrar venta manual -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          Registrar Venta Manualmente
        </div>
        <div class="card-body">
          <form id="formVenta" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
              <label class="form-label">Documento del Vendedor</label>
              <input type="number" id="docu_vendedor" name="docu_vendedor" class="form-control" placeholder="Ej. 10064..." required>
            </div>

            <div class="mb-3">
              <label class="form-label">Nombre del Vendedor</label>
              <input type="text" id="nom_vendedor" name="nom_vendedor" class="form-control" placeholder="Ej. Juan Pérez" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Documento del Comprador</label>
              <input type="number" id="docu_comprador" name="docu_comprador" class="form-control" placeholder="Ej. 20045..." required>
            </div>

            <div class="mb-3">
              <label class="form-label">Nombre del Comprador</label>
              <input type="text" id="nom_comprador" name="nom_comprador" class="form-control" placeholder="Ej. Ana Gómez" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Teléfono del Comprador</label>
              <input type="text" id="telefono" name="telefono" class="form-control" placeholder="321..." required>
            </div>

            <div class="mb-3">
              <label class="form-label">Nombre del Material</label>
              <input type="text" id="material" name="material" class="form-control" placeholder="Ej. Cemento Gris 50kg" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Cantidad</label>
              <input type="number" name="cantidad" id="cantidad" class="form-control" placeholder="Ej. 10" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Valor Unitario</label>
              <input type="number" step="0.01" name="valor_unitario" id="valor_unitario" class="form-control" placeholder="Ej. 35000" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Guardar Venta</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Cargar ventas desde CSV -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
          Cargar Ventas desde Archivo CSV
        </div>
        <div class="card-body">
          <form action="importar_scv.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="csv_file" class="form-label">Seleccionar archivo (.csv)</label>
              <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-warning">Subir Archivo</button>
            </div>
            <small class="text-muted">El archivo debe contener: documento_vendedor, documento_comprador, material, cantidad, valor_unitario, fecha.</small>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla de registros -->
  <div class="card shadow-sm mt-4">
            <div class="card-header bg-dark text-white">
                Registros de Visitas
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <form class="d-flex" role="search">
                            <!-- <label for="caja_busqueda">Buscar: </label> -->
                            <input class="form-control me-2" type="text" name="caja-busqueda" id="caja-busqueda" placeholder="Buscar" aria-label="Search">
                        </form>
                        <div id="datos"></div>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
        const form = document.getElementById('formVenta');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData();
            formData.append('docu_vendedor', document.getElementById('docu_vendedor').value);
            formData.append('nom_vendedor', document.getElementById('nom_vendedor').value);
            formData.append('docu_comprador', document.getElementById('docu_comprador').value);
            formData.append('nom_comprador', document.getElementById('nom_comprador').value);
            formData.append('telefono', document.getElementById('telefono').value);
            formData.append('material', document.getElementById('material').value);
            formData.append('cantidad', document.getElementById('cantidad').value);
            formData.append('valor_unitario', document.getElementById('valor_unitario').value);

            try {
                const response = await fetch('guardar_ventas.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                if (result.message) {
                    alert(result.message);
                    limpiarFormulario();
                } else if (result.error) {
                    alert(result.error);
                }
            } catch (error) {
                console.error(error);
                alert('Error al conectar con el servidor.');
            }
        });

        function limpiarFormulario() {
            document.getElementById('docu_vendedor').value = '';
            document.getElementById('nom_vendedor').value = '';
            document.getElementById('docu_comprador').value = '';
            document.getElementById('nom_comprador').value = '';
            document.getElementById('telefono').value = '';
            document.getElementById('material').value = '';
            document.getElementById('cantidad').value = '';
            document.getElementById('valor_unitario').value = '';
        }
    </script>
</body>
</html>
