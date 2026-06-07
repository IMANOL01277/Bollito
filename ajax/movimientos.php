<?php
require '../conexion.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

// Listar movimientos (últimos 30 días por defecto)
if ($action === 'list') {
    $query = "
        SELECT 
            'mov_' || m.id_movimiento AS id_movimiento,
            m.id_producto,
            p.nombre AS producto,
            m.tipo,
            m.cantidad,
            m.precio_unitario,
            (m.cantidad * m.precio_unitario) AS monto_total,
            CASE 
                WHEN m.tipo = 'salida' THEN m.cantidad * (m.precio_unitario - p.precio_compra)
                ELSE 0
            END AS ganancia,
            m.descripcion,
            m.fecha_movimiento
        FROM movimientos_inventario m
        LEFT JOIN productos p ON p.id_producto = m.id_producto
        WHERE m.fecha_movimiento >= CURRENT_DATE - INTERVAL '30 days'

        UNION ALL

        SELECT
            'com_' || c.id_compra AS id_movimiento,
            c.id_producto,
            p.nombre AS producto,
            'entrada'::character varying AS tipo,
            c.cantidad,
            c.precio_unitario,
            (c.cantidad * c.precio_unitario) AS monto_total,
            0::numeric AS ganancia,
            COALESCE(c.observaciones, 'Compra registrada') AS descripcion,
            c.fecha_compra AS fecha_movimiento
        FROM compras c
        LEFT JOIN productos p ON p.id_producto = c.id_producto
        WHERE c.fecha_compra >= CURRENT_DATE - INTERVAL '30 days'

        UNION ALL

        SELECT
            'ven_' || v.id_venta AS id_movimiento,
            v.id_producto,
            p.nombre AS producto,
            'salida'::character varying AS tipo,
            v.cantidad,
            v.precio_unitario,
            (v.cantidad * v.precio_unitario) AS monto_total,
            v.cantidad * (v.precio_unitario - p.precio_compra) AS ganancia,
            COALESCE(v.observaciones, 'Venta registrada') AS descripcion,
            v.fecha_venta AS fecha_movimiento
        FROM ventas v
        LEFT JOIN productos p ON p.id_producto = v.id_producto
        WHERE v.fecha_venta >= CURRENT_DATE - INTERVAL '30 days'

        ORDER BY fecha_movimiento DESC
    ";

    $result = pg_query($conn, $query);
    if (!$result) {
        res(false, ['message' => 'Error al obtener movimientos: ' . pg_last_error($conn)]);
    }

    $rows = [];
    while ($r = pg_fetch_assoc($result)) {
        $rows[] = $r;
    }

    res(true, ['movs' => $rows]);
}

// Resumen financiero para estadísticas (últimos 30 días)
if ($action === 'resumen') {
    $query = "
        WITH combined_resumen AS (
            SELECT
                m.tipo,
                m.cantidad * m.precio_unitario AS total_mov,
                CASE WHEN m.tipo = 'salida' THEN m.cantidad * p.precio_compra ELSE 0 END AS costo_mov
            FROM movimientos_inventario m
            LEFT JOIN productos p ON p.id_producto = m.id_producto
            WHERE m.fecha_movimiento >= CURRENT_DATE - INTERVAL '30 days'

            UNION ALL

            SELECT
                'entrada'::character varying AS tipo,
                c.cantidad * c.precio_unitario AS total_mov,
                0::numeric AS costo_mov
            FROM compras c
            WHERE c.fecha_compra >= CURRENT_DATE - INTERVAL '30 days'

            UNION ALL

            SELECT
                'salida'::character varying AS tipo,
                v.cantidad * v.precio_unitario AS total_mov,
                v.cantidad * p.precio_compra AS costo_mov
            FROM ventas v
            LEFT JOIN productos p ON p.id_producto = v.id_producto
            WHERE v.fecha_venta >= CURRENT_DATE - INTERVAL '30 days'
        )
        SELECT
            COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN total_mov ELSE 0 END), 0) AS inversion,
            COALESCE(SUM(CASE WHEN tipo = 'salida' THEN total_mov ELSE 0 END), 0) AS ingresos,
            COALESCE(SUM(CASE WHEN tipo = 'salida' THEN costo_mov ELSE 0 END), 0) AS costo_ventas
        FROM combined_resumen
    ";

    $result = pg_query($conn, $query);
    if (!$result) {
        res(false, ['message' => 'Error al obtener resumen: ' . pg_last_error($conn)]);
    }

    $row = pg_fetch_assoc($result);
    $inversion = (float)$row['inversion'];
    $ingresos = (float)$row['ingresos'];
    $costo_ventas = (float)$row['costo_ventas'];
    $ganancia = $ingresos - $costo_ventas;
    $margen = $ingresos > 0 ? round(($ganancia / $ingresos) * 100, 1) : 0;

    $data = [
        'inversion'     => $inversion,
        'ingresos'      => $ingresos,
        'costo_ventas'  => $costo_ventas,
        'ganancia'      => $ganancia,
        'margen'        => $margen,
    ];

    res(true, ['resumen' => $data]);
}

// Estadísticas para el dashboard principal
if ($action === 'dashboard') {
    // Total de productos
    $q1 = pg_query($conn, "SELECT COUNT(*) AS total FROM productos");
    $r1 = pg_fetch_assoc($q1);
    $total_productos = (int)$r1['total'];

    // Productos con stock bajo (usando stock_minimo)
    $q2 = pg_query($conn, "SELECT COUNT(*) AS total FROM productos WHERE stock <= stock_minimo");
    $r2 = pg_fetch_assoc($q2);
    $stock_bajo = (int)$r2['total'];

    // Valor de inventario (basado en precio de compra)
    $q3 = pg_query($conn, "SELECT COALESCE(SUM(stock * precio_compra), 0) AS valor FROM productos");
    $r3 = pg_fetch_assoc($q3);
    $valor_inventario = (float)$r3['valor'];

    // Ganancia potencial (si se vende todo al precio de venta actual)
    $q4 = pg_query($conn, "
        SELECT 
            COALESCE(SUM(stock * (precio_venta - precio_compra)), 0) AS ganancia_potencial
        FROM productos
    ");
    $r4 = pg_fetch_assoc($q4);
    $ganancia_potencial = (float)$r4['ganancia_potencial'];

    $stats = [
        'total_productos'     => $total_productos,
        'stock_bajo'          => $stock_bajo,
        'valor_inventario'    => $valor_inventario,
        'ganancia_potencial'  => $ganancia_potencial,
    ];

    res(true, ['stats' => $stats]);
}

res(false, ['message' => 'Acción no válida']);
