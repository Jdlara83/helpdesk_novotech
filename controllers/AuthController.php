<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{

    public function index()
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor ingrese correo y contraseña.']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->login($email, $password);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['mail']       = $usuario['mail'];
            $_SESSION['usuario']    = $usuario['nombre'] ?? $usuario['mail'];
            $_SESSION['id_perfil'] = intval($usuario['id_perfil']);

            // ENVIAR LA URL DE REDIRECCIÓN AL DASHBOARD
            echo json_encode([
                'status'   => 'success',
                'message'  => '¡Bienvenido al sistema!',
                'redirect' => 'index.php?controller=dashboard&action=index'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas o usuario inactivo.']);
        }
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: index.php?controller=auth&action=index');
        exit;
    }
}
