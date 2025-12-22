<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

if ($action === 'list') {
    $query = "SELECT v.id_vendedor, v.zona, v.fecha_registro, u.nombre AS usuario, v.id_usuario 
              FROM vendedores_ambulantes v
              INNER JOIN usuarios u ON u.id_usuario = v.id_usuario
              ORDER BY v.id_vendedor DESC";
    $result = pg_query($conn, $query);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['vendedores' => $rows]);
}

if ($action === 'create') {
    $id_usuario = (int)$_POST['id_usuario'];
    $zona = trim($_POST['zona']);
    
    if ($id_usuario <= 0) res(false, ['message' => 'Debe seleccionar un usuario']);
    if ($zona === '') res(false, ['message' => 'La zona es obligatoria']);
    
    // Verificar si el usuario ya es vendedor
    $check = pg_query_params($conn, "SELECT id_vendedor FROM vendedores_ambulantes WHERE id_usuario = $1", [$id_usuario]);
    if (pg_num_rows($check) > 0) {
        res(false, ['message' => 'Este usuario ya está registrado como vendedor']);
    }
    
    $query = "INSERT INTO vendedores_ambulantes (id_usuario, zona) VALUES ($1, $2)";
    $result = pg_query_params($conn, $query, [$id_usuario, $zona]);
    res(!!$result, ['message' => $result ? 'Vendedor creado correctamente' : 'Error al crear vendedor']);
}

if ($action === 'update') {
    $id = (int)$_POST['id_vendedor'];
    $id_usuario = (int)$_POST['id_usuario'];
    $zona = trim($_POST['zona']);
    
    if ($zona === '') res(false, ['message' => 'La zona es obligatoria']);
    
    $query = "UPDATE vendedores_ambulantes SET id_usuario = $1, zona = $2 WHERE id_vendedor = $3";
    $result = pg_query_params($conn, $query, [$id_usuario, $zona, $id]);
    res(!!$result, ['message' => $result ? 'Vendedor actualizado correctamente' : 'Error al actualizar']);
}

if ($action === 'delete') {
    $id = (int)$_POST['id_vendedor'];
    $result = pg_query_params($conn, "DELETE FROM vendedores_ambulantes WHERE id_vendedor = $1", [$id]);
    res(!!$result, ['message' => $result ? 'Vendedor eliminado correctamente' : 'Error al eliminar']);
}

res(false, ['message' => 'Acción no válida']);