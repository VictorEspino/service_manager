<div class="w-full flex flex-col pl-4">
    <div class="w-full rounded-t-lg bg-gray-200 py-1 px-2 font-bold text-gray-600">
        Buscar
    </div>
    <div class="border-l border-r bg-white flex flex-col rounded-b-lg shadow-lg">
        <form method="GET" action="{{route('busqueda_simple')}}" id="form_busqueda_simple">
        <div class="p-2"> 
            <x-jet-input wire:model.defer="folio" name="folio" type="text" class="w-full" placeholder="Folio" />
            @error('folio') <span class="text-xs text-red-400">{{ $message }}</span>@enderror
        </div>
        <div class="p-2"> 
            <x-jet-input wire:model.defer="asunto" name="asunto" type="text" class="w-full" placeholder="Asunto" />
        </div>
        <div class="p-2"> 
            <x-jet-button wire:click.prevent='buscar' class="w-full">Buscar</x-jet-button>
        </div>
        @error('busqueda')
            <div class="p-2"> 
                <span class="text-xs text-red-400">{{ $message }}</span> 
            </div>            
        @enderror
        </form>
    </div>    
</div>
