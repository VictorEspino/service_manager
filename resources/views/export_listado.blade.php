<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=listado_tickets.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr>
    <td style="background-color:#0000FF;color:#FFFFFF"><b>Folio</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Creacion</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Solicitante</td>    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Autorizacion</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Asunto</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Prioridad</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Minutos Objetivo</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Estatus</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Ultima Atencion</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Cierra</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Fecha Cierre</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Solicitante (min)</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 1 (min)</td>
@if($n_actividades>=2)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 2 (min)</td>
@endif 
@if($n_actividades>=3)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 3 (min)</td>
@endif 
@if($n_actividades>=4)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 4 (min)</td>
@endif 
@if($n_actividades>=5)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 5 (min)</td>
@endif 
@if($n_actividades>=6)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 6 (min)</td>
@endif 
@if($n_actividades>=7)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 7 (min)</td>
@endif 
@if($n_actividades>=8)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 8 (min)</td>
@endif 
@if($n_actividades>=9)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 9 (min)</td>
@endif  
@if($n_actividades>=10)    
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Actividad 10 (min)</td>
@endif    
</tr>
<?php

foreach ($listado as $registro) {
	?>
<tr>
    <td style="background-color:#FFFFFF;color:#000000">{{ticket($registro->id)}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$registro->created_at}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$usuarios[$registro->de_id]}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$registro->emite_autorizacion==1?($registro->resultado_autorizacion==1?'AUTORIZADO':'RECHAZADO'):'NA'}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$registro->asunto}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$registro->prioridad==1?'Normal':'Alta'}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$registro->n_minutos}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$registro->estatus==1?'Abierto':'Cerrado'}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$usuarios[$registro->asignado_a]}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$registro->nombre_cerrador}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{$registro->cierre_at}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_solicitante)/60,0)}}</td>
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a0)/60,0)}}</td>
    @if($registro->n_actividades>=2)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a1)/60,0)}}</td>
    @endif
    @if($registro->n_actividades>=3)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a2)/60,0)}}</td>
    @endif
    @if($registro->n_actividades>=4)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a3)/60,0)}}</td>
    @endif
    @if($registro->n_actividades>=5)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a4)/60,0)}}</td>
    @endif
    @if($registro->n_actividades>=6)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a5)/60,0)}}</td>
    @endif
    @if($registro->n_actividades>=7)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a6)/60,0)}}</td>
    @endif
    @if($registro->n_actividades>=8)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a7)/60,0)}}</td>
    @endif
    @if($registro->n_actividades>=9)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a8)/60,0)}}</td>
    @endif
    @if($registro->n_actividades>=10)
    <td style="background-color:#FFFFFF;color:#000000">{{number_format(($registro->t_a9)/60,0)}}</td>
    @endif
</tr>
<?php
}
?>
</table>