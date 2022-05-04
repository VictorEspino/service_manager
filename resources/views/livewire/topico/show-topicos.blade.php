<div>
    <x-slot name="header">
        {{ __('Topicos') }}
    </x-slot>
    <div class="w-full flex flex-col px-5 md:py-6">
        <div class="w-full">
            <x-jet-section-title>
                <x-slot name="title">Administracion Topicos</x-slot>
                <x-slot name="description">Permite visualizar y dar mantenimiento a los topicos configurados en el sistema</x-slot>
            </x-jet-section-title>
        </div>
    </div>
    <div class="w-full px-5 pt-6">
        <div class="w-full flex items-center text-sm text-gray-600">
            <div class="px-5">
                <span>Mostrar </span>
                <select wire:model="elementos" class="text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value=5>5</option>
                    <option value=10>10</option>
                    <option value=20>20</option>
                    <option value=30>30</option>
                    <option value=50>50</option>
                </select>  
                <span> registros</span> 
            </div>
            <div class="px-5">
                <span>Grupos </span>
                <select wire:model="grupo" class="text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value=0>Todos</option>
                    @foreach($grupos as $grupo_opcion)
                        <option value={{$grupo_opcion->id}}>{{$grupo_opcion->nombre}}</option>
                    @endforeach
                </select>  
            </div>
            <div class="flex-1 flex  px-5">
                <!--
                <x-jet-input class="flex-1 text-sm mr-5" type="text"  wire:model="filtro" placeholder="¿Que desea buscar?"/>
                -->
                
                @livewire("topico.nuevo-topico")
            </div>
            
        </div>
    </div>
    <div class="w-full px-5 pt-6">
        {{$topicos->links()}}
    </div>
    <div class="w-full flex flex-col space-y-3 py-5 px-5">
        @php
        $registros=0;   
        @endphp
        @foreach ($topicos as $topico)
            @php
            $registros=$registros+1;   
            @endphp
            <div class="w-full flex flex-row bg-white rounded-lg shadow-lg p-3 border border-blue-200">
                <div class="w-1/4 text-gray-700 font-semibold text-lg px-3">{{$topico->nombre}}</div>
                <div class="w-1/2 text-gray-700 text-sm px-2">{{show_parcial($topico->descripcion,200)}}</div>
                <div class="flex-1 text-gray-700 flex flex-col text-center">
                    @if($topico->estatus=="1")
                        <div class="w-full text-3xl">
                            <i class="text-green-600 fas fa-check-circle"></i>
                        </div>
                        <div class="w-full">
                            <span class="text-xs">Activo</span>
                        </div>
                    @else
                        <div class="w-full text-3xl">
                            <i class="text-red-400 fas fa-times-circle"></i>
                        </div>
                        <div class="w-full">
                            <span class="text-xs">Inactivo</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1 flex justify-center">
                    @livewire('topico.update-topico',['id_topico'=>$topico->id,key('topico.update-topico-'.$topico->id)])
                </div>
                <div class="flex-1">
                    @livewire('topico.autorizar-puesto',['id_topico'=>$topico->id,key('topico.autorizar-puesto-'.$topico->id)])
                </div>
            </div>
        @endforeach
        @if($registros==0)
            No se encontraron registros
        @endif
    </div>
</div>
