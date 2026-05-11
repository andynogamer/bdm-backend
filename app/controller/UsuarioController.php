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

    public function postUser(){
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if(isset($data['foto']) && $data['foto']){
            $imagenBase64 = $data['foto'];
            
            
            $partes = explode(',', $imagenBase64);
            $imagenBase64 = (count($partes) > 1) ? $partes[1] : $partes[0];
            
            
            $imagenBinaria = base64_decode($imagenBase64);
            
            
            $data['foto'] = $imagenBinaria; 
        }
        
        $response = Usuario::crear($data);
        
        if($response['success']){
            $this->renderJSON($response['data'], 200);
        }else{
            $this->renderJSON($response, 500);
        }
    }

    public function getSession(){
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $response = null;
        if(!is_array($data) || !isset($data['correo_electronico']) || !isset($data['contrasena'])){
            $response = [
                'success' => false,
                'error' => 'Es necesario llenar todos los campos'
            ];
            $this->renderJSON($response, 400);
        }
        $correo_electronico = mb_strtolower(trim($data['correo_electronico']));
        $contrasena = $data['contrasena'];
        if(!$correo_electronico || !$contrasena){
            $response = [
                'success' => false,
                'error' => 'Es necesario llenar todos los campos'
            ];
            $this->renderJSON($response, 400);
        }
        
        
        $response = Usuario::obtenerSesion($correo_electronico);

        if($response['success']){
            $newResponse = null;
            
            if($response['data'] && password_verify($contrasena, $response['data']['contrasena'])){
                $newResponse = [
                    'success' => true,
                    'data' => 'Ha iniciado sesión exitosamente'
                ];
                session_start();
                
                $_SESSION['usuario'] = $response['data'];
                
                $this->renderJSON($newResponse, 200);
            }

            $newResponse = [
                'success' => false,
                'error' => 'Las credenciales no coinciden con los registros'
            ];
            $this->renderJSON($newResponse, 401);

        }
    }

    public function deleteUser(){
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $id_usuario = null;
        isset($data['id_usuario']) ? $id_usuario = $data['id_usuario'] : $this->renderJSON(['success' => false, 'error' => 'No ha seleccionado un usuario valido'], 400);
        $response = Usuario::eliminar($id_usuario);
        if($response['success']){
            $this->renderJSON($response, 200);
        }else{
            $this->renderJSON($response, 500);
        }
    }

    public function deleteSession(){
        
        session_destroy();
        $response = [
            'success' => true,
            'data' => 'La sesión se ha cerrado'
        ];
        $this->renderJSON($response, 200);
    }

    public function updatePassword(){
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if(empty($data) || !is_array($data) || !isset($data['contrasena']) || !isset($data['nueva_contrasena'])){
            $this->renderJSON([
                'success' => false,
                'error' => 'Datos incompletos'
            ], 400);
        }
        if(!password_verify($data['contrasena'], $_SESSION['usuario']['contrasena'])){
            $this->renderJSON([
                'success' => false,
                'error' => 'La contraseña ingresada no coincide con su contraseña actual'
            ], 400);
        }
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $data['nueva_contrasena'])) {
            $this->renderJSON( [
                'success' => false, 
                'error' => 'La contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo'
            ], 400);
        }
        $contrasena = password_hash($data['nueva_contrasena'], PASSWORD_DEFAULT);

        $response = Usuario::modificarContrasena($contrasena);
        
        if($response['success']){
            $response_new_user = Usuario::obtenerSesion($_SESSION['usuario']['correo_electronico']);
            if($response_new_user['success']){
                if($response_new_user['data']){
                    $_SESSION['usuario'] = $response_new_user['data'];
                    $this->renderJSON($response, 200);
                }
                $this->renderJSON($response_new_user, 500);
            }else{
                $this->renderJSON($response_new_user, 500);
            }
            
            
        }else{
            $this->renderJSON($response, 500);
        }

    }

    


}
