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
function show_parcial($texto,$largo)
{
    $final="";
    if(strlen($texto)>$largo) $final="...";
    return(substr($texto,0,$largo).''.$final);
}
function show_transcurrido($minutos)
{
    $respuesta="";
    $minutos_restantes=intval($minutos);

    $dias=$minutos_restantes/1440;
    if ($dias>=1)
    {
        $dias=intval($dias);
        $respuesta=$dias.'d ';
        $minutos_restantes=$minutos_restantes-(1440*$dias);
    }
    $horas=$minutos_restantes/60;
    if ($horas>=1)
    {
        $horas=intval($horas);
        $respuesta=$respuesta.''.$horas.'h ';
        $minutos_restantes=$minutos_restantes-(60*$horas);
    }
    $respuesta=$respuesta.''.$minutos_restantes.'m';
    return($respuesta);
}