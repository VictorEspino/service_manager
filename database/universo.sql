/*Los creados por el usuario*/
select distinct ticket_id from (
SELECT id as ticket_id FROM `tickets` WHERE de_id=11 
UNION
select distinct ticket_id from actividad_tickets where grupo_id in (SELECT grupo_id FROM `miembro_grupos` WHERE user_id=11)
UNION
select distinct ticket_id from invitado_tickets where user_id=11
)