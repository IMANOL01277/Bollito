<?php
// Conexión a PostgreSQL
$host = getenv('DB_HOST') ?: 'aws-1-us-east-1.pooler.supabase.com';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'postgres';
$user = getenv('DB_USER') ?: 'postgres.jpbshyiucotaeznpgaib';
$password = getenv('DB_PASSWORD') ?: '10654823bollito';

try {
    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    
    if (!$conn) {
        die("Error de conexión: " . pg_last_error());
    }
    
    // Establecer charset UTF-8
    pg_set_client_encoding($conn, 'UTF8');
    
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Función helper para ejecutar queries preparadas
function pg_execute_prepared($conn, $query, $params = []) {
    $placeholders = [];
    for ($i = 1; $i <= count($params); $i++) {
        $placeholders[] = '$' . $i;
    }
    
    $query = str_replace(array_fill(0, count($params), '?'), $placeholders, $query);
    $result = pg_query_params($conn, $query, $params);
    
    if (!$result) {
        error_log("PostgreSQL Error: " . pg_last_error($conn));
        return false;
    }
    
    return $result;
}

// Función para obtener el último ID insertado
function pg_last_insert_id($conn, $table, $id_column) {
    $result = pg_query($conn, "SELECT currval(pg_get_serial_sequence('$table', '$id_column')) as id");
    if ($result) {
        $row = pg_fetch_assoc($result);
        return $row['id'];
    }
    return null;
}

// Función para escapar strings
function pg_escape($conn, $string) {
    return pg_escape_string($conn, $string);
}
?>