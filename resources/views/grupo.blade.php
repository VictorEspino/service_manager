<x-app-layout>
    <x-slot name="header">
        {{ __('Grupo') }}
    </x-slot>
    @livewire('grupo-comunicacion.show-posts', ['grupo_id'=>$id])
</x-app-layout>