<?php

namespace App\Http\Livewire\GrupoComunicacion;

use Livewire\Component;
use App\Models\GrupoComunicacionPostComentario;
use Illuminate\Support\Facades\Auth;

class NuevoComentarioPost extends Component
{
    public $open=false;
    public $post_id;
    public $comentario;
    public $procesando=0;
    public function render()
    {
        return view('livewire.grupo-comunicacion.nuevo-comentario-post');
    }
    public function mount($post_id)
    {
        $this->post_id=$post_id;
    }
    public function cancelar()
    {
        $this->comentario="";
        $this->open=false;
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function abrir()
    {
        $this->procesando=0;
        $this->open=true;
    }
    public function validacion()
    {
        $reglas = [
            'comentario'=>'required',
          ];
        //dd($reglas);
        $this->validate($reglas,
            [
                'required' => 'Campo requerido.',
            ],
          );

    }
    public function guardar()
    {
        $this->validacion();
        $this->procesando=1;
        GrupoComunicacionPostComentario::create([
                'post_id'=>$this->post_id,
                'user_id'=>Auth::user()->id,
                'nombre_usuario'=>Auth::user()->name,
                'comentario'=>$this->comentario
        ]);
        $this->open=false;
        $this->comentario="";
        $this->emit('nuevo_comentario');
    }
}
