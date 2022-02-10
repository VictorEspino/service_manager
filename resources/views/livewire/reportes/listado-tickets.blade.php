<div class="w-full h-full bg-gray-100 flex flex-col pt-5 px-8">
    <div class="w-full bg-gray-200 py-1 px-2 font-bold text-gray-600 rounded-t-lg">
        Listado Tickets
    </div>
    <form action="{{route('reportes')}}" method="POST">
        @csrf
    <div class="w-full bg-white py-1 px-2 text-gray-600 flex flex-col">
        <div class="w-full flex flex-row space-x-4">
            <div class="w-1/4">
                <x-jet-label value="Grupo" />
                <select wire:model="grupo" class="w-full text-xs border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value=""></option>
                    @foreach ($grupos as $grupo_select)
                        <option value='{{$grupo_select->id}}'>{{$grupo_select->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <x-jet-label value="Topico" />
                <select wire:model.defer="topico" name="topico" class="w-full text-xs border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value=""></option>
                    @if (is_array($topicos_disponibles) || is_object($topicos_disponibles))
                        @foreach ($topicos_disponibles as $topico_opcion)
                        <option value="{{$topico_opcion->topico->id}}">{{$topico_opcion->topico->nombre}}</option>
                        @endforeach
                    @endif
                </select>
                @error('topico')<span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    <div class="w-full bg-white py-1 px-2 text-gray-600 flex flex-col">
        <div class="w-full flex flex-row space-x-4">
            <div class="w-1/3">
                <x-jet-label value="Rango en" />
                <select name="concepto_fecha" wire:model.defer="concepto_fecha" class="w-full text-xs border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="Creacion">Creacion</option>
                    <option value="Cierre">Cierre</option>                    
                </select>
            </div>
            <div class="w-1/3">
                <x-jet-label value="Desde" />
                <x-jet-input name="desde" wire:model.defer="desde" type="Date" class="w-full"/>
            </div>
            <div class="w-1/3">
                <x-jet-label value="Hasta" />
                <x-jet-input name="hasta" wire:model.defer="hasta" type="Date" class="w-full"/>
            </div>
            
        </div>
    </div>
    <div class="w-full bg-white py-1 px-2 pt-10 pb-10 text-gray-600 flex justify-end">
        <x-jet-button class="w-1/6"><i class="fa-solid fa-file-excel"></i>&nbsp;&nbsp;Exportar Excel</x-jet-button>
    </div>
    </form>
</div>