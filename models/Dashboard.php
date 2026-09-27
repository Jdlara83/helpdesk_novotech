<?php

class Dashboard
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function obtenerEstadisticasPorPerfil(int $id_perfil, int $usuario_id): array
    {
        // Para el ADMINISTRADOR (id_perfil = 1) agrupamos globalmente por tipo de soporte
        if ($id_perfil === 1) {
            $sql = "SELECT 
                        s.tipo_soporte AS categoria, 
                        COUNT(t.id_ticket) AS total 
                    FROM tickets t
                    INNER JOIN par_tipo_soporte s ON t.id_categoria_soporte = s.id_tipo_soporte
                    GROUP BY s.tipo_soporte";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        // Para USUARIO (id_perfil = 2), se filtran únicamente sus tickets
        else {
            $sql = "SELECT 
                        s.tipo_soporte AS categoria, 
                        COUNT(t.id_ticket) AS total 
                    FROM tickets t
                    INNER JOIN par_tipo_soporte s ON t.id_categoria_soporte = s.id_tipo_soporte
                    WHERE t.id_usuario_ticket = :usuario_id
                    GROUP BY s.tipo_soporte";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
