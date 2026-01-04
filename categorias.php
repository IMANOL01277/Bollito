<?php
include("includes/header.php");
include("conexion.php");

?>

<style>
.category-card {
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
.category-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}
.btn-category {
  border-radius: 25px;
  padding: 10px 25px;
  font-weight: 600;
  transition: all 0.3s ease;
}
.btn-category:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.table-category tbody tr {
  animation: fadeIn 0.5s ease-out;
  transition: all 0.3s ease;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.table-category tbody tr:hover {
  transform: translateX(5px);
  background-color: #f8f9fa;
}
</style>

<div class="container-fluid mt-4">
  <div class="category-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-bookmark-fill me-2"></i>Gestión de Categorías</h4>
        <p class="text-muted small mb-0">Organiza tus productos por categorías</p>
      </div>
      <button class="btn btn-success btn-category" data-bs-toggle="modal" data-bs-target="#modalCategoria">
        <i class="bi bi-plus-circle me-2"></i>Nueva Categoría
      </button>
    </div>

    <div id="alertCategories"></div>

    <div class="table-responsive">
      <table class="table table-hover align-middle table-category" id="tablaCategorias">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Categoría</th>
            <th>Descripción</th>
            <th>Productos</th>
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
<div class="modal fade" id="modalCategoria">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formCategoria" class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <h5 class="modal-title"><i class="bi bi-bookmark-star me-2"></i><span id="modalTitle">Nueva Categoría</span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_categoria" id="id_categoria">
        
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-tag me-2"></i>Nombre de la categoría</label>
          <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ej: Bebidas, Snacks..." required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-file-text me-2"></i>Descripción</label>
          <textarea class="form-control" name="descripcion" id="descripcion" rows="3" placeholder="Descripción opcional de la categoría"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">
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
  document.getElementById('alertCategories').innerHTML = '';
  document.getElementById('alertCategories').appendChild(alert);
  setTimeout(() => alert.remove(), 4000);
}

async function loadCategorias() {
  try {
    const res = await fetch('ajax/categorias.php?action=list');
    const data = await res.json();
    const tbody = document.querySelector('#tablaCategorias tbody');
    tbody.innerHTML = '';
    
    if (!data.success || !data.categorias || data.categorias.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No hay categorías registradas</td></tr>';
      return;
    }
    
    data.categorias.forEach((c, i) => {
      const tr = document.createElement('tr');
      tr.style.animationDelay = `${i * 0.05}s`;
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td>
          <div class="d-flex align-items-center">
            <div class="category-icon me-3">
              <i class="bi bi-bookmark-fill"></i>
            </div>
            <strong>${c.nombre}</strong>
          </div>
        </td>
        <td>${c.descripcion || '<span class="text-muted">Sin descripción</span>'}</td>
        <td><span class="badge bg-primary">${c.total_productos || 0} productos</span></td>
        <td><small class="text-muted">${new Date(c.fecha_registro).toLocaleDateString('es-CO')}</small></td>
        <td>
          <button class="btn btn-sm btn-primary me-1" onclick='editCategoria(${JSON.stringify(c)})' title="Editar">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger" onclick='deleteCategoria(${c.id_categoria})' title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error('Error:', err);
    showAlert('danger', '❌ Error al cargar categorías');
  }
}

function editCategoria(c) {
  document.getElementById('modalTitle').textContent = 'Editar Categoría';
  document.getElementById('id_categoria').value = c.id_categoria;
  document.getElementById('nombre').value = c.nombre;
  document.getElementById('descripcion').value = c.descripcion || '';
  new bootstrap.Modal('#modalCategoria').show();
}

async function deleteCategoria(id) {
  if (!confirm('¿Eliminar esta categoría? Los productos asociados quedarán sin categoría.')) return;
  
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id_categoria', id);
  
  try {
    const res = await fetch('ajax/categorias.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    if (j.success) loadCategorias();
  } catch (err) {
    showAlert('danger', '❌ Error al eliminar');
  }
}

document.getElementById('formCategoria').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const isEdit = fd.get('id_categoria');
  fd.append('action', isEdit ? 'update' : 'create');
  
  try {
    const res = await fetch('ajax/categorias.php', {method: 'POST', body: fd});
    const j = await res.json();
    showAlert(j.success ? 'success' : 'danger', j.success ? '✅ ' + j.message : '❌ ' + j.message);
    
    if (j.success) {
      bootstrap.Modal.getInstance('#modalCategoria').hide();
      e.target.reset();
      document.getElementById('modalTitle').textContent = 'Nueva Categoría';
      loadCategorias();
    }
  } catch (err) {
    showAlert('danger', '❌ Error al guardar');
  }
});

document.getElementById('modalCategoria').addEventListener('hidden.bs.modal', function() {
  document.getElementById('formCategoria').reset();
  document.getElementById('modalTitle').textContent = 'Nueva Categoría';
});

window.addEventListener('load', loadCategorias);
</script>

<?php include("includes/footer.php"); ?>