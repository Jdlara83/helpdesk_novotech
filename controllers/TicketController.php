<?php

if (!class_exists('Ticket')) {
    require_once __DIR__ . '/../models/Ticket.php';
}

class TicketController
{
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit;
        }

        $id_perfil = $_SESSION['id_perfil'] ?? 2;

        $ticketModel = new Ticket();
        $categoriasPermitidas = $ticketModel->obtenerTiposSoportePorPerfil($id_perfil);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/navbar.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/tickets/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function crear()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $id_categoria  = intval($_POST['id_categoria_soporte'] ?? 0);
        $asunto        = trim($_POST['asunto_ticket'] ?? '');
        $prioridad     = trim($_POST['prioridad'] ?? 'Media');
        $detalle       = trim($_POST['detalle_ticket'] ?? '');
        $ubicacion     = trim($_POST['detalle_ubicacion'] ?? '');
        $usuario_id    = $_SESSION['usuario_id'] ?? 1;
        // Se captura el login/usuario de la sesión o el correo
        $usuario_login = $_SESSION['usuario'] ?? $_SESSION['mail'] ?? 'sistema';

        if ($id_categoria === 0 || empty($asunto) || empty($detalle)) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor complete todos los campos obligatorios.']);
            exit;
        }

        // Subida de imágenes
        $ruta_imagenes = [];
        if (!empty($_FILES['imagenes']['name'][0])) {
            $dir = __DIR__ . '/../public/uploads/imagenes/';
            if (!file_exists($dir)) mkdir($dir, 0777, true);

            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                $filename = time() . '_' . basename($_FILES['imagenes']['name'][$key]);
                if (move_uploaded_file($tmp_name, $dir . $filename)) {
                    $ruta_imagenes[] = 'uploads/imagenes/' . $filename;
                }
            }
        }

        // Subida de archivos
        $ruta_archivos = [];
        if (!empty($_FILES['archivos']['name'][0])) {
            $dir = __DIR__ . '/../public/uploads/archivos/';
            if (!file_exists($dir)) mkdir($dir, 0777, true);

            foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
                $filename = time() . '_' . basename($_FILES['archivos']['name'][$key]);
                if (move_uploaded_file($tmp_name, $dir . $filename)) {
                    $ruta_archivos[] = 'uploads/archivos/' . $filename;
                }
            }
        }

        $str_imagenes = implode(',', $ruta_imagenes);
        $str_archivos = implode(',', $ruta_archivos);

        $ticketModel = new Ticket();
        $resultado = $ticketModel->crearCompleto(
            $id_categoria,
            $usuario_id,
            $usuario_login,
            $asunto,
            $detalle,
            $ubicacion,
            $prioridad,
            $str_imagenes,
            $str_archivos
        );

        if ($resultado['status'] === true) {
            echo json_encode(['status' => 'success', 'message' => '¡Ticket registrado con éxito en la BD!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error BD: ' . $resultado['error']]);
        }
        exit;
    }

    public function listar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $ticketModel = new Ticket();
        $tickets = $ticketModel->listar();

        $data = [];
        foreach ($tickets as $ticket) {
            $estadoTexto = strtoupper($ticket['estado']);
            $badgeEstado = match ($estadoTexto) {
                'CREADO', 'PENDIENTE' => '<span class="badge badge-warning">CREADO</span>',
                'EN PROCESO' => '<span class="badge badge-info">EN PROCESO</span>',
                'RESUELTO' => '<span class="badge badge-success">RESUELTO</span>',
                default => '<span class="badge badge-secondary">' . htmlspecialchars($estadoTexto) . '</span>'
            };

            $acciones = '
                <button class="btn btn-sm btn-warning btnVer" data-id="' . $ticket['id'] . '" title="Ver Detalle"><i class="fas fa-eye text-white"></i></button>
                <button class="btn btn-sm btn-success btnEditar" data-id="' . $ticket['id'] . '" title="Gestionar"><i class="fas fa-check"></i></button>
            ';

            $data[] = [
                'id'          => $ticket['id'],
                'titulo'      => htmlspecialchars($ticket['titulo']),
                'solicitante' => htmlspecialchars($ticket['solicitante']),
                'estado'      => $badgeEstado,
                'fecha'       => $ticket['fecha'],
                'acciones'    => $acciones
            ];
        }

        echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }
    public function listarPorUsuario()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        // Capturar ID de la sesión activa (o fallback a 1 si estamos probando)
        $usuarioId = $_SESSION['usuario_id'] ?? 1;

        $ticketModel = new Ticket();
        $ticketsBD = $ticketModel->listarPorUsuario($usuarioId);

        $data = [];
        foreach ($ticketsBD as $t) {
            $estadoTexto = strtoupper($t['estado'] ?? 'CREADO');
            $badgeEstado = match ($estadoTexto) {
                'CREADO', 'PENDIENTE'    => '<span class="badge badge-warning p-2">CREADO</span>',
                'EN PROCESO'            => '<span class="badge badge-info p-2">EN PROCESO</span>',
                'REALIZADO', 'RESUELTO' => '<span class="badge badge-success p-2">REALIZADO</span>',
                default                 => '<span class="badge badge-secondary p-2">' . htmlspecialchars($estadoTexto) . '</span>',
            };

            $acciones = '
                <button class="btn btn-warning btn-sm" title="Ver Detalle">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-success btn-sm" title="Seguimiento / Chat">
                    <i class="fas fa-check"></i>
                </button>';

            $data[] = [
                'id'          => $t['id'],
                'titulo'      => htmlspecialchars($t['asunto'] ?? ''),
                'solicitante' => htmlspecialchars($t['solicitante'] ?? ''),
                'estado'      => $badgeEstado,
                'fecha'       => $t['fecha_creacion'] ?? '',
                'acciones'    => $acciones
            ];
        }

        echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }
    // Cargar la vista HTML de Bolsa de Tickets
    public function gestion()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/navbar.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/tickets/gestion.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // Endpoint AJAX para DataTables de la Bolsa General
    public function listarBolsa()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $ticketModel = new Ticket();
        $ticketsBD = $ticketModel->listarBolsa();

        $data = [];
        foreach ($ticketsBD as $t) {
            // Badges de Estado
            $estadoTexto = strtoupper($t['estado'] ?? 'CREADO');
            $badgeEstado = match ($estadoTexto) {
                'CREADO', 'PENDIENTE'    => '<span class="badge badge-warning p-2">CREADO</span>',
                'EN PROCESO'            => '<span class="badge badge-info p-2">EN PROCESO</span>',
                'REALIZADO', 'RESUELTO' => '<span class="badge badge-success p-2">REALIZADO</span>',
                default                 => '<span class="badge badge-secondary p-2">' . htmlspecialchars($estadoTexto) . '</span>',
            };

            // Badges de Prioridad
            $prio = strtoupper($t['prioridad'] ?? 'MEDIA');
            $badgePrioridad = match ($prio) {
                'ALTA'  => '<span class="badge badge-danger">ALTA</span>',
                'MEDIA' => '<span class="badge badge-warning">MEDIA</span>',
                'BAJA'  => '<span class="badge badge-secondary">BAJA</span>',
                default => '<span class="badge badge-info">' . htmlspecialchars($prio) . '</span>',
            };

            // Botones exactos del proyecto viejo (Asignar, Ver, Resolver)
            $acciones = '
                <div class="btn-group-vertical btn-group-sm">
                    <button class="btn btn-primary btn-sm my-1" onclick="tomarTicket(' . $t['id'] . ')" title="Asignar">
                        <i class="fas fa-user"></i>
                    </button>
                    <button class="btn btn-warning btn-sm my-1" title="Ver Detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-success btn-sm my-1" title="Resolver">
                        <i class="fas fa-check"></i>
                    </button>
                </div>';

            // Arreglo sincronizado con DataTables JS
            $data[] = [
                'id'                  => $t['id'],
                'categoria'           => htmlspecialchars($t['categoria'] ?? 'SIN CATEGORÍA'),
                'proyecto'            => htmlspecialchars($t['proyecto'] ?? 'GENERAL'),
                'asunto'              => htmlspecialchars($t['asunto'] ?? ''),
                'prioridad'           => $badgePrioridad,
                'usuario_solicitante' => htmlspecialchars($t['solicitante'] ?? ''),
                'fecha'               => $t['fecha_creacion'] ?? '',
                'estado'              => $badgeEstado,
                'acciones'            => $acciones
            ];
        }

        echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Endpoint AJAX para tomar un ticket de la bolsa
    public function tomarTicket()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['status' => 'session_expired', 'message' => 'Sesión expirada.']);
            exit;
        }

        $idTicket = intval($_POST['id_ticket'] ?? 0);
        if ($idTicket > 0) {
            $ticketModel = new Ticket();
            if ($ticketModel->asignarResponsable($idTicket, $_SESSION['usuario_id'], 'EN PROCESO')) {
                echo json_encode(['status' => 'success', 'message' => 'Ticket asignado correctamente.']);
                exit;
            }
        }

        echo json_encode(['status' => 'error', 'message' => 'No se pudo asignar el ticket.']);
        exit;
    }
    // 1. Cargar la Vista HTML de Tickets Pendientes
    public function pendientes()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/navbar.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/tickets/pendientes.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // 2. Endpoint JSON para la tabla de Tickets Pendientes
    public function listarPendientes()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['data' => []], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $ticketModel = new Ticket();
        $idUsuarioSesion = $_SESSION['usuario_id'];

        // En el proyecto anterior (Imagen 5), los pendientes tienen estado_soporte = 'PENDIENTE'
        $ticketsBD = $ticketModel->listarPorEstadoSoporte($idUsuarioSesion, 'PENDIENTE');

        $data = [];
        foreach ($ticketsBD as $t) {
            $estadoTexto = strtoupper($t['estado'] ?? 'PENDIENTE');
            $badgeEstado = '<span class="badge badge-warning p-2">' . htmlspecialchars($estadoTexto) . '</span>';
            $badgeEstadoSoporte = '<span class="badge badge-info p-2">' . htmlspecialchars(strtoupper($t['estado_soporte'] ?? 'PENDIENTE')) . '</span>';

            $prio = strtoupper($t['prioridad'] ?? 'MEDIA');
            $badgePrioridad = match ($prio) {
                'ALTA'  => '<span class="badge badge-danger">ALTA</span>',
                'MEDIA' => '<span class="badge badge-warning">MEDIA</span>',
                'BAJA'  => '<span class="badge badge-secondary">BAJA</span>',
                default => '<span class="badge badge-info">' . htmlspecialchars($prio) . '</span>',
            };

            $acciones = '
                <div class="btn-group-vertical btn-group-sm">
                    <button class="btn btn-warning btn-sm my-1" title="Ver Detalle / Seguimiento">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-success btn-sm my-1" title="Resolver Ticket">
                        <i class="fas fa-ticket"></i>
                    </button>
                </div>';

            $data[] = [
                'id'                  => $t['id'],
                'categoria'           => htmlspecialchars($t['categoria'] ?? 'SIN CATEGORÍA'),
                'proyecto'            => htmlspecialchars($t['proyecto'] ?? 'GENERAL'),
                'asunto'              => htmlspecialchars($t['asunto'] ?? ''),
                'prioridad'           => $badgePrioridad,
                'usuario_solicitante' => htmlspecialchars($t['solicitante'] ?? ''),
                'fecha'               => $t['fecha_creacion'] ?? '',
                'estado'              => $badgeEstado,
                'estado_soporte'      => $badgeEstadoSoporte,
                'acciones'            => $acciones
            ];
        }

        echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }
    // 3. Endpoint JSON para la tabla de Tickets Realizados
    public function listarRealizados()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['data' => []], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $ticketModel = new Ticket();
        // Llamamos la SQL pasándole el ID de sesión y los estados realizados
        $idUsuarioSesion = $_SESSION['usuario_id'];
        $ticketsBD = $ticketModel->listarPorEstadoSoporte($idUsuarioSesion, 'PENDIENTE');
        $ticketsBD = $ticketModel->listarPorEstadoSoporte($idUsuarioSesion, 'REALIZADO');



        $data = [];
        foreach ($ticketsBD as $t) {
            $estadoTexto = strtoupper($t['estado'] ?? 'EN PROCESO');
            $badgeEstado = '<span class="badge badge-info p-2">' . htmlspecialchars($estadoTexto) . '</span>';

            $prio = strtoupper($t['prioridad'] ?? 'MEDIA');
            $badgePrioridad = match ($prio) {
                'ALTA'  => '<span class="badge badge-danger">ALTA</span>',
                'MEDIA' => '<span class="badge badge-warning">MEDIA</span>',
                'BAJA'  => '<span class="badge badge-secondary">BAJA</span>',
                default => '<span class="badge badge-info">' . htmlspecialchars($prio) . '</span>',
            };

            $acciones = '
                <div class="btn-group-vertical btn-group-sm">
                    <button class="btn btn-warning btn-sm my-1" title="Ver Detalle / Seguimiento">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-success btn-sm my-1" title="Resolver Ticket">
                        <i class="fas fa-check"></i>
                    </button>
                </div>';

            $data[] = [
                'id'                  => $t['id'],
                'categoria'           => htmlspecialchars($t['categoria'] ?? 'SIN CATEGORÍA'),
                'proyecto'            => htmlspecialchars($t['proyecto'] ?? 'GENERAL'),
                'asunto'              => htmlspecialchars($t['asunto'] ?? ''),
                'prioridad'           => $badgePrioridad,
                'usuario_solicitante' => htmlspecialchars($t['solicitante'] ?? ''),
                'fecha'               => $t['fecha_creacion'] ?? '',
                'estado'              => $badgeEstado,
                'acciones'            => $acciones
            ];
        }

        echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }
    // Cargar la vista HTML de Tickets Realizados
    public function realizados()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/navbar.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/tickets/realizados.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}