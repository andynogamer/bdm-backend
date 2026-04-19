<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Manejo de preflight (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'controller/UsuarioController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = explode('/', trim($uri, '/'));

// Ejemplo: /mi_api/usuarios
if (isset($path[3]) && $path[3] === 'usuarios') {

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($path[4])) {
        (new UsuarioController())->getAll();
        exit;
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($path[4])) {
        (new UsuarioController())->getOne($path[4]);
        exit;
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        (new UsuarioController())->postUser();
        exit;
    }
    

    // Método no permitido
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Ruta no encontrada
http_response_code(404);
echo json_encode(["error" => "Ruta no encontrada"]);