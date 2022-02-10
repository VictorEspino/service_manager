<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=listado_tickets.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr>
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
<td style="background-color:#0000FF;color:#FFFFFF"><b>Tiempo Act 1 (min)</td>
</tr>
<?php

foreach ($listado as $registro) {
	?>
<tr>
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
</tr>
<?php
}
?>
</table>