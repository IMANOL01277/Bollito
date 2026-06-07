<?php
require '../conexion.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

// ── Listar ventas ───────────────────────────────────────────────────────────
if ($action === 'list') {
    $query = "
        SELECT
            v.id_venta,
            p.nombre        AS producto,
            v.cantidad,
            v.precio_unitario,
            v.cantidad * v.precio_unitario AS total,
            v.cantidad * (v.precio_unitario - p.precio_compra) AS ganancia,
            v.observaciones,
            u.nombre        AS usuario,
            v.fecha_venta
        FROM ventas v
        LEFT JOIN productos p ON p.id_producto = v.id_producto
        LEFT JOIN usuarios  u ON u.id_usuario  = v.id_usuario
        ORDER BY v.fecha_venta DESC, v.id_venta DESC
    ";
    $result = pg_query($conn, $query);
    if (!$result) res(false, ['message' => 'Error al obtener ventas: ' . pg_last_error($conn)]);

    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['ventas' => $rows]);
}

// ── Listar productos disponibles para el select ────────────────────────────
if ($action === 'productos') {
    $result = pg_query($conn, "SELECT id_producto, nombre, precio_venta, precio_compra, stock FROM productos WHERE activo = true AND stock > 0 ORDER BY nombre ASC");
    if (!$result) res(false, ['message' => 'Error al obtener productos']);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['productos' => $rows]);
}

// ── Crear venta ─────────────────────────────────────────────────────────────
if ($action === 'create') {
    $id_producto     = (int)($_POST['id_producto'] ?? 0);
    $cantidad        = (int)($_POST['cantidad'] ?? 0);
    $precio_unitario = (float)($_POST['precio_unitario'] ?? 0);
    $observaciones   = pg_escape_string($conn, trim($_POST['observaciones'] ?? ''));
    $id_usuario      = (int)($_SESSION['id_usuario'] ?? 0);

    if ($id_producto <= 0 || $cantidad <= 0 || $precio_unitario <= 0) {
        res(false, ['message' => 'Datos inválidos. Verifica producto, cantidad y precio.']);
    }

    // Verificar stock disponible
    $check = pg_query_params($conn, "SELECT stock FROM productos WHERE id_producto = $1 AND activo = true", [$id_producto]);
    if (!$check || pg_num_rows($check) === 0) {
        res(false, ['message' => 'Producto no encontrado.']);
    }
    $prod = pg_fetch_assoc($check);
    if ((int)$prod['stock'] < $cantidad) {
        res(false, ['message' => "Stock insuficiente. Solo hay {$prod['stock']} unidades disponibles."]);
    }

    // Iniciar transacción
    pg_query($conn, 'BEGIN');

    // Insertar venta
    $ins = pg_query_params($conn,
        "INSERT INTO ventas (id_producto, cantidad, precio_unitario, observaciones, id_usuario)
         VALUES ($1, $2, $3, $4, $5)",
        [$id_producto, $cantidad, $precio_unitario, $observaciones, $id_usuario ?: null]
    );

    if (!$ins) {
        pg_query($conn, 'ROLLBACK');
        res(false, ['message' => 'Error al registrar venta: ' . pg_last_error($conn)]);
    }

    // Actualizar stock: RESTA cantidad
    $upd = pg_query_params($conn,
        "UPDATE productos SET stock = stock - $1, fecha_modificacion = NOW() WHERE id_producto = $2",
        [$cantidad, $id_producto]
    );

    if (!$upd) {
        pg_query($conn, 'ROLLBACK');
        res(false, ['message' => 'Error al actualizar stock: ' . pg_last_error($conn)]);
    }

    pg_query($conn, 'COMMIT');
    res(true, ['message' => "Venta registrada. Se descontaron $cantidad unidades del inventario."]);
}

// ── Eliminar venta (revierte stock) ────────────────────────────────────────
if ($action === 'delete') {
    $id_venta = (int)($_POST['id_venta'] ?? 0);
    if ($id_venta <= 0) res(false, ['message' => 'ID inválido']);

    // Obtener datos de la venta antes de borrar
    $data = pg_query_params($conn, "SELECT id_producto, cantidad FROM ventas WHERE id_venta = $1", [$id_venta]);
    if (!$data || pg_num_rows($data) === 0) res(false, ['message' => 'Venta no encontrada']);
    $row = pg_fetch_assoc($data);

    pg_query($conn, 'BEGIN');

    // Revertir stock: SUMA la cantidad que se había descontado
    $upd = pg_query_params($conn,
        "UPDATE productos SET stock = stock + $1, fecha_modificacion = NOW() WHERE id_producto = $2",
        [$row['cantidad'], $row['id_producto']]
    );

    $del = pg_query_params($conn, "DELETE FROM ventas WHERE id_venta = $1", [$id_venta]);

    if (!$upd || !$del) {
        pg_query($conn, 'ROLLBACK');
        res(false, ['message' => 'Error al eliminar venta: ' . pg_last_error($conn)]);
    }

    pg_query($conn, 'COMMIT');
    res(true, ['message' => 'Venta eliminada y stock restaurado.']);
}

// ── Resumen para estadísticas ───────────────────────────────────────────────
if ($action === 'resumen') {
    $query = "
        SELECT
            COALESCE(SUM(v.cantidad * v.precio_unitario), 0)                     AS total_ventas,
            COALESCE(SUM(v.cantidad * (v.precio_unitario - p.precio_compra)), 0) AS ganancia,
            COALESCE(SUM(v.cantidad), 0)                                         AS unidades_vendidas
        FROM ventas v
        LEFT JOIN productos p ON p.id_producto = v.id_producto
        WHERE v.fecha_venta >= CURRENT_DATE - INTERVAL '30 days'
    ";
    $result = pg_query($conn, $query);
    if (!$result) res(false, ['message' => 'Error']);
    $row = pg_fetch_assoc($result);
    res(true, ['resumen' => $row]);
}

res(false, ['message' => 'Acción inválida']);
?>
