<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

if ($action === 'list') {
    $query = "SELECT pr.*, p.nombre AS producto, p.precio_venta AS precio_original 
              FROM promociones pr
              INNER JOIN productos p ON p.id_producto = pr.id_producto
              ORDER BY pr.fecha_inicio DESC";
    $result = pg_query($conn, $query);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['promociones' => $rows]);
}

if ($action === 'activas') {
    $hoy = date('Y-m-d');
    $query = "SELECT pr.*, p.nombre AS producto, p.precio_venta AS precio_original 
              FROM promociones pr
              INNER JOIN productos p ON p.id_producto = pr.id_producto
              WHERE pr.fecha_inicio <= '$hoy' AND pr.fecha_fin >= '$hoy'
              ORDER BY pr.id_promocion DESC";
    $result = pg_query($conn, $query);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) {
        $r['precio_promocion'] = floatval($r['precio_original']) * (1 - floatval($r['descuento']) / 100);
        $rows[] = $r;
    }
    res(true, ['promociones_activas' => $rows]);
}

if ($action === 'create') {
    $id_producto = (int)$_POST['id_producto'];
    $descuento = (float)$_POST['descuento'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    
    if ($id_producto <= 0) res(false, ['message' => 'Debe seleccionar un producto']);
    if ($descuento <= 0 || $descuento >= 100) res(false, ['message' => 'El descuento debe estar entre 1% y 99%']);
    if ($fecha_fin <= $fecha_inicio) res(false, ['message' => 'La fecha de fin debe ser posterior a la de inicio']);
    
    // Verificar si el producto ya tiene una promoción activa
    $check = pg_query_params($conn, 
        "SELECT id_promocion FROM promociones 
         WHERE id_producto = $1 
         AND fecha_fin >= CURRENT_DATE 
         AND fecha_inicio <= $2", 
        [$id_producto, $fecha_fin]
    );
    
    if (pg_num_rows($check) > 0) {
        res(false, ['message' => 'Este producto ya tiene una promoción activa en ese periodo']);
    }
    
    $query = "INSERT INTO promociones (id_producto, descuento, fecha_inicio, fecha_fin) 
              VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($conn, $query, [$id_producto, $descuento, $fecha_inicio, $fecha_fin]);
    res(!!$result, ['message' => $result ? 'Promoción creada correctamente' : 'Error al crear promoción']);
}

if ($action === 'update') {
    $id = (int)$_POST['id_promocion'];
    $id_producto = (int)$_POST['id_producto'];
    $descuento = (float)$_POST['descuento'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    
    if ($descuento <= 0 || $descuento >= 100) res(false, ['message' => 'El descuento debe estar entre 1% y 99%']);
    if ($fecha_fin <= $fecha_inicio) res(false, ['message' => 'La fecha de fin debe ser posterior a la de inicio']);
    
    $query = "UPDATE promociones SET id_producto = $1, descuento = $2, fecha_inicio = $3, fecha_fin = $4 
              WHERE id_promocion = $5";
    $result = pg_query_params($conn, $query, [$id_producto, $descuento, $fecha_inicio, $fecha_fin, $id]);
    res(!!$result, ['message' => $result ? 'Promoción actualizada correctamente' : 'Error al actualizar']);
}

if ($action === 'delete') {
    $id = (int)$_POST['id_promocion'];
    $result = pg_query_params($conn, "DELETE FROM promociones WHERE id_promocion = $1", [$id]);
    res(!!$result, ['message' => $result ? 'Promoción eliminada correctamente' : 'Error al eliminar']);
}

res(false, ['message' => 'Acción no válida']);