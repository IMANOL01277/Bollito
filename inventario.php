<?php include("includes/header.php"); ?>
<?php require 'conexion.php'; ?>


<style>
.card-style {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.stock-badge {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}
.stock-ok { background: #d4edda; color: #155724; }
.stock-warning { background: #fff3cd; color: #856404; }
.stock-danger { background: #f8d7da; color: #721c24; }
.btn-action {
  transition: all 0.3s ease;
}
.btn-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 10px rgba(0,0,0,0.2);
}
.alert-stock {
  animation: slideInRight 0.5s ease-out;
  border-left: 4px solid #ffc107;
}
@keyframes slideInRight {
  from { opacity: 0; transform: translateX(50px); }
  to { opacity: 1; transform: translateX(0); }
}
.profit-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  background: #d1f2eb;
  color: #0f5132;
}
</style>

<div class="card-style">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1"><i class="bi bi-box-seam"></i> Inventario</h4>
      <p class="text-muted small mb-0">Gestiona tus productos y stock</p>
    </div>
    <button class="btn btn-success btn-action" data-bs-toggle="modal" data-bs-target="#modalCreateProduct">
      <i class="bi bi-plus-circle"></i> Nuevo Producto
    </button>
  </div>

  <!-- Alerta de Stock Bajo -->
  <div id="stockAlerts"></div>

  <div id="alerts"></div>

  <div class="table-responsive">
    <table class="table table-hover align-middle" id="productosTable">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Proveedor</th>
          <th>P. Compra</th>
          <th>P. Venta</th>
          <th>Margen</th>
          <th>Stock</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="productosBody"></tbody>
    </table>
  </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modalCreateProduct" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="formCreateProduct" class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nuevo producto</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Nombre *</label>
            <input name="nombre" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Categoría *</label>
            <select name="id_categoria" id="selectCategoria" class="form-select" required></select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Proveedor</label>
            <select name="id_proveedor" id="selectProveedor" class="form-select">
              <option value="">Seleccione...</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Stock Inicial *</label>
            <input name="stock" type="number" min="0" class="form-control" value="0" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Precio de Compra *</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input name="precio_compra" id="precioCompra" type="number" step="0.01" min="0" class="form-control" required>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Precio de Venta *</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input name="precio_venta" id="precioVenta" type="number" step="0.01" min="0" class="form-control" required>
            </div>
            <small id="margenInfo" class="text-muted"></small>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-success" type="submit">
          <i class="bi bi-save me-1"></i>Guardar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditProduct" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="formEditProduct" class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar producto</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_producto" id="edit_id">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Nombre *</label>
            <input id="edit_nombre" name="nombre" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Categoría *</label>
            <select name="id_categoria" id="edit_categoria" class="form-select" required></select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea id="edit_descripcion" name="descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Proveedor</label>
            <select name="id_proveedor" id="edit_proveedor" class="form-select">
              <option value="">Seleccione...</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Stock *</label>
            <input id="edit_stock" name="stock" type="number" min="0" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Precio de Compra *</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input id="edit_precio_compra" name="precio_compra" type="number" step="0.01" min="0" class="form-control" required>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Precio de Venta *</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input id="edit_precio_venta" name="precio_venta" type="number" step="0.01" min="0" class="form-control" required>
            </div>
            <small id="margenInfoEdit" class="text-muted"></small>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-save me-1"></i>Actualizar
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const alertsContainer = document.getElementById('alerts');
const stockAlertsContainer = document.getElementById('stockAlerts');

// Calcular margen
function calcularMargen(compra, venta) {
  if (compra > 0 && venta > 0) {
    const margen = ((venta - compra) / compra * 100).toFixed(1);
    return `Margen: ${margen}% ($${(venta - compra).toFixed(2)})`;
  }
  return '';
}

// Actualizar margen en crear
document.getElementById('precioCompra').addEventListener('input', updateMargen);
document.getElementById('precioVenta').addEventListener('input', updateMargen);
function updateMargen() {
  const compra = parseFloat(document.getElementById('precioCompra').value) || 0;
  const venta = parseFloat(document.getElementById('precioVenta').value) || 0;
  document.getElementById('margenInfo').textContent = calcularMargen(compra, venta);
}

// Actualizar margen en editar
document.getElementById('edit_precio_compra').addEventListener('input', updateMargenEdit);
document.getElementById('edit_precio_venta').addEventListener('input', updateMargenEdit);
function updateMargenEdit() {
  const compra = parseFloat(document.getElementById('edit_precio_compra').value) || 0;
  const venta = parseFloat(document.getElementById('edit_precio_venta').value) || 0;
  document.getElementById('margenInfoEdit').textContent = calcularMargen(compra, venta);
}

function showAlert(container, type, msg, autoClose = true) {
  const alert = document.createElement('div');
  alert.className = `alert alert-${type} alert-dismissible fade show`;
  alert.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  container.innerHTML = '';
  container.appendChild(alert);
  if (autoClose) setTimeout(() => alert.remove(), 4000);
}

// Verificar stock bajo
async function checkLowStock() {
  try {
    const res = await fetch('ajax/productos.php?action=check_stock');
    const data = await res.json();
    if (data.success && data.low_stock.length > 0) {
      let html = '<div class="alert alert-warning alert-stock alert-dismissible fade show" role="alert">';
      html += '<strong><i class="bi bi-exclamation-triangle me-2"></i>¡Alerta de Stock Bajo!</strong><br>';
      html += '<ul class="mb-0 mt-2">';
      data.low_stock.forEach(p => {
        const icon = p.stock === 0 ? '🔴' : p.stock <= 5 ? '🟠' : '🟡';
        html += `<li>${icon} <strong>${p.nombre}</strong>: Solo quedan ${p.stock} unidades</li>`;
      });
      html += '</ul>';
      html += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
      html += '</div>';
      stockAlertsContainer.innerHTML = html;
    } else {
      stockAlertsContainer.innerHTML = '';
    }
  } catch (err) {
    console.error('Error al verificar stock:', err);
  }
}

async function loadProductos() {
  try {
    const res = await fetch('ajax/productos.php?action=list');
    const data = await res.json();
    const tbody = document.getElementById('productosBody');
    tbody.innerHTML = '';
    
    if (!data.success) {
      showAlert(alertsContainer, 'danger', data.message);
      return;
    }
    
    data.products.forEach((p, i) => {
      const compra = parseFloat(p.precio_compra);
      const venta = parseFloat(p.precio_venta);
      const margen = compra > 0 ? ((venta - compra) / compra * 100).toFixed(0) : 0;
      const ganancia = (venta - compra).toFixed(2);
      
      let stockClass = 'stock-ok';
      let stockIcon = '✅';
      if (p.stock === 0 || p.stock === '0') {
        stockClass = 'stock-danger';
        stockIcon = '❌';
      } else if (p.stock <= 10) {
        stockClass = 'stock-warning';
        stockIcon = '⚠️';
      }
      
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td><strong>${p.nombre}</strong></td>
        <td>${p.categoria ?? '-'}</td>
        <td>${p.proveedor ?? '-'}</td>
        <td>$${Number(compra).toFixed(2)}</td>
        <td>$${Number(venta).toFixed(2)}</td>
        <td><span class="profit-badge">+${margen}% ($${ganancia})</span></td>
        <td><span class="stock-badge ${stockClass}">${stockIcon} ${p.stock}</span></td>
        <td>
          <button class="btn btn-sm btn-primary btn-action btn-edit" data-id="${p.id_producto}" title="Editar">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger btn-action btn-delete" data-id="${p.id_producto}" title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>`;
      tbody.appendChild(tr);
    });
    
    document.querySelectorAll('.btn-edit').forEach(b => b.onclick = onEditClick);
    document.querySelectorAll('.btn-delete').forEach(b => b.onclick = onDeleteClick);
    
    checkLowStock();
  } catch (err) {
    console.error('Error:', err);
    showAlert(alertsContainer, 'danger', 'Error al cargar productos');
  }
}

async function loadSelects() {
  try {
    const resCat = await fetch('ajax/productos.php?action=categories');
    const dataCat = await resCat.json();
    const selectsCat = [document.getElementById('selectCategoria'), document.getElementById('edit_categoria')];
    selectsCat.forEach(s => s.innerHTML = '<option value="">Seleccione...</option>');
    if (dataCat.success) {
      dataCat.categorias.forEach(c => {
        selectsCat.forEach(s => {
          const opt = document.createElement('option');
          opt.value = c.id_categoria;
          opt.textContent = c.nombre;
          s.appendChild(opt);
        });
      });
    }

    const resProv = await fetch('ajax/productos.php?action=proveedores');
    const dataProv = await resProv.json();
    const selectsProv = [document.getElementById('selectProveedor'), document.getElementById('edit_proveedor')];
    selectsProv.forEach(s => s.innerHTML = '<option value="">Seleccione...</option>');
    if (dataProv.success) {
      dataProv.proveedores.forEach(p => {
        selectsProv.forEach(s => {
          const opt = document.createElement('option');
          opt.value = p.id_proveedor;
          opt.textContent = p.nombre;
          s.appendChild(opt);
        });
      });
    }
  } catch (err) {
    console.error('Error cargando selects:', err);
  }
}

document.getElementById('formCreateProduct').addEventListener('submit', async e => {
  e.preventDefault();
  const form = new FormData(e.target);
  form.append('action', 'create');
  
  try {
    const res = await fetch('ajax/productos.php', { method: 'POST', body: form });
    const json = await res.json();
    if (json.success) {
      showAlert(alertsContainer, 'success', '✅ ' + json.message);
      e.target.reset();
      document.getElementById('margenInfo').textContent = '';
      bootstrap.Modal.getInstance(document.getElementById('modalCreateProduct')).hide();
      loadProductos();
    } else {
      showAlert(alertsContainer, 'danger', '❌ ' + json.message);
    }
  } catch (err) {
    showAlert(alertsContainer, 'danger', '❌ Error al crear producto');
  }
});

async function onEditClick() {
  const id = this.dataset.id;
  try {
    const res = await fetch('ajax/productos.php?action=get&id=' + id);
    const r = await res.json();
    if (!r.success) {
      showAlert(alertsContainer, 'danger', r.message);
      return;
    }

    document.getElementById('edit_id').value = r.product.id_producto;
    document.getElementById('edit_nombre').value = r.product.nombre;
    document.getElementById('edit_descripcion').value = r.product.descripcion;
    document.getElementById('edit_precio_compra').value = r.product.precio_compra;
    document.getElementById('edit_precio_venta').value = r.product.precio_venta;
    document.getElementById('edit_stock').value = r.product.stock;
    document.getElementById('edit_categoria').value = r.product.id_categoria;
    document.getElementById('edit_proveedor').value = r.product.id_proveedor || '';
    updateMargenEdit();

    new bootstrap.Modal(document.getElementById('modalEditProduct')).show();
  } catch (err) {
    showAlert(alertsContainer, 'danger', 'Error al cargar producto');
  }
}

document.getElementById('formEditProduct').addEventListener('submit', async e => {
  e.preventDefault();
  const form = new FormData(e.target);
  form.append('action', 'update');
  
  try {
    const res = await fetch('ajax/productos.php', { method: 'POST', body: form });
    const json = await res.json();
    if (json.success) {
      showAlert(alertsContainer, 'success', '✅ ' + json.message);
      bootstrap.Modal.getInstance(document.getElementById('modalEditProduct')).hide();
      loadProductos();
    } else {
      showAlert(alertsContainer, 'danger', '❌ ' + json.message);
    }
  } catch (err) {
    showAlert(alertsContainer, 'danger', '❌ Error al actualizar');
  }
});

async function onDeleteClick() {
  if (!confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')) return;
  
  const form = new FormData();
  form.append('action', 'delete');
  form.append('id_producto', this.dataset.id);
  
  try {
    const res = await fetch('ajax/productos.php', { method: 'POST', body: form });
    const json = await res.json();
    if (json.success) {
      showAlert(alertsContainer, 'success', '🗑️ ' + json.message);
      loadProductos();
    } else {
      showAlert(alertsContainer, 'danger', '❌ ' + json.message);
    }
  } catch (err) {
    showAlert(alertsContainer, 'danger', '❌ Error al eliminar');
  }
}

window.addEventListener('load', () => {
  loadSelects();
  loadProductos();
  setInterval(checkLowStock, 30000); // Verificar cada 30 segundos
});

// Agregar esto al script existente de inventario.php

// Cargar promociones activas
async function loadPromocionesActivas() {
  try {
    const res = await fetch('ajax/promociones.php?action=activas');
    const data = await res.json();
    
    if (data.success && data.promociones_activas && data.promociones_activas.length > 0) {
      // Crear alerta de promociones
      const alertDiv = document.createElement('div');
      alertDiv.className = 'alert alert-warning alert-dismissible fade show';
      alertDiv.style.animation = 'slideInRight 0.5s ease-out';
      alertDiv.innerHTML = `
        <strong><i class="bi bi-tag-fill me-2"></i>¡Productos en promoción!</strong><br>
        <ul class="mb-0 mt-2">
          ${data.promociones_activas.map(p => {
            const ahorro = (parseFloat(p.precio_original) - parseFloat(p.precio_promocion)).toFixed(2);
            return `<li>
              <strong>${p.producto}</strong>: 
              <del class="text-muted">$${parseFloat(p.precio_original).toFixed(2)}</del> 
              → <span class="text-danger fw-bold">$${parseFloat(p.precio_promocion).toFixed(2)}</span>
              (${p.descuento}% OFF - Ahorra $${ahorro})
              <small class="text-muted">- Hasta ${new Date(p.fecha_fin).toLocaleDateString('es-CO')}</small>
            </li>`;
          }).join('')}
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      // Insertar antes de la tabla
      const container = document.querySelector('.card-style');
      const alerts = document.getElementById('alerts');
      if (alerts.childNodes.length === 0) {
        alerts.appendChild(alertDiv);
      }
    }
  } catch (err) {
    console.error('Error cargando promociones:', err);
  }
}

// Modificar la función loadProductos para mostrar badge de promoción
async function loadProductosConPromociones() {
  try {
    const [resProductos, resPromociones] = await Promise.all([
      fetch('ajax/productos.php?action=list'),
      fetch('ajax/promociones.php?action=activas')
    ]);
    
    const dataProductos = await resProductos.json();
    const dataPromociones = await resPromociones.json();
    
    const tbody = document.getElementById('productosBody');
    tbody.innerHTML = '';
    
    if (!dataProductos.success) {
      showAlert(alertsContainer, 'danger', dataProductos.message);
      return;
    }
    
    // Crear mapa de promociones por id_producto
    const promocionesMap = new Map();
    if (dataPromociones.success && dataPromociones.promociones_activas) {
      dataPromociones.promociones_activas.forEach(p => {
        promocionesMap.set(parseInt(p.id_producto), p);
      });
    }
    
    dataProductos.products.forEach((p, i) => {
      const compra = parseFloat(p.precio_compra);
      const venta = parseFloat(p.precio_venta);
      const margen = compra > 0 ? ((venta - compra) / compra * 100).toFixed(0) : 0;
      const ganancia = (venta - compra).toFixed(2);
      
      let stockClass = 'stock-ok';
      let stockIcon = '✅';
      if (p.stock === 0 || p.stock === '0') {
        stockClass = 'stock-danger';
        stockIcon = '❌';
      } else if (p.stock <= 10) {
        stockClass = 'stock-warning';
        stockIcon = '⚠️';
      }
      
      // Verificar si tiene promoción activa
      const promo = promocionesMap.get(parseInt(p.id_producto));
      let promoHTML = '';
      let precioDisplay = `$${venta.toFixed(2)}`;
      
      if (promo) {
        const precioPromo = parseFloat(promo.precio_promocion);
        promoHTML = `<span class="badge bg-danger ms-2 animate-pulse" style="animation: pulse 1.5s infinite;">
          🏷️ ${promo.descuento}% OFF
        </span>`;
        precioDisplay = `<del class="text-muted">$${venta.toFixed(2)}</del> 
          <strong class="text-danger">$${precioPromo.toFixed(2)}</strong>`;
      }
      
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td><strong>${p.nombre}</strong>${promoHTML}</td>
        <td>${p.categoria ?? '-'}</td>
        <td>${p.proveedor ?? '-'}</td>
        <td>$${compra.toFixed(2)}</td>
        <td>${precioDisplay}</td>
        <td><span class="profit-badge">+${margen}% ($${ganancia})</span></td>
        <td><span class="stock-badge ${stockClass}">${stockIcon} ${p.stock}</span></td>
        <td>
          <button class="btn btn-sm btn-primary btn-action btn-edit" data-id="${p.id_producto}" title="Editar">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger btn-action btn-delete" data-id="${p.id_producto}" title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>`;
      tbody.appendChild(tr);
    });
    
    document.querySelectorAll('.btn-edit').forEach(b => b.onclick = onEditClick);
    document.querySelectorAll('.btn-delete').forEach(b => b.onclick = onDeleteClick);
    
    checkLowStock();
  } catch (err) {
    console.error('Error:', err);
    showAlert(alertsContainer, 'danger', 'Error al cargar productos');
  }
}

// CSS para la animación del badge
const style = document.createElement('style');
style.textContent = `
@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.05); opacity: 0.9; }
}
.animate-pulse {
  animation: pulse 1.5s ease-in-out infinite;
}
`;
document.head.appendChild(style);

// Al cargar la página, usar la nueva función
window.addEventListener('load', () => {
  loadSelects();
  loadProductosConPromociones();
  loadPromocionesActivas();
  setInterval(checkLowStock, 30000);
});
</script>

<?php include("includes/footer.php"); ?>