<?php include("includes/header.php"); ?>

<style>
.welcome-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 20px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  animation: slideDown 0.6s ease-out;
}
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}
.dashboard-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  border-left: 4px solid;
  animation: fadeInUp 0.5s ease-out;
  animation-fill-mode: both;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.dashboard-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}
.dashboard-card.yellow { border-left-color: #f59e0b; }
.dashboard-card.green { border-left-color: #10b981; }
.dashboard-card.blue { border-left-color: #3b82f6; }
.dashboard-card.purple { border-left-color: #8b5cf6; }
.dashboard-card.red { border-left-color: #ef4444; }
.dashboard-card.orange { border-left-color: #f97316; }
.dashboard-card.teal { border-left-color: #14b8a6; }
.dashboard-card.pink { border-left-color: #ec4899; }
.dashboard-card.indigo { border-left-color: #6366f1; }

.card-icon {
  font-size: 3rem;
  margin-bottom: 15px;
  display: inline-block;
  animation: bounce 2s infinite;
}
@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.card-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 10px 0;
}
.card-link {
  text-decoration: none;
  color: inherit;
  display: block;
}
.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #667eea;
  margin: 10px 0;
}
.quick-stats {
  background: white;
  border-radius: 15px;
  padding: 20px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  margin-top: 20px;
}
</style>

<div class="welcome-section">
  <div class="d-flex align-items-center justify-content-between">
    <div>
      <h2 class="mb-2">¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h2>
      <p class="mb-0 opacity-75">Sistema de Gestión - Mi Bollito</p>
      <small class="opacity-75"><?= date('l, d F Y') ?></small>
    </div>
    <div class="text-end">
      <div class="badge bg-light text-dark px-3 py-2">
        <i class="bi bi-shield-check me-2"></i><?= ucfirst($_SESSION['rol']) ?>
      </div>
    </div>
  </div>
</div>

<!-- Estadísticas Rápidas -->
<div class="quick-stats mb-4">
  <h5 class="mb-3"><i class="bi bi-speedometer2 me-2"></i>Resumen Rápido</h5>
  <div class="row g-3">
    <div class="col-md-3">
      <div class="text-center">
        <i class="bi bi-box-seam text-primary" style="font-size: 2rem;"></i>
        <h4 class="mt-2 mb-0" id="totalProductos">0</h4>
        <small class="text-muted">Total Productos</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="text-center">
        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
        <h4 class="mt-2 mb-0" id="stockBajo">0</h4>
        <small class="text-muted">Stock Bajo</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="text-center">
        <i class="bi bi-cash-stack text-success" style="font-size: 2rem;"></i>
        <h4 class="mt-2 mb-0" id="valorInventario">$0</h4>
        <small class="text-muted">Valor Inventario</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="text-center">
        <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
        <h4 class="mt-2 mb-0" id="gananciaPotencial">$0</h4>
        <small class="text-muted">Ganancia Potencial</small>
      </div>
    </div>
  </div>
</div>

<!-- Módulos Principales -->
<h4 class="mb-4"><i class="bi bi-grid-3x3-gap me-2"></i>Módulos del Sistema</h4>
<div class="row g-4">
  <div class="col-md-3">
    <a href="inventario.php" class="card-link">
      <div class="dashboard-card yellow" style="animation-delay: 0.1s;">
        <i class="bi bi-box-seam card-icon text-warning"></i>
        <h5 class="card-title">Inventario</h5>
        <p class="text-muted small">Gestiona productos y stock</p>
        <button class="btn btn-outline-warning btn-sm mt-2">Acceder</button>
      </div>
    </a>
  </div>
  
  <div class="col-md-3">
    <a href="estadisticas.php" class="card-link">
      <div class="dashboard-card green" style="animation-delay: 0.2s;">
        <i class="bi bi-graph-up card-icon text-success"></i>
        <h5 class="card-title">Estadísticas</h5>
        <p class="text-muted small">Análisis y reportes financieros</p>
        <button class="btn btn-outline-success btn-sm mt-2">Ver Reportes</button>
      </div>
    </a>
  </div>
  
  <div class="col-md-3">
    <a href="domicilios.php" class="card-link">
      <div class="dashboard-card blue" style="animation-delay: 0.3s;">
        <i class="bi bi-bicycle card-icon text-primary"></i>
        <h5 class="card-title">Domicilios</h5>
        <p class="text-muted small">Gestión de entregas</p>
        <button class="btn btn-outline-primary btn-sm mt-2">Gestionar</button>
      </div>
    </a>
  </div>

  <div class="col-md-3">
    <a href="devoluciones.php" class="card-link">
      <div class="dashboard-card pink" style="animation-delay: 0.5s;">
        <i class="bi bi-arrow-return-left card-icon" style="color: #ec4899;"></i>
        <h5 class="card-title">Devoluciones</h5>
        <p class="text-muted small">Gestión de devoluciones</p>
        <button class="btn btn-outline-danger btn-sm mt-2" style="border-color: #ec4899; color: #ec4899;">Gestionar</button>
      </div>
    </a>
  </div>

  <?php if ($_SESSION['rol'] === 'administrador'): ?>
  <div class="col-md-3">
    <a href="usuarios.php" class="card-link">
      <div class="dashboard-card purple" style="animation-delay: 0.6s;">
        <i class="bi bi-people card-icon text-purple"></i>
        <h5 class="card-title">Usuarios</h5>
        <p class="text-muted small">Administrar accesos</p>
        <button class="btn btn-outline-primary btn-sm mt-2">Administrar</button>
      </div>
    </a>
  </div>
  
  <div class="col-md-3">
    <a href="categorias.php" class="card-link">
      <div class="dashboard-card orange" style="animation-delay: 0.7s;">
        <i class="bi bi-bookmark card-icon text-warning"></i>
        <h5 class="card-title">Categorías</h5>
        <p class="text-muted small">Organiza productos</p>
        <button class="btn btn-outline-warning btn-sm mt-2">Gestionar</button>
      </div>
    </a>
  </div>
  
  <div class="col-md-3">
    <a href="proveedores.php" class="card-link">
      <div class="dashboard-card teal" style="animation-delay: 0.8s;">
        <i class="bi bi-truck card-icon text-info"></i>
        <h5 class="card-title">Proveedores</h5>
        <p class="text-muted small">Gestiona proveedores</p>
        <button class="btn btn-outline-info btn-sm mt-2">Ver Lista</button>
      </div>
    </a>
  </div>
  
  <div class="col-md-3">
    <a href="vendedores.php" class="card-link">
      <div class="dashboard-card indigo" style="animation-delay: 0.9s;">
        <i class="bi bi-person-badge card-icon" style="color: #6366f1;"></i>
        <h5 class="card-title">Vendedores</h5>
        <p class="text-muted small">Vendedores ambulantes</p>
        <button class="btn btn-outline-primary btn-sm mt-2" style="border-color: #6366f1; color: #6366f1;">Administrar</button>
      </div>
    </a>
  </div>
  <?php endif; ?>
</div>

<div class="col-md-3">
    <a href="promociones.php" class="card-link">
      <div class="dashboard-card red" style="animation-delay: 0.4s;">
        <i class="bi bi-tag-fill card-icon text-danger"></i>
        <h5 class="card-title">Promociones</h5>
        <p class="text-muted small">Descuentos y ofertas</p>
        <button class="btn btn-outline-danger btn-sm mt-2">Ver Promociones</button>
      </div>
    </a>
  </div>

<script>
async function loadDashboardStats() {
  try {
    const res = await fetch('ajax/movimientos.php?action=dashboard');
    const data = await res.json();
    
    if (data.success) {
      const s = data.stats;
      document.getElementById('totalProductos').textContent = s.total_productos;
      document.getElementById('stockBajo').textContent = s.stock_bajo;
      document.getElementById('valorInventario').textContent = '$' + Number(s.valor_inventario).toLocaleString('es-CO');
      document.getElementById('gananciaPotencial').textContent = '$' + Number(s.ganancia_potencial).toLocaleString('es-CO');
    }
  } catch (err) {
    console.error('Error loading stats:', err);
  }
}

window.addEventListener('load', loadDashboardStats);
</script>

<?php include("includes/footer.php"); ?>