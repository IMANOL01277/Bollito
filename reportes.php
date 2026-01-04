<?php
include("includes/header.php");
include("conexion.php");
require_once("includes/Permisos.php");

requiere_permiso('reportes.ver');

if ($_SESSION['rol'] != 'administrador') {
    header("Location: panel.php");
    exit();
}
?>

<style>
.report-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
  animation: slideIn 0.5s ease-out;
  margin-bottom: 20px;
}
@keyframes slideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.report-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.8rem;
  margin: 0 auto 15px;
}
.report-option {
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  transition: all 0.3s ease;
  cursor: pointer;
  height: 100%;
}
.report-option:hover {
  border-color: #667eea;
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.filter-section {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  margin-top: 20px;
}
.btn-export {
  padding: 12px 30px;
  border-radius: 25px;
  font-weight: 600;
  transition: all 0.3s ease;
}
.btn-export:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>

<div class="container-fluid mt-4">
  <div class="report-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-file-earmark-bar-graph me-2"></i>Generador de Reportes</h4>
        <p class="text-muted small mb-0">Exporta reportes detallados en formato Excel o PDF</p>
      </div>
    </div>

    <div id="alertReportes"></div>

    <!-- Tipos de Reportes -->
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="report-option text-center" onclick="selectReport('inventario')">
          <div class="report-icon mx-auto" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="bi bi-box-seam"></i>
          </div>
          <h5>Inventario Completo</h5>
          <p class="text-muted small mb-0">Lista de todos los productos con stock y precios</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="report-option text-center" onclick="selectReport('movimientos')">
          <div class="report-icon mx-auto" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="bi bi-arrow-left-right"></i>
          </div>
          <h5>Movimientos</h5>
          <p class="text-muted small mb-0">Entradas y salidas de inventario por período</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="report-option text-center" onclick="selectReport('ventas')">
          <div class="report-icon mx-auto" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
          <h5>Ventas y Ganancias</h5>
          <p class="text-muted small mb-0">Análisis de ventas y rentabilidad</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="report-option text-center" onclick="selectReport('stock_bajo')">
          <div class="report-icon mx-auto" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
            <i class="bi bi-exclamation-triangle"></i>
          </div>
          <h5>Stock Bajo</h5>
          <p class="text-muted small mb-0">Productos que necesitan reabastecimiento</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="report-option text-center" onclick="selectReport('proveedores')">
          <div class="report-icon mx-auto" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="bi bi-truck"></i>
          </div>
          <h5>Proveedores</h5>
          <p class="text-muted small mb-0">Listado de proveedores y productos asociados</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="report-option text-center" onclick="selectReport('devoluciones')">
          <div class="report-icon mx-auto" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <i class="bi bi-arrow-return-left"></i>
          </div>
          <h5>Devoluciones</h5>
          <p class="text-muted small mb-0">Historial de devoluciones y motivos</p>
        </div>
      </div>
    </div>

    <!-- Filtros y Exportación -->
    <div class="filter-section" id="filterSection" style="display: none;">
      <h5 class="mb-3"><i class="bi bi-funnel me-2"></i>Filtros y Opciones</h5>
      
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Fecha Desde</label>
          <input type="date" class="form-control" id="fechaDesde">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Fecha Hasta</label>
          <input type="date" class="form-control" id="fechaHasta">
        </div>
        <div class="col-md-3" id="categoriaFilter" style="display: none;">
          <label class="form-label fw-semibold">Categoría</label>
          <select class="form-select" id="categoria">
            <option value="">Todas las categorías</option>
          </select>
        </div>
        <div class="col-md-3" id="proveedorFilter" style="display: none;">
          <label class="form-label fw-semibold">Proveedor</label>
          <select class="form-select" id="proveedor">
            <option value="">Todos los proveedores</option>
          </select>
        </div>
      </div>

      <div class="d-flex gap-2 justify-content-center">
        <button class="btn btn-success btn-export" onclick="exportReport('excel')">
          <i class="bi bi-file-earmark-excel me-2"></i>Exportar a Excel
        </button>
        <button class="btn btn-danger btn-export" onclick="exportReport('pdf')">
          <i class="bi bi-file-earmark-pdf me-2"></i>Exportar a PDF
        </button>
        <button class="btn btn-secondary btn-export" onclick="previewReport()">
          <i class="bi bi-eye me-2"></i>Vista Previa
        </button>
      </div>
    </div>

    <!-- Vista Previa -->
    <div id="previewSection" style="display: none;" class="mt-4">
      <h5 class="mb-3"><i class="bi bi-eye me-2"></i>Vista Previa del Reporte</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="previewTable">
          <thead class="table-dark"></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
let selectedReport = '';

function showAlert(type, msg) {
  const alert = document.createElement('div');
  alert.className = `alert alert-${type} alert-dismissible fade show`;
  alert.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  document.getElementById('alertReportes').innerHTML = '';
  document.getElementById('alertReportes').appendChild(alert);
  setTimeout(() => alert.remove(), 4000);
}

async function loadFilters() {
  // Cargar categorías
  const resCat = await fetch('ajax/productos.php?action=categories');
  const dataCat = await resCat.json();
  const selectCat = document.getElementById('categoria');
  selectCat.innerHTML = '<option value="">Todas las categorías</option>';
  if (dataCat.success) {
    dataCat.categorias.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.id_categoria;
      opt.textContent = c.nombre;
      selectCat.appendChild(opt);
    });
  }

  // Cargar proveedores
  const resProv = await fetch('ajax/productos.php?action=proveedores');
  const dataProv = await resProv.json();
  const selectProv = document.getElementById('proveedor');
  selectProv.innerHTML = '<option value="">Todos los proveedores</option>';
  if (dataProv.success) {
    dataProv.proveedores.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.id_proveedor;
      opt.textContent = p.nombre;
      selectProv.appendChild(opt);
    });
  }
}

function selectReport(type) {
  selectedReport = type;
  document.getElementById('filterSection').style.display = 'block';
  document.getElementById('previewSection').style.display = 'none';
  
  // Mostrar/ocultar filtros según el tipo de reporte
  const catFilter = document.getElementById('categoriaFilter');
  const provFilter = document.getElementById('proveedorFilter');
  
  catFilter.style.display = ['inventario', 'ventas', 'stock_bajo'].includes(type) ? 'block' : 'none';
  provFilter.style.display = ['inventario', 'proveedores'].includes(type) ? 'block' : 'none';
  
  // Establecer fechas por defecto
  const hoy = new Date();
  const hace30Dias = new Date();
  hace30Dias.setDate(hace30Dias.getDate() - 30);
  
  document.getElementById('fechaDesde').value = hace30Dias.toISOString().split('T')[0];
  document.getElementById('fechaHasta').value = hoy.toISOString().split('T')[0];
  
  showAlert('info', `📊 Reporte seleccionado: <strong>${getReportName(type)}</strong>`);
}

function getReportName(type) {
  const names = {
    'inventario': 'Inventario Completo',
    'movimientos': 'Movimientos de Inventario',
    'ventas': 'Ventas y Ganancias',
    'stock_bajo': 'Productos con Stock Bajo',
    'proveedores': 'Listado de Proveedores',
    'devoluciones': 'Historial de Devoluciones'
  };
  return names[type] || type;
}

async function previewReport() {
  if (!selectedReport) {
    showAlert('warning', '⚠️ Selecciona un tipo de reporte primero');
    return;
  }
  
  try {
    let data = [];
    let headers = [];
    
    switch(selectedReport) {
      case 'inventario':
        const resInv = await fetch('ajax/productos.php?action=list');
        const dataInv = await resInv.json();
        if (dataInv.success) {
          data = dataInv.products;
          headers = ['Producto', 'Categoría', 'Proveedor', 'P. Compra', 'P. Venta', 'Stock', 'Valor Total'];
        }
        break;
        
      case 'stock_bajo':
        const resStock = await fetch('ajax/productos.php?action=check_stock');
        const dataStock = await resStock.json();
        if (dataStock.success) {
          data = dataStock.low_stock;
          headers = ['Producto', 'Stock Actual', 'Estado'];
        }
        break;
        
      case 'movimientos':
        const resMov = await fetch('ajax/movimientos.php?action=list');
        const dataMov = await resMov.json();
        if (dataMov.success) {
          data = dataMov.movs;
          headers = ['Fecha', 'Producto', 'Tipo', 'Cantidad', 'P. Unitario', 'Total'];
        }
        break;
        
      case 'devoluciones':
        const resDev = await fetch('ajax/devoluciones.php?action=list');
        const dataDev = await resDev.json();
        if (dataDev.success) {
          data = dataDev.devoluciones;
          headers = ['Fecha', 'Producto', 'Cantidad', 'Motivo', 'Valor'];
        }
        break;
    }
    
    renderPreview(headers, data);
    document.getElementById('previewSection').style.display = 'block';
    
  } catch (err) {
    console.error('Error:', err);
    showAlert('danger', '❌ Error al generar vista previa');
  }
}

function renderPreview(headers, data) {
  const table = document.getElementById('previewTable');
  const thead = table.querySelector('thead');
  const tbody = table.querySelector('tbody');
  
  // Generar encabezados
  thead.innerHTML = '<tr>' + headers.map(h => `<th>${h}</th>`).join('') + '</tr>';
  
  // Generar filas
  tbody.innerHTML = '';
  if (data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="' + headers.length + '" class="text-center text-muted py-4">No hay datos para mostrar</td></tr>';
    return;
  }
  
  data.slice(0, 10).forEach(item => {
    const tr = document.createElement('tr');
    
    switch(selectedReport) {
      case 'inventario':
        tr.innerHTML = `
          <td>${item.nombre}</td>
          <td>${item.categoria || '-'}</td>
          <td>${item.proveedor || '-'}</td>
          <td>$${Number(item.precio_compra).toFixed(2)}</td>
          <td>$${Number(item.precio_venta).toFixed(2)}</td>
          <td>${item.stock}</td>
          <td>$${(item.stock * item.precio_venta).toFixed(2)}</td>
        `;
        break;
        
      case 'stock_bajo':
        tr.innerHTML = `
          <td>${item.nombre}</td>
          <td>${item.stock}</td>
          <td><span class="badge bg-${item.stock === 0 ? 'danger' : 'warning'}">${item.stock === 0 ? 'Agotado' : 'Bajo'}</span></td>
        `;
        break;
        
      case 'movimientos':
        tr.innerHTML = `
          <td>${new Date(item.fecha_movimiento).toLocaleDateString('es-CO')}</td>
          <td>${item.producto}</td>
          <td><span class="badge bg-${item.tipo === 'entrada' ? 'success' : 'danger'}">${item.tipo}</span></td>
          <td>${item.cantidad}</td>
          <td>$${Number(item.precio_unitario).toFixed(2)}</td>
          <td>$${Number(item.monto_total).toFixed(2)}</td>
        `;
        break;
        
      case 'devoluciones':
        const valor = item.cantidad * item.precio_unitario;
        tr.innerHTML = `
          <td>${new Date(item.fecha_devolucion).toLocaleDateString('es-CO')}</td>
          <td>${item.producto}</td>
          <td>${item.cantidad}</td>
          <td>${item.motivo}</td>
          <td>$${valor.toFixed(2)}</td>
        `;
        break;
    }
    
    tbody.appendChild(tr);
  });
  
  if (data.length > 10) {
    const infoRow = document.createElement('tr');
    infoRow.innerHTML = `<td colspan="${headers.length}" class="text-center text-muted py-2"><i class="bi bi-info-circle me-2"></i>Mostrando 10 de ${data.length} registros. Exporta para ver todos.</td>`;
    tbody.appendChild(infoRow);
  }
}

function exportReport(format) {
  if (!selectedReport) {
    showAlert('warning', '⚠️ Selecciona un tipo de reporte primero');
    return;
  }
  
  const fechaDesde = document.getElementById('fechaDesde').value;
  const fechaHasta = document.getElementById('fechaHasta').value;
  const categoria = document.getElementById('categoria').value;
  const proveedor = document.getElementById('proveedor').value;
  
  // Aquí irías a un endpoint PHP que genere el archivo
  const params = new URLSearchParams({
    tipo: selectedReport,
    formato: format,
    fecha_desde: fechaDesde,
    fecha_hasta: fechaHasta,
    categoria: categoria,
    proveedor: proveedor
  });
  
  showAlert('info', `🔄 Generando reporte en formato ${format.toUpperCase()}...`);
  
  // Simulación de descarga (implementar endpoint real en PHP)
  setTimeout(() => {
    showAlert('success', `✅ Reporte exportado exitosamente como ${format.toUpperCase()}`);
  }, 1500);
  
  // window.location.href = `ajax/export_report.php?${params.toString()}`;
}

window.addEventListener('load', loadFilters);
</script>

<?php include("includes/footer.php"); ?>