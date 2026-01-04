<?php
include("includes/header.php");
include("conexion.php");

?>

<style>
.promo-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
  animation: slideIn 0.5s ease-out;
}
@keyframes slideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.promo-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}
.promo-badge {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}
.promo-active { background: #10b981; color: white; }
.promo-expired { background: #ef4444; color: white; }
.promo-pending { background: #fbbf24; color: #78350f; }
.discount-tag {
  display: inline-block;
  padding: 8px 15px;
  background: #fef3c7;
  color: #92400e;
  border-radius: 25px;
  font-weight: bold;
  font-size: 1.1rem;
}
</style>

<div class="container-fluid mt-4">
  <div class="promo-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-tag-fill me-2"></i>Gestión de Promociones</h4>
        <p class="text-muted small mb-0">Crea y administra promociones para tus productos</p>
      </div>
      <button class="btn btn-danger" style="border-radius: 25px; padding: 10px 25px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalPromocion">
        <i class="bi bi-percent me-2"></i>Nueva Promoción
      </button>
    </div>

    <div id="alertPromociones"></div>

    <div class="table-responsive">
      <table class="table table-hover align-middle" id="tablaPromociones">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Descuento</th>
            <th>Precio Original</th>
            <th>Precio Promoción</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalPromocion">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="formPromocion" class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);">
        <h5 class="modal-title"><i class="bi bi-tag-fill me-2"></i><span id="modalTitle">Nueva Promoción</span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_promocion" id="id_promocion">
        
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-box me-2"></i>Producto</label>
            <select name="id_producto" id="id_producto" class="form-select" required onchange="updatePrecio()">
              <option value="">Seleccione un producto...</option>
            </select>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-percent me-2"></i>Descuento (%)</label>
            <input type="number" class="form-control" name="descuento" id="descuento" min="1" max="99" step="0.01" required onchange="calcularPrecioPromo()">
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-currency-dollar me-2"></i>Precio Original</label>
            <input type="text" class="form-control" id="precio_original_display" readonly>
            <input type="hidden" id="precio_original_value">
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-currency-dollar me-2"></i>Precio en Promoción</label>
            <input type="text" class="form-control" id="precio_promocion_display" readonly>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-calendar-check me-2"></i>Fecha de Inicio</label>
            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-calendar-x me-2"></i>Fecha de Fin</label>
            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" required>
          </div>
        </div>
        
        <div class="alert alert-info mt-3" id="alertPreview" style="display:none;">
          <strong>Vista previa:</strong><br>
          <span id="preview_text"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">
          <i class="bi bi-save me-2"></i>Guardar Promoción
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function showAlert(type, msg) {
  const alert = document.createElement('div');
  alert.className = `alert alert-${type} alert-dismissible fade show`;
  alert.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  document.getElementById('alertPromociones').innerHTML = '';
  document.getElementById('alertPromociones').appendChild(alert);
  setTimeout(() => alert.remove(), 4000);
}

async function loadProductos() {
  try {
    const res = await fetch('ajax/productos.php?action=list');
    const data = await res.json();
    const select = document.getElementById('id_producto');
    select.innerHTML = '<option value="">Seleccione un producto...</option>';
    
    if (data.success && data.products) {
      data.products.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id_producto;
        opt.textContent = `${p.nombre} - $${Number(p.precio_venta).toFixed(2)}`;
        opt.dataset.precio = p.precio_venta;
        select.appendChild(opt);
      });
    }
  } catch (err) {
    console.error('Error cargando productos:', err);
  }
}

function updatePrecio() {
  const select = document.getElementById('id_producto');
  const option = select.options[select.selectedIndex];
  
  if (option.value) {
    const precio = parseFloat(option.dataset.precio);
    document.getElementById('precio_original_display').value = '$' + precio.toFixed(2);
    document.getElementById('precio_original_value').value = precio;
    calcularPrecioPromo();
  } else {
    document.getElementById('precio_original_display').value = '';
    document.getElementById('precio_promocion_display').value = '';
    document.getElementById('alertPreview').style.display = 'none';
  }
}

function calcularPrecioPromo() {
  const precioOriginal = parseFloat(document.getElementById('precio_original_value').value);
  const descuento = parseFloat(document.getElementById('descuento').value);
  
  if (precioOriginal && descuento) {
    const precioPromo = precioOriginal - (precioOriginal * (descuento / 100));
    document.getElementById('precio_promocion_display').value = '$' + precioPromo.toFixed(2);
    
    const selectProducto = document.getElementById('id_producto');
    const nombreProducto = selectProducto.options[selectProducto.selectedIndex].text.split(' - ')[0];
    
    document.getElementById('alertPreview').style.display = 'block';
    document.getElementById('preview_text').innerHTML = 
      `<strong>${nombreProducto}</strong><br>` +
      `🏷️ <del>$${precioOriginal.toFixed(2)}</del> → <span class="text-danger fw-bold">$${precioPromo.toFixed(2)}</span><br>` +
      `💰 Ahorro: $${(precioOriginal - precioPromo).toFixed(2)} (${descuento}% OFF)`;
  }
}

async function loadPromociones() {
  try {
    const res = await fetch('ajax/promociones.php?action=list');
    const data = await res.json();
    const tbody = document.querySelector('#tablaPromociones tbody');
    tbody.innerHTML = '';
    
    if (!data.success || !data.promociones || data.promociones.length === 0) {
      tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay promociones registradas</td></tr>';
      return;
    }
    
    const hoy = new Date().toISOString().split('T')[0];
    
    data.promociones.forEach((p, i) => {
      let estadoClass, estadoText, estadoIcon;
      
      if (p.fecha_fin < hoy) {
        estadoClass = 'promo-expired';
        estadoText = 'Expirada';
        estadoIcon = '❌';
      } else if (p.fecha_inicio > hoy) {
        estadoClass = 'promo-pending';
        estadoText = 'Pendiente';
        estadoIcon = '⏳';
      } else {
        estadoClass = 'promo-active';
        estadoText = 'Activa';
        estadoIcon = '✅';
      }
      
      const precioOriginal = parseFloat(p.precio_original);
      const precioPromo = precioOriginal - (precioOriginal * (parseFloat(p.descuento) / 100));
      
      const tr = document.createElement('tr');
      tr.style.animation = `fadeIn 0.5s ease-out ${i * 0.05}s both`;
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td>
          <div class="d-flex align-items-center">
            <div class="promo-icon me-3">
              <i class="bi bi-tag-fill"></i>
            </div>
            <strong>${p.producto}</strong>
          </div>
        </td>
        <td><span class="discount-tag">${p.descuento}% OFF</span></td>
        <td><del class="text-muted">$${precioOriginal.toFixed(2)}</del></td>
        <td><strong class="text-danger">$${precioPromo.toFixed(2)}</strong></td>
        <td><small>${new Date(p.fecha_inicio).toLocaleDateString('es-CO')}</small></td>
        <td><small>${new Date(p.fecha_fin).toLocaleDateString('es-CO')}</small></td>
        <td><span class="promo-badge ${estadoClass}">${estadoIcon} ${estadoText}</span></td>
        <td>
          <button class="btn btn-sm btn-primary me-1" onclick='editPromocion(${JSON.stringify(p)})' title="Editar">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger" onclick='deletePromocion(${p.id_promocion})' title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error('Error:', err);
    showAlert('danger', '❌ Error al cargar promociones');
  }
}

function editPromocion(p) {
  document.getElementById('modalTitle').textContent = 'Editar Promoción';
  document.getElementById('id_promocion').value = p.id_promocion;
  document.getElementById('id_producto').value = p.id_producto;
  document.getElementById('descuento').value = p.descuento;
  document.getElementById('fecha_inicio').value = p.fecha_inicio;
  document.getElementById('fecha_fin').value = p.fecha_fin;
  updatePrecio();
  new bootstrap.Modal('#modalPromocion').show();
}

async function deletePromocion(id) {
  if (!confirm('¿Eliminar esta promoción?')) return;
  
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id_promocion', id);
  
  try {
    const res = await fetch('ajax/promociones.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    if (j.success) loadPromociones();
  } catch (err) {
    showAlert('danger', '❌ Error al eliminar');
  }
}

document.getElementById('formPromocion').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const isEdit = fd.get('id_promocion');
  fd.append('action', isEdit ? 'update' : 'create');
  
  // Validar fechas
  const inicio = new Date(fd.get('fecha_inicio'));
  const fin = new Date(fd.get('fecha_fin'));
  
  if (fin <= inicio) {
    showAlert('warning', '⚠️ La fecha de fin debe ser posterior a la fecha de inicio');
    return;
  }
  
  try {
    const res = await fetch('ajax/promociones.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    
    if (j.success) {
      bootstrap.Modal.getInstance('#modalPromocion').hide();
      e.target.reset();
      document.getElementById('modalTitle').textContent = 'Nueva Promoción';
      document.getElementById('alertPreview').style.display = 'none';
      loadPromociones();
    }
  } catch (err) {
    showAlert('danger', '❌ Error al guardar');
  }
});

document.getElementById('modalPromocion').addEventListener('hidden.bs.modal', function() {
  document.getElementById('formPromocion').reset();
  document.getElementById('modalTitle').textContent = 'Nueva Promoción';
  document.getElementById('alertPreview').style.display = 'none';
});

// Establecer fecha mínima como hoy
document.getElementById('fecha_inicio').min = new Date().toISOString().split('T')[0];
document.getElementById('fecha_fin').min = new Date().toISOString().split('T')[0];

window.addEventListener('load', () => {
  loadProductos();
  loadPromociones();
});
</script>

<?php include("includes/footer.php"); ?>