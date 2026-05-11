
------VISTA MENSAJE-------
CREATE OR REPLACE VIEW vw_mensaje AS
SELECT 
    m.id_mensaje,
    m.id_siniestro,
    m.id_usuario,
    m.texto texto_mensaje,
    m.fecha_hora fecha_hora_mensaje,
        u.nombre nombre_usuario,
        u.apellido apellido_usuario,
        u.alias alias_usuario,
        u.foto foto_usuario
FROM mensaje m
INNER JOIN usuario u ON m.id_usuario = u.id_usuario;

