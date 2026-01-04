<?php
include 'includes/header.php';
include 'conexion.php';

require_once("includes/Permisos.php");

requiere_permiso('vendedores.ver');
?>

<style>
.seller-card {
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
.seller-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}
.zone-badge {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  background: #fef3c7;
  color: #92400e;
}
</style>

<div class="container-fluid mt-4">
  <div class="seller-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-person-badge me-2"></i>Gestión de Vendedores</h4>
        <p class="text-muted small mb-0">Administra vendedores ambulantes y sus zonas</p>
      </div>
      <button class="btn btn-warning btn-action" data-bs-toggle="modal" data-bs-target="#modalVendedor">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Vendedor
      </button>
    </div>

    <div id="alertVendedores"></div>

    <div class="table-responsive">
      <table class="table table-hover align-middle" id="tablaVendedores">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Vendedor</th>
            <th>Zona Asignada</th>
            <th>Fecha de Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalVendedor">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formVendedor" class="modal-content">
      <div class="modal-header text-dark" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
        <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i><span id="modalTitle">Nuevo Vendedor</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_vendedor" id="id_vendedor">
        
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-person me-2"></i>Seleccionar usuario</label>
          <select name="id_usuario" id="id_usuario" class="form-select" required>
            <option value="">Seleccione un usuario...</option>
          </select>
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-geo-alt me-2"></i>Zona asignada</label>
          <input type="text" class="form-control" name="zona" id="zona" placeholder="Ej: Centro, Norte, Sur..." required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">
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
  document.getElementById('alertVendedores').innerHTML = '';
  document.getElementById('alertVendedores').appendChild(alert);
  setTimeout(() => alert.remove(), 4000);
}

async function loadUsuarios() {
  try {
    const res = await fetch('ajax/usuarios.php?action=list');
    const data = await res.json();
    const select = document.getElementById('id_usuario');
    select.innerHTML = '<option value="">Seleccione un usuario...</option>';
    
    if (data.success && data.users) {
      data.users.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id_usuario;
        opt.textContent = u.nombre;
        select.appendChild(opt);
      });
    }
  } catch (err) {
    console.error('Error cargando usuarios:', err);
  }
}

async function loadVendedores() {
  try {
    const res = await fetch('ajax/vendedores.php?action=list');
    const data = await res.json();
    const tbody = document.querySelector('#tablaVendedores tbody');
    tbody.innerHTML = '';
    
    if (!data.success || !data.vendedores || data.vendedores.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No hay vendedores registrados</td></tr>';
      return;
    }
    
    data.vendedores.forEach((v, i) => {
      const tr = document.createElement('tr');
      tr.style.animation = `fadeIn 0.5s ease-out ${i * 0.05}s both`;
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td>
          <div class="d-flex align-items-center">
            <div class="seller-icon me-3">
              <i class="bi bi-person-badge"></i>
            </div>
            <strong>${v.usuario}</strong>
          </div>
        </td>
        <td><span class="zone-badge"><i class="bi bi-geo-alt me-1"></i>${v.zona}</span></td>
        <td><small class="text-muted">${new Date(v.fecha_registro).toLocaleDateString('es-CO')}</small></td>
        <td>
          <button class="btn btn-sm btn-primary me-1" onclick='editVendedor(${JSON.stringify(v)})' title="Editar">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger" onclick='deleteVendedor(${v.id_vendedor})' title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error('Error:', err);
    showAlert('danger', '❌ Error al cargar vendedores');
  }
}

function editVendedor(v) {
  document.getElementById('modalTitle').textContent = 'Editar Vendedor';
  document.getElementById('id_vendedor').value = v.id_vendedor;
  document.getElementById('id_usuario').value = v.id_usuario;
  document.getElementById('zona').value = v.zona;
  new bootstrap.Modal('#modalVendedor').show();
}

async function deleteVendedor(id) {
  if (!confirm('¿Eliminar este vendedor?')) return;
  
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id_vendedor', id);
  
  try {
    const res = await fetch('ajax/vendedores.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    if (j.success) loadVendedores();
  } catch (err) {
    showAlert('danger', '❌ Error al eliminar');
  }
}

document.getElementById('formVendedor').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const isEdit = fd.get('id_vendedor');
  fd.append('action', isEdit ? 'update' : 'create');
  
  try {
    const res = await fetch('ajax/vendedores.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    
    if (j.success) {
      bootstrap.Modal.getInstance('#modalVendedor').hide();
      e.target.reset();
      document.getElementById('modalTitle').textContent = 'Nuevo Vendedor';
      loadVendedores();
    }
  } catch (err) {
    showAlert('danger', '❌ Error al guardar');
  }
});

document.getElementById('modalVendedor').addEventListener('hidden.bs.modal', function() {
  document.getElementById('formVendedor').reset();
  document.getElementById('modalTitle').textContent = 'Nuevo Vendedor';
});

window.addEventListener('load', () => {
  loadUsuarios();
  loadVendedores();
});
</script>

<?php include 'includes/footer.php'; ?>