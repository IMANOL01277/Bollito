<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Avanzado - Mi Bollito</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f5f7fa;
      font-family: 'Inter', sans-serif;
      padding: 20px;
    }
    .dashboard-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin-bottom: 20px;
      animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 15px;
      padding: 20px;
      position: relative;
      overflow: hidden;
      transition: transform 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-5px);
    }
    .stat-card.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-card.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-icon {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 3rem;
      opacity: 0.3;
    }
    .trend-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.85rem;
      background: rgba(255,255,255,0.2);
    }
    .chart-container {
      position: relative;
      height: 300px;
    }
    h6 {
      color: #6b7280;
      font-weight: 600;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="mb-4">
    <h3 class="fw-bold mb-1">📊 Dashboard Ejecutivo</h3>
    <p class="text-muted">Vista general del negocio en tiempo real</p>
  </div>

  <!-- Estadísticas Principales -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
        <p class="mb-1 opacity-75">Ventas del Mes</p>
        <h3 class="mb-0">$24,350</h3>
        <div class="trend-badge mt-2">
          <i class="bi bi-arrow-up"></i>
          <span>+12.5%</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
        <p class="mb-1 opacity-75">Ganancia Neta</p>
        <h3 class="mb-0">$8,420</h3>
        <div class="trend-badge mt-2">
          <i class="bi bi-arrow-up"></i>
          <span>+8.2%</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card orange">
        <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
        <p class="mb-1 opacity-75">Productos Vendidos</p>
        <h3 class="mb-0">1,284</h3>
        <div class="trend-badge mt-2">
          <i class="bi bi-arrow-up"></i>
          <span>+15.3%</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-people"></i></div>
        <p class="mb-1 opacity-75">Clientes Activos</p>
        <h3 class="mb-0">342</h3>
        <div class="trend-badge mt-2">
          <i class="bi bi-arrow-up"></i>
          <span>+5.7%</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <!-- Ventas por Mes -->
    <div class="col-md-8">
      <div class="dashboard-card">
        <h6><i class="bi bi-bar-chart-fill me-2"></i>Ventas Últimos 6 Meses</h6>
        <div class="chart-container">
          <canvas id="salesChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Top Productos -->
    <div class="col-md-4">
      <div class="dashboard-card">
        <h6><i class="bi bi-trophy-fill me-2"></i>Productos Más Vendidos</h6>
        <div class="chart-container">
          <canvas id="topProductsChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Distribución por Categoría -->
    <div class="col-md-6">
      <div class="dashboard-card">
        <h6><i class="bi bi-pie-chart-fill me-2"></i>Ventas por Categoría</h6>
        <div class="chart-container">
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Análisis de Inventario -->
    <div class="col-md-6">
      <div class="dashboard-card">
        <h6><i class="bi bi-speedometer2 me-2"></i>Estado del Inventario</h6>
        <div class="chart-container">
          <canvas id="inventoryChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Ventas vs Costos -->
    <div class="col-md-12">
      <div class="dashboard-card">
        <h6><i class="bi bi-graph-up me-2"></i>Análisis de Rentabilidad</h6>
        <div class="chart-container" style="height: 250px;">
          <canvas id="profitChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Configuración común
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'bottom'
    }
  }
};

// Gráfico de Ventas Mensuales
new Chart(document.getElementById('salesChart'), {
  type: 'line',
  data: {
    labels: ['Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre', 'Enero'],
    datasets: [{
      label: 'Ventas',
      data: [18500, 20300, 19800, 22100, 23400, 24350],
      borderColor: '#667eea',
      backgroundColor: 'rgba(102, 126, 234, 0.1)',
      tension: 0.4,
      fill: true
    }, {
      label: 'Objetivo',
      data: [20000, 20000, 20000, 22000, 22000, 25000],
      borderColor: '#f093fb',
      borderDash: [5, 5],
      tension: 0.4,
      fill: false
    }]
  },
  options: {
    ...chartOptions,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: value => '$' + value.toLocaleString()
        }
      }
    }
  }
});

// Gráfico de Top Productos
new Chart(document.getElementById('topProductsChart'), {
  type: 'doughnut',
  data: {
    labels: ['Empanadas', 'Arepas', 'Jugos', 'Café', 'Otros'],
    datasets: [{
      data: [35, 25, 20, 12, 8],
      backgroundColor: [
        '#667eea',
        '#f093fb',
        '#11998e',
        '#f59e0b',
        '#6b7280'
      ]
    }]
  },
  options: chartOptions
});

// Gráfico de Categorías
new Chart(document.getElementById('categoryChart'), {
  type: 'bar',
  data: {
    labels: ['Comidas', 'Bebidas', 'Snacks', 'Postres'],
    datasets: [{
      label: 'Ventas ($)',
      data: [12500, 8200, 4800, 3100],
      backgroundColor: [
        'rgba(102, 126, 234, 0.8)',
        'rgba(240, 147, 251, 0.8)',
        'rgba(17, 153, 142, 0.8)',
        'rgba(245, 158, 11, 0.8)'
      ],
      borderRadius: 8
    }]
  },
  options: {
    ...chartOptions,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: value => '$' + value.toLocaleString()
        }
      }
    }
  }
});

// Gráfico de Estado de Inventario
new Chart(document.getElementById('inventoryChart'), {
  type: 'radar',
  data: {
    labels: ['Stock Óptimo', 'Stock Bajo', 'Rotación', 'Valor', 'Diversidad'],
    datasets: [{
      label: 'Actual',
      data: [85, 40, 75, 90, 70],
      borderColor: '#667eea',
      backgroundColor: 'rgba(102, 126, 234, 0.2)'
    }, {
      label: 'Objetivo',
      data: [90, 20, 85, 95, 80],
      borderColor: '#f093fb',
      backgroundColor: 'rgba(240, 147, 251, 0.2)'
    }]
  },
  options: {
    ...chartOptions,
    scales: {
      r: {
        beginAtZero: true,
        max: 100
      }
    }
  }
});

// Gráfico de Rentabilidad
new Chart(document.getElementById('profitChart'), {
  type: 'line',
  data: {
    labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
    datasets: [{
      label: 'Ingresos',
      data: [5800, 6200, 5900, 6450],
      borderColor: '#11998e',
      backgroundColor: 'rgba(17, 153, 142, 0.1)',
      fill: true
    }, {
      label: 'Costos',
      data: [3500, 3800, 3600, 3920],
      borderColor: '#f5576c',
      backgroundColor: 'rgba(245, 87, 108, 0.1)',
      fill: true
    }, {
      label: 'Ganancia',
      data: [2300, 2400, 2300, 2530],
      borderColor: '#667eea',
      backgroundColor: 'rgba(102, 126, 234, 0.1)',
      fill: true
    }]
  },
  options: {
    ...chartOptions,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: value => '$' + value.toLocaleString()
        }
      }
    }
  }
});

// Actualizar datos cada 30 segundos (simulado)
setInterval(() => {
  console.log('Actualizando dashboard...');
  // Aquí irían las llamadas AJAX para datos reales
}, 30000);
</script>

</body>
</html>