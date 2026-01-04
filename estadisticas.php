<?php include("includes/header.php");
require_once("includes/Permisos.php");


?>

<style>
.stats-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
  animation: fadeInUp 0.5s ease-out;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.stats-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}
.stats-card.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stats-card.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stats-card.blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stats-icon {
  font-size: 2.5rem;
  opacity: 0.9;
  animation: bounce 2s infinite;
}
@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.chart-container {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  margin-top: 20px;
  animation: fadeIn 0.8s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.badge-mov {
  padding: 5px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.85rem;
}
</style>

<div class="container-fluid">
  <h3 class="mb-4"><i class="bi bi-bar-chart-fill me-2"></i>Estadísticas y Análisis</h3>
  <p class="text-muted">Últimos 30 días</p>
  
  <!-- Cards de Resumen -->
  <div class="row g-4 mb-4" id="resumenCards">
    <div class="col-md-3">
      <div class="stats-card green" style="animation-delay: 0.1s;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-white-50 mb-1">INVERSIÓN</h6>
            <h3 class="mb-0" id="cardInversion">$0</h3>
          </div>
          <div class="stats-icon"><i class="bi bi-cash-stack"></i></div>
        </div>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stats-card blue" style="animation-delay: 0.2s;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-white-50 mb-1">INGRESOS</h6>
            <h3 class="mb-0" id="cardIngresos">$0</h3>
          </div>
          <div class="stats-icon"><i class="bi bi-graph-up-arrow"></i></div>
        </div>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stats-card orange" style="animation-delay: 0.3s;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-white-50 mb-1">COSTO VENTAS</h6>
            <h3 class="mb-0" id="cardCosto">$0</h3>
          </div>
          <div class="stats-icon"><i class="bi bi-cart-dash"></i></div>
        </div>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="stats-card" style="animation-delay: 0.4s;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-white-50 mb-1">GANANCIA</h6>
            <h3 class="mb-0" id="cardGanancia">$0</h3>
            <small id="margenText" class="text-white-50"></small>
          </div>
          <div class="stats-icon"><i class="bi bi-trophy-fill"></i></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráfico -->
  <div class="chart-container">
    <h5 class="mb-3"><i class="bi bi-pie-chart me-2"></i>Análisis Financiero</h5>
    <canvas id="grafico" height="100"></canvas>
  </div>

  <!-- Tabla de Movimientos -->
  <div class="chart-container">
    <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i>Movimientos Recientes</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle" id="tablaMovs">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Precio Unit.</th>
            <th>Total</th>
            <th>Ganancia</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function loadEstadisticas(){
  try {
    const res = await fetch('ajax/movimientos.php?action=resumen');
    const j = await res.json();
    
    if (!j.success) {
      console.error('Error al cargar resumen');
      return;
    }
    
    const r = j.resumen;
    
    // Actualizar cards con animación
    animateValue('cardInversion', 0, r.inversion, 1000);
    animateValue('cardIngresos', 0, r.ingresos, 1000);
    animateValue('cardCosto', 0, r.costo_ventas, 1000);
    animateValue('cardGanancia', 0, r.ganancia, 1000);
    
    document.getElementById('margenText').textContent = `Margen: ${r.margen}%`;

    // Gráfico mejorado
    const ctx = document.getElementById('grafico');
    if(window.graficoChart) window.graficoChart.destroy();
    
    window.graficoChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Inversión', 'Ingresos', 'Costo Ventas', 'Ganancia'],
        datasets: [{
          label: 'Valores ($)',
          data: [r.inversion, r.ingresos, r.costo_ventas, r.ganancia],
          backgroundColor: [
            'rgba(54, 162, 235, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(153, 102, 255, 0.8)'
          ],
          borderColor: [
            'rgb(54, 162, 235)',
            'rgb(75, 192, 192)',
            'rgb(255, 159, 64)',
            'rgb(153, 102, 255)'
          ],
          borderWidth: 2,
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                return '$' + Number(context.parsed.y).toLocaleString('es-CO', {minimumFractionDigits: 2});
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '$' + Number(value).toLocaleString('es-CO');
              }
            }
          }
        },
        animation: {
          duration: 1500,
          easing: 'easeInOutQuart'
        }
      }
    });

    // Movimientos recientes
    const r2 = await fetch('ajax/movimientos.php?action=list');
    const m = await r2.json();
    const tb = document.querySelector('#tablaMovs tbody');
    tb.innerHTML = '';
    
    if (!m.success || m.movs.length === 0) {
      tb.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No hay movimientos registrados en los últimos 30 días</td></tr>';
      return;
    }
    
    m.movs.forEach((x, i) => {
      const tipo_class = x.tipo === 'entrada' ? 'bg-success' : 'bg-danger';
      const ganancia_texto = x.tipo === 'salida' ? `$${Number(x.ganancia || 0).toLocaleString('es-CO', {minimumFractionDigits: 2})}` : '-';
      
      tb.innerHTML += `<tr style="animation: fadeIn 0.5s ease-out ${i * 0.05}s both;">
        <td>${i + 1}</td>
        <td><strong>${x.producto || 'N/A'}</strong></td>
        <td><span class="badge-mov ${tipo_class} text-white">${x.tipo}</span></td>
        <td>${x.cantidad}</td>
        <td>$${Number(x.precio_unitario).toLocaleString('es-CO', {minimumFractionDigits: 2})}</td>
        <td><strong>$${Number(x.monto_total || 0).toLocaleString('es-CO', {minimumFractionDigits: 2})}</strong></td>
        <td class="text-success fw-bold">${ganancia_texto}</td>
        <td>${new Date(x.fecha_movimiento).toLocaleDateString('es-CO')}</td>
      </tr>`;
    });
  } catch(err){
    console.error('Error:', err);
  }
}

// Función para animar números
function animateValue(id, start, end, duration) {
  const element = document.getElementById(id);
  const range = end - start;
  const increment = range / (duration / 16);
  let current = start;
  
  const timer = setInterval(() => {
    current += increment;
    if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
      current = end;
      clearInterval(timer);
    }
    element.textContent = '$' + Math.floor(current).toLocaleString('es-CO');
  }, 16);
}

window.addEventListener('load', loadEstadisticas);
</script>

<?php include("includes/footer.php"); ?>