<?php
include("conexion.php");
session_start();

// Validar que los datos existan
if (!isset($_POST['correo']) || !isset($_POST['contraseña'])) {
    header("Location: login.php?error=Datos incompletos");
    exit();
}

$correo = trim($_POST['correo']);
$contraseña = $_POST['contraseña'];

// Validar que no estén vacíos
if (empty($correo) || empty($contraseña)) {
    header("Location: login.php?error=Por favor complete todos los campos");
    exit();
}

// Buscar usuario por correo - NOTA: la columna en BD es 'contrasena' sin ñ
$query = "SELECT id_usuario, nombre, correo, contrasena, rol FROM usuarios WHERE correo = $1";
$result = pg_query_params($conn, $query, [$correo]);

if (!$result) {
    error_log("Error en query: " . pg_last_error($conn));
    header("Location: login.php?error=Error en el servidor");
    exit();
}

if (pg_num_rows($result) > 0) {
    $usuario = pg_fetch_assoc($result);
    
    // Verificar que la contraseña exista en la BD
    if (empty($usuario['contrasena'])) {
        header("Location: login.php?error=Error en la configuración del usuario");
        exit();
    }
    
    // Verificar la contraseña
    if (password_verify($contraseña, $usuario['contrasena'])) {
        // Login exitoso
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['rol'] = $usuario['rol'];
        
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
        
        header("Location: panel.php");
        exit();
    } else {
        header("Location: login.php?error=Contraseña incorrecta");
        exit();
    }
} else {
    header("Location: login.php?error=Usuario no encontrado");
    exit();
}

pg_close($conn);
?>