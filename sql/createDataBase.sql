CREATE DATABASE gestion_siniestros;

USE gestion_siniestros;

CREATE TABLE IF NOT EXISTS `usuario` (
  id_usuario int UNSIGNED NOT NULL PRIMARY KEY,
  nombre varchar(255) NOT NULL COMMENT 'No se aceptan números, solamente espacios en caso de multiples nombres',
  apellido varchar(255) NOT NULL COMMENT 'No se aceptan números, solamente espacios en caso de multiples nombres',
  fecha_nacimiento date NOT NULL COMMENT 'Se guarda la fecha de nacimiento del usuario recordando que tiene que ser mayor de edad',
  foto mediumblob DEFAULT NULL COMMENT 'Foto de perfil del usuario, no es obligatoria, maximo 16mb',
  genero char(1) NOT NULL COMMENT 'Se basa en los estándares legales de Mexico, M (Masculino), F (Femenino), X (No binario/Otro)',
  correo_electronico varchar(321) NOT NULL,
  contrasena varchar(255) NOT NULL COMMENT 'Se guarda la contraseña ya encriptada con password_hash de php',
  alias varchar(255) NOT NULL COMMENT 'Identificador alfanumerico único por cliente',
  tipo_usuario tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Hay tres tipos de usuario. 0 (asegurado es default) ,1 (ajustador), 2 (supervisor)',
  estatus tinyint NOT NULL DEFAULT 1 COMMENT 'false (inactivo/eliminado), true(activo es default)'
) COMMENT='Tabla para almacenar a los usuarios del sistema';

CREATE TABLE IF NOT EXISTS compania(
    id_compania INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE COMMENT 'Nombre de la compañía, no pueden haber dos compañías con el mismo nombre',
    logo MEDIUMBLOB,
    estatus BOOLEAN NOT NULL DEFAULT true COMMENT 'false (inactivo/eliminado), true(activo es default)'
) COMMENT='Tabla para almacenar las diferentes compañías de seguro';

CREATE TABLE IF NOT EXISTS unidad(
    id_unidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    placas VARCHAR(20) NOT NULL UNIQUE COMMENT 'Dato alfanumerico único, regularmente tiene 6 o 7 caracteres, pero puede variar',
    numero_serie VARCHAR(17) NOT NULL UNIQUE COMMENT 'Dato alfanumerico único (NIV o VIN) a nivel mundial, unicamente puede contener 17 caracteres',
    valor DECIMAL(10,2) NOT NULL COMMENT 'Valor del vehículo en MXN, puede cambiar al hacerse una evaluación',
    marca VARCHAR(255) NOT NULL COMMENT 'Marca del vehículo',
    anio YEAR NOT NULL COMMENT 'Año en el que fue adquirido el vehículo',
    color VARCHAR(50) NOT NULL COMMENT 'Color predominante del vehículo',
    modelo VARCHAR(255) NOT NULL COMMENT 'Modelo y año de lanzamiento del vehículo',
    estatus BOOLEAN NOT NULL DEFAULT true COMMENT 'false (inactivo/eliminado), true(activo es default)'
) COMMENT='Tabla para almacenar las unidades aseguradas';

CREATE TABLE IF NOT EXISTS poliza(
    id_poliza INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_poliza VARCHAR(65) NOT NULL COMMENT 'El formato puede variar entre cada compañía, puede tener un maximo de 65 caracteres y puede repetirse entre diferentes compañías',
    estatus varchar(65) NOT NULL COMMENT 'Se deja como varchar bajo la posibilidad de un tercer caso o más de estatus',
    porcentaje_deducible DECIMAL(5,2) NOT NULL DEFAULT 0 COMMENT 'Porcentaje fijo de deducible que puede establecer una compañía',
    id_compania INT UNSIGNED NOT NULL,
    id_asegurado INT UNSIGNED NOT NULL,
    id_unidad INT UNSIGNED NOT NULL,
    CONSTRAINT fk_poliza_compania FOREIGN KEY (id_compania) REFERENCES compania(id_compania),
    CONSTRAINT fk_poliza_usuario FOREIGN KEY (id_asegurado) REFERENCES usuario(id_usuario),
    CONSTRAINT fk_poliza_unidad FOREIGN KEY (id_unidad) REFERENCES unidad(id_unidad),
    UNIQUE(numero_poliza, id_compania)
    
) COMMENT='Tabla donde se almacenan las pólizas del seguro, esto determina los términos entre la compañía y el usuario respecto al vehículo asegurado';

CREATE TABLE IF NOT EXISTS siniestro(
    id_siniestro INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_poliza INT UNSIGNED NOT NULL,
    id_ajustador INT UNSIGNED NOT NULL COMMENT 'El ajustador hace referencia a la persona que da de alta el siniestro',
    nombre_chofer VARCHAR(355) NOT NULL COMMENT 'Persona que manejaba la unidad al momento del siniestro' ,
    fecha_nacimiento_chofer DATE NOT NULL COMMENT 'Fecha de nacimiento de la persona que manejaba la unidad al momento del siniestro',
    fecha_hora_siniestro DATETIME NOT NULL COMMENT 'Momento aproximado en el que ocurrió el siniestro',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Momento en el que el ajustador dio de alta el siniestro',
    ubicacion VARCHAR(255) NOT NULL COMMENT 'Lugar del siniestro, el formato no es especifico',
    descripcion VARCHAR(512) NOT NULL COMMENT 'Texto donde se describe lo ocurrido en el siniestro',
    unidad_involucrada VARCHAR(255) COMMENT 'Se guardan datos en caso de que se necesite contactar con alguna posible persona afectada o su seguro, el ajustador es quien determina que dato o datos son necesarios',
    monto_pago DECIMAL(10,2) COMMENT 'El pago en MXN que se le hará al asegurado en caso de que se determine esto',
    monto_deducible_aplicado DECIMAL(10,2) COMMENT 'Pago en MXN que le correpondería hacer al asegurado',
    fecha_compromiso DATE COMMENT 'Fecha aproximada de resolución',
    CONSTRAINT fk_siniestro_poliza FOREIGN KEY (id_poliza) REFERENCES poliza(id_poliza),
    CONSTRAINT fk_siniestro_ajustador FOREIGN KEY (id_ajustador) REFERENCES usuario(id_usuario)
    
)COMMENT='Tabla donde se almacenan los datos del siniestro y la respuesta del seguro';

CREATE TABLE IF NOT EXISTS historial(
    id_historial INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_siniestro INT UNSIGNED NOT NULL,
    estatus_siniestro VARCHAR(90) NOT NULL COMMENT 'Estado en el que se encuentra el proceso de resolución del siniestro',
    fecha_estatus DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en la que se da de alta el estatus del siniestro',
    CONSTRAINT fk_historial_siniestro FOREIGN KEY (id_siniestro) REFERENCES siniestro(id_siniestro)  
)COMMENT='Tabla donde se guardan las actualizaciones respecto a un siniestro';

CREATE TABLE multimedia(
    id_multimedia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_siniestro INT UNSIGNED NOT NULL,
    evidencia MEDIUMBLOB NOT NULL COMMENT 'Evidencia del siniestro guardada en imagen (varios formatos posibles) o video (unicamente mp4)',
    etiqueta VARCHAR(100) NOT NULL COMMENT 'Texto que describe lo que muestra la multimedia',
    mime_type VARCHAR(50) NOT NULL COMMENT 'Dato que determina si la multimedia es imagen o video y que formato utiliza',
    CONSTRAINT fk_multimedia_siniestro FOREIGN KEY (id_siniestro) REFERENCES siniestro(id_siniestro)
)COMMENT='Tabla donde se guarda la evidencia del siniestro en diferentes formatos (mp4, jpg, png, etc.)';

CREATE TABLE IF NOT EXISTS mensaje(
    id_mensaje INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_siniestro INT UNSIGNED NOT NULL,
    id_usuario INT UNSIGNED NOT NULL COMMENT 'Usuario que envia el mensaje',
    texto VARCHAR(1024) NOT NULL COMMENT 'Contenido del mensaje',
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Momento en el que se manda el mensaje',
    CONSTRAINT fk_mensaje_siniestro FOREIGN KEY (id_siniestro) REFERENCES siniestro(id_siniestro),
    CONSTRAINT fk_mensaje_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
)COMMENT='Tabla encargada de guardar los mensajes de la conversación entre usuarios en el siniestro';

SELECT 
    TABLE_NAME AS 'Tabla', 
    COLUMN_NAME AS 'Columna', 
    COLUMN_TYPE AS 'Tipo de Dato', 
    IS_NULLABLE AS 'Permite Nulos', 
    COLUMN_KEY AS 'Llave', 
    COLUMN_DEFAULT AS 'Valor Default', 
    COLUMN_COMMENT AS 'Descripción y Dominio'
FROM 
    INFORMATION_SCHEMA.COLUMNS 
WHERE 
    TABLE_SCHEMA = 'gestion_siniestros' 
ORDER BY 
    TABLE_NAME, ORDINAL_POSITION;