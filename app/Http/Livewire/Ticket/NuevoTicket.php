<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Topico;
use App\Models\User;
use App\Models\ActividadTopico;
use App\Models\ActividadCampos;

use Illuminate\Support\Facades\Auth;

class NuevoTicket extends Component
{

    public $open=false;

    public $grupo;//solo se usa para aplicar los filtros del segundo combo
    public $grupos;//es parte de la pantalla para filtrar el topico
    public $topicos_disponibles;

    public $asunto;
    public $de_id;
    public $de_etiqueta;
    public $descripcion;
    public $topico;
    public $descripcion_topico;
    public $campos_requeridos;
    public $prioridad;
    public $atencion_por;

    public $cambiar_usuario=false;
    public $buscar_usuario;
    public $usuarios_disponibles;

    public $agregar_invitado=false;
    public $buscar_invitado;
    public $invitados_disponibles;
    public $invitados_ticket;

    public $file_include=false;

    public function render()
    {
        return view('livewire.ticket.nuevo-ticket');
    }

    public function mount()
    {
        $this->grupos=Grupo::orderBy('nombre')->get();
        $this->de_id=Auth::user()->id;
        $this->de_etiqueta=Auth::user()->name." <".Auth::user()->email.">";
    }

    public function updatedGrupo()
    {
        $this->descripcion_topico='';
        $this->topicos_disponibles=ActividadTopico::with('topico')
                                ->where('grupo_id',$this->grupo)
                                ->where('secuencia',0)
                                ->get();
    }
    public function updatedTopico()
    {
        $this->descripcion_topico=Topico::find($this->topico)->descripcion;
        $actividad_principal=ActividadTopico::where('topico_id',$this->topico)
                                            ->where('secuencia',0)
                                            ->get()
                                            ->first()
                                            ->id;
        $campos_actividad=ActividadCampos::where('actividad_id',$actividad_principal)
                                            ->get();
        $this->campos_requeridos=[];
        foreach($campos_actividad as $campos)
        {
            $this->campos_requeridos[]=[
                                    'referencia'=>$campos->id,
                                    'etiqueta'=>$campos->etiqueta,
                                    'tipo_control'=>$campos->tipo_control,
                                    'requerido'=>$campos->requerido,
                                    'lista_id'=>$campos->lista_id,
                                    'valor'=>'',
            ];
        }
    }
    public function updatedBuscarUsuario()
    {
        if(strlen($this->buscar_usuario)>1)
        {
        $this->usuarios_disponibles=User::where('name','like','%'.$this->buscar_usuario.'%')
                                        ->get()
                                        ->take(5);
        }
    }
    public function cambiar_usuario($id,$nombre,$email)
    {
        $this->de_id=$id;
        $this->de_etiqueta=$nombre." <".$email.">";
        $this->cambiar_usuario=false;
        $this->buscar_usuario="";
        $this->usuarios_disponibles="";
    }
    public function reset_usuario()
    {
        $this->de_id=Auth::user()->id;
        $this->de_etiqueta=Auth::user()->name." <".Auth::user()->email.">";
        $this->cambiar_usuario=false;
        $this->buscar_usuario="";
        $this->usuarios_disponibles="";
    }
    public function updatedBuscarInvitado()
    {        
        if(strlen($this->buscar_invitado)>1)
        {
        $this->invitados_disponibles=User::where('name','like','%'.$this->buscar_invitado.'%')
                                        ->get()
                                        ->take(5);
        $this->agregar_invitado=true;
        }
        else
        {
            $this->agregar_invitado=false;
        }

    }
    public function agregar_invitado_ticket($id,$nombre,$email)
    {
        $this->invitados_ticket[]=[
                                    'id'=>$id,
                                    'nombre'=>$nombre,
                                    'email'=>$email,
                                    ];
        $this->agregar_invitado=false;
        $this->buscar_invitado="";
    }
    public function borrar_invitado_ticket($id)
    {
        unset($this->invitados_ticket[$id]);
        $this->invitados_ticket=array_values($this->invitados_ticket);
    }
    public function cancelar()
    {
        $this->agregar_invitado=false;
        $this->de_id="";
        $this->de_etiqueta="";
        $this->descripcion_topico="";
        $this->topico="";
        $this->grupo="";
        $this->asunto="";
        $this->invitados_ticket=[];
        $this->de_id=Auth::user()->id;
        $this->de_etiqueta=Auth::user()->name." <".Auth::user()->email.">";
        $this->cambiar_usuario=false;
        $this->buscar_usuario="";
        $this->buscar_invitado="";
        $this->usuarios_disponibles="";
        $this->campos_requeridos=[];
        $this->open=false;
    }
}
