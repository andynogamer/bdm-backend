<?php
class Database {

    private static $host = "localhost";
    private static $db = "gestion_siniestros";
    private static $user = "root";
    private static $pass = "";
    private static $port = "3307"; 

    public static function connect() {
        try {
            $dsn = "mysql:host=" . self::$host . 
                   ";port=" . self::$port . 
                   ";dbname=" . self::$db . 
                   ";charset=utf8mb4";

            return new PDO($dsn, self::$user, self::$pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false 
            ]);

        } catch (PDOException $e) {
            error_log($e->getMessage()); 

            
            //http_response_code(500);
            //echo json_encode([
            //    'error' => true,
            //    'mensaje' => 'Error de conexión a la base de datos'
            //]);
            //exit;
            throw $e;
        }
    }
}