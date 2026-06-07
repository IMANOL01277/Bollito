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
      <div class="modal-body">
        <div class="row g-3">

          <div class="col-md-12">
            <label class="form-label fw-semibold"><i class="bi bi-box-seam me-1"></i>Producto *</label>
            <select name="id_producto" id="selectProductoV" class="form-select" required>
              <option value="">Cargando productos...</option>
            </select>
            <div id="stockInfoV" class="small mt-1"></div>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-hash me-1"></i>Cantidad *</label>
            <input type="number" name="cantidad" id="inputCantidadV" class="form-control" min="1" placeholder="0" required>
            <div id="cantidadWarning" class="small text-danger mt-1" style="display:none">
              ⚠️ La cantidad supera el stock disponible
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-cash me-1"></i>Precio Unitario de Venta *</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" name="precio_unitario" id="inputPrecioV" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
            </div>
          </div>

          <div class="col-12">
            <div class="alert alert-primary py-2 mb-0" id="totalPreviewV" style="display:none;">
              <div class="d-flex gap-4">
                <span><strong>Total:</strong> <span id="totalSpanV">$0.00</span></span>
                <span><strong>Ganancia estimada:</strong> <span id="gananciaSpanV" class="text-success fw-bold">$0.00</span></span>
              </div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-chat-text me-1"></i>Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="2" placeholder="Ej: Venta a cliente Juan, pedido online #456..."></textarea>
          </div>

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

// ── Cargar productos en el select ────────────────────────────────────────────
let productosData = {};
async function loadProductos() {
  try {
    const res = await fetch('ajax/ventas.php?action=productos');
    const j   = await res.json();
    const sel = document.getElementById('selectProductoV');
    sel.innerHTML = '<option value="">— Selecciona un producto —</option>';
    if (j.success && j.productos) {
      j.productos.forEach(p => {
        productosData[p.id_producto] = p;
        const opt = document.createElement('option');
        opt.value          = p.id_producto;
        opt.dataset.precio = p.precio_venta;
        opt.dataset.costo  = p.precio_compra;
        opt.dataset.stock  = p.stock;
        opt.textContent    = `${p.nombre}  (Stock disponible: ${p.stock})`;
        sel.appendChild(opt);
      });
    }
  } catch(err) { console.error(err); }
}

// ── Prellenar precio al seleccionar producto ─────────────────────────────────
document.getElementById('selectProductoV').addEventListener('change', function() {
  const opt   = this.options[this.selectedIndex];
  const precio = opt.dataset.precio || '';
  const stock  = opt.dataset.stock  || '';
  document.getElementById('inputPrecioV').value = precio;
  const info = document.getElementById('stockInfoV');
  if (stock) {
    const clr = parseInt(stock) <= 5 ? 'stock-warning-text' : 'text-success fw-semibold';
    info.innerHTML = `<span class="${clr}">📦 Stock disponible: ${stock} unidades</span>`;
  } else {
    info.textContent = '';
  }
  document.getElementById('inputCantidadV').max = stock || 9999;
  updateTotalV();
});

// ── Vista previa del total y ganancia ────────────────────────────────────────
function updateTotalV() {
  const sel    = document.getElementById('selectProductoV');
  const opt    = sel.options[sel.selectedIndex];
  const cant   = parseFloat(document.getElementById('inputCantidadV').value) || 0;
  const precio = parseFloat(document.getElementById('inputPrecioV').value)   || 0;
  const costo  = parseFloat(opt.dataset?.costo || 0);
  const stock  = parseInt(opt.dataset?.stock   || 9999);
  const prev   = document.getElementById('totalPreviewV');
  const warn   = document.getElementById('cantidadWarning');

  // Validar stock
  if (cant > stock && stock > 0) {
    warn.style.display = '';
    document.getElementById('btnSubmitVenta').disabled = true;
  } else {
    warn.style.display = 'none';
    document.getElementById('btnSubmitVenta').disabled = false;
  }

  if (cant > 0 && precio > 0) {
    const total   = cant * precio;
    const ganancia = cant * (precio - costo);
    document.getElementById('totalSpanV').textContent    = fmt(total);
    document.getElementById('gananciaSpanV').textContent = fmt(ganancia);
    document.getElementById('gananciaSpanV').className   = ganancia >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
    prev.style.display = '';
  } else {
    prev.style.display = 'none';
  }
}
document.getElementById('inputCantidadV').addEventListener('input', updateTotalV);
document.getElementById('inputPrecioV').addEventListener('input',   updateTotalV);

// ── Enviar formulario ─────────────────────────────────────────────────────────
document.getElementById('formVenta').addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  fd.append('action', 'create');
  try {
    const res = await fetch('ajax/ventas.php', {method:'POST', body:fd});
    const j   = await res.json();
    if (j.success) {
      showAlert('success', `✅ ${j.message}`);
      bootstrap.Modal.getInstance('#modalVenta').hide();
      e.target.reset();
      document.getElementById('stockInfoV').textContent = '';
      document.getElementById('totalPreviewV').style.display = 'none';
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
  document.getElementById('formVenta').reset();
  document.getElementById('stockInfoV').textContent       = '';
  document.getElementById('totalPreviewV').style.display  = 'none';
  document.getElementById('cantidadWarning').style.display = 'none';
  document.getElementById('btnSubmitVenta').disabled       = false;
});

// ── Inicializar ───────────────────────────────────────────────────────────────
window.addEventListener('load', () => {
  loadVentas();
  loadProductos();
});
</script>

<?php include("includes/footer.php"); ?>
