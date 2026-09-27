<?php

if (!class_exists('Dashboard')) {
    require_once __DIR__ . '/../models/Dashboard.php';
}

class DashboardController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=auth&action=index');
            exit;
        }

        $id_perfil  = $_SESSION['id_perfil'] ?? 2;
        $usuario_id = $_SESSION['usuario_id'];
        $nombre     = $_SESSION['usuario'] ?? $_SESSION['mail'] ?? 'Usuario';

        $dashboardModel = new Dashboard();

        // Obtener datos para las gráficas según el perfil
        $datosGraficas = $dashboardModel->obtenerEstadisticasPorPerfil($id_perfil, $usuario_id);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/navbar.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/dashboard/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
