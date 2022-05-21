<?php

namespace App\Http\Livewire\Grupo;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use App\Models\Grupo;

class ShowGrupos extends Component
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
        $grupos=Grupo::where('nombre','like','%'.$this->filtro.'%')
                        ->orWhere('descripcion','like','%'.$this->filtro.'%')
                        ->orderBy('nombre','asc')
                        ->paginate($this->elementos);
        return view('livewire.grupo.show-grupos',['grupos'=>$grupos]);
    }
    public function mount()
    {
        if(Auth::user()->perfil=='MIEMBRO')
        {
            return redirect()->to('/');
        }
    }
}
