<?php
require_once __DIR__ . '/../config/database.php';

class Usuario{
    public static function obtenerTodos(){
        try{
            $db = Database::connect();
            $stmt = $db->query("CALL sp_GestionUsuarios('SELECT_ALL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($usuarios as &$user) {
                if ($user['foto']) {
                    
                    $user['foto'] = base64_encode($user['foto']);
                }
            }
            return [
                'success' => true,
                'data' => $usuarios
            ];

        }catch(Exception $e){
            return[
              'success' => false,
              'error' => $e->getMessage()
            ];
        }
    }
    public static function obtenerUno($id){
        try{
            $db = Database::connect();
            $stmt = $db->prepare("CALL sp_GestionUsuarios('SELECT_ONE', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
            $stmt->execute([
                $id
            ]);
            return [
                'success' => true,
                'data' => $stmt->fetch(PDO::FETCH_ASSOC)
            ];

        }catch(Exception $e){
            return[
              'success' => false,
              'error' => $e->getMessage()
            ];
        }
    }

    public static function crear($data){
        try{
            $db = Database::connect();
            $nombre = $data['nombre'];
            $apellido = $data['apellido'];
            $fecha_nacimiento = $data['fecha_nacimiento'];
            $foto = $data['foto'];
            $genero = $data['genero'];
            $correo_electronico = $data['correo_electronico'];
            $contrasena = $data['contrasena'];
            $alias = $data['alias'];
            $tipo_usuario = $data['tipo_usuario'];
            if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $contrasena)) {
                return [
                    'success' => false, 
                    'error' => 'La contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo'];
            }

            $stmt = $db->prepare("CALL sp_GestionUsuarios('INSERT', NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nombre,
                $apellido,
                $fecha_nacimiento,
                $foto,
                $genero,
                $correo_electronico,
                password_hash($data['contrasena'], PASSWORD_DEFAULT),
                $alias,
                $tipo_usuario
            ]);
            return [
                'success' => true,
                'data' => 'Se han registrado correctamente los datos'
            ];

        }catch(Exception $e){
            return[
                'success' => false,
                'error' => $e->getMessage()
            ];

        }
    }

    public static function eliminar($id){
        try{
            $db = Database::connect();
            $stmt = $db->prepare("CALL sp_GestionUsuarios('DELETE', ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
            $stmt->execute([
                $id
            ]);
            return [
                'success' => true,
                'data' => 'Se ha eliminado el usuario'
            ];


        }catch(Exception $e){
            return[
                'success' => false,
                'error' => $e->getMessage()
            ];

        }
    }
    

}