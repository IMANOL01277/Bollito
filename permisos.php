<?php
include("includes/header.php");
include("conexion.php");
require_once("includes/Permisos.php");

$permisos = new Permisos($conn);

if (!$permisos->tiene('usuarios.gestionar_permisos')) {
    echo "<div class='alert alert-danger m-4'>🚫 No tienes permisos para acceder a esta sección.</div>";
    include("includes/footer.php");
    exit();
}
?>

<style>
.permisos-card {
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
.modulo-section {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
}
.modulo-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 2px solid #dee2e6;
}
.modulo-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
}
.permiso-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 15px;
  background: white;
  border-radius: 8px;
  margin-bottom: 8px;
  transition: all 0.3s ease;
}
.permiso-item:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transform: translateX(5px);
}
.permiso-info {
  flex: 1;
}
.permiso-tipo {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  margin-left: 10px;
}
.tipo-crear { background: #d1fae5; color: #065f46; }
.tipo-leer { background: #dbeafe; color: #1e40af; }
.tipo-actualizar { background: #fef3c7; color: #92400e; }
.tipo-eliminar { background: #fee2e2; color: #991b1b; }
.tipo-especial { background: #e9d5ff; color: #6b21a8; }
.switch-permiso {
  width: 50px;
  height: 26px;
}
.rol-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 15px;
  border-radius: 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}
.rol-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.rol-badge.active {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}
.log-item {
  padding: 15px;
  background: #f8f9fa;
  border-left: 4px solid;
  border-radius: 8px;
  margin-bottom: 10px;
}
.log-otorgar { border-left-color: #10b981; }
.log-revocar { border-left-color: #ef4444; }
.log-modificar { border-left-color: #f59e0b; }
.tab-content {
  animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>

<div class="container-fluid mt-4">
  <div class="permisos-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-shield-lock me-2"></i>Gestión de Permisos</h4>
        <p class="text-muted small mb-0">Control granular de accesos y permisos del sistema</p>
      </div>
    </div>

    <div id="alertPermisos"></div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabRoles">
          <i class="bi bi-people-fill me-2"></i>Gestión por Roles
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabUsuarios">
          <i class="bi bi-person-fill me-2"></i>Permisos Individuales
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabHistorial">
          <i class="bi bi-clock-history me-2"></i>Historial de Cambios
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
      <!-- Tab Roles -->
      <div class="tab-pane fade show active" id="tabRoles">
        <div class="row mb-4">
          <div class="col-12">
            <h5 class="mb-3">Selecciona un Rol</h5>
            <div id="rolesContainer" class="d-flex flex-wrap gap-2"></div>
          </div>
        </div>

        <div id="permisosRolContainer" style="display: none;">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Permisos del Rol: <span id="rolNombre" class="text-primary"></span></h5>
            <button class="btn btn-success" onclick="guardarPermisosRol()">
              <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
          </div>
          <div id="modulosContainer"></div>
        </div>
      </div>

      <!-- Tab Usuarios -->
      <div class="tab-pane fade" id="tabUsuarios">
        <div class="row mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Selecciona un Usuario</label>
            <select class="form-select" id="selectUsuario" onchange="cargarPermisosUsuario()">
              <option value="">Seleccione un usuario...</option>
            </select>
          </div>
        </div>

        <div id="permisosUsuarioContainer" style="display: none;">
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Los permisos individuales <strong>sobrescriben</strong> los permisos del rol.
          </div>
          
          <div id="permisosUsuarioLista"></div>
        </div>
      </div>

      <!-- Tab Historial -->
      <div class="tab-pane fade" id="tabHistorial">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Filtrar por Usuario</label>
            <select class="form-select" id="filtroUsuario" onchange="cargarHistorial()">
              <option value="">Todos los usuarios</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Acción</label>
            <select class="form-select" id="filtroAccion" onchange="cargarHistorial()">
              <option value="">Todas</option>
              <option value="otorgar">Otorgar</option>
              <option value="revocar">Revocar</option>
              <option value="modificar">Modificar</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Tipo</label>
            <select class="form-select" id="filtroTipo" onchange="cargarHistorial()">
              <option value="">Todos</option>
              <option value="rol">Rol</option>
              <option value="usuario">Usuario</option>
            </select>
          </div>
        </div>
        
        <div id="historialContainer"></div>
      </div>
    </div>
  </div>
</div>

<script>
let rolSeleccionado = null;
let permisosOriginales = {};

function showAlert(type, msg) {
  const alert = document.createElement('div');
  alert.className = `alert alert-${type} alert-dismissible fade show`;
  alert.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  document.getElementById('alertPermisos').innerHTML = '';
  document.getElementById('alertPermisos').appendChild(alert);
  setTimeout(() => alert.remove(), 5000);
}

// Cargar roles
async function cargarRoles() {
  try {
    const res = await fetch('ajax/permisos.php?action=listar_roles');
    const data = await res.json();
    
    if (!data.success) return;
    
    const container = document.getElementById('rolesContainer');
    container.innerHTML = '';
    
    const colores = [
      'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
      'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
      'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
      'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
      'linear-gradient(135deg, #30cfd0 0%, #330867 100%)'
    ];
    
    data.roles.forEach((rol, i) => {
      const badge = document.createElement('div');
      badge.className = 'rol-badge';
      badge.style.background = colores[i % colores.length];
      badge.style.color = 'white';
      badge.onclick = () => seleccionarRol(rol);
      badge.innerHTML = `
        <i class="bi bi-shield-fill"></i>
        <span>${rol.nombre}</span>
        ${rol.es_sistema === 't' ? '<i class="bi bi-lock-fill" title="Rol del sistema"></i>' : ''}
      `;
      container.appendChild(badge);
    });
  } catch (err) {
    console.error('Error:', err);
  }
}

// Seleccionar rol
async function seleccionarRol(rol) {
  rolSeleccionado = rol;
  document.getElementById('rolNombre').textContent = rol.nombre;
  document.getElementById('permisosRolContainer').style.display = 'block';
  
  // Marcar como activo
  document.querySelectorAll('.rol-badge').forEach(b => b.classList.remove('active'));
  event.target.closest('.rol-badge').classList.add('active');
  
  await cargarPermisosRol(rol.id_rol);
}

// Cargar permisos del rol
async function cargarPermisosRol(id_rol) {
  try {
    const res = await fetch(`ajax/permisos.php?action=obtener_permisos_rol&id_rol=${id_rol}`);
    const data = await res.json();
    
    if (!data.success) return;
    
    permisosOriginales = {};
    const container = document.getElementById('modulosContainer');
    container.innerHTML = '';
    
    // Agrupar por módulo
    const porModulo = {};
    data.permisos.forEach(p => {
      if (!porModulo[p.modulo]) {
        porModulo[p.modulo] = {
          nombre: p.modulo,
          icono: p.icono,
          permisos: []
        };
      }
      porModulo[p.modulo].permisos.push(p);
      permisosOriginales[p.id_permiso] = p.tiene_permiso === 't';
    });
    
    // Renderizar módulos
    Object.values(porModulo).forEach(modulo => {
      const section = document.createElement('div');
      section.className = 'modulo-section';
      
      let html = `
        <div class="modulo-header">
          <div class="modulo-icon"><i class="${modulo.icono}"></i></div>
          <h6 class="mb-0">${modulo.nombre}</h6>
        </div>
      `;
      
      modulo.permisos.forEach(p => {
        const checked = p.tiene_permiso === 't' ? 'checked' : '';
        const disabled = rolSeleccionado.es_sistema === 't' ? 'disabled' : '';
        
        html += `
          <div class="permiso-item">
            <div class="permiso-info">
              <div>
                <strong>${p.nombre}</strong>
                <span class="permiso-tipo tipo-${p.tipo}">${p.tipo}</span>
              </div>
              <small class="text-muted">${p.descripcion || ''}</small>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input switch-permiso" type="checkbox" 
                     id="permiso_${p.id_permiso}" 
                     data-permiso="${p.id_permiso}"
                     ${checked} ${disabled}>
            </div>
          </div>
        `;
      });
      
      section.innerHTML = html;
      container.appendChild(section);
    });
    
  } catch (err) {
    console.error('Error:', err);
  }
}

// Guardar permisos del rol
async function guardarPermisosRol() {
  if (!rolSeleccionado || rolSeleccionado.es_sistema === 't') {
    showAlert('warning', '⚠️ No se pueden modificar los roles del sistema');
    return;
  }
  
  const switches = document.querySelectorAll('.switch-permiso');
  const cambios = [];
  
  switches.forEach(sw => {
    const id_permiso = parseInt(sw.dataset.permiso);
    const nuevo_valor = sw.checked;
    const valor_original = permisosOriginales[id_permiso];
    
    if (nuevo_valor !== valor_original) {
      cambios.push({
        id_permiso: id_permiso,
        otorgado: nuevo_valor
      });
    }
  });
  
  if (cambios.length === 0) {
    showAlert('info', 'ℹ️ No hay cambios para guardar');
    return;
  }
  
  try {
    const res = await fetch('ajax/permisos.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        action: 'actualizar_permisos_rol',
        id_rol: rolSeleccionado.id_rol,
        cambios: cambios
      })
    });
    
    const data = await res.json();
    showAlert(data.success ? 'success' : 'danger', 
              data.success ? '✅ Permisos actualizados correctamente' : '❌ ' + data.message);
    
    if (data.success) {
      await cargarPermisosRol(rolSeleccionado.id_rol);
    }
  } catch (err) {
    showAlert('danger', '❌ Error al guardar cambios');
  }
}

// Cargar usuarios
async function cargarUsuarios() {
  try {
    const res = await fetch('ajax/usuarios.php?action=list');
    const data = await res.json();
    
    if (!data.success) return;
    
    const selects = [
      document.getElementById('selectUsuario'),
      document.getElementById('filtroUsuario')
    ];
    
    selects.forEach(select => {
      const opciones = data.users.map(u => 
        `<option value="${u.id_usuario}">${u.nombre} (${u.correo})</option>`
      ).join('');
      
      if (select.id === 'filtroUsuario') {
        select.innerHTML = '<option value="">Todos los usuarios</option>' + opciones;
      } else {
        select.innerHTML = '<option value="">Seleccione un usuario...</option>' + opciones;
      }
    });
  } catch (err) {
    console.error('Error:', err);
  }
}

// Cargar permisos de usuario
async function cargarPermisosUsuario() {
  const id_usuario = document.getElementById('selectUsuario').value;
  if (!id_usuario) {
    document.getElementById('permisosUsuarioContainer').style.display = 'none';
    return;
  }
  
  try {
    const res = await fetch(`ajax/permisos.php?action=obtener_permisos_usuario&id_usuario=${id_usuario}`);
    const data = await res.json();
    
    if (!data.success) return;
    
    document.getElementById('permisosUsuarioContainer').style.display = 'block';
    const container = document.getElementById('permisosUsuarioLista');
    container.innerHTML = '';
    
    // Agrupar por módulo
    const porModulo = {};
    data.permisos.forEach(p => {
      if (!porModulo[p.modulo]) {
        porModulo[p.modulo] = { nombre: p.modulo, permisos: [] };
      }
      porModulo[p.modulo].permisos.push(p);
    });
    
    Object.values(porModulo).forEach(modulo => {
      const section = document.createElement('div');
      section.className = 'modulo-section';
      
      let html = `<h6 class="mb-3">${modulo.nombre}</h6>`;
      
      modulo.permisos.forEach(p => {
        const estado = p.tiene_permiso === 't';
        const fuente = p.fuente_permiso;
        const claseBadge = fuente === 'usuario' ? 'bg-warning' : 'bg-info';
        
        html += `
          <div class="permiso-item">
            <div class="permiso-info">
              <div>
                <strong>${p.nombre}</strong>
                <span class="badge ${claseBadge} ms-2">${fuente}</span>
              </div>
              <small class="text-muted">${p.descripcion || ''}</small>
            </div>
            <button class="btn btn-sm ${estado ? 'btn-danger' : 'btn-success'}" 
                    onclick="togglePermisoUsuario(${id_usuario}, ${p.id_permiso}, ${!estado}, '${p.nombre}')">
              <i class="bi bi-${estado ? 'x-circle' : 'check-circle'}"></i>
              ${estado ? 'Revocar' : 'Otorgar'}
            </button>
          </div>
        `;
      });
      
      section.innerHTML = html;
      container.appendChild(section);
    });
    
  } catch (err) {
    console.error('Error:', err);
  }
}

// Toggle permiso de usuario
async function togglePermisoUsuario(id_usuario, id_permiso, otorgar, nombre_permiso) {
  const motivo = prompt(`Motivo para ${otorgar ? 'otorgar' : 'revocar'} el permiso "${nombre_permiso}":`);
  if (motivo === null) return;
  
  try {
    const res = await fetch('ajax/permisos.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        action: otorgar ? 'otorgar_permiso_usuario' : 'revocar_permiso_usuario',
        id_usuario: id_usuario,
        id_permiso: id_permiso,
        motivo: motivo
      })
    });
    
    const data = await res.json();
    showAlert(data.success ? 'success' : 'danger', 
              data.success ? `✅ Permiso ${otorgar ? 'otorgado' : 'revocado'}` : '❌ ' + data.message);
    
    if (data.success) {
      await cargarPermisosUsuario();
    }
  } catch (err) {
    showAlert('danger', '❌ Error al modificar permiso');
  }
}

// Cargar historial
async function cargarHistorial() {
  const id_usuario = document.getElementById('filtroUsuario').value;
  const accion = document.getElementById('filtroAccion').value;
  const tipo = document.getElementById('filtroTipo').value;
  
  try {
    const params = new URLSearchParams({
      action: 'obtener_historial',
      id_usuario: id_usuario,
      accion: accion,
      tipo: tipo
    });
    
    const res = await fetch(`ajax/permisos.php?${params}`);
    const data = await res.json();
    
    if (!data.success) return;
    
    const container = document.getElementById('historialContainer');
    container.innerHTML = '';
    
    if (data.historial.length === 0) {
      container.innerHTML = '<div class="alert alert-info">No hay registros en el historial</div>';
      return;
    }
    
    data.historial.forEach(log => {
      const div = document.createElement('div');
      div.className = `log-item log-${log.accion}`;
      
      const fecha = new Date(log.fecha_cambio).toLocaleString('es-CO');
      const icono = log.accion === 'otorgar' ? 'check-circle' : 
                    log.accion === 'revocar' ? 'x-circle' : 'arrow-repeat';
      
      div.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h6 class="mb-1">
              <i class="bi bi-${icono} me-2"></i>
              ${log.usuario_ejecutor} <strong>${log.accion}</strong> 
              ${log.tipo === 'rol' ? 'rol' : 'permiso'} de ${log.usuario_afectado}
            </h6>
            <p class="mb-1 text-muted small">
              ${log.tipo === 'rol' ? `Rol: ${log.rol}` : `Permiso: ${log.permiso}`}
            </p>
            ${log.motivo ? `<p class="mb-0"><small><strong>Motivo:</strong> ${log.motivo}</small></p>` : ''}
          </div>
          <small class="text-muted">${fecha}</small>
        </div>
      `;
      
      container.appendChild(div);
    });
    
  } catch (err) {
    console.error('Error:', err);
  }
}

window.addEventListener('load', () => {
  cargarRoles();
  cargarUsuarios();
  cargarHistorial();
});
</script>

<?php include("includes/footer.php"); ?>