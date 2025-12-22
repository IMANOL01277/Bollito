<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function r($ok, $d = []) {
    echo json_encode(array_merge(['success' => $ok], $d));
    exit();
}

if($action === 'resumen'){
    // Calcular inversión (entradas con precio de compra)
    $query_inversion = "SELECT COALESCE(SUM(cantidad * precio_unitario), 0) as total 
                        FROM movimientos_inventario 
                        WHERE tipo = 'entrada' 
                        AND fecha_movimiento >= CURRENT_DATE - INTERVAL '30 days'";
    $result_inv = pg_query($conn, $query_inversion);
    $inversion = pg_fetch_assoc($result_inv)['total'];
    
    // Calcular ingresos (salidas con precio de venta)
    // Las salidas deberían registrarse con el precio de venta del producto
    $query_ingresos = "SELECT COALESCE(SUM(m.cantidad * p.precio_venta), 0) as total 
                       FROM movimientos_inventario m
                       INNER JOIN productos p ON p.id_producto = m.id_producto
                       WHERE m.tipo = 'salida' 
                       AND m.fecha_movimiento >= CURRENT_DATE - INTERVAL '30 days'";
    $result_ing = pg_query($conn, $query_ingresos);
    $ingresos = pg_fetch_assoc($result_ing)['total'];
    
    // Calcular costo de productos vendidos (salidas con precio de compra)
    $query_costo = "SELECT COALESCE(SUM(m.cantidad * p.precio_compra), 0) as total 
                    FROM movimientos_inventario m
                    INNER JOIN productos p ON p.id_producto = m.id_producto
                    WHERE m.tipo = 'salida' 
                    AND m.fecha_movimiento >= CURRENT_DATE - INTERVAL '30 days'";
    $result_costo = pg_query($conn, $query_costo);
    $costo = pg_fetch_assoc($result_costo)['total'];
    
    $ganancia_real = $ingresos - $costo;
    
    $data = [
        'inversion' => floatval($inversion),
        'ingresos' => floatval($ingresos),
        'costo_ventas' => floatval($costo),
        'ganancia' => floatval($ganancia_real),
        'margen' => $ingresos > 0 ? round(($ganancia_real / $ingresos) * 100, 2) : 0
    ];
    
    r(true, ['resumen' => $data]);
}

if($action === 'list'){
    $query = "SELECT m.*, p.nombre AS producto, p.precio_compra, p.precio_venta,
                     CASE 
                         WHEN m.tipo = 'entrada' THEN m.cantidad * m.precio_unitario
                         WHEN m.tipo = 'salida' THEN m.cantidad * p.precio_venta
                     END as monto_total,
                     CASE 
                         WHEN m.tipo = 'salida' THEN m.cantidad * (p.precio_venta - p.precio_compra)
                         ELSE 0
                     END as ganancia
              FROM movimientos_inventario m 
              LEFT JOIN productos p ON p.id_producto = m.id_producto 
              WHERE fecha_movimiento >= CURRENT_DATE - INTERVAL '30 days' 
              ORDER BY fecha_movimiento DESC";
    $result = pg_query($conn, $query);
    $rows = [];
    while($x = pg_fetch_assoc($result)) $rows[] = $x;
    r(true, ['movs' => $rows]);
}

if($action === 'dashboard'){
    // Resumen para el panel principal
    $stats = [];
    
    // Total de productos
    $r1 = pg_query($conn, "SELECT COUNT(*) as total FROM productos");
    $stats['total_productos'] = pg_fetch_assoc($r1)['total'];
    
    // Productos con stock bajo
    $r2 = pg_query($conn, "SELECT COUNT(*) as total FROM productos WHERE stock <= 10");
    $stats['stock_bajo'] = pg_fetch_assoc($r2)['total'];
    
    // Valor del inventario (stock * precio_venta)
    $r3 = pg_query($conn, "SELECT COALESCE(SUM(stock * precio_venta), 0) as total FROM productos");
    $stats['valor_inventario'] = floatval(pg_fetch_assoc($r3)['total']);
    
    // Ganancia potencial (stock * (precio_venta - precio_compra))
    $r4 = pg_query($conn, "SELECT COALESCE(SUM(stock * (precio_venta - precio_compra)), 0) as total FROM productos");
    $stats['ganancia_potencial'] = floatval(pg_fetch_assoc($r4)['total']);
    
    r(true, ['stats' => $stats]);
}

r(false, ['message'=>'Acción no válida']);