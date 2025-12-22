<?php
require '../conexion.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

if ($action === 'resumen') {
    $query = "SELECT 
                COUNT(*) as total_devoluciones,
                COALESCE(SUM(cantidad), 0) as unidades_devueltas,
                COALESCE(SUM(cantidad * precio_unitario), 0) as valor_devuelto
              FROM devoluciones
              WHERE fecha_devolucion >= CURRENT_DATE - INTERVAL '30 days'";
    $result = pg_query($conn, $query);
    $resumen = pg_fetch_assoc($result);
    
    $data = [
        'total_devoluciones' => (int)$resumen['total_devoluciones'],
        'unidades_devueltas' => (int)$resumen['unidades_devueltas'],
        'valor_devuelto' => (float)$resumen['valor_devuelto']
    ];
    
    res(true, ['resumen' => $data]);
}

if ($action === 'list') {
    $query = "SELECT d.*, p.nombre AS producto, u.nombre AS usuario
              FROM devoluciones d
              INNER JOIN productos p ON p.id_producto = d.id_producto
              LEFT JOIN usuarios u ON u.id_usuario = d.id_usuario
              WHERE d.fecha_devolucion >= CURRENT_DATE - INTERVAL '30 days'
              ORDER BY d.fecha_devolucion DESC";
    $result = pg_query($conn, $query);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['devoluciones' => $rows]);
}

if ($action === 'create') {
    $id_producto = (int)$_POST['id_producto'];
    $cantidad = (int)$_POST['cantidad'];
    $motivo = pg_escape_string($conn, trim($_POST['motivo']));
    $observaciones = pg_escape_string($conn, trim($_POST['observaciones'] ?? ''));
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    
    if ($id_producto <= 0) res(false, ['message' => 'Debe seleccionar un producto']);
    if ($cantidad <= 0) res(false, ['message' => 'La cantidad debe ser mayor a 0']);
    if (empty($motivo)) res(false, ['message' => 'Debe especificar el motivo']);
    
    // Obtener información del producto
    $prod_query = pg_query_params($conn, "SELECT precio_venta, precio_compra FROM productos WHERE id_producto = $1", [$id_producto]);
    if (pg_num_rows($prod_query) === 0) {
        res(false, ['message' => 'Producto no encontrado']);
    }
    $producto = pg_fetch_assoc($prod_query);
    $precio_unitario = $producto['precio_venta'];
    $precio_compra = $producto['precio_compra'];
    
    // Iniciar transacción
    pg_query($conn, "BEGIN");
    
    try {
        // Registrar la devolución
        $insert_dev = "INSERT INTO devoluciones (id_producto, cantidad, motivo, observaciones, precio_unitario, id_usuario) 
                       VALUES ($1, $2, $3, $4, $5, $6) RETURNING id_devolucion";
        $result_dev = pg_query_params($conn, $insert_dev, [$id_producto, $cantidad, $motivo, $observaciones, $precio_unitario, $id_usuario]);
        
        if (!$result_dev) {
            throw new Exception('Error al registrar devolución');
        }
        
        // Incrementar stock (devolver al inventario)
        $update_stock = "UPDATE productos SET stock = stock + $1 WHERE id_producto = $2";
        $result_stock = pg_query_params($conn, $update_stock, [$cantidad, $id_producto]);
        
        if (!$result_stock) {
            throw new Exception('Error al actualizar stock');
        }
        
        // Registrar movimiento de entrada (la devolución vuelve al inventario)
        $desc_movimiento = "Devolución - Motivo: $motivo";
        if ($observaciones) $desc_movimiento .= " - $observaciones";
        
        $insert_mov = "INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, descripcion) 
                       VALUES ($1, 'entrada', $2, $3, $4)";
        $result_mov = pg_query_params($conn, $insert_mov, [$id_producto, $cantidad, $precio_compra, $desc_movimiento]);
        
        if (!$result_mov) {
            throw new Exception('Error al registrar movimiento');
        }
        
        // Confirmar transacción
        pg_query($conn, "COMMIT");
        res(true, ['message' => 'Devolución registrada correctamente. Stock actualizado.']);
        
    } catch (Exception $e) {
        pg_query($conn, "ROLLBACK");
        res(false, ['message' => $e->getMessage()]);
    }
}

if ($action === 'delete') {
    $id = (int)$_POST['id_devolucion'];
    
    // Obtener información de la devolución antes de eliminarla
    $dev_query = pg_query_params($conn, 
        "SELECT id_producto, cantidad FROM devoluciones WHERE id_devolucion = $1", 
        [$id]
    );
    
    if (pg_num_rows($dev_query) === 0) {
        res(false, ['message' => 'Devolución no encontrada']);
    }
    
    $devolucion = pg_fetch_assoc($dev_query);
    
    // Iniciar transacción
    pg_query($conn, "BEGIN");
    
    try {
        // Revertir el stock (quitar las unidades que se habían devuelto)
        $update_stock = "UPDATE productos SET stock = stock - $1 WHERE id_producto = $2";
        $result_stock = pg_query_params($conn, $update_stock, [$devolucion['cantidad'], $devolucion['id_producto']]);
        
        if (!$result_stock) {
            throw new Exception('Error al revertir stock');
        }
        
        // Eliminar la devolución
        $delete_dev = pg_query_params($conn, "DELETE FROM devoluciones WHERE id_devolucion = $1", [$id]);
        
        if (!$delete_dev) {
            throw new Exception('Error al eliminar devolución');
        }
        
        pg_query($conn, "COMMIT");
        res(true, ['message' => 'Devolución eliminada y stock revertido']);
        
    } catch (Exception $e) {
        pg_query($conn, "ROLLBACK");
        res(false, ['message' => $e->getMessage()]);
    }
}

res(false, ['message' => 'Acción no válida']);