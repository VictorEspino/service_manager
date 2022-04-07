<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\MiembroGrupo;
use App\Models\User;

class Abiertos extends Component
{
    use WithPagination;

    public $elementos=10;
    public $filtro="";
    public $filtro_asesor=-1;
    public $asesores;

    public function render()
    {
        $sql=getSQLUniverso(Auth::user()->id);
        $universo=DB::select(DB::raw($sql));
        $universo=collect($universo)->pluck('ticket_id');
        $filtro=$this->filtro;
        $filtro_asesor=$this->filtro_asesor;
        $tickets=Ticket::with('solicitante','topico','asesor','actividades')
                    ->select('*',DB::raw('TIMESTAMPDIFF(MINUTE,updated_at,NOW()) as min_adicional'))
                    ->when($filtro!="",function ($query) use ($filtro)
                    {   
                        $query->where('asunto','like','%'.$filtro.'%');
                    }
                    )
                    ->when($filtro_asesor!=-1,function ($query) use ($filtro_asesor)
                    {   
                        $query->where('asignado_a',$filtro_asesor);
                    }
                    )
                    ->whereIn('id',$universo)
                    ->where('estatus',1)
                    ->paginate($this->elementos);
        return view('livewire.ticket.abiertos',['tickets'=>$tickets,'asesores'=>$this->asesores]);
    }
    public function mount()
    {
        $grupos_usuario=MiembroGrupo::select('grupo_id')
                                    ->where('user_id',Auth::user()->id)
                                    ->get();
        $grupos_usuario=$grupos_usuario->pluck('grupo_id');
        $usuarios_relacionados=MiembroGrupo::select('user_id')
                                    ->whereIn('grupo_id',$grupos_usuario)
                                    ->get();
        $this->asesores=User::whereIn('id',$usuarios_relacionados->pluck('user_id'))
                            ->orderBy('name','asc')
                            ->get();

    }
}
