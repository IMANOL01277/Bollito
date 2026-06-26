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

// ── Crear venta (soporta múltiples items) ──────────────────────────────────
if ($action === 'create') {
    $observaciones = pg_escape_string($conn, trim($_POST['observaciones'] ?? ''));
    $id_usuario    = (int)($_SESSION['id_usuario'] ?? 0);

    // Soporte multi-item: items[]=JSON o campos únicos para compatibilidad
    $items = [];
    if (!empty($_POST['items'])) {
        // Formato multi-item enviado como JSON string
        $decoded = json_decode($_POST['items'], true);
        if (is_array($decoded)) {
            $items = $decoded;
        }
    } else {
        // Formato de un solo item (compatibilidad hacia atrás)
        $id_producto     = (int)($_POST['id_producto'] ?? 0);
        $cantidad        = (int)($_POST['cantidad'] ?? 0);
        $precio_unitario = (float)($_POST['precio_unitario'] ?? 0);
        if ($id_producto > 0 && $cantidad > 0 && $precio_unitario > 0) {
            $items[] = ['id_producto' => $id_producto, 'cantidad' => $cantidad, 'precio_unitario' => $precio_unitario];
        }
    }

    if (empty($items)) {
        res(false, ['message' => 'No hay productos en la venta.']);
    }

    // Validar todos los items antes de procesar
    foreach ($items as $idx => $item) {
        $id_p  = (int)($item['id_producto'] ?? 0);
        $cant  = (int)($item['cantidad'] ?? 0);
        $precio = (float)($item['precio_unitario'] ?? 0);
        $num = $idx + 1;

        if ($id_p <= 0 || $cant <= 0 || $precio <= 0) {
            res(false, ['message' => "Item #$num: datos inválidos. Verifica producto, cantidad y precio."]);
        }

        $check = pg_query_params($conn, "SELECT stock FROM productos WHERE id_producto = $1 AND activo = true", [$id_p]);
        if (!$check || pg_num_rows($check) === 0) {
            res(false, ['message' => "Item #$num: producto no encontrado."]);
        }
        $prod = pg_fetch_assoc($check);
        if ((int)$prod['stock'] < $cant) {
            res(false, ['message' => "Item #$num: stock insuficiente. Solo hay {$prod['stock']} unidades disponibles."]);
        }
    }

    // Iniciar transacción única para todos los items
    pg_query($conn, 'BEGIN');

    $totalItems = 0;
    foreach ($items as $item) {
        $id_p   = (int)$item['id_producto'];
        $cant   = (int)$item['cantidad'];
        $precio = (float)$item['precio_unitario'];

        // Insertar venta
        $ins = pg_query_params($conn,
            "INSERT INTO ventas (id_producto, cantidad, precio_unitario, observaciones, id_usuario)
             VALUES ($1, $2, $3, $4, $5)",
            [$id_p, $cant, $precio, $observaciones, $id_usuario ?: null]
        );

        if (!$ins) {
            pg_query($conn, 'ROLLBACK');
            res(false, ['message' => 'Error al registrar venta: ' . pg_last_error($conn)]);
        }

        // Actualizar stock: RESTA cantidad
        $upd = pg_query_params($conn,
            "UPDATE productos SET stock = stock - $1, fecha_modificacion = NOW() WHERE id_producto = $2",
            [$cant, $id_p]
        );

        if (!$upd) {
            pg_query($conn, 'ROLLBACK');
            res(false, ['message' => 'Error al actualizar stock: ' . pg_last_error($conn)]);
        }

        $totalItems += $cant;
    }

    pg_query($conn, 'COMMIT');
    $numProductos = count($items);
    res(true, ['message' => "Venta registrada con $numProductos producto(s). Se descontaron $totalItems unidades del inventario."]);
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
