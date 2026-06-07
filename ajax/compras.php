<?php
require '../conexion.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

// ── Listar compras ──────────────────────────────────────────────────────────
if ($action === 'list') {
    $query = "
        SELECT
            c.id_compra,
            p.nombre        AS producto,
            c.cantidad,
            c.precio_unitario,
            c.cantidad * c.precio_unitario AS total,
            c.observaciones,
            u.nombre        AS usuario,
            c.fecha_compra
        FROM compras c
        LEFT JOIN productos p ON p.id_producto = c.id_producto
        LEFT JOIN usuarios  u ON u.id_usuario  = c.id_usuario
        ORDER BY c.fecha_compra DESC, c.id_compra DESC
    ";
    $result = pg_query($conn, $query);
    if (!$result) res(false, ['message' => 'Error al obtener compras: ' . pg_last_error($conn)]);

    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['compras' => $rows]);
}

// ── Listar productos disponibles para el select ────────────────────────────
if ($action === 'productos') {
    $result = pg_query($conn, "SELECT id_producto, nombre, precio_compra, stock FROM productos WHERE activo = true ORDER BY nombre ASC");
    if (!$result) res(false, ['message' => 'Error al obtener productos']);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['productos' => $rows]);
}

// ── Crear compra ────────────────────────────────────────────────────────────
if ($action === 'create') {
    $id_producto     = (int)($_POST['id_producto'] ?? 0);
    $cantidad        = (int)($_POST['cantidad'] ?? 0);
    $precio_unitario = (float)($_POST['precio_unitario'] ?? 0);
    $observaciones   = pg_escape_string($conn, trim($_POST['observaciones'] ?? ''));
    $id_usuario      = (int)($_SESSION['id_usuario'] ?? 0);

    if ($id_producto <= 0 || $cantidad <= 0 || $precio_unitario <= 0) {
        res(false, ['message' => 'Datos inválidos. Verifica producto, cantidad y precio.']);
    }

    // Verificar que el producto existe
    $check = pg_query_params($conn, "SELECT id_producto FROM productos WHERE id_producto = $1 AND activo = true", [$id_producto]);
    if (!$check || pg_num_rows($check) === 0) {
        res(false, ['message' => 'Producto no encontrado.']);
    }

    // Iniciar transacción
    pg_query($conn, 'BEGIN');

    // Insertar compra
    $ins = pg_query_params($conn,
        "INSERT INTO compras (id_producto, cantidad, precio_unitario, observaciones, id_usuario)
         VALUES ($1, $2, $3, $4, $5)",
        [$id_producto, $cantidad, $precio_unitario, $observaciones, $id_usuario ?: null]
    );

    if (!$ins) {
        pg_query($conn, 'ROLLBACK');
        res(false, ['message' => 'Error al registrar compra: ' . pg_last_error($conn)]);
    }

    // Actualizar stock: SUMA cantidad
    $upd = pg_query_params($conn,
        "UPDATE productos SET stock = stock + $1, fecha_modificacion = NOW() WHERE id_producto = $2",
        [$cantidad, $id_producto]
    );

    if (!$upd) {
        pg_query($conn, 'ROLLBACK');
        res(false, ['message' => 'Error al actualizar stock: ' . pg_last_error($conn)]);
    }

    pg_query($conn, 'COMMIT');
    res(true, ['message' => "Compra registrada. Se agregaron $cantidad unidades al inventario."]);
}

// ── Eliminar compra (revierte stock) ───────────────────────────────────────
if ($action === 'delete') {
    $id_compra = (int)($_POST['id_compra'] ?? 0);
    if ($id_compra <= 0) res(false, ['message' => 'ID inválido']);

    // Obtener datos de la compra antes de borrar
    $data = pg_query_params($conn, "SELECT id_producto, cantidad FROM compras WHERE id_compra = $1", [$id_compra]);
    if (!$data || pg_num_rows($data) === 0) res(false, ['message' => 'Compra no encontrada']);
    $row = pg_fetch_assoc($data);

    pg_query($conn, 'BEGIN');

    // Revertir stock: RESTA la cantidad que se había sumado
    $upd = pg_query_params($conn,
        "UPDATE productos SET stock = GREATEST(stock - $1, 0), fecha_modificacion = NOW() WHERE id_producto = $2",
        [$row['cantidad'], $row['id_producto']]
    );

    $del = pg_query_params($conn, "DELETE FROM compras WHERE id_compra = $1", [$id_compra]);

    if (!$upd || !$del) {
        pg_query($conn, 'ROLLBACK');
        res(false, ['message' => 'Error al eliminar compra: ' . pg_last_error($conn)]);
    }

    pg_query($conn, 'COMMIT');
    res(true, ['message' => 'Compra eliminada y stock actualizado.']);
}

// ── Resumen para estadísticas ───────────────────────────────────────────────
if ($action === 'resumen') {
    $query = "
        SELECT
            COALESCE(SUM(cantidad * precio_unitario), 0) AS total_compras,
            COALESCE(SUM(cantidad), 0)                   AS unidades_compradas
        FROM compras
        WHERE fecha_compra >= CURRENT_DATE - INTERVAL '30 days'
    ";
    $result = pg_query($conn, $query);
    if (!$result) res(false, ['message' => 'Error']);
    $row = pg_fetch_assoc($result);
    res(true, ['resumen' => $row]);
}

res(false, ['message' => 'Acción inválida']);
?>
