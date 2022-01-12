<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;

use App\Models\Ticket;
use App\Models\ActividadTicket;
use App\Models\ActividadTicketCampos;
use App\Models\TicketAvance;

use App\Models\Grupo;
use App\Models\User;
use App\Models\MiembroGrupo;

use Illuminate\Support\Facades\Auth;

class TicketDetalle extends Component
{
    public $ticket_id;

    public $open_confirm_status=false;
    public $open_reasignar=false;
    public $procesando=0;

    public $nuevo_posible_estatus;
    public $nuevo_posible_valor_estatus;
    public $valor_boton_cambio_estatus;
    
    public $asunto;
    public $topico_nombre;
    public $solicitante;
    public $solicitante_id;
    public $asesor;

    public $estatus;

    public $file_include=false;

    public $avance;
    public $cerrar_al_responder;
    public $esperando_respuesta;

    public $avances_ticket=[];

    public $grupos_disponibles;
    public $miembros_disponibles;

    public $grupo_seleccionado;
    public $miembro_seleccionado;
    public $mensaje_reasignacion;

    public $user_cerrador;
    public $nombre_cerrador;
    public $cierre_at;

    public function render()
    {
        $ticket=Ticket::with('topico','solicitante','asesor')->find($this->ticket_id);
        
        $this->asunto=$ticket->asunto;
        $this->topico_nombre=$ticket->topico->nombre;
        $this->solicitante=$ticket->solicitante->name;
        $this->solicitante_id=$ticket->solicitante->id;
        $this->asesor=$ticket->asesor->name;
        $this->user_cerrador=$ticket->user_cerrador;
        $this->nombre_cerrador=$ticket->nombre_cerrador;
        $this->cierre_at=$ticket->cierre_at;

        $this->estatus=$ticket->estatus;

        if($ticket->estatus=='1')
        {
            $this->nuevo_posible_estatus="CERRADO";
            $this->nuevo_posible_valor_estatus=2;
            $this->valor_boton_cambio_estatus="CERRAR";
        }
        if($ticket->estatus=='2' || $ticket->estatus=='3')
        {
            $this->nuevo_posible_estatus="ABIERTO";
            $this->nuevo_posible_valor_estatus=1;
            $this->valor_boton_cambio_estatus="ABRIR";
        }

        $this->avances_ticket=$this->getAvances();
        return view('livewire.ticket.ticket-detalle');
    }
    public function mount($id)
    {
        $this->ticket_id=$id;
        $this->grupos_disponibles=Grupo::orderBy('nombre')->get();

        $ticket=Ticket::find($this->ticket_id);
        $this->grupo_seleccionado=ActividadTicket::where('ticket_id',$this->ticket_id)
                                        ->where('secuencia',$ticket->actividad_actual)
                                        ->get()
                                        ->first()
                                        ->grupo_id;
        $this->miembro_seleccionado=$ticket->asignado_a;

        $this->miembros_disponibles=MiembroGrupo::with('user')->where('grupo_id',$this->grupo_seleccionado)
                                    ->get();
    }

    private function getAvances()
    {
        $avances=[];

        $ticket=Ticket::with('solicitante')->find($this->ticket_id);

        $descripcion_inicial=ActividadTicket::where('ticket_id',$this->ticket_id)
                                            ->where('secuencia',0)
                                            ->get()
                                            ->first();

        $campos_personalizados=ActividadTicketCampos::where('actividad_ticket_id',$descripcion_inicial->id)
                                ->get();

        $desc_inicial=$descripcion_inicial->descripcion;
        $x=0;
        foreach($campos_personalizados as $campos)
        {
            if($x==0)
            {
                $desc_inicial=$desc_inicial."<br /><br /><b>Campos Incluidos</b>";
            }
            if($campos->tipo_control=="Texto" || $campos->tipo_control=="Lista")
            {
                $desc_inicial=$desc_inicial."<br />".$campos->etiqueta.": ".$campos->valor;
            }
            if($campos->tipo_control=="File")
            {
                $desc_inicial=$desc_inicial."<br />".$campos->etiqueta.": <a href='/archivos/".$campos->valor."' download><i class='text-red-400 text-base fas fa-file-download'></i></a>";
            }
            $x=$x+1;
        }

        if($ticket->adjunto=='1')
        {
            $desc_inicial=$desc_inicial."<br /><br /><b>Archivo adjunto</b>";
            $desc_inicial=$desc_inicial."<br />Descargar: <a href='/archivos/".$ticket->archivo_adjunto."' download><i class='text-red-400 text-base fas fa-file-download'></i></a>";
        }
                               
        $avances[]=[
            'created_at'=>$ticket->created_at,
            'tipo_avance'=>'1',
            'nombre'=>$ticket->solicitante->name,
            'avance'=>$desc_inicial,
            'adjunto'=>0,
            'archivo_adjunto'=>''
        ];

        $avances_atencion=TicketAvance::where('ticket_id',$this->ticket_id)
                                    ->get();

        foreach($avances_atencion as $avance)
        {
            $avances[]=[
                'created_at'=>$avance->created_at,
                'tipo_avance'=>$avance->tipo_avance,
                'nombre'=>$avance->nombre_usuario,
                'avance'=>$avance->avance.($avance->adjunto=='1'?"<br>Archivo adjunto: <a href='/archivos/".$avance->archivo_adjunto."' download><i class='text-red-400 text-base fas fa-file-download'></i></a>":""),
                'adjunto'=>$avance->adjunto,
                'archivo_adjunto'=>$avance->archivo_adjunto
            ];
        }

        return($avances);
    }
    public function cambio_estatus()
    {
        $this->procesando=1;
        $this->open_confirm_status=false;

        $this->user_cerrador=NULL;
        $this->nombre_cerrador=NULL;
        $this->cierre_at=NULL;

        if($this->nuevo_posible_valor_estatus==2)
        {
            $this->user_cerrador=Auth::user()->id;
            $this->nombre_cerrador=Auth::user()->name;
            $this->cierre_at=now()->toDateTimeString();        
        }

        Ticket::where('id',$this->ticket_id)
            ->update(['estatus'=>$this->nuevo_posible_valor_estatus,
                      'user_cerrador'=>$this->user_cerrador,
                      'nombre_cerrador'=>$this->nombre_cerrador,
                      'cierre_at'=>$this->cierre_at
                    ]);  
        
        $estatus_actual="";
        if($this->estatus=='1')
        {
            $estatus_actual="ABIERTO";
        }
        if($this->estatus=='2')
        {
            $estatus_actual="CERRADO"; 
        }
        if($this->estatus=='3')
        {
            $estatus_actual="TERMINADO";
        }

        TicketAvance::create([
            'ticket_id'=>$this->ticket_id,
            'user_id'=>Auth::user()->id,
            'nombre_usuario'=>Auth::user()->name,
            'avance'=>'Cambió el estatus '.$estatus_actual.' -> '.$this->nuevo_posible_estatus,
            'tipo_avance'=>2,
            ]);
        return;
    }
    public function open_modal_confirm_status()
    {
        $this->open_confirm_status=true;
        $this->procesando=0;

    }
    public function open_reasignar_modal()
    {
        $this->open_reasignar=true;
        $this->procesando=0;
    }

    public function updatedGrupoSeleccionado()
    {
        $this->miembros_disponibles=MiembroGrupo::with('user')->where('grupo_id',$this->grupo_seleccionado)
                                    ->get();
    }
    public function reasignar()
    {
        $this->procesando=1;
        $usuario=User::find($this->miembro_seleccionado);
        TicketAvance::create([
            'ticket_id'=>$this->ticket_id,
            'user_id'=>Auth::user()->id,
            'nombre_usuario'=>Auth::user()->name,
            'avance'=>"Reasignacion de ticket a ".strtoupper($usuario->name)."\r\nMensaje reasignacion: ".$this->mensaje_reasignacion,
            'tipo_avance'=>3,
            ]);
        Ticket::where('id',$this->ticket_id)->update(['asignado_a'=>$this->miembro_seleccionado]);
        $this->open_reasignar=false;
    }
}
