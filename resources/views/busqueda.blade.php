<x-app-layout>
    <x-slot name="header">
        {{ __('Busqueda') }}
    </x-slot>
    <div>
    @if ($ok=="NO")
        <div class="sm:px-6 lg:px-8 py-12">
            Especifique una busqueda de al menos 3 caracteres                    
        </div>
    @else
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col space-y-3 py-5">
            <div class="text-xl text-gray-700">
                Resultados de la busqueda : "{{$query}}"
            </div>
            <div class="text-xl text-gray-700">
                {{$registros->links()}}
            </div>
            @foreach ($registros as $registro )
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2 flex flex-col">
                <div class="w-full text-lg font-normal text-blue-600"><a href="{{route('ticket',['id'=>$registro->ticket_id])}}?q={{$query}}">Ticket: {{ticket($registro->ticket_id)}} {{$registro->solicitante}}</a></div>
                <div class="w-full text-sm font-normal text-green-600">/ticket/{{$registro->ticket_id}} <span class="text-gray-700">- Campo: <b>{{$registro->campo}}</b></span></div>
                <div class="w-full text-sm font-normal text-gray-400">{{$registro->created_at}} <span class="text-gray-700">- {!!busqueda_format($registro->texto,$query,150)!!}</span></div>
            </div>
                
            @endforeach                
        </div>
        @endif
    </div>
</x-app-layout>