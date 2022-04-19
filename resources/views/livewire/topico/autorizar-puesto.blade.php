<div>
    <div class="w-full text-3xl flex flex-col">
        <div class="w-full">
            <i class="text-cyan-400 fas fa-sign-language" wire:click='edit_open' style="cursor: pointer;"></i>
        </div>
        <div class="text-xs w-full">
            Autorizar
        </div>
    </div>
    <x-jet-dialog-modal wire:model="open" maxWidth="5xl">
        <x-slot name="title">
            <div class="w-full flex flex-row">
                <div class="w-3/4">
                    Autorizar topico {{$id_topico}}
                </div>
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="w-full text-base pb-4 text-gray-700">
                Seleccione los puestos que podran levantar tickets con el topico actual
            </div>
            <div class="w-full flex flex-row justify-center text-center pb-3">
                <div class="w-1/2"><x-jet-secondary-button wire:click="select_todo">Select Todo</x-jet-secondary-button></div>
                <div class="w-1/2"><x-jet-secondary-button wire:click="quitar_todo">Quitar Todo</x-jet-secondary-button></div>
            </div>
            <div class="w-full flex flex-wrap text-xs">
            @foreach ($this->puestos_autorizados as $index=>$puestos_pantalla)
                <div class="w-1/4 flex flex-row px-4 py-2">
                    <div class="w-10">
                        <x-jet-checkbox wire:model.defer='puestos_autorizados.{{$index}}.autorizado'></x-jet-checkbox>
                    </div>
                    <div class="flex-1">
                        {{$puestos_pantalla['puesto']}}
                    </div>
                </div>
            @endforeach
            </div>
            
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="cancelar">CANCELAR</x-jet-secondary-button>
            <x-jet-danger-button wire:click="guardar">GUARDAR AUTORIZACION</x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>