<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>
    @livewire('ticket.ticket-detalle', ['id' => $id,'buscar'=>$buscar,'busqueda'=>$busqueda])
</x-app-layout>