<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');
$a = $_GET['action'] ?? $_POST['action'] ?? '';

function r($ok, $d = []) {
    echo json_encode(array_merge(['success' => $ok], $d));
    exit();
}

if($a === 'list'){
    $result = pg_query($conn, "SELECT * FROM domicilios ORDER BY id_domicilio DESC");
    $rows = [];
    while($x = pg_fetch_assoc($result)) $rows[] = $x;
    r(true, ['domicilios' => $rows]);
}

if($a === 'create'){
    $c = pg_escape($conn, $_POST['cliente']);
    $d = pg_escape($conn, $_POST['direccion']);
    $p = pg_escape($conn, $_POST['producto']);
    $cant = (int)$_POST['cantidad'];
    $e = pg_escape($conn, $_POST['estado']);
    
    $query = "INSERT INTO domicilios(cliente, direccion, producto, cantidad, estado) 
              VALUES ($1, $2, $3, $4, $5)";
    $result = pg_query_params($conn, $query, [$c, $d, $p, $cant, $e]);
    r(!!$result);
}

if($a === 'update'){
    $id = (int)$_POST['id_domicilio'];
    $c = pg_escape($conn, $_POST['cliente']);
    $d = pg_escape($conn, $_POST['direccion']);
    $p = pg_escape($conn, $_POST['producto']);
    $cant = (int)$_POST['cantidad'];
    $e = pg_escape($conn, $_POST['estado']);
    
    $query = "UPDATE domicilios SET cliente=$1, direccion=$2, producto=$3, cantidad=$4, estado=$5 
              WHERE id_domicilio=$6";
    $result = pg_query_params($conn, $query, [$c, $d, $p, $cant, $e, $id]);
    r(!!$result);
}

if($a === 'delete'){
    $id = (int)$_POST['id_domicilio'];
    $result = pg_query($conn, "DELETE FROM domicilios WHERE id_domicilio=$id");
    r(!!$result);
}

r(false);