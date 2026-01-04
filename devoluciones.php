<?php
include("includes/header.php");
include("conexion.php");
require_once("includes/Permisos.php");

requiere_permiso('devoluciones.ver');
?>

<style>
.return-card {
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
.return-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}
.return-reason {
  padding: 5px 10px;
  background: #fee2e2;
  color: #991b1b;
  border-radius: 8px;
  font-size: 0.85rem;
}
</style>

<div class="container-fluid mt-4">
  <div class="return-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-arrow-return-left me-2"></i>Gestión de Devoluciones</h4>
        <p class="text-muted small mb-0">Registra devoluciones de productos y actualiza el inventario</p>
      </div>
      <button class="btn btn-danger" style="border-radius: 25px; padding: 10px 25px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalDevolucion">
        <i class="bi bi-plus-circle me-2"></i>Nueva Devolución
      </button>
    </div>

    <div id="alertDevoluciones"></div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card border-danger">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="bi bi-arrow-return-left text-danger me-3" style="font-size: 2rem;"></i>
              <div>
                <h6 class="text-muted mb-0">Total Devoluciones (30 días)</h6>
                <h3 class="mb-0" id="totalDevoluciones">0</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-warning">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="bi bi-box text-warning me-3" style="font-size: 2rem;"></i>
              <div>
                <h6 class="text-muted mb-0">Unidades Devueltas</h6>
                <h3 class="mb-0" id="unidadesDevueltas">0</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="bi bi-cash-stack text-info me-3" style="font-size: 2rem;"></i>
              <div>
                <h6 class="text-muted mb-0">Valor Devuelto</h6>
                <h3 class="mb-0" id="valorDevuelto">$0</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle" id="tablaDevoluciones">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Valor</th>
            <th>Motivo</th>
            <th>Observaciones</th>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Crear Devolución -->
<div class="modal fade" id="modalDevolucion">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="formDevolucion" class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
        <h5 class="modal-title"><i class="bi bi-arrow-return-left me-2"></i>Registrar Devolución</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label fw-semibold"><i class="bi bi-box me-2"></i>Producto</label>
            <select name="id_producto" id="id_producto" class="form-select" required onchange="updateProductInfo()">
              <option value="">Seleccione un producto...</option>
            </select>
          </div>
          
          <div class="col-md-4">
            <label class="form-label fw-semibold"><i class="bi bi-hash me-2"></i>Cantidad</label>
            <input type="number" class="form-control" name="cantidad" id="cantidad" min="1" required onchange="updateProductInfo()">
          </div>
          
          <div class="col-12">
            <div id="productInfo" class="alert alert-info" style="display:none;">
              <strong>Información del producto:</strong><br>
              <span id="infoText"></span>
            </div>
          </div>
          
          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-exclamation-triangle me-2"></i>Motivo de la devolución</label>
            <select name="motivo" class="form-select" required>
              <option value="">Seleccione un motivo...</option>
              <option value="Producto dañado">Producto dañado</option>
              <option value="Producto vencido">Producto vencido</option>
              <option value="Error en el pedido">Error en el pedido</option>
              <option value="Cliente insatisfecho">Cliente insatisfecho</option>
              <option value="Defecto de fábrica">Defecto de fábrica</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
          
          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-chat-dots me-2"></i>Observaciones adicionales</label>
            <textarea class="form-control" name="observaciones" rows="3" placeholder="Detalles adicionales sobre la devolución..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">
          <i class="bi bi-save me-2"></i>Registrar Devolución
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
  document.getElementById('alertDevoluciones').innerHTML = '';
  document.getElementById('alertDevoluciones').appendChild(alert);
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
        opt.textContent = `${p.nombre} - Stock: ${p.stock}`;
        opt.dataset.precio = p.precio_venta;
        opt.dataset.stock = p.stock;
        opt.dataset.nombre = p.nombre;
        select.appendChild(opt);
      });
    }
  } catch (err) {
    console.error('Error cargando productos:', err);
  }
}

function updateProductInfo() {
  const select = document.getElementById('id_producto');
  const option = select.options[select.selectedIndex];
  const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
  
  if (option.value && cantidad > 0) {
    const precio = parseFloat(option.dataset.precio);
    const stock = parseInt(option.dataset.stock);
    const nombre = option.dataset.nombre;
    const total = precio * cantidad;
    
    document.getElementById('infoText').innerHTML = 
      `📦 <strong>${nombre}</strong><br>` +
      `💰 Precio unitario: $${precio.toFixed(2)}<br>` +
      `📊 Stock actual: ${stock} unidades<br>` +
      `💸 Valor total de devolución: <strong class="text-danger">$${total.toFixed(2)}</strong><br>` +
      `📈 Stock después de la devolución: ${stock + cantidad} unidades`;
    
    document.getElementById('productInfo').style.display = 'block';
  } else {
    document.getElementById('productInfo').style.display = 'none';
  }
}

async function loadResumen() {
  try {
    const res = await fetch('ajax/devoluciones.php?action=resumen');
    const data = await res.json();
    
    if (data.success) {
      const r = data.resumen;
      document.getElementById('totalDevoluciones').textContent = r.total_devoluciones;
      document.getElementById('unidadesDevueltas').textContent = r.unidades_devueltas;
      document.getElementById('valorDevuelto').textContent = '$' + Number(r.valor_devuelto).toLocaleString('es-CO', {minimumFractionDigits: 2});
    }
  } catch (err) {
    console.error('Error cargando resumen:', err);
  }
}

async function loadDevoluciones() {
  try {
    const res = await fetch('ajax/devoluciones.php?action=list');
    const data = await res.json();
    const tbody = document.querySelector('#tablaDevoluciones tbody');
    tbody.innerHTML = '';
    
    if (!data.success || !data.devoluciones || data.devoluciones.length === 0) {
      tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay devoluciones registradas</td></tr>';
      return;
    }
    
    data.devoluciones.forEach((d, i) => {
      const valor = parseFloat(d.cantidad) * parseFloat(d.precio_unitario);
      
      const tr = document.createElement('tr');
      tr.style.animation = `fadeIn 0.5s ease-out ${i * 0.05}s both`;
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td>
          <div class="d-flex align-items-center">
            <div class="return-icon me-3">
              <i class="bi bi-box"></i>
            </div>
            <strong>${d.producto}</strong>
          </div>
        </td>
        <td><span class="badge bg-warning text-dark">${d.cantidad} unidades</span></td>
        <td><strong class="text-danger">$${valor.toFixed(2)}</strong></td>
        <td><span class="return-reason">${d.motivo}</span></td>
        <td><small>${d.observaciones || '<span class="text-muted">-</span>'}</small></td>
        <td><small class="text-muted">${new Date(d.fecha_devolucion).toLocaleDateString('es-CO')}</small></td>
        <td><small>${d.usuario || 'Sistema'}</small></td>
        <td>
          <button class="btn btn-sm btn-danger" onclick='deleteDevolucion(${d.id_devolucion})' title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error('Error:', err);
    showAlert('danger', '❌ Error al cargar devoluciones');
  }
}

async function deleteDevolucion(id) {
  if (!confirm('¿Eliminar esta devolución? Esto revertirá el movimiento del inventario.')) return;
  
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id_devolucion', id);
  
  try {
    const res = await fetch('ajax/devoluciones.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    if (j.success) {
      loadDevoluciones();
      loadResumen();
    }
  } catch (err) {
    showAlert('danger', '❌ Error al eliminar');
  }
}

document.getElementById('formDevolucion').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  fd.append('action', 'create');
  
  try {
    const res = await fetch('ajax/devoluciones.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    
    if (j.success) {
      bootstrap.Modal.getInstance('#modalDevolucion').hide();
      e.target.reset();
      document.getElementById('productInfo').style.display = 'none';
      loadDevoluciones();
      loadResumen();
    }
  } catch (err) {
    showAlert('danger', '❌ Error al guardar');
  }
});

document.getElementById('modalDevolucion').addEventListener('hidden.bs.modal', function() {
  document.getElementById('formDevolucion').reset();
  document.getElementById('productInfo').style.display = 'none';
});

window.addEventListener('load', () => {
  loadProductos();
  loadDevoluciones();
  loadResumen();
});
</script>

<?php include("includes/footer.php"); ?>