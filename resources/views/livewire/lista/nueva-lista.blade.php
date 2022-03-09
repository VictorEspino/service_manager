<div>
    <x-jet-danger-button wire:click="abrir">CREAR NUEVA LISTA</x-jet-danger-button>

    <x-jet-dialog-modal wire:model="open" maxWidth="5xl">
        <x-slot name="title">
            Crear nueva lista de valores
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
                    <span class="text-base text-gray-100">Valores</span>
                </div>
                <div class="w-full mb-2 flex flex-row text-xs">
                    <div class="w-1/2 flex flex-col">
                        <div class="w-full p-2">
                            <x-jet-label value="Nuevo valor" />
                            <x-jet-input type="text" class="flex-1 text-sm" wire:model.defer="valor" />
                            <x-jet-button wire:click.prevent="agregar_valor">Agregar</x-jet-button>
                        </div>
                        <div class="w-full p-2">
                            
                        </div>
                    </div>
                    <div class="w-1/2 flex flex-col">
                        <div class="w-full rounded p-2 bg-gray-200 text-center">
                            Valores de la lista
                        </div>
                        <div class="w-full p-2 flex justify-center flex flex-col">
                            <div class="w-full">
                            @if (is_array($valores) || is_object($valores))
                                <table>
                                    <tr>
                                        <td class="border bg-blue-500 text-white px-2">Valor</td>
                                        <td class="border bg-blue-500 text-white px-2">Eliminar</td>
                                    </tr>

                                @foreach ($valores as $index => $valor)
                                     <tr>
                                        <td class="border px-2">{{$valor['texto']}}</td>
                                        <td class="border"><center><i wire:click="eliminar_valor({{$index}})" class="text-red-400 text-lg fas fa-user-minus" style="cursor:pointer"></i></td>
                                    </tr>
                                @endforeach
                                </table>
                            @endif
                            </div>
                            <div class="w-full">
                            @error('numero_de_valores') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                </div>    
                
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="cancelar">CANCELAR</x-jet-secondary-button>
            <button {{$procesando==1?'disabled':''}} class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition' wire:click.prevent="guardar">GUARDAR LISTA</button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
