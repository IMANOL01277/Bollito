<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// === CREAR DOMICILIO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create') {
    $conductor = trim($_POST['conductor_responsable']);
    $matricula = trim($_POST['matricula_vehiculo']);
    $observaciones = trim($_POST['observaciones']);
    $id_producto = (int) $_POST['id_producto'];
    $cantidad = (int) $_POST['cantidad'];

    // Obtener datos del producto
    $producto_q = pg_query_params($conn, "SELECT nombre, precio_venta, precio_compra, stock FROM productos WHERE id_producto = $1", [$id_producto]);
    $producto_data = pg_fetch_assoc($producto_q);

    if ($producto_data && $cantidad > 0) {
        // Verificar stock disponible
        if ($producto_data['stock'] < $cantidad) {
            header("Location: domicilios.php?msg=insufficient_stock");
            exit();
        }
        
        $nombre_producto = $producto_data['nombre'];
        $precio_venta = $producto_data['precio_venta'];
        $precio_compra = $producto_data['precio_compra'];
        $total_venta = $precio_venta * $cantidad;
        $ganancia = ($precio_venta - $precio_compra) * $cantidad;

        // Registrar el domicilio
        $sql = "INSERT INTO domicilios (conductor_responsable, matricula_vehiculo, observaciones, producto) 
                VALUES ($1, $2, $3, $4)";
        $result = pg_query_params($conn, $sql, [$conductor, $matricula, $observaciones, $nombre_producto]);
        $ok = !!$result;

        if ($ok) {
            // Descontar stock
            pg_query_params($conn, "UPDATE productos SET stock = stock - $1 WHERE id_producto = $2", [$cantidad, $id_producto]);

            // Registrar movimiento con precio de venta (para calcular ganancia correctamente)
            $desc = "Domicilio entregado por $conductor - Venta: $$total_venta - Ganancia: $$ganancia";
            $mov = "INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, descripcion)
                    VALUES ($1, 'salida', $2, $3, $4)";
            pg_query_params($conn, $mov, [$id_producto, $cantidad, $precio_compra, $desc]);
        }

        header("Location: domicilios.php?msg=" . ($ok ? "created" : "error"));
        exit();
    } else {
        header("Location: domicilios.php?msg=invalid");
        exit();
    }
}

// === ELIMINAR DOMICILIO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $id = (int) $_POST['id_domicilio'];
    $result = pg_query_params($conn, "DELETE FROM domicilios WHERE id_domicilio = $1", [$id]);
    $ok = !!$result;

    header("Location: domicilios.php?msg=" . ($ok ? "deleted" : "error"));
    exit();
}

// === CONSULTA PRINCIPAL ===
$result = pg_query($conn, "SELECT * FROM domicilios ORDER BY fecha_registro DESC");

// === CARGAR PRODUCTOS ===
$productos = pg_query($conn, "SELECT id_producto, nombre, stock, precio_venta, precio_compra FROM productos WHERE stock > 0 ORDER BY nombre ASC");

include 'includes/header.php';
?>

<style>
.delivery-card {
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
.btn-delivery {
  border-radius: 25px;
  padding: 10px 25px;
  font-weight: 600;
  transition: all 0.3s ease;
}
.btn-delivery:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.product-selector {
  animation: fadeIn 0.6s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>

<div class="container-fluid mt-4">
  <div class="delivery-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1 fw-bold">🛵 Gestión de Domicilios</h4>
        <p class="text-muted small mb-0">Registra y gestiona entregas a domicilio</p>
      </div>
      <button class="btn btn-success btn-delivery" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Domicilio
      </button>
    </div>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-<?= in_array($_GET['msg'], ['error','invalid','insufficient_stock']) ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?php
          switch($_GET['msg']) {
            case 'created': echo '✅ Domicilio registrado correctamente.'; break;
            case 'deleted': echo '🗑️ Domicilio eliminado.'; break;
            case 'invalid': echo '⚠️ Producto inválido o cantidad incorrecta.'; break;
            case 'insufficient_stock': echo '❌ Stock insuficiente para realizar el domicilio.'; break;
            default: echo '❌ Error al procesar la operación.';
          }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Conductor</th>
            <th>Matrícula</th>
            <th>Producto</th>
            <th>Observaciones</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (pg_num_rows($result) === 0): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">No hay domicilios registrados.</td></tr>
          <?php else: $i=1; while($row = pg_fetch_assoc($result)): ?>
            <tr style="animation: fadeIn 0.5s ease-out <?= $i * 0.05 ?>s both;">
              <td><?= $i++ ?></td>
              <td><i class="bi bi-person-fill me-2 text-primary"></i><?= htmlspecialchars($row['conductor_responsable']) ?></td>
              <td><i class="bi bi-car-front me-2 text-info"></i><?= htmlspecialchars($row['matricula_vehiculo']) ?></td>
              <td><strong><?= htmlspecialchars($row['producto']) ?></strong></td>
              <td><?= nl2br(htmlspecialchars($row['observaciones'])) ?></td>
              <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></small></td>
              <td>
                <form method="POST" onsubmit="return confirm('¿Eliminar domicilio?');" style="display:inline-block;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id_domicilio" value="<?= $row['id_domicilio'] ?>">
                  <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modalCreate" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form method="POST" class="modal-content">
      <input type="hidden" name="action" value="create">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="bi bi-bicycle me-2"></i>Registrar Nuevo Domicilio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-person me-2"></i>Conductor responsable</label>
            <input type="text" name="conductor_responsable" class="form-control" placeholder="Nombre del conductor" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-car-front me-2"></i>Matrícula del vehículo</label>
            <input type="text" name="matricula_vehiculo" class="form-control" placeholder="ABC-123" required>
          </div>
          <div class="col-md-8">
            <label class="form-label fw-semibold"><i class="bi bi-box-seam me-2"></i>Producto</label>
            <select name="id_producto" id="selectProducto" class="form-select" required onchange="updateProductInfo()">
              <option value="">Seleccione un producto</option>
              <?php while($p = pg_fetch_assoc($productos)): ?>
                <option value="<?= $p['id_producto'] ?>" 
                        data-stock="<?= $p['stock'] ?>"
                        data-precio-venta="<?= $p['precio_venta'] ?>"
                        data-precio-compra="<?= $p['precio_compra'] ?>">
                  <?= htmlspecialchars($p['nombre']) ?> - Stock: <?= $p['stock'] ?> - $<?= number_format($p['precio_venta'], 2) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold"><i class="bi bi-hash me-2"></i>Cantidad</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required onchange="updateProductInfo()">
          </div>
          
          <!-- Info del producto -->
          <div class="col-12" id="productInfo" style="display:none;">
            <div class="alert alert-info mb-0">
              <strong>Resumen de venta:</strong><br>
              <div class="mt-2">
                <i class="bi bi-cash me-2"></i>Total venta: <strong id="totalVenta">$0</strong><br>
                <i class="bi bi-graph-up me-2"></i>Ganancia: <strong id="ganancia" class="text-success">$0</strong>
              </div>
            </div>
          </div>
          
          <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-chat-dots me-2"></i>Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="3" placeholder="Detalles adicionales del domicilio..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">
          <i class="bi bi-check-circle me-2"></i>Registrar Domicilio
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function updateProductInfo() {
  const select = document.getElementById('selectProducto');
  const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
  const option = select.options[select.selectedIndex];
  
  if (option.value && cantidad > 0) {
    const stock = parseInt(option.dataset.stock);
    const precioVenta = parseFloat(option.dataset.precioVenta);
    const precioCompra = parseFloat(option.dataset.precioCompra);
    
    if (cantidad > stock) {
      alert('⚠️ La cantidad solicitada excede el stock disponible (' + stock + ' unidades)');
      document.getElementById('cantidad').value = stock;
      return;
    }
    
    const totalVenta = precioVenta * cantidad;
    const ganancia = (precioVenta - precioCompra) * cantidad;
    
    document.getElementById('totalVenta').textContent = '$' + totalVenta.toLocaleString('es-CO', {minimumFractionDigits: 2});
    document.getElementById('ganancia').textContent = '$' + ganancia.toLocaleString('es-CO', {minimumFractionDigits: 2});
    document.getElementById('productInfo').style.display = 'block';
  } else {
    document.getElementById('productInfo').style.display = 'none';
  }
}

// Auto-ocultar alertas
setTimeout(() => {
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    alert.style.transition = 'opacity 0.5s';
    alert.style.opacity = '0';
    setTimeout(() => alert.remove(), 500);
  });
}, 5000);
</script>

<?php include 'includes/footer.php'; ?>