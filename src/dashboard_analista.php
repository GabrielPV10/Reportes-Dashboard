<?php //Ya quedo 2
session_start(); 
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard del Analista</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Panel de Analista</a>
    <ul class="navbar-nav ms-auto"><li class="nav-item"><a class="btn btn-danger" href="logout.php">Cerrar Sesión</a></li></ul>
  </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header"><h3>Análisis Interactivo de Ventas</h3></div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filtro-reporte" class="form-label"><b>Seleccionar Reporte:</b></label>
                    <select id="filtro-reporte" class="form-select">
                        <optgroup label="Análisis General">
                            <option value="ventas_por_categoria" selected>Ventas por Categoría</option>
                            <option value="ventas_por_ciudad">Ventas por Ciudad</option>
                        </optgroup>
                        <optgroup label="Análisis de Tiempo">
                            <option value="ventas_diarias">Ventas Diarias</option>
                            <option value="ventas_semanales">Ventas Semanales</option>
                            <option value="ventas_mensuales">Ventas Mensuales</option>
                        </optgroup>
                        <optgroup label="Ranking de Top 5">
                            <option value="top_clientes_ventas">Top Clientes (por Ventas)</option>
                            <option value="top_productos_ventas">Top Productos (por Ventas)</option>
                            <option value="top_productos_unidades">Top Productos (por Unidades)</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filtro-grafico" class="form-label"><b>Tipo de Gráfico:</b></label>
                    <select id="filtro-grafico" class="form-select">
                        <option value="bar" selected>Barras</option>
                        <option value="line">Línea</option>
                        <option value="pie">Pastel</option>
                        <option value="doughnut">Dona</option>
                        <option value="polarArea">Área Polar</option>
                        <option value="radar">Radar</option>
                    </select>
                </div>
            </div>
            <div style="width: 100%;"><canvas id="graficoPrincipal"></canvas></div>
        </div>
    </div>
</div>

<script>
    const filtroReporte = document.getElementById('filtro-reporte');
    const filtroGrafico = document.getElementById('filtro-grafico');
    const ctx = document.getElementById('graficoPrincipal').getContext('2d');
    let miGrafico;

    function actualizarGrafico() {
        const reporteSeleccionado = filtroReporte.value;
        const tipoGrafico = filtroGrafico.value;

        fetch(`api.php?reporte=${reporteSeleccionado}`)
            .then(response => response.json())
            .then(datos => {
                if (miGrafico) { miGrafico.destroy(); }

                miGrafico = new Chart(ctx, {
                    type: tipoGrafico,
                    data: {
                        labels: datos.labels,
                        datasets: [{
                            label: 'Total ($ o Unidades)',
                            data: datos.data,
                            backgroundColor: ['rgba(54, 162, 235, 0.7)', 'rgba(255, 99, 132, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: { display: true, text: `Reporte de ${filtroReporte.options[filtroReporte.selectedIndex].text}` },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) { label += ': '; }
                                        
                                        // --- INICIO DE LA SOLUCIÓN ---
                                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        // Verificamos que el total no sea cero para evitar NaN
                                        if ((tipoGrafico === 'pie' || tipoGrafico === 'doughnut') && total > 0) {
                                            const porcentaje = (context.raw / total * 100).toFixed(2) + '%';
                                            label += `${context.formattedValue} (${porcentaje})`;
                                        } else {
                                            label += context.formattedValue;
                                        }
                                        // --- FIN DE LA SOLUCIÓN ---
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            });
    }

    filtroReporte.addEventListener('change', actualizarGrafico);
    filtroGrafico.addEventListener('change', actualizarGrafico);
    document.addEventListener('DOMContentLoaded', actualizarGrafico);
</script>

</body>
</html>