<?php

namespace App\Http\Livewire\Topico;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Topico;
use App\Models\ActividadTopico;
use App\Models\Grupo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShowTopicos extends Component
{
    use WithPagination;

    public $filtro;
    public $elementos;
    public $grupo;
    public $grupos;
    
    protected $listeners = ['topicoAgregado' => 'render','topicoModificado'=>'render'];

    public function mount()
    {
        if(Auth::user()->perfil=='MIEMBRO')
        {
            return redirect()->to('/');
        }
        $this->grupos=Grupo::orderBy('nombre','asc')->get();
        $this->filtro='';
        $this->grupo=0;
        $this->elementos=10;
    }
    public function updatedFiltro()
    {
        $this->resetPage();
    }
    public function updatedElementos()
    {
        $this->resetPage();
    }
    public function updatedGrupo()
    {
        $this->resetPage();
    }

    public function render()
    {
       // $topicos=Topico::where('nombre','like','%'.$this->filtro.'%')
       //                 ->orWhere('descripcion','like','%'.$this->filtro.'%')
       //                 ->orderBy('nombre','asc')
       //                 ->paginate($this->elementos);

        $filtro_local=$this->filtro;
        $grupo_local=$this->grupo;

        $topicos=DB::table('actividad_topicos')
                    ->where('secuencia',0)
                    ->join('topicos', 'actividad_topicos.topico_id', '=', 'topicos.id')
                    ->select('topicos.*', 'actividad_topicos.grupo_id')
                    ->when($this->filtro!="",function ($query) use ($filtro_local)
                            {
                                $query->where('topicos.nombre','like','%'.$filtro_local.'%');
                                $query->orWhere('topicos.descripcion','like','%'.$filtro_local.'%');
                            }
                        )
                    ->when($this->grupo!=0,function ($query) use ($grupo_local)
                        {
                            $query->where('actividad_topicos.grupo_id',$grupo_local);
                        }
                    )
                    ->orderBy('topicos.nombre','asc')
                    ->paginate($this->elementos);
        
        return view('livewire.topico.show-topicos',['topicos'=>$topicos]);
    }
}
