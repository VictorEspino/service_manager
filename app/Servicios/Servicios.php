<?php

function getSQLUniverso($user_id)
{
    $usuario=App\Models\User::find($user_id);
    $subarea=$usuario->sub_area;
    $puesto=$usuario->puesto;

    if($subarea == 10 || $puesto==114)
    {
        $subarea=100000;
    }

    $sql="
    select distinct ticket_id from (
        SELECT id as ticket_id FROM `tickets` WHERE de_id=".$user_id." or asignado_a=".$user_id." or subarea_id=".$subarea." 
        UNION 
        select distinct ticket_id from actividad_tickets where grupo_id in (SELECT grupo_id FROM `miembro_grupos` WHERE user_id=".$user_id.") 
        UNION 
        select distinct ticket_id from invitado_tickets where user_id=".$user_id." 
    ) as a
    ";
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
    $cuantos=App\Models\MiembroGrupo::select(Illuminate\Support\Facades\DB::raw('count(*) as n'))
                                    ->where('user_id',Illuminate\Support\Facades\Auth::user()->id)
                                    ->where('manager',1)
                                    ->get()->first();
    if($cuantos->n>0) return(true);
    return(false);
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