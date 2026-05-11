<?php
class AuthMiddleware{
    private static function renderJSON($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        try {
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            http_response_code(500);
            echo json_encode([
                "error" => "Error de codificación JSON",
                "detalle" => $e->getMessage()
            ]);
        }

        exit;
    }

    private static function isLogged(){
        if(isset($_SESSION['usuario'])){
            return true;
        }else{
            return false;
        }
    }

    public static function checkAuthorization($tipos_autorizados){
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if(self::isLogged()){
            $isAuthorized = false;
            foreach($tipos_autorizados as $tipo_usuario){
                if($tipo_usuario == $_SESSION['usuario']['tipo_usuario']){
                    $isAuthorized = true;
                }
            }
            if(!$isAuthorized){
                $response = [
                    'success' => false,
                    'error' => 'No autorizado'
                ];
                self::renderJSON($response, 403);
            }
            

        }else{
            
            $response = [
                'success' => false,
                'error' => 'Debes de iniciar sesión para realizar esta acción'
            ];
            self::renderJSON($response, 401);

        }
    }
}