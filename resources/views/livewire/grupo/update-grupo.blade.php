<div>    
    <div class="w-full text-3xl flex flex-col">
        <div class="w-full">
            <i class="text-blue-400 fas fa-pen" wire:click='edit_open' style="cursor: pointer;"></i>
        </div>
        <div class="text-xs w-full">
            Editar
        </div>
    </div>
    <x-jet-dialog-modal wire:model="open" maxWidth="5xl">
        <x-slot name="title">
            Editar grupo de atención
        </x-slot>
        <x-slot name="content">
            <div class="flex flex-col w-full">
                <div class="w-full mb-2">
                    <x-jet-label value="Nombre" />
                    <x-jet-input class="w-full text-sm" type="text" wire:model.defer="nombre"/>
                    @error('nombre') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="w-full mb-2">
                    <x-jet-label value="Descripcion" />
                    <textarea class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"  wire:model.defer="descripcion"></textarea>
                    @error('descripcion') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="w-full bg-gray-700 p-2 rounded flex justify-between">
                    <span class="text-base text-gray-100">Miembros</span>
                </div>
                <div class="w-full mb-2 flex flex-row text-xs">
                    <div class="w-1/2 flex flex-col">
                        <div class="w-full p-2">
                            <x-jet-label value="Buscar (minimo 4 caracteres para iniciar busqueda )" />
                            <x-jet-input type="text" class="flex-1 text-sm" name="miembros_buscar" wire:model="miembros_buscar" />
                        </div>
                        <div class="w-full p-2">
                            @if (is_array($usuarios_disponibles) || is_object($usuarios_disponibles))
                                <table>
                                    <tr>
                                        <td class="border bg-blue-500 text-white px-2">User</td>
                                        <td class="border bg-blue-500 text-white px-2">Nombre</td>
                                        <td class="border bg-blue-500 text-white px-2">Agregar<br>Miembro</td>
                                        <td class="border bg-blue-500 text-white px-2">Agregar<br>Manager</td>
                                    </tr>

                                @foreach ($usuarios_disponibles as $opcion)
                                     <tr>
                                        <td class="border px-2">{{$opcion->user}}</td>
                                        <td class="border px-2">{{$opcion->name}}</td>
                                        <td class="border"><center><i wire:click="agregar_miembro_principal({{$opcion->id}},'{{$opcion->user}}','{{$opcion->name}}','1')" class="text-green-500 text-lg fas fa-user-plus" style="cursor:pointer"></i></td>
                                        <td class="border"><center><i wire:click="agregar_miembro_principal({{$opcion->id}},'{{$opcion->user}}','{{$opcion->name}}','2')" class="text-yellow-500 text-lg fas fa-user-plus" style="cursor:pointer"></i></td>
                                    </tr>
                                @endforeach
                                </table>
                            @endif
                        </div>
                    </div>
                    <div class="w-1/2 flex flex-col">
                        <div class="w-full rounded p-2 bg-gray-200 text-center">
                            Miembros del grupo
                        </div>
                        <div class="w-full p-2 flex justify-center flex flex-col">
                            <div class="w-full">
                            @if (is_array($usuarios_principal) || is_object($usuarios_principal))
                                <table>
                                    <tr>
                                        <td class="border bg-blue-500 text-white px-2">User</td>
                                        <td class="border bg-blue-500 text-white px-2">Nombre</td>
                                        <td class="border bg-blue-500 text-white px-2">Manager</td>
                                        <td class="border bg-blue-500 text-white px-2">Eliminar</td>
                                    </tr>

                                @foreach ($usuarios_principal as $index => $miembro)
                                     <tr>
                                        <td class="border px-2">{{$miembro['empleado']}}</td>
                                        <td class="border px-2">{{$miembro['name']}}</td>
                                        <td class="border px-2"><center>{!!$miembro['tipo']=="2"?'<i class="fas text-green-500 fa-check"></i>':''!!}</center></td>
                                        <td class="border"><center><i wire:click="eliminar_miembro_principal({{$index}})" class="text-red-400 text-lg fas fa-user-minus" style="cursor:pointer"></i></td>
                                    </tr>
                                @endforeach
                                </table>
                            @endif
                            </div>
                            <div class="w-full">
                            @error('miembros') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-full">
                            @error('manager') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                </div>    
                
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="cancelar">CANCELAR</x-jet-secondary-button>
            <button {{$procesando==1?'disabled':''}} class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition' wire:click.prevent="guardar">GUARDAR GRUPO</button>
        </x-slot>
    </x-jet-dialog-modal>
</div>