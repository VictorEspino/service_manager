<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Topico;
use App\Models\User;
use App\Models\ActividadTopico;
use App\Models\ActividadCampos;
use App\Models\MiembroGrupo;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Livewire\WithFileUploads;

class NuevoTicket extends Component
{

    use WithFileUploads;

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
    public $emite_autorizacion;
    public $campos_requeridos=[];
    public $prioridad;

    public $atencion_seleccionable=false;
    public $atencion_por;
    public $usuarios_atencion_seleccionable=[];

    public $cambiar_usuario=false;
    public $buscar_usuario;
    public $usuarios_disponibles;

    public $agregar_invitado=false;
    public $buscar_invitado;
    public $invitados_disponibles;
    public $invitados_ticket;

    public $file_include=false;

    public $procesando=0;

    public function render()
    {
        return view('livewire.ticket.nuevo-ticket');
    }

    public function mount()
    {
        $sql_grupos_con_topico_autorizado="SELECT distinct a.grupo_id FROM actividad_topicos a,topico_puestos b WHERE a.topico_id=b.topico_id and b.puesto_id=".Auth::user()->puesto." and b.autorizado=1";
        $grupos_autorizados=DB::select(DB::raw($sql_grupos_con_topico_autorizado));
        $grupos_autorizados=collect($grupos_autorizados);
        $grupos_autorizados=$grupos_autorizados->pluck('grupo_id');

        $this->grupos=Grupo::where('estatus','1')
                        ->whereIn('id',$grupos_autorizados)
                        ->orderBy('nombre')->get();
        $this->de_id=Auth::user()->id;
        $this->de_etiqueta=Auth::user()->name;
    }

    public function updatedGrupo()
    {
        $this->descripcion_topico='';
        $this->campos_requeridos=[];  
        $this->emite_autorizacion=0;
        $this->atencion_seleccionable=false;
        $this->usuarios_atencion_seleccionable=[];

        $sql_topicos_autorizados_en_grupo="SELECT distinct a.topico_id FROM actividad_topicos a,topico_puestos b WHERE a.topico_id=b.topico_id and b.puesto_id=".Auth::user()->puesto." and b.autorizado=1 and a.grupo_id='".$this->grupo."'";
        $topicos_autorizados=DB::select(DB::raw($sql_topicos_autorizados_en_grupo));
        $topicos_autorizados=collect($topicos_autorizados);
        $topicos_autorizados=$topicos_autorizados->pluck('topico_id');
        
        $this->topicos_disponibles=ActividadTopico::with('topico')
                                ->where('grupo_id',$this->grupo)
                                ->whereIn('topico_id',$topicos_autorizados)
                                ->where('secuencia',0)
                                ->get();
                                
    }
    public function updatedTopico()
    {
        if($this->topico!="")
        {
            $topico=Topico::find($this->topico);
            $this->descripcion_topico=$topico->descripcion;
            $this->emite_autorizacion=$topico->emite_autorizacion;
            $actividad_inicial_ticket=ActividadTopico::where('topico_id',$this->topico)
                                        ->where('secuencia',0)
                                        ->get()
                                        ->first();

            $actividad_principal=$actividad_inicial_ticket->id;
            $grupo_inicial_atencion=$actividad_inicial_ticket->grupo_id;
            $tipo_asignacion_inicial=$actividad_inicial_ticket->tipo_asignacion;

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
            if($tipo_asignacion_inicial=='6') //SELECCIONABLE POR USUARIO
            {
                $this->atencion_seleccionable=true;
                $this->usuarios_atencion_seleccionable=MiembroGrupo::with('user')
                                                        ->where('grupo_id',$grupo_inicial_atencion)
                                                        ->get();
            }
            else
            { 
                $this->atencion_seleccionable=false;
                $this->usuarios_atencion_seleccionable=[];
            }
        }
        else
        {
            $this->campos_requeridos=[];  
            $this->descripcion_topico="";
            $this->emite_autorizacion=0;
            $this->atencion_seleccionable=false;
            $this->usuarios_atencion_seleccionable=[];
        }


    }
    public function updatedBuscarUsuario()
    {
        if(strlen($this->buscar_usuario)>2)
        {
        $this->usuarios_disponibles=User::where('name','like','%'.$this->buscar_usuario.'%')
                                        ->where('visible',1)
                                        ->where('estatus',1)
                                        ->get()
                                        ->take(5);
        }
    }
    public function cambiar_usuario($id,$nombre,$email)
    {
        $this->de_id=$id;
        $this->de_etiqueta=$nombre;
        $this->cambiar_usuario=false;
        $this->buscar_usuario="";
        $this->usuarios_disponibles="";
    }
    public function reset_usuario()
    {
        $this->de_id=Auth::user()->id;
        $this->de_etiqueta=Auth::user()->name;
        $this->cambiar_usuario=false;
        $this->buscar_usuario="";
        $this->usuarios_disponibles="";
    }
    public function updatedBuscarInvitado()
    {        
        if(strlen($this->buscar_invitado)>1)
        {
        $this->invitados_disponibles=User::where('name','like','%'.$this->buscar_invitado.'%')
                                        ->where('visible',1)
                                        ->where('estatus',1)
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
        $this->de_etiqueta=Auth::user()->name;
        $this->cambiar_usuario=false;
        $this->buscar_usuario="";
        $this->buscar_invitado="";
        $this->usuarios_disponibles="";
        $this->campos_requeridos=[];
        $this->open=false;
        $this->atencion_seleccionable=false;
        $this->atencion_por=null;
        $this->usuarios_atencion_seleccionable=[];
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function validacion()
    {
        $reglas = [
            'topico' => 'required',
            'asunto' => 'required',
            'descripcion_topico'=>'required',
          ];
        foreach ($this->campos_requeridos as $index => $campo) 
          {
            if($campo['requerido']=='1')
            {
                $reglas = array_merge($reglas, [
                    'campos_requeridos.'.$index.'.valor' => 'required',
                  ]);
            }
          }
        if($this->atencion_seleccionable)
        {
            $reglas = array_merge($reglas, [
                'atencion_por' => 'required',
              ]);   
        }
        
        //dd($reglas);
        $this->validate($reglas,
            [
                'required' => 'Campo requerido.',
                'numeric'=>'Debe ser un numero'
            ],
          );
    }
    public function guardar()
    {
        $this->validacion();
        $this->procesando=1;
        $this->emit('livewire_to_controller','nuevo_ticket');
    }

    public function archivo_seleccionado($valor,$campo)
    {
        $campo_archivo=explode('.',$campo);
        $this->campos_requeridos[intval($campo_archivo[1])]['valor']=$valor;
    }
    public function abrir_ventana()
    {
        $this->open=true;
        $this->procesando=0;
    }
}
