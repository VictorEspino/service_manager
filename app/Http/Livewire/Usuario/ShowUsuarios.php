<?php

namespace App\Http\Livewire\Usuario;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\User;

class ShowUsuarios extends Component
{
    use WithPagination;

    public $filtro='';
    public $elementos=10;

    protected $listeners = ['usuarioAgregado' => 'render','usuarioModificado'=>'render'];

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
        $users=User::with('area_user','subarea','puesto_desc')->where('name','like','%'.$this->filtro.'%')
                        ->where('visible',1)
                        ->orderBy('name','asc')
                        //->get();
        //dd($users);
                        ->paginate($this->elementos);
        return view('livewire.usuario.show-usuarios',['users'=>$users]);
    }

}
