<?php
require_once __DIR__ . '/../models/Requerimiento.php';

class RequerimientoController
{
    // Vista principal del modulo
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/navbar.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/requerimientos/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // Vista HTML para la Bolsa de Requerimientos
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
        require_once __DIR__ . '/../views/requerimientos/gestion.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    // Endpoint JSON para la Bolsa de Requerimientos
    public function listarBolsa()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $reqModel = new Requerimiento();
        $lista = $reqModel->listarBolsa();

        $data = [];
        foreach ($lista as $r) {
            $estadoTexto = strtoupper($r['estado'] ?? 'PENDIENTE');
            $badgeEstado = match ($estadoTexto) {
                'PENDIENTE', 'CREADO'  => '<span class="badge badge-warning p-2">PENDIENTE</span>',
                'EN PROCESO'          => '<span class="badge badge-info p-2">EN PROCESO</span>',
                'REALIZADO', 'RESUELTO' => '<span class="badge badge-success p-2">REALIZADO</span>',
                default               => '<span class="badge badge-secondary p-2">' . htmlspecialchars($estadoTexto) . '</span>',
            };

            $acciones = '
                <div class="btn-group" role="group">
                    <button class="btn btn-primary btn-sm" title="Tomar Requerimiento">
                        <i class="fas fa-user-check"></i>
                    </button>
                    <button class="btn btn-warning btn-sm" title="Ver Detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-success btn-sm" title="Resolver">
                        <i class="fas fa-check"></i>
                    </button>
                </div>';

            $data[] = [
                'id'                  => $r['id'],
                'categoria'           => htmlspecialchars($r['categoria']),
                'proyecto'            => htmlspecialchars($r['proyecto']),
                'asunto'              => htmlspecialchars($r['asunto']),
                /*'prioridad'           => '<span class="badge badge-info">MEDIA</span>',*/
                'usuario_solicitante' => htmlspecialchars($r['solicitante']),
                'fecha'               => $r['fecha_creacion'],
                'estado'              => $badgeEstado,
                'acciones'            => $acciones
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

        // Si no hay sesión, retornamos array data vacío para que DataTables no rompa
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['data' => []], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $model = new Requerimiento();
        $lista = $model->listarPorUsuario($_SESSION['usuario_id']);

        $data = [];
        foreach ($lista as $r) {
            $estadoTexto = strtoupper($r['estado'] ?? 'PENDIENTE');
            $badgeEstado = match ($estadoTexto) {
                'PENDIENTE', 'CREADO'    => '<span class="badge badge-warning p-2">PENDIENTE</span>',
                'EN PROCESO'            => '<span class="badge badge-info p-2">EN PROCESO</span>',
                'REALIZADO', 'RESUELTO' => '<span class="badge badge-success p-2">REALIZADO</span>',
                default                 => '<span class="badge badge-secondary p-2">' . htmlspecialchars($estadoTexto) . '</span>',
            };

            $acciones = '
                <button class="btn btn-warning btn-sm" title="Ver Detalle">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-success btn-sm" title="Seguimiento">
                    <i class="fas fa-check"></i>
                </button>';

            $data[] = [
                'id'          => $r['id'],
                'asunto'      => htmlspecialchars($r['asunto'] ?? ''),
                'solicitante' => htmlspecialchars($r['solicitante'] ?? ''),
                'estado'      => $badgeEstado,
                'fecha'       => $r['fecha_creacion'] ?? '',
                'acciones'    => $acciones
            ];
        }

        // SIEMPRE envolver el resultado dentro de la clave 'data'
        echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }
}