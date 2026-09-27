<?php
require_once __DIR__ . '/../config/database.php';

class Requerimiento
{
    private PDO $conn;
    private string $table = "requerimientos";

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // Listar solo los requerimientos creados por el usuario en sesión
    public function listarPorUsuario(int $idUsuario): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT 
                    r.id_requerimiento AS id,
                    r.asunto_requerimiento AS asunto,
                    COALESCE(u.nombre, u.usuario, 'Sin Nombre') AS solicitante,
                    COALESCE(r.estado_requerimiento, 'PENDIENTE') AS estado,
                    COALESCE(r.fecha_crea, NOW()) AS fecha_creacion
                FROM {$this->table} r
                LEFT JOIN usuarios u ON r.id_usuario_requerimiento = u.id
                WHERE r.id_usuario_requerimiento = :id_usuario
                ORDER BY r.id_requerimiento DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar mis requerimientos: " . $e->getMessage());
            return [];
        }
    }

    // Insertar nuevo requerimiento
    public function crear(
        int $idCategoria,
        int $idProyecto,
        int $idUsuario,
        string $asunto,
        string $especificacion,
        string $restriccion,
        string $usuarioCrea,
        string $imagenes = '',
        string $archivos = ''
    ): bool {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "INSERT INTO {$this->table} (
                    id_categoria_requerimiento,
                    id_proyecto_requerimiento,
                    id_usuario_requerimiento,
                    asunto_requerimiento,
                    especificacion_requerimiento,
                    restriccion_requerimiento,
                    ruta_imagenes,
                    ruta_archivos,
                    usuario_crea,
                    fecha_crea,
                    fecha_ini_requerimiento,
                    estado_requerimiento,
                    estado_requerimiento_soporte
                ) VALUES (
                    :id_cat,
                    :id_proy,
                    :id_usu,
                    :asunto,
                    :espec,
                    :restr,
                    :img,
                    :arc,
                    :usu_crea,
                    NOW(),
                    NOW(),
                    'PENDIENTE',
                    'PENDIENTE'
                )";

        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':id_cat'    => $idCategoria,
                ':id_proy'   => $idProyecto,
                ':id_usu'    => $idUsuario,
                ':asunto'    => $asunto,
                ':espec'     => $especificacion,
                ':restr'     => $restriccion,
                ':img'       => $imagenes,
                ':arc'       => $archivos,
                ':usu_crea'  => $usuarioCrea
            ]);
        } catch (PDOException $e) {
            error_log("Error al crear requerimiento: " . $e->getMessage());
            return false;
        }
    }
    public function listarBolsa(): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT
                    r.id_requerimiento AS id,
                    COALESCE(cat.tipo_soporte, 'REQUERIMIENTO') AS categoria,
                    COALESCE(proy.proyecto, 'GENERAL') AS proyecto,
                    r.asunto_requerimiento AS asunto,
                    'MEDIA' AS prioridad,
                    COALESCE(u_sol.nombre, u_sol.usuario, 'Sin Solicitar') AS solicitante,
                    COALESCE(u_resp.nombre, u_resp.usuario, 'Sin Asignar') AS responsable,
                    COALESCE(r.fecha_crea, r.fecha_ini_requerimiento, NOW()) AS fecha_creacion,
                    COALESCE(r.estado_requerimiento, 'PENDIENTE') AS estado
                FROM {$this->table} r
                LEFT JOIN par_tipo_soporte cat ON r.id_categoria_requerimiento = cat.id_tipo_soporte
                LEFT JOIN par_proyecto_soporte proy ON r.id_proyecto_requerimiento = proy.id_par_proyecto
                LEFT JOIN usuarios u_sol ON r.id_usuario_requerimiento = u_sol.id
                LEFT JOIN usuarios u_resp ON r.id_usuario_responsable = u_resp.id
                ORDER BY r.id_requerimiento DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar bolsa de requerimientos: " . $e->getMessage());
            return [];
        }
    }
}