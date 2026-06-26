<?php include("includes/header.php"); ?>

<style>
.compras-card {
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
  background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  border: none;
  border-radius: 12px;
  padding: 12px 24px;
  font-weight: 700;
  color: white;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(17,153,142,0.35);
}
.btn-generar:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(17,153,142,0.5);
  color: white;
}
.badge-compra {
  background: linear-gradient(135deg, #11998e, #38ef7d);
  color: white;
  padding: 5px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}
.total-chip {
  background: #e8f5e9;
  color: #1b5e20;
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
  background: linear-gradient(135deg, #e0f7fa 0%, #e8f5e9 100%);
  border-radius: 12px;
  padding: 16px 22px;
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
  margin-bottom: 22px;
  border: 1px solid rgba(17,153,142,0.15);
}
.summary-item h6 { margin: 0; font-size: 0.75rem; color: #607d8b; text-transform: uppercase; letter-spacing: .5px; }
.summary-item p  { margin: 0; font-size: 1.25rem; font-weight: 700; color: #1e293b; }

/* Categoría badge en el selector */
.categoria-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #11998e22, #38ef7d22);
  border: 1px solid #11998e44;
  color: #0a6b63;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  margin-top: 6px;
}
.frito-info {
  background: linear-gradient(135deg, #fff3e0, #ffe0b2);
  border: 1px solid #ff980044;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 0.85rem;
  color: #e65100;
  display: none;
  margin-top: 8px;
}
</style>

<div class="compras-card">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold"><i class="bi bi-cart-plus-fill me-2 text-success"></i>Agregar Inventario</h4>
      <p class="text-muted small mb-0">Registro de entradas al inventario</p>
    </div>
    <button class="btn-generar" data-bs-toggle="modal" data-bs-target="#modalCompra">
      <i class="bi bi-plus-circle me-2"></i>Generar Compra
    </button>
  </div>

  <!-- Barra de resumen -->
  <div class="summary-bar" id="summaryBar">
    <div class="summary-item">
      <h6>Total invertido (30 días)</h6>
      <p id="sumTotal">$0</p>
    </div>
    <div class="summary-item">
      <h6>Unidades compradas</h6>
      <p id="sumUnidades">0</p>
    </div>
    <div class="summary-item">
      <h6>Registros</h6>
      <p id="sumRegistros">0</p>
    </div>
  </div>

  <!-- Alertas -->
  <div id="alertCompras"></div>

  <!-- Tabla -->
  <div class="table-responsive">
    <table class="table table-hover align-middle" id="tablaCompras">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio Unit.</th>
          <th>Total</th>
          <th>Observaciones</th>
          <th>Registrado por</th>
          <th>Fecha y Hora</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tbodyCompras">
        <tr>
          <td colspan="9">
            <div class="empty-state">
              <i class="bi bi-cart-x d-block"></i>
              <p class="fw-semibold mb-1">Sin compras registradas</p>
              <small>Haz clic en "Generar Compra" para registrar la primera entrada</small>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ── Modal Generar Compra ────────────────────────────────────────── -->
<div class="modal fade" id="modalCompra" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form id="formCompra" class="modal-content" novalidate>
      <div class="modal-header text-white" style="background: linear-gradient(135deg,#11998e,#38ef7d);">
        <h5 class="modal-title"><i class="bi bi-cart-plus me-2"></i>Nueva Compra / Agregar Inventario</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">

          <!-- Selector de Categoría -->
          <div class="col-md-12">
            <label class="form-label fw-semibold"><i class="bi bi-bookmark-fill me-1 text-success"></i>Categoría</label>
            <select id="selectCategoria" class="form-select">
              <option value="">— Todas las categorías —</option>
            </select>
          </div>

          <!-- Selector de Producto (filtrado por categoría) -->
          <div class="col-md-12">
            <label class="form-label fw-semibold"><i class="bi bi-box-seam me-1"></i>Producto *</label>
            <select name="id_producto" id="selectProducto" class="form-select" required>
              <option value="">— Primero selecciona una categoría —</option>
            </select>
            <div id="stockInfo" class="small text-muted mt-1"></div>
            <!-- Mensaje especial para fritos -->
            <div class="frito-info" id="fritoInfo">
              <i class="bi bi-fire me-1"></i>
              <strong>Producto elaborado (Frito):</strong> El precio de compra puede ser $0 ya que este producto es de elaboración propia.
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-hash me-1"></i>Cantidad *</label>
            <input type="number" name="cantidad" id="inputCantidad" class="form-control" min="1" placeholder="0" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-cash me-1"></i>Precio Unitario de Compra</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" name="precio_unitario" id="inputPrecio" class="form-control" step="0.01" min="0" placeholder="0.00">
            </div>
            <div class="small text-muted mt-1" id="precioHint"></div>
          </div>

          <div class="col-12">
            <div class="alert alert-info py-2 mb-0" id="totalPreview" style="display:none;">
              <strong>Total estimado:</strong> <span id="totalSpan">$0.00</span>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-chat-text me-1"></i>Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="2" placeholder="Ej: Compra a proveedor X, lote #123..."></textarea>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success fw-bold">
          <i class="bi bi-check-circle me-2"></i>Registrar Compra
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
  const cont = document.getElementById('alertCompras');
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

// ── Cargar lista de compras ──────────────────────────────────────────────────
async function loadCompras() {
  try {
    const res = await fetch('ajax/compras.php?action=list');
    const j   = await res.json();
    const tb  = document.getElementById('tbodyCompras');
    tb.innerHTML = '';

    if (!j.success || !j.compras || j.compras.length === 0) {
      tb.innerHTML = `
        <tr><td colspan="9">
          <div class="empty-state">
            <i class="bi bi-cart-x d-block"></i>
            <p class="fw-semibold mb-1">Sin compras registradas</p>
            <small>Haz clic en "Generar Compra" para registrar la primera entrada</small>
          </div>
        </td></tr>`;
      return;
    }

    let totalInv = 0, totalUnid = 0;

    j.compras.forEach((c, i) => {
      totalInv  += parseFloat(c.total || 0);
      totalUnid += parseInt(c.cantidad || 0);

      const tr = document.createElement('tr');
      tr.style.animation = `fadeIn 0.4s ease-out ${i * 0.04}s both`;
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td><strong>${c.producto || '-'}</strong></td>
        <td><span class="badge-compra">+${c.cantidad}</span></td>
        <td>${fmt(c.precio_unitario)}</td>
        <td><span class="total-chip">${fmt(c.total)}</span></td>
        <td><small class="text-muted">${c.observaciones || '—'}</small></td>
        <td><small>${c.usuario || '—'}</small></td>
        <td><small>${fmtDate(c.fecha_compra)}</small></td>
        <td>
          <button class="btn btn-sm btn-outline-danger" onclick="deleteCompra(${c.id_compra})" title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
        </td>`;
      tb.appendChild(tr);
    });

    document.getElementById('sumTotal').textContent     = fmt(totalInv);
    document.getElementById('sumUnidades').textContent  = totalUnid;
    document.getElementById('sumRegistros').textContent = j.compras.length;

  } catch(err) {
    console.error(err);
    showAlert('danger', '❌ Error al cargar compras');
  }
}

// ── Cargar categorías ─────────────────────────────────────────────────────
async function loadCategorias() {
  try {
    const res  = await fetch('ajax/compras.php?action=categorias');
    const j    = await res.json();
    const sel  = document.getElementById('selectCategoria');
    if (j.success && j.categorias) {
      j.categorias.forEach(cat => {
        const opt = document.createElement('option');
        opt.value       = cat.id_categoria;
        opt.textContent = cat.nombre;
        sel.appendChild(opt);
      });
    }
  } catch(err) { console.error(err); }
}

// ── Cargar productos filtrados por categoría ──────────────────────────────
let esCategoriaFrito = false;

async function loadProductos(id_categoria = '') {
  try {
    const url  = id_categoria
      ? `ajax/compras.php?action=productos&id_categoria=${id_categoria}`
      : 'ajax/compras.php?action=productos';
    const res  = await fetch(url);
    const j    = await res.json();
    const sel  = document.getElementById('selectProducto');
    sel.innerHTML = '<option value="">— Selecciona un producto —</option>';
    if (j.success && j.productos) {
      if (j.productos.length === 0) {
        sel.innerHTML = '<option value="">Sin productos en esta categoría</option>';
      } else {
        j.productos.forEach(p => {
          const opt = document.createElement('option');
          opt.value           = p.id_producto;
          opt.dataset.precio  = p.precio_compra;
          opt.dataset.stock   = p.stock;
          opt.textContent     = `${p.nombre}  (Stock: ${p.stock})`;
          sel.appendChild(opt);
        });
      }
    }
    // Reset info
    document.getElementById('stockInfo').textContent = '';
    document.getElementById('inputPrecio').value = '';
    document.getElementById('inputCantidad').value = '';
    updateTotal();
  } catch(err) { console.error(err); }
}

// ── Cambio de categoría ───────────────────────────────────────────────────
document.getElementById('selectCategoria').addEventListener('change', function() {
  const id   = this.value;
  const text = this.options[this.selectedIndex].text.toLowerCase();
  esCategoriaFrito = text.includes('frito');

  // Mostrar/ocultar info de fritos
  const fritoInfo = document.getElementById('fritoInfo');
  fritoInfo.style.display = esCategoriaFrito ? 'block' : 'none';

  // Actualizar hint del precio
  const precioHint = document.getElementById('precioHint');
  if (esCategoriaFrito) {
    precioHint.innerHTML = '<i class="bi bi-info-circle text-warning me-1"></i>Para productos fritos elaborados, puede dejar el precio en $0.';
    document.getElementById('inputPrecio').min = '0';
    document.getElementById('inputPrecio').removeAttribute('required');
  } else {
    precioHint.textContent = '';
    document.getElementById('inputPrecio').min = '0.01';
    document.getElementById('inputPrecio').setAttribute('required', '');
  }

  loadProductos(id);
});

// ── Prellenar precio al seleccionar producto ─────────────────────────────────
document.getElementById('selectProducto').addEventListener('change', function() {
  const opt    = this.options[this.selectedIndex];
  const precio = opt.dataset.precio || '';
  const stock  = opt.dataset.stock  || '';
  document.getElementById('inputPrecio').value = precio;
  document.getElementById('stockInfo').textContent = stock ? `Stock actual: ${stock} unidades` : '';
  updateTotal();
});

// ── Vista previa del total ────────────────────────────────────────────────────
function updateTotal() {
  const cant   = parseFloat(document.getElementById('inputCantidad').value) || 0;
  const precio = parseFloat(document.getElementById('inputPrecio').value)   || 0;
  const prev   = document.getElementById('totalPreview');
  if (cant > 0 && precio > 0) {
    document.getElementById('totalSpan').textContent = fmt(cant * precio);
    prev.style.display = '';
  } else {
    prev.style.display = 'none';
  }
}
document.getElementById('inputCantidad').addEventListener('input', updateTotal);
document.getElementById('inputPrecio').addEventListener('input',   updateTotal);

// ── Enviar formulario ─────────────────────────────────────────────────────────
document.getElementById('formCompra').addEventListener('submit', async e => {
  e.preventDefault();

  // Validación manual del precio si no es frito
  const idProducto = document.getElementById('selectProducto').value;
  const cantidad   = document.getElementById('inputCantidad').value;
  const precio     = parseFloat(document.getElementById('inputPrecio').value) || 0;

  if (!idProducto) {
    showAlert('warning', '⚠️ Selecciona un producto.');
    return;
  }
  if (!cantidad || parseInt(cantidad) < 1) {
    showAlert('warning', '⚠️ Ingresa una cantidad válida.');
    return;
  }
  if (!esCategoriaFrito && precio <= 0) {
    showAlert('warning', '⚠️ El precio unitario debe ser mayor a $0 para este tipo de producto.');
    return;
  }

  const fd = new FormData(e.target);
  fd.append('action', 'create');
  try {
    const res = await fetch('ajax/compras.php', {method:'POST', body:fd});
    const j   = await res.json();
    if (j.success) {
      showAlert('success', `✅ ${j.message}`);
      bootstrap.Modal.getInstance('#modalCompra').hide();
      e.target.reset();
      document.getElementById('stockInfo').textContent = '';
      document.getElementById('totalPreview').style.display = 'none';
      document.getElementById('fritoInfo').style.display = 'none';
      document.getElementById('precioHint').textContent = '';
      esCategoriaFrito = false;
      // Recargar productos
      loadProductos();
      loadCompras();
    } else {
      showAlert('danger', `❌ ${j.message}`);
    }
  } catch(err) {
    showAlert('danger', '❌ Error de conexión');
  }
});

// ── Eliminar compra ───────────────────────────────────────────────────────────
async function deleteCompra(id) {
  if (!confirm('¿Eliminar esta compra? El stock será ajustado automáticamente.')) return;
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id_compra', id);
  try {
    const res = await fetch('ajax/compras.php', {method:'POST', body:fd});
    const j   = await res.json();
    showAlert(j.success ? 'success' : 'danger', (j.success ? '✅ ' : '❌ ') + j.message);
    if (j.success) loadCompras();
  } catch(err) {
    showAlert('danger', '❌ Error de conexión');
  }
}

// ── Reset modal al cerrar ─────────────────────────────────────────────────────
document.getElementById('modalCompra').addEventListener('hidden.bs.modal', () => {
  document.getElementById('formCompra').reset();
  document.getElementById('stockInfo').textContent = '';
  document.getElementById('totalPreview').style.display = 'none';
  document.getElementById('fritoInfo').style.display = 'none';
  document.getElementById('precioHint').textContent = '';
  document.getElementById('selectCategoria').value = '';
  esCategoriaFrito = false;
  loadProductos();
});

// ── Inicializar ───────────────────────────────────────────────────────────────
window.addEventListener('load', () => {
  loadCompras();
  loadCategorias();
  loadProductos();
});
</script>

<?php include("includes/footer.php"); ?>
