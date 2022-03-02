<?php

namespace App\Http\Livewire\GrupoComunicacion;

use Livewire\Component;
use App\Models\GrupoComunicacion;
use App\Models\MiembroGrupoComunicacion;
use App\Models\GrupoComunicacionPost;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ShowPosts extends Component
{
    use WithPagination;

    public $filtro='';
    public $elementos=10;

    public $grupo_nombre;
    public $grupo_id;
    public $post;

    public $file_include=false;
    public $puede_publicar=false;

    public function render()
    {
        $posts=GrupoComunicacionPost::where('post','like','%'.$this->filtro.'%')
                        ->where('grupo_id',$this->grupo_id)
                        ->orderBy('id','desc')
                        ->paginate($this->elementos);
        return view('livewire.grupo-comunicacion.show-posts',['posts'=>$posts]);
    }
    public function mount($grupo_id)
    {
        $grupo=GrupoComunicacion::find($grupo_id);
        $this->grupo_id=$grupo_id;
        $this->grupo_nombre=$grupo->nombre;
        $managers=MiembroGrupoComunicacion::select('user_id')
                                ->where('manager',1)
                                ->where('grupo_id',$grupo_id)
                                ->get();
        foreach($managers as $manager)
        {
            if($manager->user_id==Auth::user()->id)
                $this->puede_publicar=true;
        }
                        
    }
    public function guardar_post()
    {
        $reglas = [
            'post' => 'required',
          ];
        $this->validate($reglas,
                [
                    'required' => 'Campo requerido.',
                    'numeric'=>'Debe ser un numero'
                ],
            );
        $this->emit('livewire_to_controller','save_post');
    }
    public function updatingElementos()
    {
        $this->resetPage();
    }
    public function updatingFiltro()
    {
        $this->resetPage();
    }
}
