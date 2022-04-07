<?php

function getSQLUniverso($user_id)
{
    $sql="select distinct ticket_id from (SELECT id as ticket_id FROM `tickets` WHERE de_id=".$user_id." UNION select distinct ticket_id from actividad_tickets where grupo_id in (SELECT grupo_id FROM `miembro_grupos` WHERE user_id=".$user_id.") UNION select distinct ticket_id from invitado_tickets where user_id=".$user_id.") as a";   
    return($sql);
}
function getSQLParticipante($user_id)
{
    $sql="select distinct ticket_id from (select distinct ticket_id from actividad_tickets where grupo_id in (SELECT grupo_id FROM `miembro_grupos` WHERE user_id=".$user_id.") UNION select distinct ticket_id from invitado_tickets where user_id=".$user_id.") as a";
    return($sql);
}
function getSQLGruposComunicacion($user_id)
{
    $sql="select distinct grupo_id from miembro_grupo_comunicacions where user_id='".$user_id."'";
    return($sql);
}

function esManagerDeGrupo()
{
    return(true);
}
function actividadesAtrasadas()
{
    $grupos_manager=App\Models\MiembroGrupo::select('grupo_id')
                                    ->where('user_id',Illuminate\Support\Facades\Auth::user()->id)
                                    ->where('manager',1)
                                    ->get();
    $grupos_manager=$grupos_manager->pluck('grupo_id');
    $registros_escalacion=App\Models\TiempoTranscurrido::select(Illuminate\Support\Facades\DB::raw('count(*) as n'))
                                                ->whereIn('grupo_id',$grupos_manager)
                                                ->whereRaw('tiempo_transcurrido>sla')
                                                ->get();
    return($registros_escalacion->first()->n);
}