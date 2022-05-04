<form action="{{route('save_ticket')}}" method="POST" enctype="multipart/form-data" id="nuevo_ticket">
@csrf
<div>
    <x-jet-danger-button wire:click.prevent="$set('open',true)" class="bg-green-500 hover:bg-green-700 border-green-600"><b> + </b>CREAR NUEVO TICKET</x-jet-danger-button>

    <x-jet-dialog-modal wire:model="open" maxWidth="5xl">
        <x-slot name="title">
            Crear nuevo ticket
        </x-slot>
        <x-slot name="content">
            
            <div class="flex flex-row w-full">
                <div class="flex flex-col w-9/12 border rounded shadow">
                    <table class="w-full table-auto border">
                        <tr class="p-2 border">
                            <td class="w-1/12 border py-2">
                                <x-jet-label class="text-gray-400 font-bold" value="De" />
                            </td>
                            <td class="flex-1 flex py-2 px-2 text-sm text-gray-500 font-semibold items-center">
                                {{$de_etiqueta}}
                                <!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="text-sm text-blue-500 hover:underline" wire:click="$set('cambiar_usuario',true)">cambiar</span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="text-sm text-blue-500 hover:underline" wire:click="reset_usuario">reset</span>
                                -->
                                <input type="hidden" name="de_id" wire:model="de_id">
                            </td>
                        </tr>
                        @if($cambiar_usuario)
                        <tr class="p-2 border">
                            <td></td>
                            <td class="w-full flex py-2 px-2 text-sm text-gray-500 font-semibold items-center">
                                <div class="flex flex-col w-full">
                                    <div class="w-full flex items-center">
                                        <x-jet-label class="text-gray-400 font-bold" value="Buscar" /> 
                                        <x-jet-input class="text-sm flex flex-1 ml-2 mr-2" type="text" wire:model="buscar_usuario"/>
                                    </div>
                                    @if (is_array($usuarios_disponibles) || is_object($usuarios_disponibles))
                                    <div class="w-full flex justify-center pt-2">
                                        <table>
                                            <tr class="rounded-tl rounded-tr">
                                                <td class="border bg-blue-500 text-white px-2 rounded-tl">User</td>
                                                <td class="border bg-blue-500 text-white px-2">Nombre</td>
                                                <td class="border bg-blue-500 text-white px-2 rounded-tr"></td>
                                            </tr>

                                        @foreach ($usuarios_disponibles as $opcion)
                                            <tr>
                                                <td class="border px-2">{{$opcion->user}}</td>
                                                <td class="border px-2">{{$opcion->name}}</td>
                                                <td class="border px-3"><center><i wire:click="cambiar_usuario({{$opcion->id}},'{{$opcion->name}}','{{$opcion->email}}')" class="text-green-500 text-lg far fa-hand-pointer" style="cursor:pointer"></i></td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </div>
                                @endif
                                </div>
                            </td>
                        </tr>

                        @endif
                        
                        <tr class="p-2 border">
                            <td class="w-1/12 border py-2">
                                <x-jet-label class="text-gray-400 font-bold" value="Participantes" />
                            </td>
                            <td class="w-full flex py-2 px-2 text-sm text-gray-500 font-semibold items-center">
                                <div class="flex flex-col w-full">                                    
                                    @if (is_array($invitados_ticket) || is_object($invitados_ticket))
                                    <div class="w-full flex flex-wrap">
                                        @foreach($invitados_ticket as $index=>$invitado)
                                        <div class="ml-2 mb-2 bg-lime-300 border py-2 px-3 text-sm rounded">
                                            {{$invitado['nombre']}}
                                            <span wire:click="borrar_invitado_ticket({{$index}})" style="cursor: pointer;" class="p-2 text-xs font-bold bg-lime-500 rounded border border-gray-300">X</span>
                                            <input type="hidden" name="invitados[{{$index}}][id]" value="{{$invitado['id']}}">
                                        </div>    
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="w-full flex items-center">
                                        <x-jet-input class="text-sm flex flex-1" type="text" wire:model="buscar_invitado" placeholder='Buscar'/>
                                    </div>
                                    @if ($agregar_invitado)
                                    <div class="w-full flex justify-center pt-2">
                                        <table>
                                            <tr class="rounded-tl rounded-tr">
                                                <td class="border bg-blue-500 text-white px-2 rounded-tl">User</td>
                                                <td class="border bg-blue-500 text-white px-2">Nombre</td>
                                                <td class="border bg-blue-500 text-white px-2 rounded-tr"></td>
                                            </tr>

                                        @foreach ($invitados_disponibles as $opcion)
                                            <tr>
                                                <td class="border px-2">{{$opcion->user}}</td>
                                                <td class="border px-2">{{$opcion->name}}</td>
                                                <td class="border px-3"><center><i wire:click="agregar_invitado_ticket({{$opcion->id}},'{{$opcion->name}}','{{$opcion->email}}')" class="text-green-500 text-lg far fa-hand-pointer" style="cursor:pointer"></i></td>
                                            </tr>
                                        @endforeach
                                        </table>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr class="p-2 border">
                            <td class="w-1/12 py-2 border">
                                <x-jet-label class="text-gray-400 font-bold" value="Topico" />
                            </td>
                            <td class="flex-1 flex py-2">
                                <div class="w-full flex flex-col px-2">
                                    <div class="w-full">
                                        <x-jet-label class="text-gray-400 text-xs" value="Grupo" />
                                        <select wire:model="grupo" class="w-full text-xs border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                            <option value=""></option>
                                            @foreach ($grupos as $grupo_select)
                                                <option value='{{$grupo_select->id}}'>{{$grupo_select->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-full">
                                        <x-jet-label class="text-gray-400 text-xs" value="Topico" />
                                        <select wire:model="topico" name="topico" class="text-xs w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                            <option value=""></option>
                                            @if (is_array($topicos_disponibles) || is_object($topicos_disponibles))
                                                @foreach ($topicos_disponibles as $topico_opcion)
                                                @if($topico_opcion->topico->estatus=='1')
                                                    <option value="{{$topico_opcion->topico->id}}">{{$topico_opcion->topico->nombre}}</option>
                                                @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="w-full">
                                        @error('topico')<span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                    </div>
                            </div>
                            
                                <input name="emite_autorizacion" wire:model="emite_autorizacion" type="hidden">
                            </td>
                        </tr>
                        <tr class="p-2 border">
                            <td class="w-1/12 border py-2">
                                <x-jet-label class="text-gray-400 font-bold" value="Asunto" />
                            </td>
                            <td class="flex-1 flex py-2">
                                <x-jet-input class="text-sm flex flex-1 ml-2 mr-2" type="text" name="asunto" wire:model.defer="asunto"/><br/>
                                @error('asunto')<span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </td>
                        </tr>
                        <tr class="p-2 border">
                            <td class="w-1/12 border py-2">
                                <x-jet-label class="text-gray-400 font-bold" value="Descripcion" />
                            </td>
                            <td class="flex-1 flex py-2">
                                <div class="w-full flex flex-col">
                                    <div class="w-full px-2">
                                        <textarea rows=10 class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" name="descripcion"  wire:model.defer="descripcion_topico"></textarea>
                                        @error('descripcion_topico')<span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    @if (is_array($campos_requeridos) || is_object($campos_requeridos))
                                        @foreach ($campos_requeridos as $index=>$campo)
                                            <div class="w-full px-2 py-1 flex flex-row items-center">
                                                <div class="w-1/4">
                                                    <x-jet-label class="text-gray-400 font-bold" value="{{$campo['etiqueta']}}" />
                                                </div>
                                                <div class="flex-1 px-2">
                                                    <input type="hidden" name="campos[{{$index}}][tipo]" value="{{$campo['tipo_control']}}">
                                                    <input type="hidden" name="campos[{{$index}}][etiqueta]" value="{{$campo['etiqueta']}}">
                                                    <input type="hidden" name="campos[{{$index}}][referencia]" value="{{$campo['referencia']}}">
                                                    <input type="hidden" name="campos[{{$index}}][requerido]" value="{{$campo['requerido']}}">
                                                    @if($campo['tipo_control']=="Texto")
                                                        <x-jet-input name="campos[{{$index}}][valor]" class="w-full text-sm flex flex-1" type="text" wire:model="campos_requeridos.{{$index}}.valor"/>
                                                    @endif
                                                    @if($campo['tipo_control']=="CheckBox")
                                                        <x-jet-checkbox name="campos[{{$index}}][valor]" class="ml-2 text-sm" wire:model="campos_requeridos.{{$index}}.valor"/>
                                                    @endif
                                                    @if($campo['tipo_control']=="File")
                                                        <input type="file" class="p-2 w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="campos[{{$index}}][valor]" class="text-sm" wire:change="archivo_seleccionado($event.target.value,'campos_requeridos.{{$index}}.valor')"/>
                                                        <input type="hidden" wire:model="campos_requeridos.{{$index}}.valor">
                                                    @endif
                                                    @if($campo['tipo_control']=="Lista")
                                                    <select name="campos[{{$index}}][valor]" class="text-xs flex-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model="campos_requeridos.{{$index}}.valor">
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
                                                    @error('campos_requeridos.'.$index.'.valor')<span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Adjuntar</td>
                            <td><x-jet-secondary-button wire:click.prevent="$set('file_include',true)"><i class="fas fa-plus"></i></x-jet-secondary-button></td>
                        </tr>
                        @if($file_include)
                        <tr>
                            <td></td>
                            <td>
                                <div class="flex flex-row pt-3">
                                    <div class="w-24 flex justify-center text-base font-bold text-gray-600">                        
                                    </div>
                                    <div class="flex-1 flex flex-row items-center">
                                        <x-jet-danger-button wire:click.prevent="$set('file_include',false)" class="py-1"><i class="fas fa-times-circle text-base"></i></x-jet-danger-button>
                                        <input type="file" class="p-2 flex-1 text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="adjunto" class="text-sm"/>                
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="flex-1 flex flex-col p-4">
                    <div class="">
                        <x-jet-label class="text-gray-400 font-bold" value="Priodidad" />
                    </div>     
                    <div class="w-full">
                        <select name="prioridad" class="text-xs w-full ml-2 mr-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="1">Normal</option>
                            <option value="2">Alta</option>
                        </select>
                    </div>
                    @if($atencion_seleccionable)
                    <div class="pt-4">
                        <x-jet-label class="text-gray-400 font-bold" value="Seras atendido por:" />
                    </div>     
                    <div class="w-full">
                        <select wire:model="atencion_por" name="atencion_por" class="text-xs w-full ml-2 mr-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option></option>
                            @foreach ($usuarios_atencion_seleccionable as $seleccionable)
                            <option value="{{$seleccionable->user->id}}">{{$seleccionable->user->name}}</option> 
                            @endforeach                           
                        </select>
                        @error('atencion_por')<span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>    
                    @endif
                </div>
            </div>

        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click.prevent="cancelar">CANCELAR</x-jet-secondary-button>
            <x-jet-danger-button wire:click.prevent="guardar">GUARDAR TICKET</x-jet-secondary-button>
        </x-slot>
    
    </x-jet-dialog-modal>
</div>
<script>
    function archivoCambio(campo,archivo_sel)
    {
        alert ("cambio_archivo=" + campo + ", valor=" + archivo_sel);
        document.getElementById(campo).value=archivo_sel;
    }
</script>
</form>
