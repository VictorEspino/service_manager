create or replace view tiempo_transcurridos as
SELECT a.id as ticket_id,
		a.de_id,
        a.topico_id,
        a.asunto,
        a.actividad_actual,
        a.asignado_a,
	    a.prioridad,
        b.nombre,
        b.sla,
        b.grupo_id,
CASE
    WHEN a.actividad_actual = 0 THEN a.t_a0/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
	WHEN a.actividad_actual = 1 THEN a.t_a1/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
	WHEN a.actividad_actual = 2 THEN a.t_a2/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
	WHEN a.actividad_actual = 3 THEN a.t_a3/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
	WHEN a.actividad_actual = 4 THEN a.t_a4/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
	WHEN a.actividad_actual = 5 THEN a.t_a5/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
	WHEN a.actividad_actual = 6 THEN a.t_a6/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
	WHEN a.actividad_actual = 7 THEN a.t_a7/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
	WHEN a.actividad_actual = 8 THEN a.t_a8/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
    WHEN a.actividad_actual = 8 THEN a.t_a9/60+TIMESTAMPDIFF(MINUTE,a.updated_at,NOW())
END as tiempo_transcurrido,
a.updated_at as ultima_respuesta,
a.created_at
FROM tickets a,actividad_tickets b WHERE 
a.actividad_actual=b.secuencia AND
a.id=b.ticket_id AND
a.estatus=1 AND
a.time_to!=-1