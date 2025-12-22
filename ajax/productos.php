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
    $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio_compra, p.precio_venta, p.stock, 
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
    $result = pg_query_params($conn, "SELECT * FROM productos WHERE id_producto = $1", [$id]);
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

// === VERIFICAR STOCK BAJO ===
if ($action === 'check_stock') {
    $result = pg_query($conn, "SELECT id_producto, nombre, stock FROM productos WHERE stock <= 10 ORDER BY stock ASC");
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['low_stock' => $rows]);
}

// === CREAR PRODUCTO ===
if ($action === 'create') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio_compra = (float)($_POST['precio_compra'] ?? 0);
    $precio_venta = (float)($_POST['precio_venta'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $id_proveedor = $_POST['id_proveedor'] ? (int)$_POST['id_proveedor'] : null;

    if ($nombre === '') res(false, ['message' => 'El nombre es obligatorio']);
    if ($precio_venta < $precio_compra) res(false, ['message' => 'El precio de venta no puede ser menor al de compra']);

    $query = "INSERT INTO productos (nombre, descripcion, precio_compra, precio_venta, stock, id_categoria, id_proveedor) 
              VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING id_producto";
    $result = pg_query_params($conn, $query, [$nombre, $descripcion, $precio_compra, $precio_venta, $stock, $id_categoria, $id_proveedor]);
    
    if ($result) {
        $row = pg_fetch_assoc($result);
        $newId = $row['id_producto'];
        
        // Registrar movimiento de entrada inicial si stock > 0
        if ($stock > 0) {
            $total = $precio_compra * $stock;
            $mov_query = "INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, descripcion) 
                          VALUES ($1, 'entrada', $2, $3, 'Registro inicial de stock')";
            pg_query_params($conn, $mov_query, [$newId, $stock, $precio_compra]);
        }
        
        res(true, ['message' => 'Producto creado correctamente']);
    } else {
        res(false, ['message' => 'Error al crear producto']);
    }
}

// === ACTUALIZAR PRODUCTO ===
if ($action === 'update') {
    $id = (int)($_POST['id_producto'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio_compra = (float)($_POST['precio_compra'] ?? 0);
    $precio_venta = (float)($_POST['precio_venta'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $id_proveedor = $_POST['id_proveedor'] ? (int)$_POST['id_proveedor'] : null;

    if ($id <= 0) res(false, ['message' => 'ID inválido']);
    if ($precio_venta < $precio_compra) res(false, ['message' => 'El precio de venta no puede ser menor al de compra']);

    // Obtener stock anterior
    $old_result = pg_query_params($conn, "SELECT stock, precio_compra FROM productos WHERE id_producto = $1", [$id]);
    $old = pg_fetch_assoc($old_result);
    $oldStock = (int)$old['stock'];
    $oldPrecioCompra = (float)$old['precio_compra'];

    $query = "UPDATE productos SET nombre=$1, descripcion=$2, precio_compra=$3, precio_venta=$4, stock=$5, id_categoria=$6, id_proveedor=$7 
              WHERE id_producto=$8";
    $result = pg_query_params($conn, $query, [$nombre, $descripcion, $precio_compra, $precio_venta, $stock, $id_categoria, $id_proveedor, $id]);
    
    if ($result) {
        // Registrar movimiento automático si cambió el stock
        $cantidad = $stock - $oldStock;
        if ($cantidad != 0) {
            $tipo = $cantidad > 0 ? 'entrada' : 'salida';
            $cantidadAbs = abs($cantidad);
            $precioUsado = $precio_compra > 0 ? $precio_compra : $oldPrecioCompra;

            $mov_query = "INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, descripcion) 
                          VALUES ($1, $2, $3, $4, 'Actualización de stock')";
            pg_query_params($conn, $mov_query, [$id, $tipo, $cantidadAbs, $precioUsado]);
        }
        
        res(true, ['message' => 'Producto actualizado correctamente']);
    } else {
        res(false, ['message' => 'Error al actualizar producto']);
    }
}

// === ELIMINAR PRODUCTO ===
if ($action === 'delete') {
    $id = (int)($_POST['id_producto'] ?? 0);
    if ($id <= 0) res(false, ['message' => 'ID inválido']);

    pg_query_params($conn, "DELETE FROM movimientos_inventario WHERE id_producto = $1", [$id]);
    $result = pg_query_params($conn, "DELETE FROM productos WHERE id_producto = $1", [$id]);

    res(!!$result, ['message' => $result ? 'Producto eliminado' : 'Error al eliminar']);
}

res(false, ['message' => 'Acción no válida']);