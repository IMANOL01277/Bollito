<?php
include("includes/header.php");
?>

<style>
.user-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
  animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.user-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 1.2rem;
}
.role-badge {
  padding: 5px 15px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}
.role-admin { background: #fbbf24; color: #78350f; }
.role-empleado { background: #a7f3d0; color: #065f46; }
.btn-action {
  transition: all 0.3s ease;
  border-radius: 8px;
}
.btn-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 10px rgba(0,0,0,0.2);
}
.password-strength {
  height: 5px;
  border-radius: 3px;
  transition: all 0.3s ease;
}
</style>

<div class="container-fluid mt-4">
  <div class="user-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h4>
        <p class="text-muted small mb-0">Administra los usuarios del sistema</p>
      </div>
      <button class="btn btn-success btn-action" data-bs-toggle="modal" data-bs-target="#modalUser">
        <i class="bi bi-person-plus me-2"></i>Nuevo Usuario
      </button>
    </div>

    <div id="alertUsers"></div>

    <div class="table-responsive">
      <table class="table table-hover align-middle" id="tablaUsuarios">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Fecha Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalUser" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formUser" class="modal-content">
      <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i><span id="modalTitle">Nuevo Usuario</span></h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_usuario" id="id_usuario">
        
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-person me-2"></i>Nombre completo</label>
          <input name="nombre" id="nombre" class="form-control" placeholder="Juan Pérez" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-envelope me-2"></i>Correo electrónico</label>
          <input name="correo" id="correo" type="email" class="form-control" placeholder="usuario@ejemplo.com" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-lock me-2"></i>Contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
          <input name="contraseña" id="contraseña" type="password" class="form-control" placeholder="••••••••">
          <div class="password-strength bg-secondary mt-2" id="passwordStrength"></div>
          <small class="text-muted" id="passwordHint">Mínimo 8 caracteres, una mayúscula y un carácter especial</small>
        </div>
        
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-shield-check me-2"></i>Rol del usuario</label>
          <select name="rol" id="rol" class="form-select">
            <option value="empleado">👤 Empleado</option>
            <option value="administrador">👑 Administrador</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-success" type="submit">
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
  document.getElementById('alertUsers').innerHTML = '';
  document.getElementById('alertUsers').appendChild(alert);
  setTimeout(() => alert.remove(), 4000);
}

async function loadUsers() {
  try {
    const res = await fetch('ajax/usuarios.php?action=list');
    const data = await res.json();
    const tbody = document.querySelector('#tablaUsuarios tbody');
    tbody.innerHTML = '';
    
    if (!data.success || !data.users) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados</td></tr>';
      return;
    }
    
    data.users.forEach((u, i) => {
      const roleClass = u.rol === 'administrador' ? 'role-admin' : 'role-empleado';
      const roleIcon = u.rol === 'administrador' ? '👑' : '👤';
      const initials = u.nombre.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
      
      const tr = document.createElement('tr');
      tr.style.animation = `fadeIn 0.5s ease-out ${i * 0.05}s both`;
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td>
          <div class="d-flex align-items-center">
            <div class="user-avatar me-3">${initials}</div>
            <strong>${u.nombre}</strong>
          </div>
        </td>
        <td><i class="bi bi-envelope me-2 text-primary"></i>${u.correo}</td>
        <td><span class="role-badge ${roleClass}">${roleIcon} ${u.rol}</span></td>
        <td><small class="text-muted">${new Date().toLocaleDateString('es-CO')}</small></td>
        <td>
          <button class="btn btn-sm btn-primary btn-action me-1" onclick='editUser(${JSON.stringify(u)})' title="Editar">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger btn-action" onclick='deleteUser(${u.id_usuario})' title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error('Error:', err);
    showAlert('danger', '❌ Error al cargar usuarios');
  }
}

function editUser(u) {
  document.getElementById('modalTitle').textContent = 'Editar Usuario';
  document.getElementById('id_usuario').value = u.id_usuario;
  document.getElementById('nombre').value = u.nombre;
  document.getElementById('correo').value = u.correo;
  document.getElementById('rol').value = u.rol;
  document.getElementById('contraseña').value = '';
  new bootstrap.Modal('#modalUser').show();
}

async function deleteUser(id) {
  if (!confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')) return;
  
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id_usuario', id);
  
  try {
    const res = await fetch('ajax/usuarios.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    if (j.success) loadUsers();
  } catch (err) {
    showAlert('danger', '❌ Error al eliminar usuario');
  }
}

// Validación de contraseña en tiempo real
document.getElementById('contraseña').addEventListener('input', function() {
  const password = this.value;
  const strength = document.getElementById('passwordStrength');
  const hint = document.getElementById('passwordHint');
  
  if (password.length === 0) {
    strength.style.width = '0%';
    strength.className = 'password-strength bg-secondary mt-2';
    return;
  }
  
  let score = 0;
  if (password.length >= 8) score++;
  if (/[A-Z]/.test(password)) score++;
  if (/[\W_]/.test(password)) score++;
  
  const colors = ['bg-danger', 'bg-warning', 'bg-success'];
  const widths = ['33%', '66%', '100%'];
  const messages = ['Débil', 'Media', 'Fuerte'];
  
  strength.style.width = widths[score - 1] || '33%';
  strength.className = `password-strength mt-2 ${colors[score - 1] || 'bg-danger'}`;
  hint.textContent = score === 3 ? '✅ Contraseña fuerte' : messages[score - 1] || 'Débil';
  hint.className = score === 3 ? 'text-success' : 'text-muted';
});

document.getElementById('formUser').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const isEdit = fd.get('id_usuario');
  fd.append('action', isEdit ? 'update' : 'create');
  
  try {
    const res = await fetch('ajax/usuarios.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    
    if (j.success) {
      bootstrap.Modal.getInstance('#modalUser').hide();
      e.target.reset();
      document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
      loadUsers();
    }
  } catch (err) {
    showAlert('danger', '❌ Error al guardar usuario');
  }
});

// Reset modal al cerrar
document.getElementById('modalUser').addEventListener('hidden.bs.modal', function() {
  document.getElementById('formUser').reset();
  document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
  document.getElementById('passwordStrength').style.width = '0%';
});

window.addEventListener('load', loadUsers);
</script>

<?php include("includes/footer.php"); ?>