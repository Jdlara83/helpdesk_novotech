<?php
require_once __DIR__ . '/../models/Responsable.php';
require_once __DIR__ . '/../models/Usuario.php';

class ResponsableController
{
    // Cargar la vista principal con el Layout (Header, Navbar, Sidebar, Footer)
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/navbar.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/responsables/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // Endpoint AJAX para listar los registros en DataTables
    public function listar()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['status' => 'session_expired', 'data' => []], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $responsableModel = new Responsable();
        $listado = $responsableModel->listar();

        echo json_encode(['data' => $listado], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Endpoint AJAX obligatorio llamado desde la modal al hacer submit
    public function guardar()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        // Validar sesión sin provocar redirección HTTP brusca en AJAX
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['status' => 'session_expired', 'message' => 'Sesión expirada. Por favor inicie sesión de nuevo.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCategoria = intval($_POST['id_categoria_soporte'] ?? 0);
            $idProyecto  = intval($_POST['id_proyecto_soporte'] ?? 0);
            $idUsuario   = intval($_POST['id_usuario'] ?? 0);

            if ($idCategoria <= 0 || $idProyecto <= 0 || $idUsuario <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Seleccione una Categoría, un Proyecto y un Usuario válido.']);
                exit;
            }

            $responsableModel = new Responsable();
            if ($responsableModel->crear($idCategoria, $idProyecto, $idUsuario)) {
                echo json_encode(['status' => 'success', 'message' => 'Responsable asignado con éxito.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar la asignación en la base de datos.']);
            }
        }
        exit;
    }

    // Endpoint AJAX para eliminar asignación
    public function eliminar()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['status' => 'session_expired', 'message' => 'Sesión expirada.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0) {
                $responsableModel = new Responsable();
                if ($responsableModel->eliminar($id)) {
                    echo json_encode(['status' => 'success', 'message' => 'Asignación eliminada con éxito.']);
                    exit;
                }
            }
            echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar la asignación.']);
        }
        exit;
    }

    // Endpoint AJAX para llenar el select de Proyectos en el Modal desde par_proyecto_soporte
    public function listarProyectos()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $stmt = Database::getConnection()->prepare("SELECT id_par_proyecto AS id, proyecto FROM par_proyecto_soporte WHERE estado = '1'");
            $stmt->execute();
            $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'success', 'data' => $proyectos], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'data' => []], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Endpoint AJAX para llenar las Categorías en el Modal desde par_categorias_soporte
    public function listarCategorias()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $stmt = Database::getConnection()->prepare("SELECT id_categoria_soporte AS id, nombre_categoria_soporte AS categoria FROM par_categorias_soporte WHERE estado = 1");
            $stmt->execute();
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fallback de respaldo si la tabla no devuelve filas
            if (empty($categorias)) {
                $categorias = [
                    ['id' => 1, 'categoria' => 'SOFTWARE'],
                    ['id' => 2, 'categoria' => 'INFRAESTRUCTURA'],
                    ['id' => 3, 'categoria' => 'REQUERIMIENTO']
                ];
            }

            echo json_encode(['status' => 'success', 'data' => $categorias], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'success',
                'data' => [
                    ['id' => 1, 'categoria' => 'SOFTWARE'],
                    ['id' => 2, 'categoria' => 'INFRAESTRUCTURA'],
                    ['id' => 3, 'categoria' => 'REQUERIMIENTO']
                ]
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}
