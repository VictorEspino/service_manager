<div>
    <button class='inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-100 disabled:opacity-25 transition' wire:click.prevent="abrir">Comentar</button>
    <x-jet-dialog-modal wire:model="open" maxWidth="3xl">
        <x-slot name="title">
            Comentar post
        </x-slot>
        <x-slot name="content">
            <div class="flex flex-row w-full">
                <div class="flex flex-col w-full">
                    <table class="w-full table-auto">
                        <tr class="p-2">
                            <td class="w-1/12 py-2">
                                <x-jet-label class="text-gray-700 font-normal" value="Comentario" />
                            </td>
                            <td class="flex-1 flex py-2 px-2 text-sm text-gray-500 font-normal items-center">
                                <textarea rows=5 class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" wire:model.defer="comentario"></textarea><br />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>@error('comentario') <span class="text-xs text-red-400">{{ $message }}</span> @enderror</td>
                        </tr>
                        
                    </table>
                </div>                
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click.prevent="cancelar">CANCELAR</x-jet-secondary-button>
            <button {{$procesando==1?'disabled':''}} class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition' wire:click.prevent="guardar">Guardar</button>
        </x-slot>    
    </x-jet-dialog-modal>
</div>
    
    