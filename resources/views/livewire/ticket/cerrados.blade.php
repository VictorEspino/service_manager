<div>
    <x-slot name="header">
        {{ __('Cerrados') }}
    </x-slot>
    <x-ticket-nav />
    <div class="w-full flex flex-col px-5 md:py-6">
        <div class="w-full">
            <x-jet-section-title>
                <x-slot name="title">Tickets Cerrados</x-slot>
                <x-slot name="description">Permite visualizar todos los tickets abiertos atendidos por los grupos de los cuales eres miembro o has sido invitado</x-slot>
            </x-jet-section-title>
        </div>
    </div>
    <div class="w-full px-5 pt-6">
        <div class="w-full flex items-center text-sm text-gray-600">
            <div class="px-5">
                <span>Mostrar </span>
                <select wire:model="elementos" class="text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value=5>5</option>
                    <option value=10>10</option>
                    <option value=20>20</option>
                    <option value=30>30</option>
                    <option value=50>50</option>
                </select>  
                <span> registros</span> 
            </div>
            <div class="flex px-5 items-center">
                <span>Asesor&nbsp;&nbsp;&nbsp;</span>
                <select wire:model="filtro_asesor" class="text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="-1"></option>
                    <option value=0>Por Asignar</option>
                    @foreach($asesores as $asesor)
                    <option value={{$asesor->id}}>{{$asesor->name}}</option>
                    @endforeach
                </select>                  
            </div>
            <div class="flex flex-1 px-5">
                <x-jet-input class="flex-1 text-sm mr-5" type="text"  wire:model="filtro" placeholder="¿Que desea buscar?"/>
            </div>
            
        </div>
    </div>
    <div class="w-full px-5 pt-6">
        {{$tickets->links()}}
    </div>
    <div class="w-full flex flex-col space-y-3 py-5 px-5">
        @php
        $registros=0;   
        @endphp
        @foreach ($tickets as $ticket)
            @php
            $registros=$registros+1;   
            @endphp
            <div class="w-full flex flex-row bg-white rounded-lg shadow-lg p-3 border border-blue-200">
                <div class="w-20 text-gray-700 font-semibold text-xl px-3">
                    <div class="w-full py-1 px-3 {{$ticket->prioridad=="1"?'bg-green-400':'bg-red-400'}} text-gray-100 text-xs font-semibold flex justify-center rounded">
                        {{$ticket->prioridad=="1"?'Normal':'Alta'}}
                    </div>
                </div>
                <div class="w-48 text-gray-700 font-semibold text-xs px-3">
                    Atiende: {{$ticket->asesor->name}}
                </div>
                <div class="w-1/2 flex flex-col">
                    <div class="w-full text-lg text-blue-500">
                        <a href="{{route('ticket',['id'=>$ticket->id])}}">[{{ticket($ticket->id)}}] - {{$ticket->asunto}}</a>
                    </div>
                    <div class="w-full text-xs font-normal">
                        Topico: {{$ticket->topico->nombre}}
                    </div>
                    <div class="w-full text-xs font-normal">
                        Por: {{$ticket->solicitante->name}}
                    </div>
                    <div class="w-full text-xs font-normal">
                        Creado: {{$ticket->created_at}}
                    </div>
                </div>
                <div class="flex-1 flex flex-col">
                    <div class="w-full flex flex-row text-xs font-bold border-b">
                        <div class="w-6"></div>
                        <div class="w-2/3">
                            Activ
                        </div>
                        <div class="w-1/3">
                            Obj
                        </div>
                        <div class="w-1/3">
                            Trans
                        </div>
                        <div class="w-1/3">
                        </div>
                    </div>
                    @foreach ($ticket->actividades as $actividad)
                        @php
                            $actividad_actual=$ticket->actividad_actual;
                            $minutos_transcurridos=0;
                            if($actividad->secuencia==0)
                            {
                                $minutos_transcurridos=$ticket->t_a0;
                            }
                            if($actividad->secuencia==1)
                            {
                                $minutos_transcurridos=$ticket->t_a1;
                            }
                            if($actividad->secuencia==2)
                            {
                                $minutos_transcurridos=$ticket->t_a2;
                            }
                            if($actividad->secuencia==3)
                            {
                                $minutos_transcurridos=$ticket->t_a3;
                            }
                            if($actividad->secuencia==4)
                            {
                                $minutos_transcurridos=$ticket->t_a4;
                            }
                            if($actividad->secuencia==5)
                            {
                                $minutos_transcurridos=$ticket->t_a5;
                            }
                            if($actividad->secuencia==6)
                            {
                                $minutos_transcurridos=$ticket->t_a6;
                            }
                            if($actividad->secuencia==7)
                            {
                                $minutos_transcurridos=$ticket->t_a7;
                            }
                            if($actividad->secuencia==8)
                            {
                                $minutos_transcurridos=$ticket->t_a8;
                            }
                            if($actividad->secuencia==9)
                            {
                                $minutos_transcurridos=$ticket->t_a9;
                            }
                            $minutos_transcurridos=$minutos_transcurridos/60;
                            $bandera="";
                            if($actividad_actual==$actividad->secuencia)
                            {
                                $minutos_transcurridos=$minutos_transcurridos+$ticket->min_adicional;
                                $bandera="->";
                            }
                            $leyenda="";
                            if($actividad->secuencia==0)
                            {
                                $leyenda=" (INICIAL)";
                            }

                        @endphp
                        <div class="w-full flex flex-row text-xs border-b">
                            <div class="w-6 font-bold text-blue-500">{{$bandera}}</div>
                            <div class="w-2/3">
                                {{$actividad->nombre}}{{$leyenda}}
                            </div>
                            <div class="w-1/3">
                                {{$actividad->sla}}
                            </div>
                            <div class="w-1/3">
                                {{number_format($minutos_transcurridos,0)}}
                            </div>
                            <div class="w-1/3 font-bold">
                                @if($actividad->sla>=$minutos_transcurridos)
                                <span class="text-green-500">OK</span>
                                @else
                                <span class="text-red-500">FAIL</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        @if($registros==0)
            No se encontraron registros
        @endif
    </div>
</div>
