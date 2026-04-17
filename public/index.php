<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$controller = $_GET['controller'] ?? 'category';
$action = $_GET['action'] ?? 'index';

$controllerClass = 'controllers\\' . ucfirst($controller) . 'Controller';

if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
    
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        http_response_code(404);
        echo 'Action not found';
    }
} else {
    http_response_code(404);
    echo 'Controller not found';
}
