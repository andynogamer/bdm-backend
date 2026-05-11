<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'controller/UsuarioController.php';
require_once 'middleware/AuthMiddleware.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = explode('/', trim($uri, '/'));


if (isset($path[3]) && $path[3] === 'usuarios') {

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($path[4])) {
        
        AuthMiddleware::checkAuthorization([2]); 
        (new UsuarioController())->getAll();
        exit;
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($path[4]) && $path[4] != 'logout') {
        (new UsuarioController())->getOne($path[4]);
        exit;
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($path[4]) && $path[4] == 'register') {
        (new UsuarioController())->postUser();
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($path[4]) && $path[4] === 'login'){
        
        (new UsuarioController())->getSession();
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($path[4]) && $path[4] === 'logout'){
        
        AuthMiddleware::checkAuthorization([0, 1, 2]);
        
        (new UsuarioController())->deleteSession();
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($path[4]) && $path[4] === 'delete'){
        
        AuthMiddleware::checkAuthorization([2]);
        (new UsuarioController())->deleteUser();
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($path[4]) && $path[4] === 'password'){
        
        AuthMiddleware::checkAuthorization([0, 1, 2]);
        (new UsuarioController())->updatePassword();
        exit;
    }

    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}


http_response_code(404);
echo json_encode(["error" => "Ruta no encontrada"]);