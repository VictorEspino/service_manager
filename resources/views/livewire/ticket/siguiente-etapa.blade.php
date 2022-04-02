<form  action="{{route('avanzar_etapa')}}" method="POST" enctype="multipart/form-data" id="cambio_etapa">
    @csrf
    <button {{$actividad_actual==$actividades_total-1?'disabled':''}} class='inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition' wire:click.prevent="open_avanzar_modal">Siguiente</button>
<x-jet-dialog-modal wire:model="open_avanzar" maxWidth="3xl">        
    <x-slot name="title">
        Avanzar a etapa : {{$siguiente_etapa_nombre}}
        <input type="hidden" name="id" value="{{$ticket_id}}">
        <input type="hidden" name="id_sig_etapa" value="{{$siguiente_etapa_id}}">
        <input type="hidden" name="nombre" value="{{$siguiente_etapa_nombre}}">
    </x-slot>
    <x-slot name="content">
        <div class="w-full flex flex-col">
            <div class="py-2 w-full flex flex-row">
                <div class="px-4 w-32 flex justify-end items-start">
                    <x-jet-label value="Descripcion"/>
                </div>
                <div class="flex-1 flex flex-col">
                    <div class="w-full">
                        <textarea rows=8 class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" wire:model.defer="siguiente_etapa_descripcion" name="descripcion"></textarea>
                        @error('siguiente_etapa_descripcion') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                    @foreach ($siguiente_etapa_campos as $index=>$campo)
                    <div class="w-full px-2 py-1 flex flex-row items-center">
                        <div class="w-1/4">
                            <x-jet-label class="text-gray-400 font-bold" value="{{$campo['etiqueta']}}" />
                        </div>
                        <div class="flex-1 px-2">
                            <input type="hidden" name="campos[{{$index}}][tipo]" value="{{$campo['tipo_control']}}">
                            <input type="hidden" name="campos[{{$index}}][etiqueta]" value="{{$campo['etiqueta']}}">
                            <input type="hidden" name="campos[{{$index}}][referencia]" value="{{$campo['referencia']}}">
                            <input type="hidden" name="campos[{{$index}}][requerido]" value="{{$campo['requerido']}}" wire:model.defer="siguiente_etapa_campos.{{$index}}.requerido">
                            @if($campo['tipo_control']=="Texto")
                                <x-jet-input name="campos[{{$index}}][valor]" class="w-full text-sm flex flex-1" type="text" wire:model="siguiente_etapa_campos.{{$index}}.valor"/>
                            @endif
                            @if($campo['tipo_control']=="CheckBox")
                                <x-jet-checkbox name="campos[{{$index}}][valor]" class="ml-2 text-sm" wire:model.defer="siguiente_etapa_campos.{{$index}}.valor"/>
                            @endif
                            @if($campo['tipo_control']=="File")
                                <input type="file" class="p-2 w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="campos[{{$index}}][valor]" class="text-sm" wire:change="archivo_seleccionado($event.target.value,'siguiente_etapa_campos.{{$index}}.valor')"/>
                                <input type="hidden" wire:model="siguiente_etapa_campos.{{$index}}.valor">
                            @endif
                            @if($campo['tipo_control']=="Lista")
                            <select name="campos[{{$index}}][valor]" class="text-xs flex-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model.defer="siguiente_etapa_campos.{{$index}}.valor">
                                <option value=""></option>
                                @php
                                    $valores=App\Models\ListaValores::where('lista_id',$campo['lista_id'])->orderBy('id','asc')->get();
                                    foreach ($valores as $valor) {
                                @endphp   
                                    <option value="{{$valor->valor}}">{{$valor->valor}}</option>
                                @php
                                    } 
                                @endphp
                            </select><br />
                            @endif
                            @error('siguiente_etapa_campos.'.$index.'.valor')<span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endforeach                   
                </div>
            </div>
            @if($siguiente_etapa_asignacion_seleccionable)
            <div class="w-full flex flex-col">
                <div class="py-2 w-full flex flex-row">
                    <div class="px-4 w-32 flex justify-end items-start">
                        <x-jet-label value="Sera atendido por:"/>
                    </div>
                    <div class="">
                        <select wire:model="siguiente_etapa_atencion_seleccionada" name="siguiente_etapa_atencion_seleccionada" class="text-xs w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option></option>
                            @foreach ($siguiente_etapa_usuarios_disponibles as $seleccionable)
                            <option value="{{$seleccionable->user->id}}">{{$seleccionable->user->name}}</option> 
                            @endforeach                           
                        </select>
                        @error('siguiente_etapa_atencion_seleccionada')<br /><span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>                
            </div>
            @endif        
        </div>
    </x-slot>
    <x-slot name="footer">
        <x-jet-secondary-button wire:click.prevent="cancelar">Cancelar</x-jet-secondary-button>
        <button {{$procesando==1?'disabled':''}} class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition' wire:click.prevent="avanzar_etapa">Avanzar</button>
    </x-slot>        
</x-jet-dialog-modal>
</form>