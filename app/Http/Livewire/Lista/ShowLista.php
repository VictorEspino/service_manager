<?php

namespace App\Http\Livewire\Lista;

use Livewire\Component;
use App\Models\Lista;

use Livewire\WithPagination;

class ShowLista extends Component
{
    use WithPagination;

    public $filtro='';
    public $elementos=10;

    protected $listeners = ['listaAgregada' => 'render','listaModificada'=>'render'];

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
        $listas=Lista::where('nombre','like','%'.$this->filtro.'%')
                        ->orWhere('descripcion','like','%'.$this->filtro.'%') 
                        ->orderBy('nombre','asc')
                        ->paginate($this->elementos);
        return view('livewire.lista.show-lista',['listas'=>$listas]);
    }
}
