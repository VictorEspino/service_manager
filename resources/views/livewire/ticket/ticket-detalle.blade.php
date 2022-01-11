<div>
    <x-slot name="header">
        {{ __('Detalle Ticket') }}
    </x-slot>
    <div class="w-full flex flex-row">
        <div class="w-3/4">
            {{ticket($ticket_id)}} - {{$asunto}}<br>
            {{$topico_nombre}}<br>
            {{$solicitante}}
        </div>
        <div class="flex-1">
            {{ticket($ticket_id)}}
        </div>
    </div>
</div>
