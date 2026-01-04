<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contraseña = $_POST['contraseña'];
    $confirmar = $_POST['confirmar'];

    // ======== VALIDACIONES =========
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        header("Location: registro.php?error=El nombre solo puede contener letras y espacios");
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strpos($correo, '..') !== false) {
        header("Location: registro.php?error=El correo electrónico no es válido");
        exit();
    }

    if ($contraseña !== $confirmar) {
        header("Location: registro.php?error=Las contraseñas no coinciden");
        exit();
    }

    if (strlen($contraseña) < 8 || 
        !preg_match('/[A-Z]/', $contraseña) || 
        !preg_match('/[\W_]/', $contraseña)) {
        header("Location: registro.php?error=La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un carácter especial");
        exit();
    }

    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

    // ======== VERIFICAR SI EL CORREO YA EXISTE ========
    $verificar_query = "SELECT * FROM usuarios WHERE correo = $1";
    $verificar = pg_query_params($conn, $verificar_query, [$correo]);

    if (pg_num_rows($verificar) > 0) {
        header("Location: registro.php?error=El correo ya está registrado");
        exit();
    }

    // ======== INSERTAR USUARIO ========
    // NOTA: la columna en BD es 'contrasena' sin ñ
    $insert_query = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $insert_query, [$nombre, $correo, $contraseña_hash]);

    if ($result) {
        header("Location: login.php?mensaje=Registro exitoso, ahora puedes iniciar sesión");
    } else {
        header("Location: registro.php?error=Error al registrar usuario");
    }

    pg_close($conn);
} else {
    header("Location: registro.php");
    exit();
}
?>