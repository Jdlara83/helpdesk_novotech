<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController
{

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Control de Acceso: Solo Administrador (id_perfil = 1)
        $id_perfil = $_SESSION['id_perfil'] ?? 2;
        if ($id_perfil !== 1) {
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/navbar.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/usuarios/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
    // Endpoint en formato JSON para DataTables
    public function listar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpiar cualquier HTML o texto previo cargado accidentalmente
        if (ob_get_length()) ob_clean();

        header('Content-Type: application/json; charset=utf-8');

        $usuarioModel = new Usuario();
        $data = $usuarioModel->listarTodos();

        // Asegurar un JSON válido con la propiedad 'data'
        echo json_encode([
            'data' => is_array($data) ? $data : []
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    // Endpoint AJAX para guardar el nuevo usuario
    public function guardar()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Mapeo exacto según los atributos 'name' del HTML
            $id        = intval($_POST['id'] ?? 0);
            $nombre    = trim($_POST['nombre'] ?? '');
            $documento = trim($_POST['documento'] ?? '');
            $correo    = trim($_POST['mail'] ?? $_POST['correo'] ?? '');
            $telefono  = trim($_POST['telefono'] ?? '');
            $idPerfil  = intval($_POST['perfil'] ?? $_POST['id_perfil'] ?? 0);
            $estado    = intval($_POST['estado'] ?? 1);
            $password  = trim($_POST['password'] ?? '');

            // Validación mínima para evitar errores de BD
            if (empty($nombre) || empty($correo)) {
                echo json_encode(['status' => 'error', 'message' => 'El nombre y el correo son obligatorios.']);
                exit;
            }

            $usuarioModel = new Usuario();

            if ($id > 0) {
                // Actualizar (UPDATE) si viene un ID válido
                $exito = $usuarioModel->actualizar($id, $nombre, $documento, $correo, $telefono, $idPerfil, $estado, $password);
                $msg   = 'Usuario actualizado con éxito.';
            } else {
                // Crear (INSERT) si es un nuevo registro
                $datos = [
                    'nombre'    => $nombre,
                    'documento' => $documento,
                    'correo'    => $correo,
                    'mail'      => $correo,
                    'telefono'  => $telefono,
                    'perfil'    => $idPerfil,
                    'id_perfil' => $idPerfil,
                    'estado'    => $estado,
                    'password'  => $password
                ];

                $exito = $usuarioModel->crear($datos);
                $msg   = 'Usuario creado con éxito.';
            }

            if ($exito) {
                echo json_encode(['status' => 'success', 'message' => $msg]);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar en la base de datos. Verifique que el correo no esté duplicado.']);
                exit;
            }
        }
    }

    // Endpoint para cargar la lista de roles dinámicos
    public function obtenerRoles()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $usuarioModel = new Usuario();
        $roles = $usuarioModel->obtenerRolesBD();

        echo json_encode(['status' => 'success', 'data' => $roles], JSON_UNESCAPED_UNICODE);
        exit;
    }
    // Cargar datos JSON de un usuario por su ID
    public function obtener()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $id = intval($_GET['id'] ?? 0);
        $usuarioModel = new Usuario();
        $user = $usuarioModel->obtenerPorId($id);

        if ($user) {
            echo json_encode(['status' => 'success', 'data' => $user], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }
        exit;
    }
    // Endpoint AJAX para alternar Activo/Inactivo
    public function cambiarEstado()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = intval($_POST['id'] ?? 0);
            $estado = intval($_POST['estado'] ?? 0);

            if ($id > 0) {
                $usuarioModel = new Usuario();
                if ($usuarioModel->cambiarEstado($id, $estado)) {
                    $msg = ($estado === 1) ? 'Usuario activado con éxito.' : 'Usuario inactivado con éxito.';
                    echo json_encode(['status' => 'success', 'message' => $msg]);
                    exit;
                }
            }
            echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el estado.']);
        }
        exit;
    }
    // Endpoint para consultar datos de un usuario por su ID
    public function obtenerPorId()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $id = intval($_GET['id'] ?? 0);

        if ($id > 0) {
            $usuarioModel = new Usuario();
            $data = $usuarioModel->obtenerPorId($id);

            if ($data) {
                echo json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para el usuario.']);
        exit;
    }
}
