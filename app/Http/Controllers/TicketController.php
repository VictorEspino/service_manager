<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Ticket;
use App\Models\Invitado;
use App\Models\TicketAvance;
use App\Models\TicketAvancesCampo;
use App\Models\ActividadTicket;
use App\Models\ActividadTicketCampos;
use App\Models\ActividadTopico;
use App\Models\ActividadCampos;
use App\Models\InvitadoTicket;
use App\Models\MiembroGrupo;
use App\Models\User;

class TicketController extends Controller
{
    public function ticket(Request $request)
    {
        if(Auth::user()->perfil=='MIEMBRO')
        {
            $tickets_autorizados=DB::select(DB::raw(getSQLUniverso(Auth::user()->id)));
            $tickets_autorizados=collect($tickets_autorizados);
            $universo=$tickets_autorizados->pluck('ticket_id','ticket_id');
            try
            {
                $universo[$request->id];
            } 
            catch(\Exception $e)
            {
                return(view('no_autorizado',['mensaje'=>'No cuenta con autorizacion para revisar el ticket: '.ticket($request->id).' dado que no forma parte del grupo(s) de atencion, ni ha sido invitado a alguna de las actividades del ticket.']));
            }            
        }
        $buscar="NO";
        $busqueda="";
        if(isset($_GET['q']))
        {
            $busqueda=$_GET['q'];
            if($busqueda!="")
            {
                $buscar="SI";
            }
        }

        return(view('ticket',['id'=>$request->id,'buscar'=>$buscar,'busqueda'=>$busqueda]));
    }
    public function avanzar_etapa(Request $request)
    {
       // return $request->all();
        $ticket=Ticket::find($request->id);
        $actividad_avance=$ticket->actividad_actual+1;
        $asignacion_seleccionada_usuario=$request->siguiente_etapa_atencion_seleccionada;

        $asignado_previo=0;
        if($actividad_avance=='1'){$asignado_previo=$ticket->a_a1;}
        if($actividad_avance=='2'){$asignado_previo=$ticket->a_a2;}
        if($actividad_avance=='3'){$asignado_previo=$ticket->a_a3;}
        if($actividad_avance=='4'){$asignado_previo=$ticket->a_a4;}
        if($actividad_avance=='5'){$asignado_previo=$ticket->a_a5;}
        if($actividad_avance=='6'){$asignado_previo=$ticket->a_a6;}
        if($actividad_avance=='7'){$asignado_previo=$ticket->a_a7;}
        if($actividad_avance=='8'){$asignado_previo=$ticket->a_a8;}
        if($actividad_avance=='9'){$asignado_previo=$ticket->a_a9;}
        
        //ACTUALIZAR LOS CAMPOS DE LA ACTIVIDAD DEL TICKET

        $actividad_next=ActividadTicket::where('ticket_id',$request->id)
                        ->where('secuencia',$actividad_avance)
                        ->get()
                        ->first();

        $actividad_siguiente=$actividad_next->id;
        $asignacion=$asignado_previo;
        if(intval($asignado_previo)==0 || $actividad_next->tipo_asignacion=='6')
        {
            $asignacion=$this->obtenerAsignacion($actividad_next->tipo_asignacion,$actividad_next->grupo_id,$actividad_next->user_id_automatico,$asignacion_seleccionada_usuario);
        }
        $this->update_tiempos($ticket); 
        Ticket::where('id',$request->id)
                ->update([
                    'actividad_actual'=>$actividad_avance,
                    'asignado_a'=>$asignacion,
                    'time_to'=>$actividad_avance,
                    'a_a'.$actividad_avance=>$asignacion
                ]);

        ActividadTicket::where('ticket_id',$request->id)
                        ->where('secuencia',$actividad_avance)
                        ->update(['descripcion'=>$request->descripcion]);            
        
        $texto_avance="Avance a etapa :".strtoupper($request->nombre)."\r\n".$request->descripcion;

        $nuevo_avance=TicketAvance::create([
                            'ticket_id'=>$request->id,
                            'user_id'=>Auth::user()->id,
                            'nombre_usuario'=>Auth::user()->name,
                            'avance'=>$texto_avance,
                            'tipo_avance'=>4,
                            ]);

        if(isset($request->campos))
        {
            foreach($request->campos as $index => $campos)
            {
                $valor="";
                if($campos['tipo']=='Texto' || $campos['tipo']=='Lista')
                {
                    $valor=$campos['valor'];
                }
                if($campos['tipo']=='CheckBox')
                {
                    try
                    {
                        $valor=$campos['valor'];
                        $valor=1;
                    }
                    catch(\Exception $e)
                    {
                        $valor=0;
                    }
                }
                if($campos['tipo']=='File')
                {
                    $upload_path = public_path('dwh');
                    $upload_path ='/var/www/sm-bca.icube.com.mx/dwh';
                    try{
                        $file_name = $campos['valor']->getClientOriginalName();
                        $generated_new_name = $ticket->id.'_'.$actividad_siguiente.'_'.$campos['referencia'].'_'.time().'.'. $campos['valor']->getClientOriginalExtension();
                        $campos['valor']->move($upload_path, $generated_new_name);
                    }
                    catch(\Exception $e)
                    {
                        $generated_new_name="";
                    }
                    $valor=$generated_new_name;
                }
                ActividadTicketCampos::where('actividad_ticket_id',$actividad_siguiente)
                                    ->where('referencia',$campos['referencia'])
                                    ->update(['valor'=>$valor]);

                TicketAvancesCampo::create([
                                        'ticket_avance_id'=>$nuevo_avance->id,
                                        'etiqueta'=>$campos['etiqueta'],
                                        'tipo'=>$campos['tipo'],
                                        'valor'=>$valor,
                                        ]);
                
            }
        }
        return(back());
    }
    public function save(Request $request)
    {
        //return $request->all();
        $actividades_topico=ActividadTopico::where('topico_id',$request->topico)
                                            ->get();
        $n_actividades=0;
        $n_minutos=0;
        $tipo_asignacion_requerido=0;
        $grupo_a_asignar=0;
        $asignacion_automatica=0;
        $asignacion_seleccionada_usuario=$request->atencion_por;
        $asignacion=0;
        foreach($actividades_topico as $actividad_estructura)
        {
            $n_actividades=$n_actividades+1;
            $n_minutos=$n_minutos+intval($actividad_estructura->sla);
            if($actividad_estructura->secuencia=='0')
            {
                $tipo_asignacion_requerido=$actividad_estructura->tipo_asignacion;
                $grupo_a_asignar=$actividad_estructura->grupo_id;
                $asignacion_automatica=$actividad_estructura->user_id_automatico;
            }
        }
        $asignacion=$this->obtenerAsignacion($tipo_asignacion_requerido,$grupo_a_asignar,$asignacion_automatica,$asignacion_seleccionada_usuario);
        $ticket=Ticket::create([
                        'creador_id'=>Auth::user()->id,
                        'de_id'=>$request->de_id,
                        'area_id'=>Auth::user()->area,
                        'subarea_id'=>Auth::user()->sub_area,
                        'topico_id'=>$request->topico,
                        'asunto'=>$request->asunto,
                        'prioridad'=>$request->prioridad,
                        'asignado_a'=>$asignacion,
                        'actividad_actual'=>0,
                        'a_a0'=>$asignacion,
                        'n_actividades'=>$n_actividades,
                        'n_minutos'=>$n_minutos,
                        'emite_autorizacion'=>$request->emite_autorizacion,
                        ]);

        if(isset($request->adjunto))
        {
            $upload_path = public_path('dwh');
            $upload_path ='/var/www/sm-bca.icube.com.mx/dwh';
            $file_name = $request->adjunto->getClientOriginalName();
            $generated_new_name = $ticket->id.'_'.time().'.'. $request->adjunto->getClientOriginalExtension();
            $request->adjunto->move($upload_path, $generated_new_name);
            $adjunto=$generated_new_name;
            
            Ticket::where('id',$ticket->id)->update([
                'adjunto'=>1,
                'archivo_adjunto'=>$adjunto
            ]);
        }
        
        $actividad_principal=0;
        foreach($actividades_topico as $actividad_estructura)
        {
            $actividad_ticket=ActividadTicket::create([
                                    'ticket_id'=>$ticket->id,
                                    'secuencia'=>$actividad_estructura->secuencia,
                                    'nombre'=>$actividad_estructura->secuencia=='0'?$request->asunto:$actividad_estructura->nombre,
                                    'descripcion'=>$actividad_estructura->secuencia=='0'?$request->descripcion:$actividad_estructura->descripcion,
                                    'sla'=>$actividad_estructura->sla,
                                    'grupo_id'=>$actividad_estructura->grupo_id,
                                    'tipo_asignacion'=>$actividad_estructura->tipo_asignacion,
                                    'user_id_automatico'=>$actividad_estructura->user_id_automatico,
                                ]);

            if($actividad_estructura->secuencia=='0')
            {
                $actividad_principal=$actividad_ticket->id;
            }

            $campos_estructura=ActividadCampos::where('actividad_id',$actividad_estructura->id)
                                                ->get();
            foreach($campos_estructura as $campos_actividad_ticket)
            {
                ActividadTicketCampos::create([
                                        'actividad_ticket_id'=>$actividad_ticket->id,
                                        'etiqueta'=>$campos_actividad_ticket->etiqueta,
                                        'tipo_control'=>$campos_actividad_ticket->tipo_control,
                                        'requerido'=>$campos_actividad_ticket->requerido,
                                        'lista_id'=>$campos_actividad_ticket->lista_id,
                                        'referencia'=>$campos_actividad_ticket->id,
                                    ]);
            }
            $invitados_estructura=Invitado::where('actividad_id',$actividad_estructura->id)
                                            ->get();
            foreach($invitados_estructura as $invitado_actividad)
            {
                $invitado_al_ticket=User::find($invitado_actividad->user_id);
                InvitadoTicket::create([
                    'user_id'=>$invitado_actividad->user_id,
                    'actividad_id'=>$actividad_ticket->id,
                    'ticket_id'=>$ticket->id,
                    'area_id'=>$invitado_al_ticket->area,
                    'subarea_id'=>$invitado_al_ticket->sub_area,
                ]);
            }
        }

        if(isset($request->invitados))
        {
            foreach($request->invitados as $invitado)
            {
                $invitado_al_ticket=User::find($invitado['id']);
                InvitadoTicket::create([
                    'ticket_id'=>$ticket->id,
                    'user_id'=>$invitado['id'],
                    'actividad_id'=>$actividad_principal,
                    'area_id'=>$invitado_al_ticket->area,
                    'subarea_id'=>$invitado_al_ticket->sub_area,
                ]);
            }
        }
        if(isset($request->campos))
        {
            foreach($request->campos as $index => $campos)
            {
                $valor="";
                if($campos['tipo']=='Texto' || $campos['tipo']=='Lista')
                {
                    $valor=$campos['valor'];
                }
                if($campos['tipo']=='CheckBox')
                {
                    try
                    {
                        $valor=$campos['valor'];
                        $valor=1;
                    }
                    catch(\Exception $e)
                    {
                        $valor=0;
                    }
                }
                if($campos['tipo']=='File')
                {
                    $upload_path = public_path('dwh');
                    $upload_path ='/var/www/sm-bca.icube.com.mx/dwh';
                    try{
                    $file_name = $campos['valor']->getClientOriginalName();
                    $generated_new_name = $ticket->id.'_'.$actividad_principal.'_'.$campos['referencia'].'_'.time().'.'. $campos['valor']->getClientOriginalExtension();
                    $campos['valor']->move($upload_path, $generated_new_name);
                    }
                    catch(\Exception $e)
                    {
                       $generated_new_name="";
                    }
                    $valor=$generated_new_name;
                }
                ActividadTicketCampos::where('actividad_ticket_id',$actividad_principal)
                                    ->where('referencia',$campos['referencia'])
                                    ->update(['valor'=>$valor]);
            }
        }
        //return(view('ticket',['id'=>$ticket->id]));
        return redirect()->route('ticket',['id'=>$ticket->id]);
    }
    private function obtenerAsignacion($tipo,$grupo_id,$automatico,$seleccionada)
    {
        if($tipo=='1') return(0); //MANUAL
        if($tipo=='2') return($automatico); //AUTOMATICA
        if($tipo=='3') //ALEATORIO
        {            
            $sql_miembros="
            select user_id,manager,COALESCE(atendiendo, 0) as atendiendo from(
            (SELECT * FROM `miembro_grupos` as a where a.grupo_id=".$grupo_id.") as a
            left join 
            (select asignado_a,count(*) as atendiendo from tickets where estatus=1 group by asignado_a ) as b 
            ON a.user_id=b.asignado_a)
            where manager=0
            order by atendiendo asc
            ";
            $miembros=DB::select(DB::raw($sql_miembros));
            $miembros=collect($miembros);
            $usuarios=$miembros->pluck('user_id');            
            return($usuarios[rand(0,count($usuarios)-1)]);
        }
        if($tipo=='4') //MENOS OCUPADO
        { 
            $sql_miembros="
            select user_id,manager,COALESCE(atendiendo, 0) as atendiendo from(
            (SELECT * FROM `miembro_grupos` as a where a.grupo_id=".$grupo_id.") as a
            left join 
            (select asignado_a,count(*) as atendiendo from tickets where estatus=1 group by asignado_a ) as b 
            ON a.user_id=b.asignado_a)
            where manager=0
            order by atendiendo asc
            ";
            $miembros=DB::select(DB::raw($sql_miembros));
            $miembros=collect($miembros);
            return($miembros->first()->user_id);
        }
        if($tipo=='5')  //MANAGER
        {
            $sql_miembros="
            select user_id,manager,COALESCE(atendiendo, 0) as atendiendo from(
            (SELECT * FROM `miembro_grupos` as a where a.grupo_id=".$grupo_id.") as a
            left join 
            (select asignado_a,count(*) as atendiendo from tickets where estatus=1 group by asignado_a ) as b 
            ON a.user_id=b.asignado_a)
            where manager=1
            order by atendiendo asc
            ";
            $miembros=DB::select(DB::raw($sql_miembros));
            $miembros=collect($miembros);
            return($miembros->first()->user_id);
        }
        if($tipo=='6')  //SELECCIONADO POR USUARIO
        {
            return($seleccionada);
        }
    }
    public function show(Request $request)
    {
        $busqueda=0;
        $filtro=false;
        if(isset($_GET['f']))
        {
            $filtro=true;
            $busqueda=$_GET['tid'];
        }
        $asignados_a_mi=Ticket::with('solicitante','area_solicitante','subarea_solicitante','topico')
                            ->where(function($query_main){
                                $query_main->where('asignado_a',Auth::user()->id);
                                $query_main->orWhere(function($query) {
                                    $query->where('time_to', '-1')
                                          ->where('de_id', Auth::user()->id);
                                    });
                            })
                            ->where('estatus',1)
                            ->get();
        $creados_por_mi=Ticket::with('asesor','topico')
                            ->where('de_id',Auth::user()->id)
                            ->where('estatus',1)
                            ->get();
        $participante=Ticket::with('asesor','solicitante','topico')
                            ->where('estatus',1)
                            ->whereRaw('id in ('.getSQLParticipante(Auth::user()->id).')')
                            ->where('asignado_a','!=',Auth::user()->id)
                            ->get();
        $topicos_participante=[];

        foreach($participante as $participante_topico)
        {
            $esta="NO";
            foreach($topicos_participante as $barrido)
            {
                if($barrido['topico_id']==$participante_topico->topico_id)
                {
                    $esta="SI";
                }
            }
            if($esta=="NO")
            {
                $topicos_participante[]=[
                    'topico_id'=>$participante_topico->topico_id,
                    'nombre'=>$participante_topico->topico->nombre
                ];
            }
        }

        return (view('tickets',[
                'asignados_a_mi'=>$asignados_a_mi,
                'creados_por_mi'=>$creados_por_mi,
                'participante'=>$participante,
                'topicos_participante'=>$topicos_participante,
                'busqueda'=>$busqueda,
                'filtro'=>$filtro,
            ]));
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
    public function save_avance(Request $request)
    {
        //return $request->all();

        $ticket=Ticket::find($request->id);

        $tipo_avance=$request->solicitante==Auth::user()->id?1:2;

        $id_avance=TicketAvance::create([
            'ticket_id'=>$request->id,
            'user_id'=>Auth::user()->id,
            'nombre_usuario'=>Auth::user()->name,
            'avance'=>$request->avance,
            'tipo_avance'=>$tipo_avance,
            ]);
        if(isset($request->adjunto))
        {
            $upload_path = public_path('dwh');
            $upload_path ='/var/www/sm-bca.icube.com.mx/dwh';
            $file_name = $request->adjunto->getClientOriginalName();
            $generated_new_name = $request->id.'_'.time().'.'. $request->adjunto->getClientOriginalExtension();
            $request->adjunto->move($upload_path, $generated_new_name);
            $adjunto=$generated_new_name;
            TicketAvance::where('id',$id_avance->id)
                        ->update([
                            'adjunto'=>1,
                            'archivo_adjunto'=>$adjunto,
                        ]);
        }
        if(isset($request->cerrar_al_responder))
        {
            $estatus_actual="";
            if($request->estatus=='1')
            {
                $estatus_actual="ABIERTO";
            }
            if($request->estatus=='2')
            {
                $estatus_actual="CERRADO"; 
            }
            if($request->estatus=='3')
            {
                $estatus_actual="TERMINADO";
            }

            TicketAvance::create([
                'ticket_id'=>$request->id,
                'user_id'=>Auth::user()->id,
                'nombre_usuario'=>Auth::user()->name,
                'avance'=>'Cambió el estatus '.$estatus_actual.' -> CERRADO',
                'tipo_avance'=>2,
                ]);
            $this->update_tiempos($ticket); 
            Ticket::where('id',$request->id)->update([
                                                        'estatus'=>2,
                                                        'user_cerrador'=>Auth::user()->id,
                                                        'nombre_cerrador'=>Auth::user()->name,
                                                        'cierre_at'=>now()->toDateTimeString()
                                                    ]);
                                                    
            return redirect()->route('tickets');
        }
        //Primero actualiza los tiempos en atencion y despues marca que el ticket 
        //esta en manos del solicitante para la siguiente atencion y contabilizacion

        $this->update_tiempos($ticket); 
        if(isset($request->esperando_respuesta))
        {
            Ticket::where('id',$request->id)->update([
                                                        'time_to'=>-1 
                                                        //Comienza a contar tiempo en el solicitante
                                                    ]);
            TicketAvance::create([
                'ticket_id'=>$request->id,
                'user_id'=>Auth::user()->id,
                'nombre_usuario'=>Auth::user()->name,
                'avance'=>'Ticket en espera de respuesta de solicitante',
                'tipo_avance'=>3,
                ]);
        }
        if($ticket->time_to=='-1')
        {
            Ticket::where('id',$request->id)->update([
                'time_to'=>$ticket->actividad_actual,
                //Comienza a contar tiempo en el solicitante
            ]);
            TicketAvance::create([
                'ticket_id'=>$request->id,
                'user_id'=>Auth::user()->id,
                'nombre_usuario'=>Auth::user()->name,
                'avance'=>'Ticket vuelve a area de atencion',
                'tipo_avance'=>3,
                ]);
        }
        return(back());
    }
    public function impresion(Request $request)
    {
        if(Auth::user()->perfil=='MIEMBRO')
        {
            $tickets_autorizados=DB::select(DB::raw(getSQLUniverso(Auth::user()->id)));
            $tickets_autorizados=collect($tickets_autorizados);
            $universo=$tickets_autorizados->pluck('ticket_id','ticket_id');
            try
            {
                $universo[$request->id];
            } 
            catch(\Exception $e)
            {
                return(view('no_autorizado',['mensaje'=>'No cuenta con autorizacion para revisar el ticket: '.ticket($request->id).' dado que no forma parte del grupo(s) de atencion, ni ha sido invitado a alguna de las actividades del ticket.']));
            }            
        }
        $ticket_id=$request->id;
        $ticket=Ticket::with('solicitante','area_solicitante','subarea_solicitante','topico','asesor')->find($ticket_id);
        /*
        $invitados=InvitadoTicket::with('user','area','subarea')
                                   ->select(DB::raw('distinct user_id,area_id,subarea_id'))
                                   ->where('ticket_id',$ticket_id)
                                   ->get();
        */
        $avances_ticket=$this->getAvances($ticket_id);


        $invitados_ticket=InvitadoTicket::with('user','area','subarea')->where('ticket_id',$ticket_id)->get();
        
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
                                            ->where('ticket_id',$ticket_id)
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
        

        //dd($avances_ticket);

        return(view('ticket_impresion',['ticket_id'=>$ticket_id,
                                        'ticket'=>$ticket,
                                        'invitados'=>$involucrados_a_desplegar,
                                        'avances_ticket'=>$avances_ticket,
                                        ]));
    }
    private function getAvances($ticket_id)
    {
        $avances=[];

        $ticket=Ticket::with('solicitante')->find($ticket_id);

        $descripcion_inicial=ActividadTicket::where('ticket_id',$ticket_id)
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
                $archivo_valor_desplegar="<br />".$campos->etiqueta.": <a href='/descarga/".$campos->valor."' download><i class='text-red-400 text-base fas fa-file-download'></i></a>";
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
            $desc_inicial=$desc_inicial."<br />Descargar: <a href='/descarga/".$ticket->archivo_adjunto."' download><i class='text-red-400 text-base fas fa-file-download'></i></a>";
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
                                    ->where('ticket_id',$ticket_id)
                                    ->get();
        //dd($avances_atencion);
        foreach($avances_atencion as $avance)
        {
        
            $desc_inicial=$avance->avance.($avance->adjunto=='1'?"<br>Archivo adjunto: <a href='/descarga/".$avance->archivo_adjunto."' download><i class='text-red-400 text-base fas fa-file-download'></i></a>":"");
            
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
}
