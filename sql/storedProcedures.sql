DELIMITER //
DROP PROCEDURE IF EXISTS sp_GestionUsuarios //
CREATE PROCEDURE sp_GestionUsuarios(
    IN p_opcion VARCHAR(50),      
    IN p_id_usuario INT,
    IN p_nombre VARCHAR(255),
    IN p_apellido VARCHAR(255),
    IN p_fecha_nacimiento DATE,
    IN p_foto MEDIUMBLOB,         
    IN p_genero CHAR(1),
    IN p_correo_electronico VARCHAR(321),
    IN p_contrasena VARCHAR(255), 
    IN p_alias VARCHAR(255),
    IN p_tipo_usuario TINYINT

)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: No se pudo completar la operación en la base de datos.';
    END;
    START TRANSACTION;
    CASE p_opcion
        WHEN 'INSERT' THEN
            IF TIMESTAMPDIFF(YEAR, p_fecha_nacimiento, CURDATE()) < 18 THEN
                ROLLBACK;
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo mayores de 18 años pueden acceder.';
            ELSE
                INSERT INTO usuario (nombre, apellido, fecha_nacimiento, foto, genero, correo_electronico, contrasena, alias, tipo_usuario)
                VALUES (p_nombre, p_apellido, p_fecha_nacimiento, p_foto, p_genero, p_correo_electronico, p_contrasena, p_alias, p_tipo_usuario);
                COMMIT;
            END IF;
        WHEN 'UPDATE' THEN
            IF TIMESTAMPDIFF(YEAR, p_fecha_nacimiento, CURDATE()) < 18 THEN
                ROLLBACK;
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo mayores de 18 años pueden acceder.';
            ELSE
                UPDATE usuario
                SET nombre = p_nombre, apellido = p_apellido, fecha_nacimiento = p_fecha_nacimiento, foto = p_foto, genero = p_genero
                WHERE id_usuario = p_id_usuario;
                COMMIT;
            END IF;
        WHEN 'UPDATE_PASSWORD' THEN
            UPDATE usuario
            SET contrasena = p_contrasena
            WHERE id_usuario = p_id_usuario;
            COMMIT;

        WHEN 'SELECT_ALL' THEN
            SELECT id_usuario, nombre, apellido, fecha_nacimiento, foto, genero, correo_electronico, alias, tipo_usuario
            FROM usuario
            WHERE estatus = 1;

        WHEN 'SELECT_ONE' THEN
            SELECT id_usuario, nombre, apellido, fecha_nacimiento, foto, genero, correo_electronico, alias, tipo_usuario
            FROM usuario
            WHERE id_usuario = p_id_usuario;

        WHEN 'DELETE' THEN
            UPDATE usuario
            SET estatus = 0
            WHERE id_usuario = p_id_usuario;
            COMMIT;
        ELSE 
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Opción de gestión no valida';
    END CASE;
END //