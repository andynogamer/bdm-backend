-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 19-04-2026 a las 07:17:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `INFORMATION_SCHEMA`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `COLUMNS`
--

CREATE TEMPORARY TABLE `COLUMNS` (
  `TABLE_CATALOG` varchar(512) NOT NULL,
  `TABLE_SCHEMA` varchar(64) NOT NULL,
  `Tabla` varchar(64) NOT NULL,
  `Columna` varchar(64) NOT NULL,
  `ORDINAL_POSITION` bigint(21) UNSIGNED NOT NULL,
  `Valor Default` longtext,
  `Permite Nulos` varchar(3) NOT NULL,
  `DATA_TYPE` varchar(64) NOT NULL,
  `CHARACTER_MAXIMUM_LENGTH` bigint(21) UNSIGNED,
  `CHARACTER_OCTET_LENGTH` bigint(21) UNSIGNED,
  `NUMERIC_PRECISION` bigint(21) UNSIGNED,
  `NUMERIC_SCALE` bigint(21) UNSIGNED,
  `DATETIME_PRECISION` bigint(21) UNSIGNED,
  `CHARACTER_SET_NAME` varchar(32),
  `COLLATION_NAME` varchar(32),
  `Tipo de Dato` longtext NOT NULL,
  `Llave` varchar(3) NOT NULL,
  `EXTRA` varchar(80) NOT NULL,
  `PRIVILEGES` varchar(80) NOT NULL,
  `Descripción y Dominio` varchar(1024) NOT NULL,
  `IS_GENERATED` varchar(6) NOT NULL,
  `GENERATION_EXPRESSION` longtext
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `COLUMNS`
--

INSERT INTO `COLUMNS` (`Tabla`, `Columna`, `Tipo de Dato`, `Permite Nulos`, `Llave`, `Valor Default`, `Descripción y Dominio`) VALUES
('compania', 'id_compania', 'int(10) unsigned', 'NO', 'PRI', NULL, ''),
('compania', 'nombre', 'varchar(255)', 'NO', 'UNI', NULL, 'Nombre de la compañía, no pueden haber dos compañías con el mismo nombre'),
('compania', 'logo', 'mediumblob', 'YES', '', 'NULL', ''),
('compania', 'estatus', 'tinyint(1)', 'NO', '', '1', 'false (inactivo/eliminado), true(activo es default)'),
('historial', 'id_historial', 'int(10) unsigned', 'NO', 'PRI', NULL, ''),
('historial', 'id_siniestro', 'int(10) unsigned', 'NO', 'MUL', NULL, ''),
('historial', 'estatus_siniestro', 'varchar(90)', 'NO', '', NULL, 'Estado en el que se encuentra el proceso de resolución del siniestro'),
('historial', 'fecha_estatus', 'datetime', 'NO', '', 'current_timestamp()', 'Fecha en la que se da de alta el estatus del siniestro'),
('mensaje', 'id_mensaje', 'int(10) unsigned', 'NO', 'PRI', NULL, ''),
('mensaje', 'id_siniestro', 'int(10) unsigned', 'NO', 'MUL', NULL, ''),
('mensaje', 'id_usuario', 'int(10) unsigned', 'NO', 'MUL', NULL, 'Usuario que envia el mensaje'),
('mensaje', 'texto', 'varchar(1024)', 'NO', '', NULL, 'Contenido del mensaje'),
('mensaje', 'fecha_hora', 'datetime', 'NO', '', 'current_timestamp()', 'Momento en el que se manda el mensaje'),
('multimedia', 'id_multimedia', 'int(10) unsigned', 'NO', 'PRI', NULL, ''),
('multimedia', 'id_siniestro', 'int(10) unsigned', 'NO', 'MUL', NULL, ''),
('multimedia', 'evidencia', 'mediumblob', 'NO', '', NULL, 'Evidencia del siniestro guardada en imagen (varios formatos posibles) o video (unicamente mp4)'),
('multimedia', 'etiqueta', 'varchar(100)', 'NO', '', NULL, 'Texto que describe lo que muestra la multimedia'),
('multimedia', 'mime_type', 'varchar(50)', 'NO', '', NULL, 'Dato que determina si la multimedia es imagen o video y que formato utiliza'),
('poliza', 'id_poliza', 'int(10) unsigned', 'NO', 'PRI', NULL, ''),
('poliza', 'numero_poliza', 'varchar(65)', 'NO', 'MUL', NULL, 'El formato puede variar entre cada compañía, puede tener un maximo de 65 caracteres y puede repetirse entre diferentes compañías'),
('poliza', 'estatus', 'varchar(65)', 'NO', '', NULL, 'Se deja como varchar bajo la posibilidad de un tercer caso o más de estatus'),
('poliza', 'porcentaje_deducible', 'decimal(5,2)', 'NO', '', '0.00', 'Porcentaje fijo de deducible que puede establecer una compañía'),
('poliza', 'id_compania', 'int(10) unsigned', 'NO', 'MUL', NULL, ''),
('poliza', 'id_asegurado', 'int(10) unsigned', 'NO', 'MUL', NULL, ''),
('poliza', 'id_unidad', 'int(10) unsigned', 'NO', 'MUL', NULL, ''),
('siniestro', 'id_siniestro', 'int(10) unsigned', 'NO', 'PRI', NULL, ''),
('siniestro', 'id_poliza', 'int(10) unsigned', 'NO', 'MUL', NULL, ''),
('siniestro', 'id_ajustador', 'int(10) unsigned', 'NO', 'MUL', NULL, 'El ajustador hace referencia a la persona que da de alta el siniestro'),
('siniestro', 'nombre_chofer', 'varchar(355)', 'NO', '', NULL, 'Persona que manejaba la unidad al momento del siniestro'),
('siniestro', 'fecha_nacimiento_chofer', 'date', 'NO', '', NULL, 'Fecha de nacimiento de la persona que manejaba la unidad al momento del siniestro'),
('siniestro', 'fecha_hora_siniestro', 'datetime', 'NO', '', NULL, 'Momento aproximado en el que ocurrió el siniestro'),
('siniestro', 'fecha_registro', 'datetime', 'NO', '', 'current_timestamp()', 'Momento en el que el ajustador dio de alta el siniestro'),
('siniestro', 'ubicacion', 'varchar(255)', 'NO', '', NULL, 'Lugar del siniestro, el formato no es especifico'),
('siniestro', 'descripcion', 'varchar(512)', 'NO', '', NULL, 'Texto donde se describe lo ocurrido en el siniestro'),
('siniestro', 'unidad_involucrada', 'varchar(255)', 'YES', '', 'NULL', 'Se guardan datos en caso de que se necesite contactar con alguna posible persona afectada o su seguro, el ajustador es quien determina que dato o datos son necesarios'),
('siniestro', 'monto_pago', 'decimal(10,2)', 'YES', '', 'NULL', 'El pago en MXN que se le hará al asegurado en caso de que se determine esto'),
('siniestro', 'monto_deducible_aplicado', 'decimal(10,2)', 'YES', '', 'NULL', 'Pago en MXN que le correpondería hacer al asegurado'),
('siniestro', 'fecha_compromiso', 'date', 'YES', '', 'NULL', 'Fecha aproximada de resolución'),
('unidad', 'id_unidad', 'int(10) unsigned', 'NO', 'PRI', NULL, ''),
('unidad', 'placas', 'varchar(20)', 'NO', 'UNI', NULL, 'Dato alfanumerico único, regularmente tiene 6 o 7 caracteres, pero puede variar'),
('unidad', 'numero_serie', 'varchar(17)', 'NO', 'UNI', NULL, 'Dato alfanumerico único (NIV o VIN) a nivel mundial, unicamente puede contener 17 caracteres'),
('unidad', 'valor', 'decimal(10,2)', 'NO', '', NULL, 'Valor del vehículo en MXN, puede cambiar al hacerse una evaluación'),
('unidad', 'marca', 'varchar(255)', 'NO', '', NULL, 'Marca del vehículo'),
('unidad', 'anio', 'year(4)', 'NO', '', NULL, 'Año en el que fue adquirido el vehículo'),
('unidad', 'color', 'varchar(50)', 'NO', '', NULL, 'Color predominante del vehículo'),
('unidad', 'modelo', 'varchar(255)', 'NO', '', NULL, 'Modelo y año de lanzamiento del vehículo'),
('unidad', 'estatus', 'tinyint(1)', 'NO', '', '1', 'false (inactivo/eliminado), true(activo es default)'),
('usuario', 'id_usuario', 'int(10) unsigned', 'NO', 'PRI', NULL, ''),
('usuario', 'nombre', 'varchar(255)', 'NO', '', NULL, 'No se aceptan números, solamente espacios en caso de multiples nombres'),
('usuario', 'apellido', 'varchar(255)', 'NO', '', NULL, 'No se aceptan números, solamente espacios en caso de multiples nombres'),
('usuario', 'fecha_nacimiento', 'date', 'NO', '', NULL, 'Se guarda la fecha de nacimiento del usuario recordando que tiene que ser mayor de edad'),
('usuario', 'foto', 'mediumblob', 'YES', '', 'NULL', 'Foto de perfil del usuario, no es obligatoria, maximo 16mb'),
('usuario', 'genero', 'char(1)', 'NO', '', NULL, 'Se basa en los estándares legales de Mexico, M (Masculino), F (Femenino), X (No binario/Otro)'),
('usuario', 'correo_electronico', 'varchar(321)', 'NO', 'UNI', NULL, ''),
('usuario', 'contrasena', 'varchar(255)', 'NO', '', NULL, 'Se guarda la contraseña ya encriptada con password_hash de php'),
('usuario', 'alias', 'varchar(255)', 'NO', 'UNI', NULL, 'Identificador alfanumerico único por cliente'),
('usuario', 'tipo_usuario', 'tinyint(3) unsigned', 'NO', '', '0', 'Hay tres tipos de usuario. 0 (asegurado es default) ,1 (ajustador), 2 (supervisor)'),
('usuario', 'estatus', 'tinyint(1)', 'NO', '', '1', 'false (inactivo/eliminado), true(activo es default)');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
