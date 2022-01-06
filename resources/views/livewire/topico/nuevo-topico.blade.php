<div>
    <x-jet-danger-button wire:click="$set('open',true)">CREAR NUEVO TOPICO</x-jet-danger-button>

    <x-jet-dialog-modal wire:model="open" maxWidth="5xl">
        <x-slot name="title">
            Crear nuevo topico
        </x-slot>
        <x-slot name="content">
            <div class="flex flex-col w-full">
                <div class="w-full mb-2">
                    <x-jet-label value="Nombre" />
                    <x-jet-input class="w-full text-sm" type="text" name="nombre" wire:model.defer="nombre"/>
                </div>
                <div class="w-full mb-2">
                    <x-jet-label value="Descripcion" />
                    <textarea rows=8 class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" name="descripcion"  wire:model.defer="descripcion"></textarea>
                </div>
                <div class="w-full mb-2 flex flex-row space-x-3">
                    <div class="w-1/2">
                        <x-jet-label value="Grupo" />
                        <select name="grupo" wire:model.defer="grupo" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option></option>
                            @foreach ($grupos as $grupo_opcion)
                                <option value="{{$grupo_opcion->id}}">{{$grupo_opcion->nombre}}</option>    
                            @endforeach
                            </select>   
                    </div>
                    <div class="w-1/2">
                        <x-jet-label value="Tipo de asignación" />
                        
                        <select name="tipo_asignacion" wire:model.defer="tipo_asignacion" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option></option>
                        @foreach ($tipo_asignaciones as $tipo)
                            <option value="{{$tipo->id}}">{{$tipo->descripcion}}</option>    
                        @endforeach
                        </select>                            
                    </div>
                </div>
                <div class="w-full mb-2">
                    <x-jet-label value="Tiempo de resolucion en minutos" />
                    <x-jet-input class="w-full text-sm" type="text" name="sla" wire:model.defer="sla"/>
                </div>
                <div class="w-full bg-gray-700 p-2 rounded flex justify-between">
                    <span class="text-base text-gray-100">Campos plantilla</span>
                    <i class="text-2xl text-green-500 fas fa-plus" style="cursor: pointer" wire:click="nuevo_campo_principal" cursor></i>
                </div>
                <div class="w-full mb-2 flex flex-col text-center">
                    <span class="text-sm font-semibold">Todos los tickets contienen por default un campo "DESCRIPCION" que permite al usuario detallar la solicitud</span>
                </div>
                <div class="w-full mb-2 flex flex-col">
                    @foreach ($campos_principal as $index => $campos)
                        <div class="w-full p-1 flex flex-row">
                            <div class="w-1/4 px-3">
                                <x-jet-label value="Etiqueta" />
                                <x-jet-input type="text" class="w-full text-xs p-1" wire:model="campos_principal.{{$index}}.etiqueta" />
                            </div>
                            <div class="w-1/4 px-3">
                                <x-jet-label value="Control" />
                                <select wire:model="campos_principal.{{$index}}.tipo_control" class="w-full text-xs p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="Texto">Texto</option>
                                    <option value="CheckBox">CheckBox</option>
                                    <option value="Lista">Lista</option>
                                    <option value="Archivo">Archivo</option>
                                </select>
                            </div>
                            <div class="w-1/4 px-3">
                                <x-jet-label value="Requerido" />
                                <select wire:model="campos_principal.{{$index}}.requerido" class="w-full text-xs p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="1">SI</option>
                                    <option value="0">NO</option>
                                </select>
                            </div>
                            <div class="w-1/4 px-3">
                                <x-jet-label value="Lista Valores" />
                                <select wire:model="campos_principal.{{$index}}.lista" class="w-full text-xs p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="0"></option>
                                    <option value="1">Sucursales</option>
                                    <option value="2">Equipos</option>
                                    <option value="3">Empleados</option>
                                    <option value="4">Estatus</option>
                                </select>
                            </div>
                            <div class="w-1/6 flex items-center justify-center">
                                <i class="fas fa-minus-circle text-red-400 text-2xl" style="cursor:pointer" wire:click='borrar_campo_principal({{$index}})'></i>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="w-full bg-gray-700 p-2 rounded flex justify-between">
                    <span class="text-base text-gray-100">Invitados</span>
                </div>
                <div class="w-full mb-2 flex flex-row">
                    <div class="w-1/2 flex flex-col">
                        <div class="w-full p-2">
                            <x-jet-label value="Buscar (minimo 4 caracteres para iniciar busqueda )" />
                            <x-jet-input type="text" class="flex-1 text-sm" name="invitados_buscar" wire:model="invitados_buscar" />
                        </div>
                        <div class="w-full p-2">
                            @if (is_array($invitados_disponibles) || is_object($invitados_disponibles))
                                <table>
                                    <tr>
                                        <td class="border bg-blue-500 text-white px-2">User</td>
                                        <td class="border bg-blue-500 text-white px-2">Nombre</td>
                                        <td class="border bg-blue-500 text-white px-2">Agregar</td>
                                    </tr>

                                @foreach ($invitados_disponibles as $opcion)
                                     <tr>
                                        <td class="border px-2">{{$opcion->empleado}}</td>
                                        <td class="border px-2">{{$opcion->name}}</td>
                                        <td class="border"><center><i wire:click="agregar_invitado_principal({{$opcion->id}},'{{$opcion->empleado}}','{{$opcion->name}}')" class="text-green-500 text-lg fas fa-user-plus" style="cursor:pointer"></i></td>
                                    </tr>
                                @endforeach
                                </table>
                            @endif
                        </div>
                    </div>
                    <div class="w-1/2 flex flex-col">
                        <div class="w-full rounded p-2 bg-gray-200 text-center">
                            Usuarios Invitados
                        </div>
                        <div class="w-full p-2 flex justify-center">
                            @if (is_array($invitados_principal) || is_object($invitados_principal))
                                <table>
                                    <tr>
                                        <td class="border bg-blue-500 text-white px-2">User</td>
                                        <td class="border bg-blue-500 text-white px-2">Nombre</td>
                                        <td class="border bg-blue-500 text-white px-2">Agregar</td>
                                    </tr>

                                @foreach ($invitados_principal as $index => $invitado)
                                     <tr>
                                        <td class="border px-2">{{$invitado['empleado']}}</td>
                                        <td class="border px-2">{{$invitado['name']}}</td>
                                        <td class="border"><center><i wire:click="eliminar_invitado_principal({{$index}})" class="text-red-400 text-lg fas fa-user-minus" style="cursor:pointer"></i></td>
                                    </tr>
                                @endforeach
                                </table>
                            @endif
                        </div>
                    </div>
                    
                </div>    
                
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('open',false)">CANCELAR</x-jet-secondary-button>
            <x-jet-danger-button wire:click="guardar">GUARDAR TOPICO</x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
