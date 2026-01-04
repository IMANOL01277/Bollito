<?php
include("conexion.php");
session_start();

$correo = pg_escape($conn, $_POST['correo']);
$contrasena = $_POST['contrasena'];

$query = "SELECT * FROM usuarios WHERE correo = $1";
$result = pg_query_params($conn, $query, [$correo]);

if (pg_num_rows($result) > 0) {
    $usuario = pg_fetch_assoc($result);

    if (password_verify($contrasena, $usuario['contrasena'])) {
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['rol'] = $usuario['rol'];
        header("Location: panel.php");
    } else {
        header("Location: login.php?error=Contraseña incorrecta");
    }
} else {
    header("Location: login.php?error=Usuario no encontrado");
}

pg_close($conn);

?>
