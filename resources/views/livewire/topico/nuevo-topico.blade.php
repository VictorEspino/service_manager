<div>
    <x-jet-danger-button wire:click="$set('open',true)">CREAR NUEVO TOPICO</x-jet-danger-button>

    <x-jet-dialog-modal wire:model="open" maxWidth="5xl">
        <x-slot name="title">
            Crear nuevo topico
        </x-slot>
        <x-slot name="content">
            <div class="flex flex-col w-full">
                <div class="w-full mb-2 flex flex-row space-x-4">
                    <div class="w-3/4">
                        <x-jet-label value="Nombre" />
                        <x-jet-input class="w-full text-sm" type="text" name="nombre" wire:model.defer="nombre"/>
                        @error('nombre') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div class="w-1/4">
                        <x-jet-label value="Emite autorizacion" />
                        <select name="emite_autorizacion" wire:model.defer="emite_autorizacion" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="NO">NO</option>
                            <option value="SI">SI</option>
                        </select>  
                    </div>
                </div>
                <div class="w-full mb-2">
                    <x-jet-label value="Descripcion" />
                    <textarea rows=8 class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" name="descripcion"  wire:model.defer="descripcion"></textarea>
                    @error('descripcion') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="w-full mb-2 flex flex-row space-x-3">
                    <div class="w-1/3">
                        <x-jet-label value="Grupo" />
                        <select name="grupo" wire:model="grupo" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option></option>
                            @foreach ($grupos as $grupo_opcion)
                                <option value="{{$grupo_opcion->id}}">{{$grupo_opcion->nombre}}</option>    
                            @endforeach
                        </select>  
                        @error('grupo') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                    </div>
                    <div class="w-1/3">
                        <x-jet-label value="Tipo de asignación" />                    
                        <select name="tipo_asignacion" wire:model="tipo_asignacion" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option></option>
                        @foreach ($tipo_asignaciones as $tipo)
                            <option value="{{$tipo->id}}">{{$tipo->descripcion}}</option>    
                        @endforeach
                        </select> 
                        @error('tipo_asignacion') <span class="text-xs text-red-400">{{ $message }}</span> @enderror                            
                    </div>
                    @if($enable_automatico)
                    <div class="w-1/3">
                        <x-jet-label value="Asignar automaticamente a:" />                    
                        <select name="user_id_automatico" wire:model.defer="user_id_automatico" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option></option>
                        @foreach ($usuarios_grupo_disponibles as $usuarios_automatico)
                            <option value="{{$usuarios_automatico->user->id}}">{{$usuarios_automatico->user->name}}</option>    
                        @endforeach
                        </select> 
                        @error('user_id_automatico') <span class="text-xs text-red-400">{{ $message }}</span> @enderror                            
                    </div>
                    @endif
                </div>
                <div class="w-full mb-2">
                    <x-jet-label value="Tiempo de resolucion en minutos" />
                    <x-jet-input class="w-full text-sm" type="text" name="sla" wire:model.defer="sla"/>
                    @error('sla') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
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
                                @error('campos_principal.'.$index.'.etiqueta') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                            </div>
                            <div class="w-1/4 px-3">
                                <x-jet-label value="Control" />
                                <select wire:model="campos_principal.{{$index}}.tipo_control" class="w-full text-xs p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                    <option value="Texto">Texto</option>
                                    <option value="CheckBox">CheckBox</option>
                                    <option value="Lista">Lista</option>
                                    <option value="File">File</option>
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
                                    <option></option>
                                    @foreach($listas_valores_disponibles as $lista)
                                        <option value="{{$lista->id}}">{{$lista->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('campos_principal.'.$index.'.lista') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
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
                                        <td class="border px-2">{{$opcion->user}}</td>
                                        <td class="border px-2">{{$opcion->name}}</td>
                                        <td class="border"><center><i wire:click="agregar_invitado_principal({{$opcion->id}},'{{$opcion->user}}','{{$opcion->name}}')" class="text-green-500 text-lg fas fa-user-plus" style="cursor:pointer"></i></td>
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
                                        <td class="border bg-blue-500 text-white px-2"></td>
                                    </tr>

                                @foreach ($invitados_principal as $index => $invitado)
                                     <tr>
                                        <td class="border px-2">{{$invitado['empleado']}}</td>
                                        <td class="border px-2">{{$invitado['name']}}</td>
                                        <td class="border px-3"><center><i wire:click="eliminar_invitado_principal({{$index}})" class="text-red-400 text-lg fas fa-user-minus" style="cursor:pointer"></i></td>
                                    </tr>
                                @endforeach
                                </table>
                            @endif
                        </div>
                    </div>
                </div>  
                <div class="w-full bg-gray-700 p-2 rounded flex justify-between">
                    <span class="text-base text-gray-100">Actividades posteriores</span>
                    <i class="text-2xl text-green-500 fas fa-plus" style="cursor: pointer" wire:click="agregar_actividad_adicional" cursor></i>
                </div>
                <div class="w-full mb-2 flex flex-col">
                    @foreach ($actividades_adicionales as $index => $actividad)
                        <div class="w-full p-1 flex flex-row bg-blue-300 rounded p-3">
                            <div class="w-1/12 px-3 pb-2">                 
                                <x-jet-label value="Secuencia" />               
                                <x-jet-input type="text" class="w-full" wire:model="actividades_adicionales.{{$index}}.secuencia" readonly/>
                            </div>
                            <div class="w-10/12 px-3">
                                <x-jet-label value="Nombre" />
                                <x-jet-input type="text" class="w-full" wire:model="actividades_adicionales.{{$index}}.nombre" />
                                @error('actividades_adicionales.'.$index.'.nombre') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                            </div>
                            <div class="w-1/12 flex items-center justify-center">
                                <i class="fas fa-minus-circle text-red-400 text-2xl" style="cursor:pointer" wire:click='eliminar_actividad_adicional({{$index}})'></i>
                            </div>
                        </div>
                        <div class="w-full p-1 flex flex-row">
                            <div class="w-1/12 px-3">                                  
                            </div>
                            <div class="w-10/12 px-3">
                                <x-jet-label value="Descripcion" />
                                <textarea rows=8 class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text"  wire:model.defer="actividades_adicionales.{{$index}}.descripcion"></textarea>
                                @error('actividades_adicionales.'.$index.'.descripcion') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                            </div>
                            <div class="w-1/12 flex items-center justify-center">
                            </div>
                        </div>
                        <div class="w-full p-1 flex flex-row">
                            <div class="w-1/12 px-3">                                  
                            </div>
                            <div class="w-10/12 flex flex-row">
                                <div class="w-1/2 px-3">
                                    <x-jet-label value="Grupo" />
                                    <select wire:model="actividades_adicionales.{{$index}}.grupo" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                        <option></option>
                                        @foreach ($grupos as $grupo_opcion)
                                        <option value="{{$grupo_opcion->id}}">{{$grupo_opcion->nombre}}</option>    
                                        @endforeach
                                    </select>   
                                    @error('actividades_adicionales.'.$index.'.grupo') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                                </div>
                                <div class="w-1/2 px-3">
                                    <x-jet-label value="Tiempo de resolucion en minutos" />
                                    <x-jet-input class="w-full text-sm" type="text"  wire:model.defer="actividades_adicionales.{{$index}}.sla"/>
                                    @error('actividades_adicionales.'.$index.'.sla') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                                </div>
                            </div>
                        </div>
                        <div class="w-full p-1 flex flex-row">
                            <div class="w-1/12 px-3">                                  
                            </div>
                            <div class="w-10/12 flex flex-row">
                                <div class="w-1/2 px-3">
                                    <x-jet-label value="Tipo de asignación" />                
                                    <select wire:model="actividades_adicionales.{{$index}}.tipo_asignacion" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                        <option></option>
                                    @foreach ($tipo_asignaciones as $tipo)
                                        <option value="{{$tipo->id}}">{{$tipo->descripcion}}</option>    
                                    @endforeach
                                    </select>  
                                    @error('actividades_adicionales.'.$index.'.tipo_asignacion') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                                </div>
                                <div class="w-1/2 px-3">
                                    @if($actividades_adicionales[$index]['enable_automatico'])
                                    <x-jet-label value="Asignar automaticamente a:" />                
                                    <select wire:model="actividades_adicionales.{{$index}}.user_id_automatico" class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                        <option></option>
                                        @php
                                        $array=json_decode(json_encode($actividades_adicionales[$index]['usuarios_grupo_disponibles']), true);
                                        $array=collect($array);        
                                        @endphp
                                    @foreach ($array as $user_grupo_disponible)
                                    <option value="{{$user_grupo_disponible['user']['id']}}">{{$user_grupo_disponible['user']['name']}}</option>    
                                    @endforeach
                                    </select>  
                                    @error('actividades_adicionales.'.$index.'.user_id_automatico') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                                    @endif
                                </div>
                               
                            </div>
                            <div class="w-1/12 flex items-center justify-center">
                            </div>
                        </div>
                        <div class="w-full p-1 flex flex-row">
                            <div class="w-1/12 px-3">                                  
                            </div>
                            <div class="w-10/12 bg-gray-400 p-2 rounded flex justify-between">
                                <span class="text-base text-gray-100">Campos plantilla</span>
                                <i class="text-2xl text-red-500 fas fa-plus" style="cursor: pointer" wire:click="nuevo_campo_actividad_adicional({{$index}})" cursor></i>
                            </div>
                            <div class="w-1/12 flex items-center justify-center">
                            </div>
                        </div>
                        <div class="w-full p-1 flex flex-row">
                            <div class="w-1/12 px-3">                                  
                            </div>
                            <div class="w-10/12 flex flex-col">
                                @foreach ($actividades_adicionales[$index]['campos'] as $index_campos => $aa_campos)
                                <div class="w-full p-1 flex flex-row">
                                    <div class="w-1/4 px-3">
                                        <x-jet-label value="Etiqueta" />
                                        <x-jet-input type="text" class="w-full text-xs p-1" wire:model="actividades_adicionales.{{$index}}.campos.{{$index_campos}}.etiqueta" />
                                        @error('actividades_adicionales.'.$index.'.campos.'.$index_campos.'.etiqueta') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                                    </div>
                                    <div class="w-1/4 px-3">
                                        <x-jet-label value="Control" />
                                        <select wire:model="actividades_adicionales.{{$index}}.campos.{{$index_campos}}.tipo_control" class="w-full text-xs p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                            <option value="Texto">Texto</option>
                                            <option value="CheckBox">CheckBox</option>
                                            <option value="Lista">Lista</option>
                                            <option value="Archivo">Archivo</option>
                                        </select>
                                    </div>
                                    <div class="w-1/4 px-3">
                                        <x-jet-label value="Requerido" />
                                        <select wire:model="actividades_adicionales.{{$index}}.campos.{{$index_campos}}.requerido" class="w-full text-xs p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                            <option value="1">SI</option>
                                            <option value="0">NO</option>
                                        </select>
                                    </div>
                                    <div class="w-1/4 px-3">
                                        <x-jet-label value="Lista Valores" />
                                        <select wire:model="actividades_adicionales.{{$index}}.campos.{{$index_campos}}.lista" class="w-full text-xs p-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                            <option></option>
                                            @foreach($listas_valores_disponibles as $lista)
                                                <option value="{{$lista->id}}">{{$lista->nombre}}</option>
                                            @endforeach
                                        </select>
                                        @error('actividades_adicionales.'.$index.'.campos.'.$index_campos.'.lista') <span class="text-xs text-red-400">{{ $message }}</span> @enderror 
                                    </div>
                                    <div class="w-1/6 flex items-center justify-center">
                                        <i class="fas fa-minus-circle text-red-400 text-2xl" style="cursor:pointer" wire:click='borrar_campo_actividad_adicional({{$index}},{{$index_campos}})'></i>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="w-1/12 flex items-center justify-center">
                            </div>
                        </div>
                        <div class="w-full p-1 flex flex-row">
                            <div class="w-1/12 px-3">                                  
                            </div>
                            <div class="w-10/12 bg-gray-400 p-2 rounded flex justify-between">
                                <span class="text-base text-gray-100">Usuarios invitados a la actividad</span>
                            </div>
                            <div class="w-1/12 flex items-center justify-center">
                            </div>
                        </div>
                        <div class="w-full p-1 flex flex-row">
                            <div class="w-1/12 px-3">                                  
                            </div>
                            <div class="w-10/12 flex flex-row">
                                <div class="w-1/2 flex flex-col">
                                    <div class="w-full p-2">
                                        <x-jet-label value="Buscar (minimo 4 caracteres para iniciar busqueda )" />
                                        <x-jet-input type="text" class="flex-1 text-sm" wire:model="actividades_adicionales.{{$index}}.invitados_buscar" />
                                    </div>
                                    <div class="w-full p-2">
                                        @php
                                        $array=json_decode(json_encode($actividades_adicionales[$index]['invitados_disponibles']), true);
                                        $array=collect($array);        
                                        @endphp
        
                                            <table>
                                                <tr>
                                                    <td class="border bg-blue-500 text-white px-2">User</td>
                                                    <td class="border bg-blue-500 text-white px-2">Nombre</td>
                                                    <td class="border bg-blue-500 text-white px-2">Agregar</td>
                                                </tr>
                                            @foreach ($array as $opcion_actividad)
                                                <tr>
                                                    <td class="border px-2">{{$opcion_actividad['user']}}</td>
                                                    <td class="border px-2">{{$opcion_actividad['name']}}</td>
                                                    <td class="border"><center><i wire:click="agregar_invitado_actividad_adicional({{$index}},{{$opcion_actividad['id']}},'{{$opcion_actividad['user']}}','{{$opcion_actividad['name']}}')" class="text-green-500 text-lg fas fa-user-plus" style="cursor:pointer"></i></td>
                                                    
                                                </tr>
                                            @endforeach
                            
                                            </table>
                                    </div>
                                </div>
                                <div class="w-1/2 flex flex-col">
                                    <div class="w-full rounded p-2 bg-gray-200 text-center">
                                        Usuarios Invitados
                                    </div>
                                    <div class="w-full p-2 flex justify-center">
                                        @if (is_array($actividades_adicionales[$index]['invitados']) || is_object($actividades_adicionales[$index]['invitados']))
                                            <table>
                                                <tr>
                                                    <td class="border bg-blue-500 text-white px-2">User</td>
                                                    <td class="border bg-blue-500 text-white px-2">Nombre</td>
                                                    <td class="border bg-blue-500 text-white px-2"></td>
                                                </tr>
            
                                            @foreach ($actividades_adicionales[$index]['invitados'] as $index_invitado => $invitado)
                                                 <tr>
                                                    <td class="border px-2">{{$invitado['empleado']}}</td>
                                                    <td class="border px-2">{{$invitado['name']}}</td>
                                                    <td class="border px-3"><center><i wire:click="eliminar_invitado_actividad_adicional({{$index}},{{$index_invitado}})" class="text-red-400 text-lg fas fa-user-minus" style="cursor:pointer"></i></td>
                                                </tr>
                                            @endforeach
                                            </table>
                                        @endif
                                    </div>
                                </div>
                                
                            </div>
                            <div class="w-1/12 flex items-center justify-center">
                            </div>
                        </div>
                        
                    @endforeach
                </div>
                
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="cancelar">CANCELAR</x-jet-secondary-button>
            <x-jet-danger-button wire:click="guardar">GUARDAR TOPICO</x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>