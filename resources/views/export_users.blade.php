<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=export_users.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<!--<td><b>ID</td>-->
<td><b># Empleado/User</td>
<td><b>Nombre Completo</td>
<!--<td><b>Email</td>-->
<td><b>Puesto</td>
<td><b>Area</td>
<td><b>Subarea</td>
<td><b>Perfil</td>
<!--<td><b>Admin Plantilla</td>-->
<td><b>Estatus</td>
</tr>
<?php
foreach ($usuarios as $usuario) {
	?>
	<tr>
    <!--<td>{{$usuario->id}}</td>    -->
	<td>{{$usuario->user}}</td>
	<td>{{$usuario->name}}</td>
	<!--<td>{{$usuario->email}}</td> -->
	<td>{{$usuario->puesto_desc->puesto}}</td>
	<td>{{$usuario->area_user->nombre}}</td>
    <td>{{$usuario->subarea->nombre}}</td>
	<td>{{$usuario->perfil}}</td>
	<!--<td>{{$usuario->carga_empleados=='1'?'SI':'NO'}}</td>-->
	<td>{{$usuario->estatus=='1'?'Activo':'Inactivo'}}</td>
	</tr>
<?php
}
?>
</table>