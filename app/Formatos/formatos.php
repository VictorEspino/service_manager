<?php

function ticket($ticket)
{
    return('#'.str_pad($ticket,10,'0',STR_PAD_LEFT));
}
function destaca_busqueda($texto,$busqueda)
{
    $resultado=str_ireplace($busqueda,'<span class="text-red-700 font-bold italic">'.strtoupper($busqueda).'</span>',$texto);
    return($resultado);
}
function busqueda_format($texto,$busqueda,$largo_maximo)
{
    $largo_texto=strlen($texto);
    $largo_busqueda=strlen($busqueda);
    $resultado=str_ireplace($busqueda,'<b>'.strtoupper($busqueda).'</b>',$texto);
    if($largo_texto>150)
    {
        $offset_inicial="...";
        $offset_final="...";
        $posicion_inicial=strpos($texto,$busqueda)-intval(($largo_maximo-$largo_busqueda)/2);
        if($posicion_inicial<0)
        {
            $offset_inicial="";
            $posicion_inicial=0;
        }
        if(($posicion_inicial+$largo_maximo)>=$largo_texto)
        {
            $offset_final="";
        }
        $cadena=substr($texto,$posicion_inicial,$largo_maximo);
        $resultado=$offset_inicial."".str_ireplace($busqueda,'<b>'.strtoupper($busqueda).'</b>',$cadena)."".$offset_final;
    }
    return($resultado);
}
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