<?php
include("includes/header.php");
include("conexion.php");

if ($_SESSION['rol'] != 'administrador') {
    header("Location: panel.php");
    exit();
}
?>

<style>
.supplier-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
  animation: slideIn 0.5s ease-out;
}
@keyframes slideIn {
  from { opacity: 0; transform: translateX(-30px); }
  to { opacity: 1; transform: translateX(0); }
}
.supplier-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}
.btn-supplier {
  border-radius: 25px;
  padding: 10px 25px;
  font-weight: 600;
  transition: all 0.3s ease;
}
.btn-supplier:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.contact-info {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #6b7280;
}
</style>

<div class="container-fluid mt-4">
  <div class="supplier-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-truck me-2"></i>Gestión de Proveedores</h4>
        <p class="text-muted small mb-0">Administra tus proveedores y contactos</p>
      </div>
      <button class="btn btn-info btn-supplier text-white" data-bs-toggle="modal" data-bs-target="#modalProveedor">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Proveedor
      </button>
    </div>

    <div id="alertProveedores"></div>

    <div class="table-responsive">
      <table class="table table-hover align-middle" id="tablaProveedores">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Proveedor</th>
            <th>Contacto</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Dirección</th>
            <th>Productos</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalProveedor">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="formProveedor" class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <h5 class="modal-title"><i class="bi bi-building me-2"></i><span id="modalTitle">Nuevo Proveedor</span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_proveedor" id="id_proveedor">
        
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-building me-2"></i>Nombre del proveedor</label>
            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ej: Distribuidora XYZ" required>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-person me-2"></i>Persona de contacto</label>
            <input type="text" class="form-control" name="contacto" id="contacto" placeholder="Nombre del contacto">
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-telephone me-2"></i>Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="telefono" placeholder="300 123 4567">
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-envelope me-2"></i>Correo electrónico</label>
            <input type="email" class="form-control" name="correo" id="correo" placeholder="proveedor@ejemplo.com">
          </div>
          
          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-geo-alt me-2"></i>Dirección</label>
            <textarea class="form-control" name="direccion" id="direccion" rows="2" placeholder="Dirección completa del proveedor"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-info text-white">
          <i class="bi bi-save me-2"></i>Guardar
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
  document.getElementById('alertProveedores').innerHTML = '';
  document.getElementById('alertProveedores').appendChild(alert);
  setTimeout(() => alert.remove(), 4000);
}

async function loadProveedores() {
  try {
    const res = await fetch('ajax/proveedores.php?action=list');
    const data = await res.json();
    const tbody = document.querySelector('#tablaProveedores tbody');
    tbody.innerHTML = '';
    
    if (!data.success || !data.proveedores || data.proveedores.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No hay proveedores registrados</td></tr>';
      return;
    }
    
    data.proveedores.forEach((p, i) => {
      const tr = document.createElement('tr');
      tr.style.animation = `fadeIn 0.5s ease-out ${i * 0.05}s both`;
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td>
          <div class="d-flex align-items-center">
            <div class="supplier-icon me-3">
              <i class="bi bi-truck"></i>
            </div>
            <strong>${p.nombre}</strong>
          </div>
        </td>
        <td>${p.contacto || '<span class="text-muted">-</span>'}</td>
        <td><div class="contact-info"><i class="bi bi-telephone"></i>${p.telefono || '-'}</div></td>
        <td><div class="contact-info"><i class="bi bi-envelope"></i>${p.correo || '-'}</div></td>
        <td>${p.direccion || '<span class="text-muted">-</span>'}</td>
        <td><span class="badge bg-info">${p.total_productos || 0} productos</span></td>
        <td>
          <button class="btn btn-sm btn-primary me-1" onclick='editProveedor(${JSON.stringify(p)})' title="Editar">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger" onclick='deleteProveedor(${p.id_proveedor})' title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error('Error:', err);
    showAlert('danger', '❌ Error al cargar proveedores');
  }
}

function editProveedor(p) {
  document.getElementById('modalTitle').textContent = 'Editar Proveedor';
  document.getElementById('id_proveedor').value = p.id_proveedor;
  document.getElementById('nombre').value = p.nombre;
  document.getElementById('contacto').value = p.contacto || '';
  document.getElementById('telefono').value = p.telefono || '';
  document.getElementById('correo').value = p.correo || '';
  document.getElementById('direccion').value = p.direccion || '';
  new bootstrap.Modal('#modalProveedor').show();
}

async function deleteProveedor(id) {
  if (!confirm('¿Eliminar este proveedor? Los productos asociados quedarán sin proveedor.')) return;
  
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id_proveedor', id);
  
  try {
    const res = await fetch('ajax/proveedores.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    if (j.success) loadProveedores();
  } catch (err) {
    showAlert('danger', '❌ Error al eliminar');
  }
}

document.getElementById('formProveedor').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const isEdit = fd.get('id_proveedor');
  fd.append('action', isEdit ? 'update' : 'create');
  
  try {
    const res = await fetch('ajax/proveedores.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    
    if (j.success) {
      bootstrap.Modal.getInstance('#modalProveedor').hide();
      e.target.reset();
      document.getElementById('modalTitle').textContent = 'Nuevo Proveedor';
      loadProveedores();
    }
  } catch (err) {
    showAlert('danger', '❌ Error al guardar');
  }
});

document.getElementById('modalProveedor').addEventListener('hidden.bs.modal', function() {
  document.getElementById('formProveedor').reset();
  document.getElementById('modalTitle').textContent = 'Nuevo Proveedor';
});

window.addEventListener('load', loadProveedores);
</script>

<?php include("includes/footer.php"); ?>