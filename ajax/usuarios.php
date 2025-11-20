<?php
require '../conexion.php';
header('Content-Type: application/json; charset=utf-8');
$action = $_GET['action'] ?? $_POST['action'] ?? '';

function res($ok,$data=[]){echo json_encode(array_merge(['success'=>$ok],$data));exit();}

if($action==='list'){
  $result = pg_query($conn, "SELECT id_usuario, nombre, correo, rol FROM usuarios ORDER BY id_usuario DESC");
  $users = [];
  while($u = pg_fetch_assoc($result)) $users[] = $u;
  res(true, ['users'=>$users]);
}

if($action==='create'){
  $n = pg_escape($conn, $_POST['nombre']);
  $c = pg_escape($conn, $_POST['correo']);
  $p = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
  $rol = pg_escape($conn, $_POST['rol'] ?? 'empleado');
  
  $query = "INSERT INTO usuarios(nombre, correo, contraseña, rol) VALUES ($1, $2, $3, $4)";
  $result = pg_query_params($conn, $query, [$n, $c, $p, $rol]);
  res(!!$result, ['message'=>'Usuario creado']);
}

if($action==='update'){
  $id = (int)$_POST['id_usuario'];
  $n = pg_escape($conn, $_POST['nombre']);
  $c = pg_escape($conn, $_POST['correo']);
  $rol = pg_escape($conn, $_POST['rol']);
  
  if(!empty($_POST['contraseña'])){
    $p = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
    $query = "UPDATE usuarios SET nombre=$1, correo=$2, contraseña=$3, rol=$4 WHERE id_usuario=$5";
    $result = pg_query_params($conn, $query, [$n, $c, $p, $rol, $id]);
  } else {
    $query = "UPDATE usuarios SET nombre=$1, correo=$2, rol=$3 WHERE id_usuario=$4";
    $result = pg_query_params($conn, $query, [$n, $c, $rol, $id]);
  }
  res(!!$result, ['message'=>'Usuario actualizado']);
}

if($action==='delete'){
  $id = (int)$_POST['id_usuario'];
  $result = pg_query($conn, "DELETE FROM usuarios WHERE id_usuario=$id");
  res(!!$result, ['message'=>'Usuario eliminado']);
}

res(false, ['message'=>'Acción inválida']);