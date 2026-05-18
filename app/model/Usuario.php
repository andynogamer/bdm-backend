<?php
require_once __DIR__ . '/../config/database.php';

class Usuario{
    public static function obtenerTodos(){
        try{
            $db = Database::connect();
            $stmt = $db->query("CALL sp_GestionUsuarios('SELECT_ALL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
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
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            

            return [
                'success' => true,
                'data' => $user
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
            $camposObligatorios = [
                'nombre', 'apellido', 'fecha_nacimiento', 'genero',
                'correo_electronico', 'contrasena', 'alias'
            ];
            foreach ($camposObligatorios as $campo){
                if(!isset($data[$campo]) || empty(trim($data[$campo]))){
                    return[
                        'success' => false,
                        'error' => "El campo $campo es obligatorio"
                    ];
                }
            }
            $nombre = mb_strtoupper(trim($data['nombre']));
            $apellido = mb_strtoupper(trim($data['apellido']));
            $fecha_nacimiento = mb_strtoupper(trim($data['fecha_nacimiento']));
            $foto = $data['foto'];
            $genero = mb_strtoupper(trim($data['genero']));
            $correo_electronico = strtolower($data['correo_electronico']);
            $contrasena = $data['contrasena'];
            $alias = mb_strtoupper(trim($data['alias']));
            $tipo_usuario = $data['tipo_usuario'];

            if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre)) {
                return [
                    'success' => false,
                    'error' => 'El nombre no debe contener números'
                    ];
            }

            if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $apellido)) {
                return [
                    'success' => false,
                    'error' => 'El apellido no debe contener números'
                    ];
            }

            
            if (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => 'Correo electrónico inválido'
                    ];
            }

            if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $contrasena)) {
                return [
                    'success' => false, 
                    'error' => 'La contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo'
                    ];
            }

            try {
                $fecha = new DateTime($fecha_nacimiento);
                $hoy = new DateTime();
                $edad = $hoy->diff($fecha)->y;

                if ($edad < 18) {
                    return [
                        'success' => false,
                        'error' => 'Debes tener al menos 18 años'
                        ];
                }

            } catch (Exception $e) {
                return [
                    'success' => false,
                    'error' => 'Fecha de nacimiento inválida'
                    ];
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

    public static function modificar($data){
        try{

            $db = Database::connect();
            $camposObligatorios = [
                'nombre', 'apellido', 'fecha_nacimiento', 
                'genero'
            ];
            foreach ($camposObligatorios as $campo){
                if(!isset($data[$campo]) || empty(trim($data[$campo]))){
                    return[
                        'success' => false,
                        'error' => "El campo $campo es obligatorio"
                    ];
                }
            }
            $id_usuario = $_SESSION['usuario']['id_usuario'];
            $nombre = mb_strtoupper(trim($data['nombre']));
            $apellido = mb_strtoupper(trim($data['apellido']));
            $fecha_nacimiento = mb_strtoupper(trim($data['fecha_nacimiento']));
            $foto = $data['foto'] ?? null;
            $genero = mb_strtoupper(trim($data['genero']));
            $nonhash_contrasena = $data['contrasena'] ?? null;
            $contrasena = null;

            if(is_null($nonhash_contrasena) || $nonhash_contrasena == ""){
                $contrasena = $_SESSION['usuario']['contrasena'];
            }else if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $nonhash_contrasena)){
                return [
                    'success' => false,
                    'error' => 'La contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo'
                ];
            }else{
                $contrasena = password_hash($nonhash_contrasena, PASSWORD_DEFAULT);
            }
            
            

            if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre)) {
                return [
                    'success' => false,
                    'error' => 'El nombre no debe contener números'
                    ];
            }

            if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $apellido)) {
                return [
                    'success' => false,
                    'error' => 'El apellido no debe contener números'
                    ];
            }


            try {
                $fecha = new DateTime($fecha_nacimiento);
                $hoy = new DateTime();
                $edad = $hoy->diff($fecha)->y;

                if ($edad < 18) {
                    return [
                        'success' => false,
                        'error' => 'Debes tener al menos 18 años'
                        ];
                }

            } catch (Exception $e) {
                return [
                    'success' => false,
                    'error' => 'Fecha de nacimiento inválida'
                    ];
            }

            $stmt = $db->prepare("CALL sp_GestionUsuarios('UPDATE', ?, ?, ?, ?, ?, ?, NULL, ?, NULL, NULL)");
            $stmt->execute([
                $id_usuario,
                $nombre,
                $apellido,
                $fecha_nacimiento,
                $foto,
                $genero,
                $contrasena
                
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

    public static function modificarContrasena($contrasena){
        try{
            $db = Database::connect();
            $stmt = $db->prepare("CALL sp_GestionUsuarios('UPDATE_PASSWORD', ?, NULL, NULL, NULL, NULL, NULL, NULL, ?, NULL, NULL)");
            $stmt->execute([
                $_SESSION['usuario']['id_usuario'],
                $contrasena
            ]);
            return [
                'success' => true,
                'data' => 'Se ha cambiado la contraseña'
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

    public static function obtenerSesion($correo_electronico){
        try{
            $db = Database::connect();
            $stmt = $db->prepare("CALL sp_GestionUsuarios('SELECT_ONE_BY_CORREO', NULL, NULL, NULL, NULL, NULL, NULL, ?, NULL, NULL, NULL)");
            $stmt->execute([
                $correo_electronico
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
    

}