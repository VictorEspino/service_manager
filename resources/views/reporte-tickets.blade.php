<x-app-layout>
    <x-slot name="header">
        {{ __('Reportes') }}
    </x-slot>
    <x-ticket-nav />
    @livewire('reportes.listado-tickets')

</x-app-layout>