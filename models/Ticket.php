<?php

class Ticket
{
    private PDO $conn;
    private string $table = "tickets";

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function obtenerTiposSoportePorPerfil(int $id_perfil): array
    {
        if ($id_perfil === 4) {
            $sql = "SELECT id_tipo_soporte AS id, tipo_soporte AS nombre 
                    FROM par_tipo_soporte 
                    WHERE id_tipo_soporte = 3 AND estado = 1";
        } else {
            $sql = "SELECT id_tipo_soporte AS id, tipo_soporte AS nombre 
                    FROM par_tipo_soporte 
                    WHERE id_tipo_soporte IN (1, 2) AND estado = 1";
        }

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                ['id' => 1, 'nombre' => 'SOFTWARE'],
                ['id' => 2, 'nombre' => 'INFRAESTRUCTURA']
            ];
        }
    }

    public function crearCompleto(
        int $id_categoria,
        int $usuario_id,
        string $usuario_login,
        string $asunto,
        string $detalle,
        string $ubicacion,
        string $prioridad,
        string $imagenes,
        string $archivos
    ): array {
        $sql = "INSERT INTO {$this->table} (
                    id_categoria_soporte,
                    id_proyecto_soporte,
                    id_usuario_ticket,
                    asunto_ticket,
                    detalle_ticket,
                    detalle_ubicacion,
                    ruta_imagenes,
                    ruta_archivos,
                    prioridad,
                    fecha_crea,
                    usuario_crea,
                    fecha_ini_ticket,
                    estado,
                    estado_soporte
                ) VALUES (
                    :id_cat,
                    1,
                    :id_usuario,
                    :asunto,
                    :detalle,
                    :ubicacion,
                    :imagenes,
                    :archivos,
                    :prioridad,
                    NOW(),
                    :usuario_crea,
                    NOW(),
                    'CREADO',
                    'PENDIENTE'
                )";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_cat'       => $id_categoria,
                ':id_usuario'   => $usuario_id,
                ':asunto'       => $asunto,
                ':detalle'      => $detalle,
                ':ubicacion'    => $ubicacion,
                ':imagenes'     => $imagenes,
                ':archivos'     => $archivos,
                ':prioridad'    => $prioridad,
                ':usuario_crea' => $usuario_login
            ]);
            return ['status' => true];
        } catch (PDOException $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    public function listarPorUsuario(int $idUsuario): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT 
                    t.id_ticket AS id,
                    t.asunto_ticket AS asunto,
                    COALESCE(u.nombre, u.usuario, 'Sin Nombre') AS solicitante,
                    COALESCE(t.estado, t.estado_soporte, 'CREADO') AS estado,
                    COALESCE(t.fecha_crea, t.fecha_ini_ticket, NOW()) AS fecha_creacion
                FROM {$this->table} t
                LEFT JOIN usuarios u ON t.id_usuario_ticket = u.id
                WHERE t.id_usuario_ticket = :id_usuario
                ORDER BY t.id_ticket DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar mis tickets: " . $e->getMessage());
            return [];
        }
    }
    // Método 2: Para la Bolsa General de Soporte (Todos los tickets)
    public function listar()
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT 
                    t.id_ticket AS id,
                    t.asunto_ticket AS asunto,
                    COALESCE(u.nombre, 'Sin Nombre') AS solicitante,
                    t.estado_ticket AS estado,
                    t.fecha_creacion_ticket AS fecha_creacion
                FROM {$this->table} t
                LEFT JOIN usuarios u ON t.id_usuario_ticket = u.id
                ORDER BY t.id_ticket DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar todos los tickets: " . $e->getMessage());
            return [];
        }
    }
    // Obtener todos los tickets para la Bolsa de Trabajo (Vista Administrador/Soporte)
    public function listarBolsa(): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT
                    t.id_ticket AS id,
                    COALESCE(cat.tipo_soporte, 'SIN CATEGORÍA') AS categoria,
                    COALESCE(proy.proyecto, 'GENERAL') AS proyecto,
                    t.asunto_ticket AS asunto,
                    t.prioridad AS prioridad,
                    COALESCE(u_sol.nombre, u_sol.usuario, 'Sin Solicitar') AS solicitante,
                    COALESCE(u_resp.nombre, u_resp.usuario, 'Sin Asignar') AS responsable,
                    COALESCE(t.fecha_crea, t.fecha_ini_ticket, NOW()) AS fecha_creacion,
                    COALESCE(t.estado, t.estado_soporte, 'CREADO') AS estado
                FROM {$this->table} t
                LEFT JOIN par_tipo_soporte cat ON t.id_categoria_soporte = cat.id_tipo_soporte
                LEFT JOIN par_proyecto_soporte proy ON t.id_proyecto_soporte = proy.id_par_proyecto
                LEFT JOIN usuarios u_sol ON t.id_usuario_ticket = u_sol.id
                LEFT JOIN usuarios u_resp ON t.id_usuario_responsable = u_resp.id
                ORDER BY t.id_ticket DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar bolsa de tickets: " . $e->getMessage());
            return [];
        }
    }

    // Asignar responsable (id_usuario_responsable) y actualizar estados
    public function asignarResponsable(int $idTicket, int $idResponsable, string $nuevoEstado = 'EN PROCESO'): bool
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "UPDATE tickets 
                SET id_usuario_responsable = :id_resp, 
                    estado = :estado, 
                    estado_soporte = 'PENDIENTE',
                    fecha_gestion_soporte = NOW()
                WHERE id_ticket = :id_ticket";

        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':id_resp'   => $idResponsable,
                ':estado'    => $nuevoEstado,
                ':id_ticket' => $idTicket
            ]);
        } catch (PDOException $e) {
            error_log("Error al asignar responsable en ticket: " . $e->getMessage());
            return false;
        }
    }
    // Obtener tickets filtrados por el estado de soporte exacto ('PENDIENTE' o 'REALIZADO')
    public function listarPorEstadoSoporte(int $idResponsable, string $estadoSoporte): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT 
                    t.id_ticket AS id,
                    COALESCE(cat.tipo_soporte, 'SIN CATEGORÍA') AS categoria,
                    COALESCE(proy.proyecto, 'GENERAL') AS proyecto,
                    t.asunto_ticket AS asunto,
                    t.prioridad AS prioridad,
                    COALESCE(u_sol.nombre, u_sol.usuario, 'Sin Solicitar') AS solicitante,
                    COALESCE(u_resp.nombre, u_resp.usuario, 'Sin Asignar') AS responsable,
                    COALESCE(t.fecha_crea, t.fecha_ini_ticket, NOW()) AS fecha_creacion,
                    t.fecha_gestion_soporte AS fecha_gestion,
                    COALESCE(t.estado_soporte, 'PENDIENTE') AS estado
                FROM tickets t
                LEFT JOIN par_tipo_soporte cat ON t.id_categoria_soporte = cat.id_tipo_soporte
                LEFT JOIN par_proyecto_soporte proy ON t.id_proyecto_soporte = proy.id_par_proyecto
                LEFT JOIN usuarios u_sol ON t.id_usuario_ticket = u_sol.id
                LEFT JOIN usuarios u_resp ON t.id_usuario_responsable = u_resp.id
                WHERE t.id_usuario_responsable = :id_resp 
                  AND t.estado_soporte = :estado_soporte
                ORDER BY t.id_ticket DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_resp'        => $idResponsable,
                ':estado_soporte' => $estadoSoporte
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar tickets por estado de soporte: " . $e->getMessage());
            return [];
        }
    }
    // Obtener SOLO agentes de soporte, requerimientos y admin para el modal
    public function obtenerUsuariosSoporte(): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        // Filtramos por perfil: 1 (Admin), 3 (Soporte), 4 (Requerimiento)
        $sql = "SELECT id, COALESCE(NULLIF(TRIM(nombre), ''), usuario) AS nombre, usuario 
                FROM usuarios 
                WHERE estado = 1 
                  AND (perfil IN ('1', '3', '4') OR CAST(perfil AS UNSIGNED) IN (1, 3, 4))
                ORDER BY nombre ASC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios de soporte: " . $e->getMessage());
            return [];
        }
    }
    // Obtener detalle completo de un ticket para la vista de consulta
    public function obtenerDetalleCompleto(int $idTicket)
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT 
                    t.id_ticket,
                    t.asunto_ticket,
                    t.detalle_ticket,
                    t.detalle_ubicacion,
                    t.prioridad,
                    t.estado,
                    t.ruta_imagenes,
                    t.ruta_archivos,
                    t.fecha_crea,
                    COALESCE(cat.tipo_soporte, 'SIN CATEGORÍA') AS categoria,
                    COALESCE(proy.proyecto, 'GENERAL') AS proyecto,
                    COALESCE(u_sol.nombre, u_sol.usuario, 'N/A') AS solicitante
                FROM tickets t
                LEFT JOIN par_tipo_soporte cat ON t.id_categoria_soporte = cat.id_tipo_soporte
                LEFT JOIN par_proyecto_soporte proy ON t.id_proyecto_soporte = proy.id_par_proyecto
                LEFT JOIN usuarios u_sol ON t.id_usuario_ticket = u_sol.id
                WHERE t.id_ticket = :id_ticket";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_ticket' => $idTicket]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener detalle completo: " . $e->getMessage());
            return false;
        }
    }
}
