CREATE OR REPLACE VIEW busquedas
AS 
select b.name as solicitante,a.ticket_id,a.texto,a.campo,a.created_at from 
(select b.ticket_id as ticket_id,a.valor as texto,a.etiqueta as campo,a.updated_at as created_at from actividad_ticket_campos as a,actividad_tickets as b where a.tipo_control in ('Texto','Lista') and a.actividad_ticket_id=b.id
UNION
Select id as ticket_id,asunto as texto,'Asunto' as campo,created_at from tickets
UNION
Select ticket_id,descripcion as texto,concat('Descripcion actividad ',secuencia+1) as campo,created_at from actividad_tickets
UNION
Select ticket_id,nombre as texto,concat('Titulo actividad ',secuencia+1) as campo,created_at from actividad_tickets
UNION
select ticket_id,avance as texto,'Comentarios avance' as campo,created_at from ticket_avances where avance not like 'Cambió el estatus%'
UNION
select b.ticket_id,a.valor as texto,a.etiqueta as campo,a.created_at from ticket_avances_campos as a,ticket_avances as b where a.ticket_avance_id=b.id and a.tipo in ('Texto','Lista')
) as a,
(select tickets.id,users.name from tickets,users where tickets.creador_id=users.id) as b
where a.ticket_id=b.id




select b.ticket_id,a.valor as texto,a.etiqueta as campo,a.created_at from ticket_avances_campos as a,ticket_avances as b where a.ticket_avance_id=b.id