<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

if ($action === 'list') {
    $query = "SELECT c.*, COUNT(p.id_producto) as total_productos 
              FROM categorias c 
              LEFT JOIN productos p ON p.id_categoria = c.id_categoria 
              GROUP BY c.id_categoria 
              ORDER BY c.id_categoria DESC";
    $result = pg_query($conn, $query);
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    res(true, ['categorias' => $rows]);
}

if ($action === 'create') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion'] ?? '');
    
    if ($nombre === '') res(false, ['message' => 'El nombre es obligatorio']);
    
    $query = "INSERT INTO categorias (nombre, descripcion) VALUES ($1, $2)";
    $result = pg_query_params($conn, $query, [$nombre, $descripcion]);
    res(!!$result, ['message' => $result ? 'Categoría creada correctamente' : 'Error al crear categoría']);
}

if ($action === 'update') {
    $id = (int)$_POST['id_categoria'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion'] ?? '');
    
    if ($nombre === '') res(false, ['message' => 'El nombre es obligatorio']);
    
    $query = "UPDATE categorias SET nombre = $1, descripcion = $2 WHERE id_categoria = $3";
    $result = pg_query_params($conn, $query, [$nombre, $descripcion, $id]);
    res(!!$result, ['message' => $result ? 'Categoría actualizada correctamente' : 'Error al actualizar']);
}

if ($action === 'delete') {
    $id = (int)$_POST['id_categoria'];
    
    // Verificar si tiene productos asociados
    $check = pg_query_params($conn, "SELECT COUNT(*) as total FROM productos WHERE id_categoria = $1", [$id]);
    $count = pg_fetch_assoc($check)['total'];
    
    if ($count > 0) {
        // Desvincular productos antes de eliminar
        pg_query_params($conn, "UPDATE productos SET id_categoria = NULL WHERE id_categoria = $1", [$id]);
    }
    
    $result = pg_query_params($conn, "DELETE FROM categorias WHERE id_categoria = $1", [$id]);
    res(!!$result, ['message' => $result ? 'Categoría eliminada correctamente' : 'Error al eliminar']);
}

res(false, ['message' => 'Acción no válida']);