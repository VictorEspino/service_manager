<x-app-layout>
    <x-slot name="header">
        {{ __('No autorizado') }}
    </x-slot>
    <div class="py-12 px-12 text-red-500 flex justify-center items-center w-full text-5xl flex-col">
        <div class="w-full flex justify-center">
        <i class="text-yellow-500 fas fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;
        Usuario no autorizado
        </div>
        <div class="pt-12 w-full text-base text-gray-700 flex justify-center text-center">
            {{$mensaje}}
        </div>
    </div>
</x-app-layout>