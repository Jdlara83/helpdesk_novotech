<?php
require_once __DIR__ . '/../config/database.php';

class Responsable
{
    private $conn;
    private $table = 'usuarios_responsables_proyectos';

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listar()
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT 
                r.id_responsable_proyecto AS id,
                CASE 
                    WHEN r.id_categoria_soporte = 1 THEN 'SOFTWARE'
                    WHEN r.id_categoria_soporte = 2 THEN 'INFRAESTRUCTURA'
                    WHEN r.id_categoria_soporte = 3 THEN 'REQUERIMIENTO'
                    ELSE CONCAT('Categoría ', r.id_categoria_soporte)
                END AS categoria,
                COALESCE(p.proyecto, CONCAT('Proyecto ', r.id_proyecto_soporte)) AS proyecto,
                COALESCE(u.nombre, 'Sin Asignar') AS nombre_usuario,
                u.mail,
                u.usuario
            FROM {$this->table} r
            LEFT JOIN par_proyecto_soporte p ON r.id_proyecto_soporte = p.id_par_proyecto
            LEFT JOIN usuarios u ON r.id_usuario = u.id
            ORDER BY r.id_responsable_proyecto DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar responsables: " . $e->getMessage());
            return [];
        }
    }

    public function crear(int $idCategoria, int $idProyecto, int $idUsuario): bool
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "INSERT INTO {$this->table} (id_categoria_soporte, id_proyecto_soporte, id_usuario) 
                VALUES (:categoria, :proyecto, :usuario)";

        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':categoria' => $idCategoria,
                ':proyecto'  => $idProyecto,
                ':usuario'   => $idUsuario
            ]);
        } catch (PDOException $e) {
            error_log("Error al asignar responsable: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id): bool
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "DELETE FROM {$this->table} WHERE id_responsable_proyecto = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar responsable: " . $e->getMessage());
            return false;
        }
    }
}
