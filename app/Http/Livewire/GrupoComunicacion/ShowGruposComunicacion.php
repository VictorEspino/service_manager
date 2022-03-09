<?php

namespace App\Http\Livewire\GrupoComunicacion;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\GrupoComunicacion;

class ShowGruposComunicacion extends Component
{
    use WithPagination;

    public $filtro='';
    public $elementos=10;

    protected $listeners = ['grupoAgregado' => 'render','grupoModificado'=>'render'];

    public function updatingElementos()
    {
        $this->resetPage();
    }
    public function updatingFiltro()
    {
        $this->resetPage();
    }

    public function render()
    {
        $grupos=GrupoComunicacion::where('nombre','like','%'.$this->filtro.'%')
                        ->orWhere('descripcion','like','%'.$this->filtro.'%')
                        ->orderBy('nombre','asc')
                        ->paginate($this->elementos);
        return view('livewire.grupo-comunicacion.show-grupos-comunicacion',['grupos'=>$grupos]);
    }
}
