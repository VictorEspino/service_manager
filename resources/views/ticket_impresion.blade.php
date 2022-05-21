<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}?{{rand()}}">
    <script src="https://kit.fontawesome.com/a692f93986.js" crossorigin="anonymous"></script>
    <title>Ticket {{ticket($ticket_id)}}</title>
</head>
<body class="p-8">
    <div class="text-lg font-bold py-2">Ticket</div>
    <div class="text-base font-bold pt-2">{{ticket($ticket_id)}} | {{$ticket->asunto}}</div>
    <div class="text-xs font-bold border-b">{{$ticket->topico->nombre}}</div>
    <div class="text-xs pt-2"><span class="font-bold">Autor:</span> {{$ticket->solicitante->name}} - {{$ticket->created_at}}</div>
    <div class="text-xs"><span class="font-bold">Puesto:</span> {{$ticket->solicitante->puesto_desc->puesto}}</div>
    <div class="text-xs"><span class="font-bold">Area solicitante:</span> {{$ticket->area_solicitante->nombre}}</div>
    <div class="text-xs border-b"><span class="font-bold">Subarea solicitante:</span> {{$ticket->subarea_solicitante->nombre}}</div>
    <div class="text-xs pt-2"><span class="font-bold">Asesor:</span> {{$ticket->asesor->name}}</div>
    <div class="text-xs pt-2"><span class="font-bold">Estatus:</span> {{$ticket->estatus==1?'Abierto':'Cerrado'}}</div>
    <div class="text-xs border-b"> {{$ticket->cierre_at}}</div>
    <div class="text-xs py-2 border-b"><span class="font-bold">Otros invitados al ticket:</span> <br>
    @foreach($invitados as $invitado_ticket)
        <span class="font-bold">{{$invitado_ticket['user']}}</span> ({{$invitado_ticket['area']}} - {{$invitado_ticket['subarea']}}) |
    @endforeach
    </div>
    <div class="text-lg py-2 border-b"><span class="font-bold">Avances</span></div>
    @php
        $indice_final=count($avances_ticket);
        for ($i = $indice_final; $i > 0; $i--)  
        {
    @endphp
    <div class="text-xs border-b py-2">
        <span class="font-bold">{{$avances_ticket[$i-1]['nombre']}}</span> {{$avances_ticket[$i-1]['created_at']}}<br><br>
        {!!nl2br($avances_ticket[$i-1]['avance'])!!}
    </div>
    @php
        }
    @endphp
</body>
</html>
