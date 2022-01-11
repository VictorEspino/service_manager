<div>
    <x-slot name="header">
        {{ __('Detalle Ticket') }}
    </x-slot>
    <x-ticket-nav />
    <div class="w-full flex flex-row bg-white h-screen">
        <div class="flex-1 p-4 flex flex-col text-gray-600">
            <div class="w-full">
                <div class="text-xl font-semibold text-gray-600">Ticket: {{ticket($ticket_id)}} - {{$asunto}}</div>
                <div class="text-xs">{{$topico_nombre}}</div>
                <div class="text-base pt-4 pb-6">Solicitante: {{$solicitante}}</div>
            </div>
            <div class="w-full rounded border bg-gray-200 px-2 py-3 flex flex-col py-6">
                <form action="{{route('save_avance')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$ticket_id}}">
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
            <div class="w-full pt-4 flex flex-col overflow-y-scroll">
                <div class="w-full flex justify-end flex-row pt-4">
                    <div class="px-2 bg-amber-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex flex-row pt-4">
                    <div class="px-2 bg-lime-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex justify-end flex-row pt-4">
                    <div class="px-2 bg-amber-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex flex-row pt-4">
                    <div class="px-2 bg-lime-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex justify-end flex-row pt-4">
                    <div class="px-2 bg-amber-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex flex-row pt-4">
                    <div class="px-2 bg-lime-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex justify-end flex-row pt-4">
                    <div class="px-2 bg-amber-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex flex-row pt-4">
                    <div class="px-2 bg-lime-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex justify-end flex-row pt-4">
                    <div class="px-2 bg-amber-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
                <div class="w-full flex flex-row pt-4">
                    <div class="px-2 bg-lime-100 rounded-md py-3 px-5 h-20 text-sm font-semibold">
                        no sabria que decirle señorita cuando le veo las nalgas y recuerdo que tuve mi verga en su ano hasta deslecharme
                    </div>
                </div>
            </div>
            
        </div>
        <div class="w-64 bg-blue-100 border-l-8">
            {{ticket($ticket_id)}}
        </div>
    </div>
</div>
