<?php
/**
 * Clase para gestionar el sistema de permisos granular
 */
class Permisos {
    private $conn;
    private $id_usuario;
    private $permisos_cache = null;
    
    public function __construct($conn, $id_usuario = null) {
        $this->conn = $conn;
        $this->id_usuario = $id_usuario ?? $_SESSION['id_usuario'] ?? null;
    }
    
    /**
     * Verifica si el usuario tiene un permiso específico
     */
    public function tiene($clave_permiso) {
        if (!$this->id_usuario) return false;
        
        // Super admin siempre tiene todos los permisos
        if ($this->esSuperAdmin()) return true;
        
        // Usar caché si está disponible
        if ($this->permisos_cache === null) {
            $this->cargarPermisos();
        }
        
        return isset($this->permisos_cache[$clave_permiso]) && 
               $this->permisos_cache[$clave_permiso] === true;
    }
    
    /**
     * Verifica si tiene alguno de los permisos listados
     */
    public function tieneAlguno(array $claves_permisos) {
        foreach ($claves_permisos as $clave) {
            if ($this->tiene($clave)) return true;
        }
        return false;
    }
    
    /**
     * Verifica si tiene todos los permisos listados
     */
    public function tieneTodos(array $claves_permisos) {
        foreach ($claves_permisos as $clave) {
            if (!$this->tiene($clave)) return false;
        }
        return true;
    }
    
    /**
     * Carga todos los permisos del usuario en caché
     */
    private function cargarPermisos() {
        $this->permisos_cache = [];
        
        $query = "SELECT * FROM obtener_permisos_usuario($1)";
        $result = pg_query_params($this->conn, $query, [$this->id_usuario]);
        
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $this->permisos_cache[$row['clave']] = $row['tiene_permiso'] === 't';
            }
        }
    }
    
    /**
     * Obtiene todos los permisos del usuario
     */
    public function obtenerTodos() {
        if ($this->permisos_cache === null) {
            $this->cargarPermisos();
        }
        return $this->permisos_cache;
    }
    
    /**
     * Verifica si el usuario es super administrador
     */
    public function esSuperAdmin() {
        $query = "SELECT r.nivel_jerarquia 
                  FROM usuarios u
                  INNER JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.id_usuario = $1";
        $result = pg_query_params($this->conn, $query, [$this->id_usuario]);
        
        if ($result && $row = pg_fetch_assoc($result)) {
            return (int)$row['nivel_jerarquia'] === 1;
        }
        return false;
    }
    
    /**
     * Obtiene los módulos accesibles para el usuario
     */
    public function obtenerModulosAccesibles() {
        $query = "SELECT DISTINCT m.*
                  FROM modulos m
                  INNER JOIN permisos p ON p.id_modulo = m.id_modulo
                  INNER JOIN roles_permisos rp ON rp.id_permiso = p.id_permiso
                  INNER JOIN usuarios u ON u.id_rol = rp.id_rol
                  WHERE u.id_usuario = $1 
                    AND rp.otorgado = TRUE
                    AND m.activo = TRUE
                  ORDER BY m.orden";
        
        $result = pg_query_params($this->conn, $query, [$this->id_usuario]);
        $modulos = [];
        
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $modulos[] = $row;
            }
        }
        
        return $modulos;
    }
    
    /**
     * Otorga un permiso a un usuario
     */
    public function otorgarPermiso($id_usuario_destino, $clave_permiso, $motivo = '') {
        // Verificar que tenga permiso para gestionar permisos
        if (!$this->tiene('usuarios.gestionar_permisos')) {
            return ['success' => false, 'message' => 'No tienes permiso para gestionar permisos'];
        }
        
        // Obtener ID del permiso
        $query_permiso = "SELECT id_permiso FROM permisos WHERE clave = $1";
        $result = pg_query_params($this->conn, $query_permiso, [$clave_permiso]);
        
        if (!$result || pg_num_rows($result) === 0) {
            return ['success' => false, 'message' => 'Permiso no encontrado'];
        }
        
        $permiso = pg_fetch_assoc($result);
        $id_permiso = $permiso['id_permiso'];
        
        // Insertar o actualizar permiso
        $query = "INSERT INTO usuarios_permisos (id_usuario, id_permiso, otorgado, asignado_por, motivo)
                  VALUES ($1, $2, TRUE, $3, $4)
                  ON CONFLICT (id_usuario, id_permiso) 
                  DO UPDATE SET otorgado = TRUE, asignado_por = $3, motivo = $4, fecha_asignacion = CURRENT_TIMESTAMP";
        
        $result = pg_query_params($this->conn, $query, [
            $id_usuario_destino, 
            $id_permiso, 
            $this->id_usuario, 
            $motivo
        ]);
        
        if ($result) {
            $this->registrarLog($id_usuario_destino, $id_permiso, null, 'otorgar', 'usuario', $motivo);
            return ['success' => true, 'message' => 'Permiso otorgado correctamente'];
        }
        
        return ['success' => false, 'message' => 'Error al otorgar permiso'];
    }
    
    /**
     * Revoca un permiso de un usuario
     */
    public function revocarPermiso($id_usuario_destino, $clave_permiso, $motivo = '') {
        if (!$this->tiene('usuarios.gestionar_permisos')) {
            return ['success' => false, 'message' => 'No tienes permiso para gestionar permisos'];
        }
        
        $query_permiso = "SELECT id_permiso FROM permisos WHERE clave = $1";
        $result = pg_query_params($this->conn, $query_permiso, [$clave_permiso]);
        
        if (!$result || pg_num_rows($result) === 0) {
            return ['success' => false, 'message' => 'Permiso no encontrado'];
        }
        
        $permiso = pg_fetch_assoc($result);
        $id_permiso = $permiso['id_permiso'];
        
        $query = "INSERT INTO usuarios_permisos (id_usuario, id_permiso, otorgado, asignado_por, motivo)
                  VALUES ($1, $2, FALSE, $3, $4)
                  ON CONFLICT (id_usuario, id_permiso) 
                  DO UPDATE SET otorgado = FALSE, asignado_por = $3, motivo = $4, fecha_asignacion = CURRENT_TIMESTAMP";
        
        $result = pg_query_params($this->conn, $query, [
            $id_usuario_destino, 
            $id_permiso, 
            $this->id_usuario, 
            $motivo
        ]);
        
        if ($result) {
            $this->registrarLog($id_usuario_destino, $id_permiso, null, 'revocar', 'usuario', $motivo);
            return ['success' => true, 'message' => 'Permiso revocado correctamente'];
        }
        
        return ['success' => false, 'message' => 'Error al revocar permiso'];
    }
    
    /**
     * Cambia el rol de un usuario
     */
    public function cambiarRol($id_usuario_destino, $id_rol_nuevo, $motivo = '') {
        if (!$this->tiene('usuarios.gestionar_permisos')) {
            return ['success' => false, 'message' => 'No tienes permiso para cambiar roles'];
        }
        
        // Obtener rol anterior
        $query_anterior = "SELECT id_rol FROM usuarios WHERE id_usuario = $1";
        $result = pg_query_params($this->conn, $query_anterior, [$id_usuario_destino]);
        $rol_anterior = pg_fetch_assoc($result)['id_rol'];
        
        // Actualizar rol
        $query = "UPDATE usuarios SET id_rol = $1 WHERE id_usuario = $2";
        $result = pg_query_params($this->conn, $query, [$id_rol_nuevo, $id_usuario_destino]);
        
        if ($result) {
            $this->registrarLog($id_usuario_destino, null, $id_rol_nuevo, 'modificar', 'rol', $motivo);
            return ['success' => true, 'message' => 'Rol actualizado correctamente'];
        }
        
        return ['success' => false, 'message' => 'Error al actualizar rol'];
    }
    
    /**
     * Registra cambios en el log de auditoría
     */
    private function registrarLog($id_usuario_afectado, $id_permiso, $id_rol, $accion, $tipo, $motivo) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $query = "INSERT INTO log_permisos 
                  (id_usuario_afectado, id_usuario_ejecutor, id_permiso, id_rol, accion, tipo, motivo, ip_address)
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
        
        pg_query_params($this->conn, $query, [
            $id_usuario_afectado,
            $this->id_usuario,
            $id_permiso,
            $id_rol,
            $accion,
            $tipo,
            $motivo,
            $ip
        ]);
    }
    
    /**
     * Obtiene el historial de cambios de permisos
     */
    public function obtenerHistorial($id_usuario = null, $limite = 50) {
        $where = $id_usuario ? "WHERE l.id_usuario_afectado = $1" : "";
        $params = $id_usuario ? [$id_usuario] : [];
        
        $query = "SELECT l.*, 
                         ua.nombre as usuario_afectado,
                         ue.nombre as usuario_ejecutor,
                         p.nombre as permiso,
                         r.nombre as rol
                  FROM log_permisos l
                  LEFT JOIN usuarios ua ON l.id_usuario_afectado = ua.id_usuario
                  LEFT JOIN usuarios ue ON l.id_usuario_ejecutor = ue.id_usuario
                  LEFT JOIN permisos p ON l.id_permiso = p.id_permiso
                  LEFT JOIN roles r ON l.id_rol = r.id_rol
                  $where
                  ORDER BY l.fecha_cambio DESC
                  LIMIT $limite";
        
        if ($id_usuario) {
            $result = pg_query_params($this->conn, $query . " OFFSET $2", array_merge($params, [$limite]));
        } else {
            $result = pg_query($this->conn, $query);
        }
        
        $historial = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $historial[] = $row;
            }
        }
        
        return $historial;
    }
    
    /**
     * Limpiar caché de permisos
     */
    public function limpiarCache() {
        $this->permisos_cache = null;
    }
}

/**
 * Función helper global para verificar permisos
 */
function puede($clave_permiso) {
    global $conn;
    static $permisos_instance = null;
    
    if ($permisos_instance === null) {
        $permisos_instance = new Permisos($conn);
    }
    
    return $permisos_instance->tiene($clave_permiso);
}

/**
 * Middleware para proteger rutas
 */
function requiere_permiso($clave_permiso, $redirect = 'panel.php') {
    if (!puede($clave_permiso)) {
        $_SESSION['error_permiso'] = 'No tienes permisos para acceder a esta sección';
        header("Location: $redirect");
        exit();
    }
}
?>