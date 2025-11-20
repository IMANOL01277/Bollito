<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function r($ok, $d = []) {
    echo json_encode(array_merge(['success' => $ok], $d));
    exit();
}

if($action === 'resumen'){
    $query = "SELECT tipo, SUM(cantidad * precio_unitario) as total 
              FROM movimientos_inventario 
              WHERE fecha_movimiento >= CURRENT_DATE - INTERVAL '30 days' 
              GROUP BY tipo";
    $result = pg_query($conn, $query);
    $data = ['entrada'=>0, 'salida'=>0, 'ganancia'=>0];
    while($x = pg_fetch_assoc($result)) {
        $data[$x['tipo']] = $x['total'];
    }
    $data['ganancia'] = $data['salida'] - $data['entrada'];
    r(true, ['resumen'=>$data]);
}

if($action === 'list'){
    $query = "SELECT m.*, p.nombre AS producto 
              FROM movimientos_inventario m 
              LEFT JOIN productos p ON p.id_producto = m.id_producto 
              WHERE fecha_movimiento >= CURRENT_DATE - INTERVAL '30 days' 
              ORDER BY fecha_movimiento DESC";
    $result = pg_query($conn, $query);
    $rows = [];
    while($x = pg_fetch_assoc($result)) $rows[] = $x;
    r(true, ['movs'=>$rows]);
}

r(false, ['message'=>'Acción no válida']);