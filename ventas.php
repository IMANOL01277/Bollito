<?php include("includes/header.php"); ?>

<style>
.ventas-card {
  background: white;
  border-radius: 18px;
  padding: 28px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.08);
  animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}
.btn-generar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 12px;
  padding: 12px 24px;
  font-weight: 700;
  color: white;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(102,126,234,0.35);
}
.btn-generar:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(102,126,234,0.5);
  color: white;
}
.badge-venta {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 5px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}
.ganancia-chip {
  background: #f3e8ff;
  color: #6b21a8;
  padding: 4px 12px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.85rem;
}
.total-chip {
  background: #ede9fe;
  color: #4c1d95;
  padding: 4px 12px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.9rem;
}
.empty-state {
  padding: 60px 20px;
  text-align: center;
  color: #94a3b8;
}
.empty-state i {
  font-size: 4rem;
  margin-bottom: 15px;
  opacity: 0.4;
}
.summary-bar {
  background: linear-gradient(135deg, #ede9fe 0%, #f5f3ff 100%);
  border-radius: 12px;
  padding: 16px 22px;
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
  margin-bottom: 22px;
  border: 1px solid rgba(102,126,234,0.15);
}
.summary-item h6 { margin: 0; font-size: 0.75rem; color: #607d8b; text-transform: uppercase; letter-spacing: .5px; }
.summary-item p  { margin: 0; font-size: 1.25rem; font-weight: 700; color: #1e293b; }
.stock-warning-text { color: #b45309; font-weight: 600; }

/* ── Estilos para items de venta múltiple ── */
.item-venta {
  background: #f8f7ff;
  border: 1.5px solid #e0dbff;
  border-radius: 14px;
  padding: 16px;
  position: relative;
  transition: all 0.25s ease;
  animation: slideIn 0.3s ease-out;
}
@keyframes slideIn {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}
.item-venta:hover {
  border-color: #a78bfa;
  box-shadow: 0 3px 12px rgba(124,58,237,0.1);
}
.item-number {
  width: 26px;
  height: 26px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  flex-shrink: 0;
}
.btn-remove-item {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: none;
  background: #fee2e2;
  color: #dc2626;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  font-size: 0.85rem;
  flex-shrink: 0;
}
.btn-remove-item:hover {
  background: #dc2626;
  color: white;
  transform: scale(1.1);
}
.btn-add-item {
  border: 2px dashed #a78bfa;
  border-radius: 12px;
  background: transparent;
  color: #7c3aed;
  padding: 10px 20px;
  font-weight: 600;
  width: 100%;
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.btn-add-item:hover {
  background: #f5f3ff;
  border-color: #7c3aed;
  transform: translateY(-2px);
}
.resumen-venta {
  background: linear-gradient(135deg, #ede9fe, #f5f3ff);
  border: 1px solid #c4b5fd;
  border-radius: 12px;
  padding: 14px 18px;
}
.resumen-venta-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
}
.resumen-venta-row.total {
  border-top: 1px solid #c4b5fd;
  margin-top: 8px;
  padding-top: 8px;
  font-weight: 700;
  font-size: 1rem;
}
</style>

<div class="ventas-card">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold"><i class="bi bi-bag-check-fill me-2" style="color:#667eea"></i>Ventas</h4>
      <p class="text-muted small mb-0">Registro de salidas del inventario</p>
    </div>
    <button class="btn-generar" data-bs-toggle="modal" data-bs-target="#modalVenta">
      <i class="bi bi-plus-circle me-2"></i>Generar Venta
    </button>
  </div>

  <!-- Barra de resumen -->
  <div class="summary-bar" id="summaryBar">
    <div class="summary-item">
      <h6>Total vendido (30 días)</h6>
      <p id="sumTotal">$0</p>
    </div>
    <div class="summary-item">
      <h6>Ganancia neta</h6>
      <p id="sumGanancia" style="color:#7c3aed">$0</p>
    </div>
    <div class="summary-item">
      <h6>Unidades vendidas</h6>
      <p id="sumUnidades">0</p>
    </div>
    <div class="summary-item">
      <h6>Registros</h6>
      <p id="sumRegistros">0</p>
    </div>
  </div>

  <!-- Alertas -->
  <div id="alertVentas"></div>

  <!-- Tabla -->
  <div class="table-responsive">
    <table class="table table-hover align-middle" id="tablaVentas">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio Unit.</th>
          <th>Total</th>
          <th>Ganancia</th>
          <th>Observaciones</th>
          <th>Registrado por</th>
          <th>Fecha y Hora</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tbodyVentas">
        <tr>
          <td colspan="10">
            <div class="empty-state">
              <i class="bi bi-bag-x d-block"></i>
              <p class="fw-semibold mb-1">Sin ventas registradas</p>
              <small>Haz clic en "Generar Venta" para registrar la primera salida</small>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ── Modal Generar Venta ─────────────────────────────────────────── -->
<div class="modal fade" id="modalVenta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form id="formVenta" class="modal-content" novalidate>
      <div class="modal-header text-white" style="background: linear-gradient(135deg,#667eea,#764ba2);">
        <h5 class="modal-title"><i class="bi bi-bag-plus me-2"></i>Nueva Venta</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

        <!-- Lista dinámica de items -->
        <div id="listaItems"></div>

        <!-- Botón agregar producto -->
        <button type="button" class="btn-add-item mt-2" id="btnAgregarItem">
          <i class="bi bi-plus-circle-fill"></i> Agregar otro producto
        </button>

        <!-- Separador -->
        <hr class="my-3">

        <!-- Resumen de la venta -->
        <div class="resumen-venta" id="resumenVenta">
          <div class="resumen-venta-row mb-1">
            <span class="text-muted">Productos en la venta:</span>
            <span id="resNumProductos" class="fw-semibold">0</span>
          </div>
          <div class="resumen-venta-row mb-1">
            <span class="text-muted">Total unidades:</span>
            <span id="resTotalUnidades" class="fw-semibold">0</span>
          </div>
          <div class="resumen-venta-row total">
            <span>Total venta:</span>
            <span id="resTotalVenta" style="color:#7c3aed">$0.00</span>
          </div>
        </div>

        <!-- Observaciones -->
        <div class="mt-3">
          <label class="form-label fw-semibold"><i class="bi bi-chat-text me-1"></i>Observaciones</label>
          <textarea name="observaciones" id="observacionesVenta" class="form-control" rows="2" placeholder="Ej: Venta a cliente Juan, pedido online #456..."></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" id="btnSubmitVenta" class="btn fw-bold text-white" style="background: linear-gradient(135deg,#667eea,#764ba2);">
          <i class="bi bi-check-circle me-2"></i>Registrar Venta
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// ── Helpers ─────────────────────────────────────────────────────────────────
function showAlert(type, msg) {
  const div = document.createElement('div');
  div.className = `alert alert-${type} alert-dismissible fade show`;
  div.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  const cont = document.getElementById('alertVentas');
  cont.innerHTML = '';
  cont.appendChild(div);
  if (type === 'success') setTimeout(() => div.remove(), 5000);
}

function fmt(n) {
  return '$' + Number(n).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function fmtDate(str) {
  const d = new Date(str);
  return d.toLocaleDateString('es-CO') + ' ' + d.toLocaleTimeString('es-CO', {hour:'2-digit', minute:'2-digit'});
}

// ── Cargar lista de ventas ───────────────────────────────────────────────────
async function loadVentas() {
  try {
    const res = await fetch('ajax/ventas.php?action=list');
    const j   = await res.json();
    const tb  = document.getElementById('tbodyVentas');
    tb.innerHTML = '';

    if (!j.success || !j.ventas || j.ventas.length === 0) {
      tb.innerHTML = `
        <tr><td colspan="10">
          <div class="empty-state">
            <i class="bi bi-bag-x d-block"></i>
            <p class="fw-semibold mb-1">Sin ventas registradas</p>
            <small>Haz clic en "Generar Venta" para registrar la primera salida</small>
          </div>
        </td></tr>`;
      return;
    }

    let totalVentas = 0, totalGanancia = 0, totalUnid = 0;

    j.ventas.forEach((v, i) => {
      totalVentas   += parseFloat(v.total    || 0);
      totalGanancia += parseFloat(v.ganancia || 0);
      totalUnid     += parseInt(v.cantidad   || 0);

      const gananciaPositiva = parseFloat(v.ganancia || 0) >= 0;
      const tr = document.createElement('tr');
      tr.style.animation = `fadeIn 0.4s ease-out ${i * 0.04}s both`;
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td><strong>${v.producto || '-'}</strong></td>
        <td><span class="badge-venta">-${v.cantidad}</span></td>
        <td>${fmt(v.precio_unitario)}</td>
        <td><span class="total-chip">${fmt(v.total)}</span></td>
        <td><span class="ganancia-chip ${gananciaPositiva ? '' : 'text-danger'}">${fmt(v.ganancia)}</span></td>
        <td><small class="text-muted">${v.observaciones || '—'}</small></td>
        <td><small>${v.usuario || '—'}</small></td>
        <td><small>${fmtDate(v.fecha_venta)}</small></td>
        <td>
          <button class="btn btn-sm btn-outline-danger" onclick="deleteVenta(${v.id_venta})" title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>`;
      tb.appendChild(tr);
    });

    document.getElementById('sumTotal').textContent     = fmt(totalVentas);
    document.getElementById('sumGanancia').textContent  = fmt(totalGanancia);
    document.getElementById('sumUnidades').textContent  = totalUnid;
    document.getElementById('sumRegistros').textContent = j.ventas.length;

  } catch(err) {
    console.error(err);
    showAlert('danger', '❌ Error al cargar ventas');
  }
}

// ── Datos de productos ────────────────────────────────────────────────────────
let productosData = [];

async function loadProductos() {
  try {
    const res = await fetch('ajax/ventas.php?action=productos');
    const j   = await res.json();
    if (j.success && j.productos) {
      productosData = j.productos;
    }
  } catch(err) { console.error(err); }
}

// ── Construir opciones del select ─────────────────────────────────────────────
function buildOptions(excludeIds = []) {
  let html = '<option value="">— Selecciona un producto —</option>';
  productosData.forEach(p => {
    const disabled = excludeIds.includes(String(p.id_producto)) ? 'disabled' : '';
    html += `<option value="${p.id_producto}" data-precio="${p.precio_venta}" data-costo="${p.precio_compra}" data-stock="${p.stock}" ${disabled}>
               ${p.nombre}  (Stock: ${p.stock})
             </option>`;
  });
  return html;
}

// ── Sistema de items dinámicos ────────────────────────────────────────────────
let itemCounter = 0;

function getSelectedIds(excludeItemId = null) {
  const selects = document.querySelectorAll('.select-producto-item');
  const ids = [];
  selects.forEach(s => {
    if (s.dataset.itemId !== String(excludeItemId) && s.value) {
      ids.push(s.value);
    }
  });
  return ids;
}

function refreshAllSelects() {
  const selects = document.querySelectorAll('.select-producto-item');
  selects.forEach(sel => {
    const currentVal = sel.value;
    const excludeIds = getSelectedIds(sel.dataset.itemId);
    sel.innerHTML = buildOptions(excludeIds);
    if (currentVal) {
      sel.value = currentVal;
    }
  });
}

function addItem() {
  itemCounter++;
  const id = itemCounter;
  const lista = document.getElementById('listaItems');

  const div = document.createElement('div');
  div.className = 'item-venta mb-3';
  div.dataset.itemId = id;

  const excludeIds = getSelectedIds();

  div.innerHTML = `
    <div class="d-flex align-items-center gap-2 mb-3">
      <span class="item-number">${lista.children.length + 1}</span>
      <span class="fw-semibold text-muted small">Producto</span>
      ${lista.children.length > 0 ? `
        <button type="button" class="btn-remove-item ms-auto" onclick="removeItem(${id})" title="Eliminar este producto">
          <i class="bi bi-x"></i>
        </button>
      ` : ''}
    </div>
    <div class="row g-2">
      <div class="col-12">
        <select class="form-select select-producto-item" data-item-id="${id}" id="selectProd_${id}" onchange="onProductoChange(${id})">
          ${buildOptions(excludeIds)}
        </select>
        <div class="small mt-1" id="stockInfo_${id}"></div>
      </div>
      <div class="col-6">
        <label class="form-label small fw-semibold mb-1">Cantidad *</label>
        <input type="number" class="form-control input-cantidad" id="cantidad_${id}" min="1" placeholder="0" oninput="onItemChange(${id})">
        <div class="small text-danger mt-1" id="cantWarn_${id}" style="display:none">⚠️ Supera el stock</div>
      </div>
      <div class="col-6">
        <label class="form-label small fw-semibold mb-1">Precio Unitario *</label>
        <div class="input-group">
          <span class="input-group-text">$</span>
          <input type="number" class="form-control input-precio" id="precio_${id}" step="0.01" min="0.01" placeholder="0.00" oninput="onItemChange(${id})">
        </div>
      </div>
      <div class="col-12">
        <div class="alert alert-primary py-1 px-2 mb-0 small" id="preview_${id}" style="display:none">
          Total: <strong id="previewTotal_${id}">$0.00</strong>
          &nbsp;|&nbsp; Ganancia: <strong id="previewGanancia_${id}" class="text-success">$0.00</strong>
        </div>
      </div>
    </div>
  `;

  lista.appendChild(div);
  updateResumen();
  renumberItems();
}

function removeItem(id) {
  const el = document.querySelector(`.item-venta[data-item-id="${id}"]`);
  if (el) {
    el.style.opacity = '0';
    el.style.transform = 'translateY(-10px)';
    el.style.transition = 'all 0.25s ease';
    setTimeout(() => {
      el.remove();
      renumberItems();
      refreshAllSelects();
      updateResumen();
    }, 250);
  }
}

function renumberItems() {
  const items = document.querySelectorAll('.item-venta');
  items.forEach((item, i) => {
    const numEl = item.querySelector('.item-number');
    if (numEl) numEl.textContent = i + 1;
    // Mostrar/ocultar botón eliminar en el primer item
    const btnRemove = item.querySelector('.btn-remove-item');
    if (i === 0) {
      if (btnRemove) btnRemove.style.display = items.length > 1 ? 'flex' : 'none';
    }
  });
}

function onProductoChange(id) {
  const sel   = document.getElementById(`selectProd_${id}`);
  const opt   = sel.options[sel.selectedIndex];
  const precio = opt.dataset.precio || '';
  const stock  = opt.dataset.stock  || '';

  document.getElementById(`precio_${id}`).value = precio;

  const info = document.getElementById(`stockInfo_${id}`);
  if (stock) {
    const clr = parseInt(stock) <= 5 ? 'stock-warning-text' : 'text-success fw-semibold';
    info.innerHTML = `<span class="${clr}">📦 Stock disponible: ${stock} unidades</span>`;
    document.getElementById(`cantidad_${id}`).max = stock;
  } else {
    info.textContent = '';
    document.getElementById(`cantidad_${id}`).removeAttribute('max');
  }

  // Actualizar otros selects para deshabilitar el producto ya elegido
  refreshAllSelects();
  // Restaurar valor en el select actual (puede haber sido afectado por refresh)
  document.getElementById(`selectProd_${id}`).value = sel.value;

  onItemChange(id);
}

function onItemChange(id) {
  const sel    = document.getElementById(`selectProd_${id}`);
  const opt    = sel ? sel.options[sel.selectedIndex] : null;
  const cant   = parseFloat(document.getElementById(`cantidad_${id}`).value) || 0;
  const precio = parseFloat(document.getElementById(`precio_${id}`).value)   || 0;
  const costo  = parseFloat(opt?.dataset?.costo || 0);
  const stock  = parseInt(opt?.dataset?.stock   || 9999);

  // Validar stock
  const warn = document.getElementById(`cantWarn_${id}`);
  if (cant > stock && stock > 0) {
    warn.style.display = '';
  } else {
    warn.style.display = 'none';
  }

  // Preview
  const prev = document.getElementById(`preview_${id}`);
  if (cant > 0 && precio > 0) {
    const total    = cant * precio;
    const ganancia = cant * (precio - costo);
    document.getElementById(`previewTotal_${id}`).textContent    = fmt(total);
    const gEl = document.getElementById(`previewGanancia_${id}`);
    gEl.textContent = fmt(ganancia);
    gEl.className   = ganancia >= 0 ? 'text-success' : 'text-danger';
    prev.style.display = '';
  } else {
    prev.style.display = 'none';
  }

  updateResumen();
}

function updateResumen() {
  const items = document.querySelectorAll('.item-venta');
  let totalVenta = 0, totalUnid = 0, numProductos = 0;
  let hayError = false;

  items.forEach(item => {
    const id    = item.dataset.itemId;
    const sel   = document.getElementById(`selectProd_${id}`);
    const cant  = parseFloat(document.getElementById(`cantidad_${id}`)?.value) || 0;
    const precio = parseFloat(document.getElementById(`precio_${id}`)?.value)  || 0;
    const stock = parseInt(sel?.options[sel.selectedIndex]?.dataset?.stock || 9999);

    if (sel && sel.value && cant > 0 && precio > 0) {
      numProductos++;
      totalVenta += cant * precio;
      totalUnid  += cant;
    }
    if (cant > stock && stock > 0) hayError = true;
  });

  document.getElementById('resNumProductos').textContent  = numProductos;
  document.getElementById('resTotalUnidades').textContent = totalUnid;
  document.getElementById('resTotalVenta').textContent    = fmt(totalVenta);
  document.getElementById('btnSubmitVenta').disabled      = hayError;
}

// ── Botón agregar item ─────────────────────────────────────────────────────────
document.getElementById('btnAgregarItem').addEventListener('click', () => {
  addItem();
});

// ── Enviar formulario ─────────────────────────────────────────────────────────
document.getElementById('formVenta').addEventListener('submit', async e => {
  e.preventDefault();

  // Recolectar items
  const items = [];
  let hayError = false;

  document.querySelectorAll('.item-venta').forEach(item => {
    const id    = item.dataset.itemId;
    const sel   = document.getElementById(`selectProd_${id}`);
    const cant  = parseInt(document.getElementById(`cantidad_${id}`)?.value) || 0;
    const precio = parseFloat(document.getElementById(`precio_${id}`)?.value) || 0;
    const stock = parseInt(sel?.options[sel.selectedIndex]?.dataset?.stock || 9999);

    if (sel && sel.value && cant > 0 && precio > 0) {
      if (cant > stock && stock > 0) { hayError = true; return; }
      items.push({ id_producto: sel.value, cantidad: cant, precio_unitario: precio });
    }
  });

  if (hayError) {
    showAlert('warning', '⚠️ Hay productos que superan el stock disponible.');
    return;
  }
  if (items.length === 0) {
    showAlert('warning', '⚠️ Agrega al menos un producto con cantidad y precio válidos.');
    return;
  }

  const fd = new FormData();
  fd.append('action', 'create');
  fd.append('items', JSON.stringify(items));
  fd.append('observaciones', document.getElementById('observacionesVenta').value);

  try {
    const res = await fetch('ajax/ventas.php', {method:'POST', body:fd});
    const j   = await res.json();
    if (j.success) {
      showAlert('success', `✅ ${j.message}`);
      bootstrap.Modal.getInstance('#modalVenta').hide();
      loadVentas();
    } else {
      showAlert('danger', `❌ ${j.message}`);
    }
  } catch(err) {
    showAlert('danger', '❌ Error de conexión');
  }
});

// ── Eliminar venta ────────────────────────────────────────────────────────────
async function deleteVenta(id) {
  if (!confirm('¿Eliminar esta venta? El stock será restaurado automáticamente.')) return;
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id_venta', id);
  try {
    const res = await fetch('ajax/ventas.php', {method:'POST', body:fd});
    const j   = await res.json();
    showAlert(j.success ? 'success' : 'danger', (j.success ? '✅ ' : '❌ ') + j.message);
    if (j.success) loadVentas();
  } catch(err) {
    showAlert('danger', '❌ Error de conexión');
  }
}

// ── Reset modal al cerrar ─────────────────────────────────────────────────────
document.getElementById('modalVenta').addEventListener('hidden.bs.modal', () => {
  document.getElementById('listaItems').innerHTML = '';
  document.getElementById('observacionesVenta').value = '';
  itemCounter = 0;
  // Agregar primer item vacío
  addItem();
  // Renumber: ocultar botón remove del primero
  renumberItems();
  updateResumen();
});

// ── Al mostrar el modal: recargar productos ───────────────────────────────────
document.getElementById('modalVenta').addEventListener('show.bs.modal', async () => {
  await loadProductos();
  // Si no hay items, agregar el primero
  if (document.querySelectorAll('.item-venta').length === 0) {
    addItem();
    renumberItems();
  } else {
    // Refrescar selects con datos actuales
    refreshAllSelects();
  }
});

// ── Inicializar ───────────────────────────────────────────────────────────────
window.addEventListener('load', async () => {
  loadVentas();
  await loadProductos();
  addItem();
  renumberItems();
});
</script>

<?php include("includes/footer.php"); ?>
