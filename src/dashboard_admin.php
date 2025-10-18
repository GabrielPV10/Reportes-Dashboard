<?php  //Ya quedo 4
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard del Administrador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><i class="bi bi-person-gear"></i> Panel de Administrador</a>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="navbar-text me-3">
            Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?>
          </span>
        </li>
        <li class="nav-item">
          <a class="btn btn-danger" href="logout.php">Cerrar Sesión <i class="bi bi-box-arrow-right"></i></a>
        </li>
      </ul>
  </div>
</nav>

<div class="container mt-4">

    <div class="card mb-4">
        <div class="card-header">
            <h3><i class="bi bi-file-earmark-excel"></i> Importar Reporte de Ventas</h3>
        </div>
        <div class="card-body">
            <p>Sube tu archivo de Excel (.xlsx) para actualizar los datos del dashboard.</p>
            <form action="procesar_excel.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="file" class="form-control" name="archivo_excel" id="archivo_excel" accept=".xlsx, .xls" required>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-upload"></i> Cargar y Procesar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3><i class="bi bi-graph-up"></i> Resumen General (KPIs)</h3>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4"><div class="card text-white bg-success"><div class="card-body"><h4 class="card-title">Ventas Totales</h4><p class="card-text fs-3" id="kpi-ventas">$0.00</p></div></div></div>
                <div class="col-md-4"><div class="card text-white bg-info"><div class="card-body"><h4 class="card-title">Clientes Únicos</h4><p class="card-text fs-3" id="kpi-clientes">0</p></div></div></div>
                <div class="col-md-4"><div class="card text-white bg-warning"><div class="card-body"><h4 class="card-title">Productos Vendidos</h4><p class="card-text fs-3" id="kpi-productos">0</p></div></div></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="bi bi-activity"></i> Tendencia de Ventas Diarias</h3>
        </div>
        <div class="card-body">
            <canvas id="graficoTendencia"></canvas>
        </div>
    </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Cargar los KPIs de las tarjetas ---
        fetch('get_kpi_data.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('kpi-ventas').textContent = data.total_ventas;
                document.getElementById('kpi-clientes').textContent = data.clientes_unicos;
                document.getElementById('kpi-productos').textContent = data.productos_vendidos;
            })
            .catch(error => console.error('Error al cargar los KPIs:', error));
        
        // --- Cargar y dibujar el gráfico de tendencia ---
        const ctxTendencia = document.getElementById('graficoTendencia').getContext('2d');
        fetch('get_admin_chart_data.php')
            .then(response => response.json())
            .then(datos => {
                new Chart(ctxTendencia, {
                    type: 'line', // Gráfico de línea para mostrar tendencias
                    data: {
                        labels: datos.labels,
                        datasets: [{
                            label: 'Ventas Diarias ($)',
                            data: datos.data,
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    }
                });
            })
            .catch(error => console.error('Error al cargar datos del gráfico:', error));
    });
</script>

</body>
</html>