<div>
    <x-slot name="header">
        {{ __('Detalle Ticket') }}
    </x-slot>
    <x-ticket-nav />
    <div class="w-full flex flex-row bg-white h-screen">
        <div class="flex-1 p-4 flex flex-col text-gray-600">
            <div class="w-full">
                <div class="text-xl font-semibold text-gray-600">Ticket: {{ticket($ticket_id)}} - {{$asunto}}</div>
                <div class="text-xs pb-6">{{$topico_nombre}}</div>
            </div>
            <div class="w-full overflow-y-scroll pr-4">
                <div class="w-full rounded border bg-gray-200 px-2 py-3 flex flex-col py-6">
                    <form action="{{route('save_avance')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$ticket_id}}">
                        <input type="hidden" name="solicitante" value="{{$solicitante_id}}">
                        <input type="hidden" name="estatus" value="{{$estatus}}">
                        <div class="flex flex-row">
                            <div class="w-24 flex justify-center text-base font-bold text-gray-600">
                                Avance
                            </div>
                            <div class="flex-1">
                                <textarea rows=3 class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" name="avance"  wire:avance.defer="descripcion_topico"></textarea>
                            </div>
                        </div>
                        <div class="flex flex-row py-3">
                            <div class="w-24">
                            &nbsp; 
                            </div>
                            <div class="flex-1 text-sm text-gray-600">
                                <x-jet-secondary-button wire:click.prevent="$set('file_include',true)"><i class="fas fa-plus"></i>&nbsp;&nbsp;Adjuntar archivo</x-jet-secondary-button>
                            </div>
                        </div>
                        @if($file_include)
                        <div class="flex flex-row pt-3">
                            <div class="w-24 flex justify-center text-base font-bold text-gray-600">                        
                            </div>
                            <div class="flex-1 flex flex-row items-center">
                                <x-jet-danger-button wire:click.prevent="$set('file_include',false)" class="py-1"><i class="fas fa-times-circle text-base"></i></x-jet-danger-button>
                                <input type="file" class="p-2 flex-1 text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="adjunto" class="text-sm"/>                
                            </div>
                        </div>
                        @endif
                        <div class="flex flex-row pt-3">
                            <div class="w-24">
                            &nbsp; 
                            </div>
                            <div class="flex-1 text-sm text-gray-600">
                                <x-jet-checkbox wire:model.defer="cerrar_al_responder" name="cerrar_al_responder"/>Cerrar ticket al responder
                            </div>
                        </div>
                        <div class="flex flex-row">
                            <div class="w-24">
                            &nbsp; 
                            </div>
                            <div class="flex-1 text-sm text-gray-600">
                                <x-jet-checkbox wire:model.defer="esperando_respuesta" name="esperando_respuesta"/>Marcar como "Esperando respuesta" despues de responder
                            </div>
                        </div>
                        <div class="flex flex-row">
                            <div class="w-24">
                            &nbsp; 
                            </div>
                            <div class="flex-1 text-sm text-gray-600 flex justify-end">
                                <x-jet-button ><i class="fas fa-comment"></i>&nbsp;&nbsp;&nbsp;Publicar</x-jet-button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="w-full">
                    <div class="w-full flex justify-end flex-row pt-4">
                        <div class="px-2 bg-amber-200 rounded-md"></div>
                        <div class="text-sm flex items-center">&nbsp;Nota interna</div>
                        <div class="px-2 bg-lime-200 rounded-md ml-3"></div>
                        <div class="text-sm flex items-center">&nbsp;Creador del ticket</div>
                        <div class="px-2 bg-sky-200 rounded-md ml-3"></div>
                        <div class="text-sm flex items-center">&nbsp;Staff asignado</div>
                    </div>
                </div>
                <div class="w-full pt-4 flex flex-col">
                    @php
                        $indice_final=count($avances_ticket);
                    @endphp
                    @for ($i = $indice_final; $i > 0; $i--)                    

                    <div class="w-full flex pt-4 text-sm {{$avances_ticket[$i-1]['tipo_avance']=='1'?'':'justify-end'}}">
                        <div class="w-10/12">
                            <span class="mx-3 font-bold">{{$avances_ticket[$i-1]['nombre']}}</span>
                            <span class="mx-4 text-xs">{{$avances_ticket[$i-1]['created_at']}}</span>
                        </div>

                    </div>
                    <div class="w-full flex {{$avances_ticket[$i-1]['tipo_avance']=='1'?'':'justify-end'}}">
                        <div class="w-10/12 flex-row">
                            <div class="px-2 {{$avances_ticket[$i-1]['tipo_avance']=='1'?'bg-lime-100':($avances_ticket[$i-1]['tipo_avance']=='2'?'bg-amber-100':'bg-sky-100')}} rounded-md py-3 px-5 text-xs font-semibold">
                                {!!nl2br($avances_ticket[$i-1]['avance'])!!}
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
        <div class="w-64 bg-blue-100 border-l-8 p-4 flex flex-col">
            <div class="w-full flex flex-row justify-center">
                <div class="rounded py-1 px-3 {{$estatus=='1'?'bg-lime-500':($estatus=='2'?'bg-red-500':'bg-gray-500')}} text-3xl font-extrabold text-gray-100">
                    {{$estatus=='1'?'abierto':($estatus=='2'?'cerrado':'terminado')}}
                </div>
            </div>
            <div class="w-full flex flex-row justify-center pt-3">
                <div class="py-1 px-3">
                    <x-jet-secondary-button wire:click.prevent="open_modal_confirm_status">{{$valor_boton_cambio_estatus}}</x-jet-secondary-button>
                </div>
            </div>
            <div class="w-full flex flex-row justify-center pt-6">
                <div class="py-1 px-3 font-bold text-gray-500 text-sm">
                    Asesor <span class="text-xs text-blue-500" style="cursor: pointer;" wire:click="open_reasignar_modal">reasignar</span>
                </div>
            </div>
            <div class="w-full flex flex-row justify-center text-sm">
                <div class="py-1 px-3 font-normal text-blue-500">
                    {{$asesor}}
                </div>
            </div>
            <div class="w-full flex flex-row justify-center pt-6 space-x-3">
                <div class="w-1/2 flex flex-col border ">
                    <div class="w-full flex justify-center font-bold text-gray-500 text-sm">Prioridad</div>
                    <div class="w-full flex justify-center pt-3">
                        <div class="w-20 flex justify-center px-2 items-start">
                            <div class="w-full py-1 px-3 bg-green-400 text-gray-100 text-xs font-semibold flex justify-center rounded">
                                Normal
                            </div>
                        </div>

                    </div>
                </div>
                <div class="w-1/2 flex flex-col ">
                    <div class="w-full flex justify-center font-bold text-gray-500 text-sm">Privacidad</div>
                    <div class="w-full flex justify-center pt-3">
                        <div class="w-20 flex justify-center px-2 items-start">
                            <div class="w-full py-1 px-3 bg-green-400 text-gray-100 text-xs font-semibold flex justify-center rounded">
                                Privado
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full flex flex-row justify-center pt-6">
                <div class="py-1 px-3 font-bold text-gray-500 text-sm">
                    Solicitante
                </div>
            </div>
            <div class="w-full flex flex-row justify-center text-sm">
                <div class="py-1 px-3 font-normal text-blue-500">
                    {{$solicitante}}
                </div>
            </div>
            <div class="w-full flex flex-row justify-center pt-6">
                <div class="py-1 px-3 font-bold text-gray-500 text-sm">
                    Otros invitados al ticket
                </div>
            </div>
            <div class="w-full flex flex-row justify-center text-sm">
                <div class="py-1 px-3 font-normal text-blue-500">
                    Administrador de sistema
                </div>
            </div>
        </div>
    </div>
    <x-jet-dialog-modal wire:model="open_confirm_status" maxWidth="md">
        <x-slot name="title">
            Cambio de estatus
        </x-slot>
        <x-slot name="content">
            <div class="w-full flex flex-row">
                <div class="w-24 text-7xl text-amber-500 flex justify-center py-8"><i class="far fa-question-circle"></i></div>
                <div class="flex-1 text-sm text-gray-600 px-5 flex flex-col items-center">  
                    <div class="pt-4">
                    Esta accion cambiara el estatus del ticket a <b>{{$nuevo_posible_estatus}}</b><br><br>
                    </div>
                    <div>
                    ¿Desea continuar?
                    </div>
                </div>
            </div> 
            
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click.prevent="$set('open_confirm_status',false)">Cancelar</x-jet-secondary-button>
            <button {{$procesando==1?'disabled':''}} class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition' wire:click.prevent="cambio_estatus">Confirmar</button>
        </x-slot>
    </x-jet-dialog-modal>
    <x-jet-dialog-modal wire:model="open_reasignar" maxWidth="lg">
        <x-slot name="title">
            Reasignar ticket {{$miembro_seleccionado}}
        </x-slot>
        <x-slot name="content">
            <div class="w-full flex flex-col">
                <div class="py-2 w-full flex flex-row">
                    <div class="px-4 w-32 flex justify-end items-center">
                        <x-jet-label value="Grupo"/>
                    </div>
                    <div class="flex-1">
                        <select class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model='grupo_seleccionado'>
                            <option value=""></option>
                            @foreach($grupos_disponibles as $grupo)
                            <option value={{$grupo->id}}>{{$grupo->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="py-2 w-full flex flex-row">
                    <div class="px-4 w-32 flex justify-end items-center">
                        <x-jet-label value="Staff asignado"/>
                    </div>
                    <div class="flex-1">
                        <select class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model='miembro_seleccionado'>
                            <option value=""></option>
                            @foreach($miembros_disponibles as $miembro)
                            <option value={{$miembro->user_id}}>{{$miembro->user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="py-2 w-full flex flex-row">
                    <div class="px-4 w-32 flex justify-end items-center">
                        <x-jet-label value="Mensaje"/>
                    </div>
                    <div class="flex-1">
                        <x-jet-input type="text" wire:model="mensaje_reasignacion" class="w-full"/>
                    </div>
                </div>
            
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click.prevent="$set('open_reasignar',false)">Cancelar</x-jet-secondary-button>
            <button {{$procesando==1?'disabled':''}} class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition' wire:click.prevent="reasignar">Reasignar</button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
