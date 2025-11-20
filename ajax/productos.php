<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../conexion.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

// === LISTAR PRODUCTOS ===
if ($action === 'list') {
    $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, 
                   c.nombre AS categoria, pr.nombre AS proveedor
            FROM productos p
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
            ORDER BY p.id_producto DESC";
    $result = pg_query($conn, $sql);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['products' => $rows]);
}

// === OBTENER PRODUCTO ===
if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    $result = pg_execute_prepared($conn, "SELECT * FROM productos WHERE id_producto = ?", [$id]);
    if (pg_num_rows($result) === 0) res(false, ['message' => 'Producto no encontrado']);
    res(true, ['product' => pg_fetch_assoc($result)]);
}

// === LISTAR CATEGORÍAS ===
if ($action === 'categories') {
    $result = pg_query($conn, "SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['categorias' => $rows]);
}

// === LISTAR PROVEEDORES ===
if ($action === 'proveedores') {
    $result = pg_query($conn, "SELECT id_proveedor, nombre FROM proveedores ORDER BY nombre ASC");
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['proveedores' => $rows]);
}

// === CREAR PRODUCTO ===
if ($action === 'create') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $id_proveedor = $_POST['id_proveedor'] ? (int)$_POST['id_proveedor'] : null;

    if ($nombre === '') res(false, ['message' => 'El nombre es obligatorio']);

    $query = "INSERT INTO productos (nombre, descripcion, precio, stock, id_categoria, id_proveedor) 
              VALUES ($1, $2, $3, $4, $5, $6)";
    $params = [$nombre, $descripcion, $precio, $stock, $id_categoria, $id_proveedor];
    
    $result = pg_query_params($conn, $query, $params);
    $ok = !!$result;
    $newId = $ok ? pg_last_insert_id($conn, 'productos', 'id_producto') : null;

    // Registrar movimiento de entrada inicial si stock > 0
    if ($ok && $stock > 0) {
        $total = $precio * $stock;
        $mov_query = "INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, descripcion) 
                      VALUES ($1, 'entrada', $2, $3, 'Registro inicial de stock')";
        pg_query_params($conn, $mov_query, [$newId, $stock, $precio]);
    }

    res($ok, ['message' => $ok ? 'Producto creado correctamente' : 'Error al crear producto']);
}

// === ACTUALIZAR PRODUCTO ===
if ($action === 'update') {
    $id = (int)($_POST['id_producto'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $id_proveedor = $_POST['id_proveedor'] ? (int)$_POST['id_proveedor'] : null;

    if ($id <= 0) res(false, ['message' => 'ID inválido']);

    // Obtener stock anterior
    $old_result = pg_query($conn, "SELECT stock, precio FROM productos WHERE id_producto = $id");
    $old = pg_fetch_assoc($old_result);
    $oldStock = (int)$old['stock'];
    $oldPrecio = (float)$old['precio'];

    $query = "UPDATE productos SET nombre=$1, descripcion=$2, precio=$3, stock=$4, id_categoria=$5, id_proveedor=$6 
              WHERE id_producto=$7";
    $result = pg_query_params($conn, $query, [$nombre, $descripcion, $precio, $stock, $id_categoria, $id_proveedor, $id]);
    $ok = !!$result;

    // Registrar movimiento automático si cambió el stock
    if ($ok) {
        $cantidad = $stock - $oldStock;
        if ($cantidad != 0) {
            $tipo = $cantidad > 0 ? 'entrada' : 'salida';
            $cantidadAbs = abs($cantidad);
            $precioUsado = $precio > 0 ? $precio : $oldPrecio;

            $mov_query = "INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, descripcion) 
                          VALUES ($1, $2, $3, $4, 'Actualización de stock')";
            pg_query_params($conn, $mov_query, [$id, $tipo, $cantidadAbs, $precioUsado]);
        }
    }

    res($ok, ['message' => $ok ? 'Producto actualizado correctamente' : 'Error al actualizar producto']);
}

// === ELIMINAR PRODUCTO ===
if ($action === 'delete') {
    $id = (int)($_POST['id_producto'] ?? 0);
    if ($id <= 0) res(false, ['message' => 'ID inválido']);

    // Borrar movimientos asociados
    pg_query($conn, "DELETE FROM movimientos_inventario WHERE id_producto = $id");
    $result = pg_query($conn, "DELETE FROM productos WHERE id_producto = $id");
    $ok = !!$result;

    res($ok, ['message' => $ok ? 'Producto eliminado' : 'Error al eliminar']);
}

res(false, ['message' => 'Acción no válida']);