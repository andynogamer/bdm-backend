<?php
require_once __DIR__ . '/../config/database.php';

class Compania{
    public static function crear($data){
        try{
            $db = Database::connect();
            $stmt = $db->prepare("CALL sp_GestionCompanias('INSERT', NULL, ?, ?)");
            $stmt->execute([
                $data['nombre'],
                $data['logo']
            ]);
            return [
                'success' => true,
                'data' => 'Se han registrado correctamente los datos'
            ];
        }catch(Exception $e){
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public static function obtenerTodos(){
        try{
            $db = Database::connect();
            $stmt = $db->query("CALL sp_GestionCompanias('SELECT_ALL', NULL, NULL, NULL)");
            $companias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($companias as &$compania){
                if ($compania['logo']) {
                    
                    $compania['logo'] = base64_encode($compania['logo']);
                }
            }
            return [
                'success' => true,
                'data' => $companias
            ];
        }catch(Exception $e){
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}