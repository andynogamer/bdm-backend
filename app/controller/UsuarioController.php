<?php
require_once __DIR__ . '/../model/Usuario.php';

class UsuarioController{
    private function renderJSON($data, $statusCode = 200) {
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

    public function getAll(){
        $response = Usuario::obtenerTodos();
        if($response['success']){
            $this->renderJSON($response['data'], 200);
        }else{
            $this->renderJSON($response, 500);
        }
    }

    public function getOne($id){
        
        $response = Usuario::obtenerUno($id);
        if($response['success']){
            $this->renderJSON($response['data'], 200);
        }else{
            $this->renderJSON($response, 500);
        }
    }


}
