<div class="flex flex-col">
    <div class="w-full flex flex-row bg-white h-screen">
        <div class="flex-1 p-4 flex flex-col text-gray-600">
            <div class="w-full pb-6">
                <div class="text-xl font-semibold text-gray-600">Grupo: {{$grupo_nombre}}</div>
            </div>
            <div class="w-full overflow-y-scroll pr-4">
                <div class="w-full rounded border bg-gray-200 px-2 py-3 flex flex-col py-6">
                    @if($puede_publicar)
                    <form action="{{route('save_post')}}" method="POST" enctype="multipart/form-data" id="save_post">
                        @csrf
                        <input type="hidden" name="grupo_id" value="{{$grupo_id}}">
                        <div class="flex flex-row">
                            <div class="w-20 flex justify-center text-base font-bold text-gray-600">
                                Post
                            </div>
                            <div class="flex-1 pr-4">
                                <textarea rows=3 class="w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="text" name="post"  wire:model.defer="post"></textarea>
                                @error('post') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex flex-row py-3">
                            <div class="w-20">
                            &nbsp; 
                            </div>
                            <div class="flex-1 text-sm text-gray-600">
                                <x-jet-secondary-button wire:click.prevent="$set('file_include',true)"><i class="fas fa-plus"></i>&nbsp;&nbsp;Adjuntar archivo</x-jet-secondary-button>
                            </div>
                        </div>
                        @if($file_include)
                        <div class="flex flex-row pt-3">
                            <div class="w-20 flex justify-center text-base font-bold text-gray-600">                        
                            </div>
                            <div class="flex-1 flex flex-row items-center">
                                <x-jet-danger-button wire:click.prevent="$set('file_include',false)" class="py-1"><i class="fas fa-times-circle text-base"></i></x-jet-danger-button>
                                <input type="file" class="p-2 flex-1 text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="adjunto" class="text-sm"/>                
                            </div>
                        </div>
                        @endif
                        <div class="flex flex-row">
                            <div class="w-20">
                            &nbsp; 
                            </div>
                            <div class="flex-1 pr-4 text-sm text-gray-600 flex justify-end">
                                <x-jet-button wire:click.prevent="guardar_post"><i class="fas fa-comment"></i>&nbsp;&nbsp;&nbsp;Publicar</x-jet-button>
                            </div>
                        </div>
                    </form>
                    @endif
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
                        <div class="flex flex-1 px-5">
                            <x-jet-input class="flex-1 text-sm mr-5" type="text"  wire:model="filtro" placeholder="¿Que desea buscar?"/>
                        </div>           
                    </div>
                </div>
                <div class="w-full px-5 pt-6">
                    {{$posts->links()}}
                </div>
                <div class="w-full flex flex-col py-5 px-5">
                    @php
                    $registros=0;   
                    @endphp
                    @foreach ($posts as $post_consulta)
                        @php
                        $registros=$registros+1;   
                        @endphp
                        <div class="w-full flex flex-col bg-white p-3">
                            <div class="w-full flex flex-row">
                                <div class="w-3/4 flex flex-col">
                                    <div class="w-full text-gray-700 font-bold text-sm px-3">{{$post_consulta->nombre_usuario}} <span class="font-normal">publicó</span></div>
                                    <div class="w-full text-gray-700 text-xs px-3">{{$post_consulta->created_at}}</div>
                                </div>
                                <div class="flex flex-1 justify-end">
                                    @livewire('grupo-comunicacion.nuevo-comentario-post', ['post_id' => $post_consulta->id], key($post_consulta->id))
                                </div>
                            </div>
                            
                            <div class="w-full flex flex-col border rounded-lg shadow-lg px-5 py-3">
                                <div class="w-full text-gray-700 font-normal text-sm px-3 ">{!!nl2br($post_consulta->post)!!}</div>
                                @if($post_consulta->adjunto=='1')
                                <div class="w-full text-gray-700 font-normal text-sm px-3 pt-2">Archivo adjunto:</div>
                                <div class="w-full text-gray-700 font-normal text-sm px-3 text-red-500"><a href="/descarga/{{$post_consulta->archivo_adjunto}}"><i class="fas fa-file-download"></i> Archivo</a></div>
                                @endif                                
                            </div>
                        </div>
                        @foreach($post_consulta->comentarios as $comentario)
                        <div class="w-full flex flex-row bg-white p-3 text-xs">
                            <div class="w-20"></div>
                            <div class="w-full flex flex-col">
                                <div class="w-full text-gray-700 font-bold text-sm px-3">{{$comentario->nombre_usuario}} <span class="font-normal">comentó</span></div>
                                <div class="w-full text-gray-700 text-xs px-3">{{$comentario->created_at}}</div>
                                <div class="flex flex-1 bg-green-100 shadow-lg border rounded-lg px-3 py-1">
                                    {!!nl2br($comentario->comentario)!!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                    @if($registros==0)
                        No se encontraron registros
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

