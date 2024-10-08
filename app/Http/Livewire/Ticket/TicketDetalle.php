<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;

use App\Models\Ticket;
use App\Models\ActividadTicket;
use App\Models\ActividadTicketCampos;
use App\Models\TicketAvance;
use App\Models\TicketAvancesCampo;
use App\Models\InvitadoTicket;

use App\Models\Grupo;
use App\Models\User;
use App\Models\MiembroGrupo;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketDetalle extends Component
{
    public $ticket_id;
    public $buscar;
    public $busqueda;

    public $open_confirm_status=false;
    public $open_reasignar=false;
    public $open_avanzar=false;
    public $open_previa=false;
    public $open_confirm_rechazo=false;
    public $open_confirm_autorizacion=false;
    public $procesando=0;

    public $emite_autorizacion;
    public $resultado_autorizacion;
    public $nuevo_posible_estatus;
    public $nuevo_posible_valor_estatus;
    public $valor_boton_cambio_estatus;

    
    public $asunto;
    public $topico_nombre;
    public $solicitante;
    public $solicitante_id;
    public $area_solicitante;
    public $subarea_solicitante;
    public $asignado_a;
    public $asesor;

    public $estatus;

    public $file_include=false;

    public $texto_avance;
    public $cerrar_al_responder;
    public $esperando_respuesta;

    public $avances_ticket=[];

    public $grupos_disponibles=[];
    public $miembros_disponibles=[];

    public $grupo_seleccionado;
    public $miembro_seleccionado;
    public $mensaje_reasignacion;

    public $user_cerrador;
    public $nombre_cerrador;
    public $cierre_at;

    public $time_to;
    public $actividad_actual;
    public $actividades_total;
    public $actividades_atencion=[];

    public $invitados_ticket=[];

    public function render()
    {
        $ticket=Ticket::with('topico','solicitante','asesor','area_solicitante','subarea_solicitante')->find($this->ticket_id);

        $this->asunto=$ticket->asunto;
        $this->topico_nombre=$ticket->topico->nombre;
        $this->solicitante=$ticket->solicitante->name;
        $this->solicitante_id=$ticket->solicitante->id;

        $this->area_solicitante=$ticket->area_solicitante->nombre;
        $this->subarea_solicitante=$ticket->subarea_solicitante->nombre;;

        $this->asesor=$ticket->asesor->name;
        $this->user_cerrador=$ticket->user_cerrador;
        $this->nombre_cerrador=$ticket->nombre_cerrador;
        $this->cierre_at=$ticket->cierre_at;
        $this->emite_autorizacion=$ticket->emite_autorizacion;
        $this->resultado_autorizacion=$ticket->resultado_autorizacion;
        $this->estatus=$ticket->estatus;
        $this->asignado_a=$ticket->asignado_a;

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
        
        $this->time_to=$ticket->time_to; //Dato para saber si esta en espera o en atencion
        $this->actividad_actual=$ticket->actividad_actual;
        $this->actividades_total=$ticket->n_actividades;

        if($this->actividades_total>1)
        {
            $this->actividades_atencion=ActividadTicket::select('secuencia','nombre')
                                                ->where('ticket_id',$this->ticket_id)
                                                ->orderBy('secuencia','asc')
                                                ->get();
        }
        return view('livewire.ticket.ticket-detalle');
    }
    public function mount($id)
    {
        $this->ticket_id=$id;
        $grupos_disponibles_DB=Grupo::orderBy('nombre')->get();

        $grupos_disponibles[]=['id'=>999999,'nombre'=>'INVITADOS TICKET'];

        foreach($grupos_disponibles_DB as $grupo)
        {
            $grupos_disponibles[]=['id'=>$grupo->id,'nombre'=>$grupo->nombre];
        }
        $this->grupos_disponibles=$grupos_disponibles;


        $ticket=Ticket::find($this->ticket_id);

        $this->grupo_seleccionado=ActividadTicket::where('ticket_id',$this->ticket_id)
                                        ->where('secuencia',$ticket->actividad_actual)
                                        ->get()
                                        ->first()
                                        ->grupo_id;
        $this->miembro_seleccionado=$ticket->asignado_a;

        $miembros_disponibles=MiembroGrupo::with('user')->where('grupo_id',$this->grupo_seleccionado)
                                    ->get();
        $this->miembros_disponibles=[];
        foreach($miembros_disponibles as $miembro_grupo)
        {
            $this->miembros_disponibles[]=[
                                            'user_id'=>$miembro_grupo->user_id,
                                            'name'=>$miembro_grupo->user->name
                                        ];
        }
        $invitados_ticket=InvitadoTicket::with('user','area','subarea')->where('ticket_id',$id)->get();
        
        $involucrados_a_desplegar=[];

        foreach($invitados_ticket as $invitado_del_ticket)
        {
            $esta_presente="NO";
            foreach($involucrados_a_desplegar as $existente)
            {
                if($existente["user"]==$invitado_del_ticket->user->name &&
                   $existente["area"]==$invitado_del_ticket->area->nombre &&
                   $existente["subarea"]==$invitado_del_ticket->subarea->nombre
                  )
                {
                    $esta_presente="SI";
                }
            }
            if($esta_presente=="NO")
            {
                $involucrados_a_desplegar[]=[
                    'user'=>$invitado_del_ticket->user->name,
                    'area'=>$invitado_del_ticket->area->nombre,
                    'subarea'=>$invitado_del_ticket->subarea->nombre
                ];
            }
        }
        
        //INTEGRA LOS MIEMBROS DEL GRUPO DE ATENCION

        $grupos_del_ticket=ActividadTicket::select(DB::raw('distinct grupo_id as grupo'))
                                            ->where('ticket_id',$this->ticket_id)
                                            ->get();
        $grupos_del_ticket=$grupos_del_ticket->pluck('grupo');
        
        $miembros_de_grupos_de_ticket=MiembroGrupo::with('user','user.area_user','user.subarea')
                                            ->whereIn('grupo_id',$grupos_del_ticket)
                                            ->get();

        foreach($miembros_de_grupos_de_ticket as $invitado_del_ticket)
        {
            $esta_presente="NO";
            foreach($involucrados_a_desplegar as $existente)
            {
                if($existente["user"]==$invitado_del_ticket->user->name &&
                   $existente["area"]==$invitado_del_ticket->user->area_user->nombre &&
                   $existente["subarea"]==$invitado_del_ticket->user->subarea->nombre
                  )
                {
                    $esta_presente="SI";
                }
            }
            if($esta_presente=="NO")
            {
                $involucrados_a_desplegar[]=[
                    'user'=>$invitado_del_ticket->user->name,
                    'area'=>$invitado_del_ticket->user->area_user->nombre,
                    'subarea'=>$invitado_del_ticket->user->subarea->nombre
                ];
            }
        }
        $this->invitados_ticket=$involucrados_a_desplegar;
    } 
    
    public function retroceder_etapa()
    {
        $this->procesando=1;
        $ticket=Ticket::find($this->ticket_id);
        $actividad_avance=$this->actividad_actual-1;

        $actividad_ticket=ActividadTicket::where('ticket_id',$this->ticket_id)
        ->where('secuencia',$actividad_avance)
        ->get()
        ->first();
        
        $asignado_previo=0;
        if($actividad_avance=='0'){$asignado_previo=$ticket->a_a0;}
        if($actividad_avance=='1'){$asignado_previo=$ticket->a_a1;}
        if($actividad_avance=='2'){$asignado_previo=$ticket->a_a2;}
        if($actividad_avance=='3'){$asignado_previo=$ticket->a_a3;}
        if($actividad_avance=='4'){$asignado_previo=$ticket->a_a4;}
        if($actividad_avance=='5'){$asignado_previo=$ticket->a_a5;}
        if($actividad_avance=='6'){$asignado_previo=$ticket->a_a6;}
        if($actividad_avance=='7'){$asignado_previo=$ticket->a_a7;}
        if($actividad_avance=='8'){$asignado_previo=$ticket->a_a8;}
        
        //if(intval($asignado_previo)==0)
        //{
        //    //Correr asignacion dinamica
        //    $asignado_previo=1;
        //}
        TicketAvance::create([
            'ticket_id'=>$this->ticket_id,
            'user_id'=>Auth::user()->id,
            'nombre_usuario'=>Auth::user()->name,
            'avance'=>'Ticket llevado a etapa previa : '.$actividad_ticket->nombre.($actividad_avance==0?' (INICIAL)':''),
            'tipo_avance'=>4,
            ]);

        $this->update_tiempos($ticket); 
        Ticket::where('id',$this->ticket_id)
                ->update([
                    'actividad_actual'=>$actividad_avance,
                    'asignado_a'=>$asignado_previo,
                    'time_to'=>$actividad_avance
                ]);

        $this->open_previa=false;
        $this->emit('etapa');
        //return redirect(request()->header('Referer'));
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
                $archivo_valor_desplegar="<br />".$campos->etiqueta.": <a href='/descarga/".$campos->valor."' download><i class='text-red-400 text-base fas fa-file-download'></i> <span class='text-xs text-red-400'>Archivo</span></a>";
                if($campos->valor=="")
                {
                    $archivo_valor_desplegar="<br />".$campos->etiqueta.": SIN ARCHIVO";
                }
                $desc_inicial=$desc_inicial."".$archivo_valor_desplegar;
            }
            $x=$x+1;
        }

        if($ticket->adjunto=='1')
        {
            $desc_inicial=$desc_inicial."<br /><br /><b>Archivo adjunto</b>";
            $desc_inicial=$desc_inicial."<br />Descargar: <a href='/descarga/".$ticket->archivo_adjunto."' download><i class='text-red-400 text-base fas fa-file-download'></i> <span class='text-xs text-red-400'>Archivo</span></a>";
        }
                               
        $avances[]=[
            'created_at'=>$ticket->created_at,
            'tipo_avance'=>'1',
            'nombre'=>$ticket->solicitante->name,
            'avance'=>$desc_inicial,
            'adjunto'=>0,
            'archivo_adjunto'=>''
        ];

        $avances_atencion=TicketAvance::with('campos')
                                    ->where('ticket_id',$this->ticket_id)
                                    ->get();
        //dd($avances_atencion);
        foreach($avances_atencion as $avance)
        {
        
            $desc_inicial=$avance->avance.($avance->adjunto=='1'?"<br>Archivo adjunto: <a href='/descarga/".$avance->archivo_adjunto."' download><i class='text-red-400 text-base fas fa-file-download'></i> <span class='text-xs text-red-400'>Archivo</span></a>":"");
            
            if($avance->tipo_avance=='4')
            {
                $x=0;
                foreach($avance->campos as $campos)
                {
                    if($x==0)
                    {
                        $desc_inicial=$desc_inicial."<br /><br /><b>Campos Incluidos</b>";
                    }
                    if($campos->tipo=="Texto" || $campos->tipo=="Lista")
                    {
                        $desc_inicial=$desc_inicial."<br />".$campos->etiqueta.": ".$campos->valor;
                    }
                    if($campos->tipo=="File")
                    {
                        $archivo_valor_desplegar="<br />".$campos->etiqueta.": <a href='/descarga/".$campos->valor."' download><i class='text-red-400 text-base fas fa-file-download'></i></a>";
                        if($campos->valor=="")
                        {
                            $archivo_valor_desplegar="<br />".$campos->etiqueta.": SIN ARCHIVO";
                        }
                        $desc_inicial=$desc_inicial."".$archivo_valor_desplegar;
                    }
                    $x=$x+1;
                }
            }

            $avances[]=[
                'created_at'=>$avance->created_at,
                'tipo_avance'=>$avance->tipo_avance,
                'nombre'=>$avance->nombre_usuario,
                'avance'=>$desc_inicial,
                'adjunto'=>$avance->adjunto,
                'archivo_adjunto'=>$avance->archivo_adjunto
            ];
        }

        return($avances);
    }
    private function update_tiempos($ticket)
    {
        $ultima_actualizacion = new \Carbon\Carbon($ticket->updated_at);
        $actual_actualizacion = new \Carbon\Carbon(now()->toDateTimeString());
        $minutesDiff=$ultima_actualizacion->diffInSeconds($actual_actualizacion);
        $campo_tiempos=$ticket->time_to=='-1'?'t_solicitante':'t_a'.$ticket->time_to;
        $minutos_anteriores=0;
        if($ticket->time_to=='-1') $minutos_anteriores=$ticket->t_solicitante;
        if($ticket->time_to=='0') $minutos_anteriores=$ticket->t_a0;
        if($ticket->time_to=='1') $minutos_anteriores=$ticket->t_a1;
        if($ticket->time_to=='2') $minutos_anteriores=$ticket->t_a2;
        if($ticket->time_to=='3') $minutos_anteriores=$ticket->t_a3;
        if($ticket->time_to=='4') $minutos_anteriores=$ticket->t_a4;
        if($ticket->time_to=='5') $minutos_anteriores=$ticket->t_a5;
        if($ticket->time_to=='6') $minutos_anteriores=$ticket->t_a6;
        if($ticket->time_to=='7') $minutos_anteriores=$ticket->t_a7;
        if($ticket->time_to=='8') $minutos_anteriores=$ticket->t_a8;
        if($ticket->time_to=='9') $minutos_anteriores=$ticket->t_a9;
        $nuevo_tiempo=$minutos_anteriores+$minutesDiff;

        Ticket::where('id',$ticket->id)->update([
                                            $campo_tiempos=>$nuevo_tiempo,
                                    ]);


    }
    public function autorizacion($resultado)
    {
        $this->procesando=1;
        $this->open_confirm_rechazo=false;
        $this->open_confirm_autorizacion=false;

        $this->user_cerrador=Auth::user()->id;
        $this->nombre_cerrador=Auth::user()->name;
        $this->cierre_at=now()->toDateTimeString();       
        $this->update_tiempos(Ticket::find($this->ticket_id));         
        Ticket::where('id',$this->ticket_id)
            ->update(['estatus'=>2,
                      'user_cerrador'=>$this->user_cerrador,
                      'nombre_cerrador'=>$this->nombre_cerrador,
                      'resultado_autorizacion'=>$resultado,
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
            'avance'=>$resultado==0?'Solicitud RECHAZADA':'SOLICITUD AUTORIZADA',
            'tipo_avance'=>2,
            ]);

        TicketAvance::create([
            'ticket_id'=>$this->ticket_id,
            'user_id'=>Auth::user()->id,
            'nombre_usuario'=>Auth::user()->name,
            'avance'=>'Cambió el estatus '.$estatus_actual.' -> '.$this->nuevo_posible_estatus,
            'tipo_avance'=>2,
            ]);
        
        return;
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
            $this->update_tiempos(Ticket::find($this->ticket_id)); 
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
    public function open_modal_confirm_rechazo()
    {
        $this->open_confirm_rechazo=true;
        $this->procesando=0;
        
    }
    public function open_modal_confirm_autorizacion()
    {
        $this->open_confirm_autorizacion=true;
        $this->procesando=0;
    }
    public function open_reasignar_modal()
    {
        $this->open_reasignar=true;
        $this->procesando=0;
    }
    public function open_confirm_previa()
    {
        $this->open_previa=true;
        $this->procesando=0;

    }

    public function updatedGrupoSeleccionado()
    {
        $this->miembros_disponibles=[];
        if($this->grupo_seleccionado!=999999)
        {
            $miembros_disponibles=MiembroGrupo::with('user')->where('grupo_id',$this->grupo_seleccionado)
                                    ->get();
            foreach($miembros_disponibles as $miembro_grupo_seleccionado)
            {
                $this->miembros_disponibles[]=[
                    'user_id'=>$miembro_grupo_seleccionado->user_id,
                    'name'=>$miembro_grupo_seleccionado->user->name
                ];
            }
        }
        else
        {
            $invitados=InvitadoTicket::with('user')
                            ->where('ticket_id',$this->ticket_id)
                            ->get();
            $this->miembros_disponibles=[];
            foreach($invitados as $invitado)
            {
                $this->miembros_disponibles[]=[
                    'user_id'=>$invitado->user_id,
                    'name'=>$invitado->user->name
                ];
            }
        }

    }
    public function reasignar()
    {
        
        $reglas = [
            'mensaje_reasignacion' => 'required',
            'miembro_seleccionado' => 'required',
          ];
        $this->validate($reglas,
                [
                    'required' => 'Campo requerido.',
                    'numeric'=>'Debe ser un numero'
                ],
            );
        $this->procesando=1;
        $usuario=User::find($this->miembro_seleccionado);
        TicketAvance::create([
            'ticket_id'=>$this->ticket_id,
            'user_id'=>Auth::user()->id,
            'nombre_usuario'=>Auth::user()->name,
            'avance'=>"Reasignacion de ticket a ".strtoupper($usuario->name)."\r\nMensaje reasignacion: ".$this->mensaje_reasignacion,
            'tipo_avance'=>3,
            ]);
        $this->update_tiempos(Ticket::find($this->ticket_id)); 
        Ticket::where('id',$this->ticket_id)->update(
                                    [
                                    'asignado_a'=>$this->miembro_seleccionado,
                                    'a_a'.$this->actividad_actual=>$this->miembro_seleccionado
                                    ]
                                );
        $this->open_reasignar=false;
        $this->mensaje_reasignacion='';
    }
    public function updatedEsperandoRespuesta()
    {
        if($this->esperando_respuesta=='1')
        {
            if($this->cerrar_al_responder=='1')
            {
             $this->cerrar_al_responder=null;
            }
        }
    }
    public function updatedCerrarAlResponder()
    {
        if($this->cerrar_al_responder=='1')
        {
            if($this->esperando_respuesta=='1')
            {
             $this->esperando_respuesta=null;
            }
        }
    }
    public function guardar_avance()
    {
        $reglas = [
            'texto_avance' => 'required',
          ];
        $this->validate($reglas,
                [
                    'required' => 'Campo requerido.',
                    'numeric'=>'Debe ser un numero'
                ],
            );
        $this->emit('livewire_to_controller','save_avance');
    }
}
