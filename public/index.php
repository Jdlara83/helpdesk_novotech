<?php
// Iniciar sesión global
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración de base de datos
require_once __DIR__ . '/../config/Database.php';

// Obtener controlador y acción por URL (valores por defecto)
$controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'AuthController';
$actionName     = isset($_GET['action']) ? $_GET['action'] : 'index';

// Rutas de archivos
$controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    if (class_exists($controllerName)) {
        $controller = new $controllerName();

        if (method_exists($controller, $actionName)) {
            // Ejecutar la acción solicitada
            $controller->$actionName();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => "La acción '{$actionName}' no existe."]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => "La clase '{$controllerName}' no existe."]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => "El controlador '{$controllerName}' no existe."]);
}