<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;
use App\Models\TiempoTranscurrido;
use App\Models\MiembroGrupo;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AvisoAtraso extends Component
{
    use WithPagination;

    public $elementos=10;
    public $filtro="";
    public $filtro_asesor=-1;
    public $asesores;

    public function render()
    {
        $grupos_manager=MiembroGrupo::select('grupo_id')
                                    ->where('user_id',Auth::user()->id)
                                    ->where('manager',1)
                                    ->get();
        $grupos_manager=$grupos_manager->pluck('grupo_id');
        $filtro=$this->filtro;
        $filtro_asesor=$this->filtro_asesor;
        $registros_escalacion=TiempoTranscurrido::with('solicitante','topico','asesor')
                                                ->whereIn('grupo_id',$grupos_manager)
                                                ->whereRaw('tiempo_transcurrido>sla')
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
                                                ->paginate($this->elementos);
        
        return view('livewire.ticket.aviso-atraso',['tickets'=>$registros_escalacion]);
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
