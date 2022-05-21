<div class="w-full flex flex-col pl-4 h-80">
    <div class="w-full rounded-t-lg bg-gray-200 py-1 px-2 font-bold text-gray-600">
        Cerrados recientemente
    </div>
    <div class="border-l border-r bg-white flex flex-col rounded-b-lg shadow-lg overflow-y-scroll">
        @foreach ($tickets as $ticket)
        <div class="p-3 text-xs text-gray-600 border-b"> 
            <a href="{{route('ticket',['id'=>$ticket->id])}}">{{ticket($ticket->id)}} {{$ticket->asunto}}</a>
        </div>        
        @endforeach
    </div>    
</div>

