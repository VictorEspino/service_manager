<?php

namespace App\Http\Livewire\Topico;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Topico;

class ShowTopicos extends Component
{
    use WithPagination;

    public $filtro='';
    public $elementos=10;

    protected $listeners = ['topicoAgregado' => 'render'];

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
        $topicos=Topico::where('nombre','like','%'.$this->filtro.'%')
                        ->orWhere('descripcion','like','%'.$this->filtro.'%')
                        ->orderBy('nombre','asc')
                        ->paginate($this->elementos);
        return view('livewire.topico.show-topicos',['topicos'=>$topicos]);
    }
}
