<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        @livewireStyles

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script src="https://kit.fontawesome.com/a692f93986.js" crossorigin="anonymous"></script>

        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            .bg-side-nav {
                 background-color: #ECF0F1;
                }
    
        </style>

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-blue-900">
                    <div class="max-w-7xl mx-auto py-6 px-2 sm:px-2 lg:px-4">
                        <h2 class="font-semibold text-l text-gray-100 leading-tight bg-blue-900">
    
                                <i class="fas fa-bars pr-2 text-white" onclick="sidebarToggle()"></i>
    
                                {{ $header }}

                        </h2>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <div class="flex -mb-4">
                    <div id="sidebar" class="bg-gray-300 text-gray-700 h-screen flex w-52 flex-shrink-0 border-r border-side-nav md:block lg:block">
                        <div>
                            <ul class="list-reset flex flex-col">
                                <li class=" w-full h-full py-3 px-2 border-b border-light-border {{request()->routeIs('dashboard')?'bg-gray-100':'br-gray-800'}}">
                                    <a href="{{ route('dashboard') }}" 
                                        class="font-sans font-hairline hover:font-normal text-sm text-nav-item no-underline">
                                        <i class="fas fa-tachometer-alt float-left mx-2"></i>
                                        Dashboard
                                        <span><i class="fas fa-angle-right float-right"></i></span>
                                    </a>
                                </li> 
                                <li class=" w-full h-full py-3 px-2 border-b border-light-border bg-blue-200">
                                        Configuracion
                                </li> 
                                <li class=" w-full h-full py-3 px-2 border-b border-light-border {{request()->routeIs('topicos')?'bg-gray-100':'br-gray-800'}}">
                                    <a href="{{ route('topicos') }}" 
                                        class="font-sans font-hairline hover:font-normal text-sm text-nav-item no-underline">
                                        <i class="fas fa-book float-left mx-2"></i>
                                        Topicos
                                        <span><i class="fas fa-angle-right float-right"></i></span>
                                    </a>
                                </li> 
                                <li class=" w-full h-full py-3 px-2 border-b border-light-border {{request()->routeIs('grupos')?'bg-gray-100':'br-gray-800'}}">
                                    <a href="{{ route('grupos') }}" 
                                        class="font-sans font-hairline hover:font-normal text-sm text-nav-item no-underline">
                                        <i class="fas fa-users-cog float-left mx-2"></i>
                                        Grupos
                                        <span><i class="fas fa-angle-right float-right"></i></span>
                                    </a>
                                </li> 
                                <li class=" w-full h-full py-3 px-2 border-b border-light-border {{request()->routeIs('usuarios')?'bg-gray-100':'br-gray-800'}}">
                                    <a href="{{ route('usuarios') }}" 
                                        class="font-sans font-hairline hover:font-normal text-sm text-nav-item no-underline">
                                        <i class="fas fa-user float-left mx-2"></i>
                                        Usuarios
                                        <span><i class="fas fa-angle-right float-right"></i></span>
                                    </a>
                                </li>
                                <li class=" w-full h-full py-3 px-2 border-b border-light-border bg-blue-200">
                                    Tickets
                                </li> 
                                <li class=" w-full h-full py-3 px-2 border-b border-light-border {{request()->routeIs('tickets')?'bg-gray-100':'br-gray-800'}}">
                                    <a href="{{ route('tickets') }}" 
                                        class="font-sans font-hairline hover:font-normal text-sm text-nav-item no-underline">
                                        <i class="fas fa-file-invoice float-left mx-2"></i>
                                        Tablero
                                        <span><i class="fas fa-angle-right float-right"></i></span>
                                    </a>
                                </li> 
                            </ul>                
                        </div>
                    </div>
                    <div class="w-full">
                            {{ $slot }}
                    </div>
                </div>
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        <script>
            var sidebar = document.getElementById('sidebar');

            function sidebarToggle() {
                if(sidebar.style.display!="none") {
                    sidebar.style.display="none";
                }
                else{
                    sidebar.style.display="block";
                }
            }    
        </script>
        <script>
            Livewire.on('alert_ok',function(message)
            {
                Swal.fire(
                    'OK!',
                    message,
                    'success'
                )

            });
        </script>
        <script>
            Livewire.on('livewire_to_controller',function(forma)
            {
                document.getElementById(forma).submit();
            });
        </script>  
    </body>
</html>
