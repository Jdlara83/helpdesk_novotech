<?php

require_once __DIR__ . '/../config/Database.php';

class Usuario
{
    protected ?PDO $conn = null;
    protected string $table = "usuarios";

    public function __construct()
    {
        if (class_exists('Database')) {
            $this->conn = Database::getConnection();
        }
    }

    public function login(string $email, string $password)
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT * FROM {$this->table} WHERE TRIM(LOWER(mail)) = LOWER(:email) AND estado = 1";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':email' => trim($email)]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Validar contraseña
                $passwordValida = password_verify($password, $user['password']) || ($password === $user['password']);

                if ($passwordValida) {
                    // Capturar el perfil
                    $perfilReal = $user['id_perfil'] ?? $user['id_perfiles'] ?? $user['id_rol'] ?? $user['perfil'] ?? 2;

                    // Asegurar que se guarde como número entero (int)
                    $user['id_perfil'] = intval($perfilReal);
                    return $user;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            return false;
        }
    }

    public function autenticar(string $email, string $password)
    {
        return $this->login($email, $password);
    }
    // Obtener todos los usuarios con la descripción de su perfil
    public function listarTodos(): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$usuarios) {
                return [];
            }

            // Mapeo numérico de perfiles a nombres para consistencia
            $perfilesNombres = [
                1 => 'ADMINISTRADOR',
                2 => 'USUARIO',
                3 => 'SOPORTE',
                4 => 'REQUERIMIENTO'
            ];

            foreach ($usuarios as &$u) {

                $valPerfil = $u['perfil'] ?? $u['id_perfil'] ?? $u['id_perfiles'] ?? 'USUARIO';
                if (is_numeric($valPerfil)) {
                    $u['nombre_perfil'] = $perfilesNombres[intval($valPerfil)] ?? 'USUARIO';
                } else {
                    $u['nombre_perfil'] = strtoupper(trim($valPerfil));
                }
            }

            return $usuarios;
        } catch (PDOException $e) {
            error_log("Error al listar usuarios: " . $e->getMessage());
            return [];
        }
    }
    // Crear un nuevo usuario en la BD
    public function crear(array $datos): bool
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }
        $nombre    = trim($datos['nombre'] ?? '');
        $documento = trim($datos['documento'] ?? '');
        $mail      = trim($datos['mail'] ?? '');
        $telefono  = trim($datos['telefono'] ?? '');
        $usuario   = trim($datos['usuario'] ?? '');
        $perfil    = intval($datos['perfil'] ?? $datos['id_perfil'] ?? 2);
        $estado    = intval($datos['estado'] ?? 1);
        $password  = trim($datos['password'] ?? '');

        $claveFinal = !empty($password) ? $password : 'Admin123';
        $hash       = password_hash($claveFinal, PASSWORD_BCRYPT);

        if (empty($usuario) && !empty($mail)) {
            $usuario = explode('@', $mail)[0];
        }

        $sql = "INSERT INTO {$this->table} (nombre, documento, mail, telefono, usuario, perfil, estado, password) 
                VALUES (:nombre, :documento, :mail, :telefono, :usuario, :perfil, :estado, :password)";

        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':nombre'    => $nombre,
                ':documento' => $documento,
                ':mail'      => $mail,
                ':telefono'  => $telefono,
                ':usuario'   => $usuario,
                ':perfil'    => $perfil,
                ':estado'    => $estado,
                ':password'  => $hash
            ]);
        } catch (PDOException $e) {
            error_log("Error PDO al crear usuario: " . $e->getMessage());
            return false;
        }
    }
    // Buscar un usuario por ID
    public function obtenerPorId(int $id)
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "SELECT id, nombre, documento, mail, telefono, perfil, estado FROM {$this->table} WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }
    // Cambiar estado del usuario (1 = Activo, 0 = Inactivo)
    public function cambiarEstado(int $id, int $nuevoEstado): bool
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        $sql = "UPDATE {$this->table} SET estado = :estado WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':estado' => $nuevoEstado,
                ':id'     => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error PDO al cambiar estado: " . $e->getMessage());
            return false;
        }
    }
    // Obtener la lista de perfiles
    public function obtenerRolesBD(): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }
        $sql = "SELECT id_perfil AS id, nombre_perfil AS nombre FROM par_perfiles ORDER BY nombre_perfil ASC;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error PDO al obtener roles: " . $e->getMessage());
            return [];
        }
    }
    // Actualizar datos de un usuario existente
    public function actualizar(int $id, string $nombre, string $documento, string $correo, string $telefono, int $idPerfil, int $estado, string $password = ''): bool
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }

        // Si se envió una nueva contraseña, la actualizamos; de lo contrario, conservamos la actual
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE {$this->table} 
                    SET nombre = :nombre, 
                        documento = :documento, 
                        mail = :correo, 
                        telefono = :telefono, 
                        perfil = :id_perfil, 
                        estado = :estado, 
                        password = :password 
                    WHERE id = :id";
            $params = [
                ':nombre'    => $nombre,
                ':documento' => $documento,
                ':correo'    => $correo,
                ':telefono'  => $telefono,
                ':id_perfil' => $idPerfil,
                ':estado'    => $estado,
                ':password'  => $hash,
                ':id'        => $id
            ];
        } else {
            $sql = "UPDATE {$this->table} 
                    SET nombre = :nombre, 
                        documento = :documento, 
                        mail = :correo, 
                        telefono = :telefono, 
                        perfil = :id_perfil, 
                        estado = :estado 
                    WHERE id = :id";
            $params = [
                ':nombre'    => $nombre,
                ':documento' => $documento,
                ':correo'    => $correo,
                ':telefono'  => $telefono,
                ':id_perfil' => $idPerfil,
                ':estado'    => $estado,
                ':id'        => $id
            ];
        }

        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error PDO al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
}
