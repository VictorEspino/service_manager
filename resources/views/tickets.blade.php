<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>
<div class="w-full bg-gray-200 flex flex-row border-b border-gray-400 shaddow">
    <div class="p-3 flex items-center">
        <i class="text-4xl text-amber-500 fas fa-file-invoice"></i>
        <span class="ml-5 font-semibold text-gray-600 text-2xl">TICKETS</span>
    </div>
    <div class="border-l-4 border-gray-300 flex items-center px-5 text-gray-600">
        Tablero
    </div>
    <div class="border-l-4 border-gray-300 flex items-center px-5 text-gray-600">
        Tickets Abiertos
    </div>
    <div class="border-l-4 border-gray-300 flex items-center px-5 text-gray-600">
        Tickets Cerrados
    </div>
    <div class="border-l-4 border-gray-300 flex items-center px-5 text-gray-600">
        Reportes
    </div>
    <div class="flex-1 justify-end border-l-4 border-r-4 border-gray-300 flex items-center px-5 text-gray-600">
        @livewire('ticket.nuevo-ticket');
    </div>
</div>
</x-app-layout>