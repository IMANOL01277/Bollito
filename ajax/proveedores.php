<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

if ($action === 'list') {
    $query = "SELECT p.*, COUNT(pr.id_producto) as total_productos 
              FROM proveedores p 
              LEFT JOIN productos pr ON pr.id_proveedor = p.id_proveedor 
              GROUP BY p.id_proveedor 
              ORDER BY p.id_proveedor DESC";
    $result = pg_query($conn, $query);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['proveedores' => $rows]);
}

if ($action === 'create') {
    $nombre = trim($_POST['nombre']);
    $contacto = trim($_POST['contacto'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    
    if ($nombre === '') res(false, ['message' => 'El nombre es obligatorio']);
    
    $query = "INSERT INTO proveedores (nombre, contacto, telefono, correo, direccion) 
              VALUES ($1, $2, $3, $4, $5)";
    $result = pg_query_params($conn, $query, [$nombre, $contacto, $telefono, $correo, $direccion]);
    res(!!$result, ['message' => $result ? 'Proveedor creado correctamente' : 'Error al crear proveedor']);
}

if ($action === 'update') {
    $id = (int)$_POST['id_proveedor'];
    $nombre = trim($_POST['nombre']);
    $contacto = trim($_POST['contacto'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    
    if ($nombre === '') res(false, ['message' => 'El nombre es obligatorio']);
    
    $query = "UPDATE proveedores SET nombre = $1, contacto = $2, telefono = $3, correo = $4, direccion = $5 
              WHERE id_proveedor = $6";
    $result = pg_query_params($conn, $query, [$nombre, $contacto, $telefono, $correo, $direccion, $id]);
    res(!!$result, ['message' => $result ? 'Proveedor actualizado correctamente' : 'Error al actualizar']);
}

if ($action === 'delete') {
    $id = (int)$_POST['id_proveedor'];
    
    // Desvincular productos antes de eliminar
    pg_query_params($conn, "UPDATE productos SET id_proveedor = NULL WHERE id_proveedor = $1", [$id]);
    
    $result = pg_query_params($conn, "DELETE FROM proveedores WHERE id_proveedor = $1", [$id]);
    res(!!$result, ['message' => $result ? 'Proveedor eliminado correctamente' : 'Error al eliminar']);
}

res(false, ['message' => 'Acción no válida']);