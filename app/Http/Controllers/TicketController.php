<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Ticket;
use App\Models\TicketAvance;
use App\Models\ActividadTicket;
use App\Models\ActividadTicketCampos;
use App\Models\ActividadTopico;
use App\Models\ActividadCampos;
use App\Models\InvitadoTicket;

class TicketController extends Controller
{
    public function save(Request $request)
    {
        //return $request->all();
        $asignacion=$this->obtenerAsignacion();
        $actividades_topico=ActividadTopico::where('topico_id',$request->topico)
                                            ->get();
        $n_actividades=0;
        $n_minutos=0;
        foreach($actividades_topico as $actividad_estructura)
        {
            $n_actividades=$n_actividades+1;
            $n_minutos=$n_minutos+intval($actividad_estructura->sla);
        }
        $ticket=Ticket::create([
                        'creador_id'=>Auth::user()->id,
                        'de_id'=>$request->de_id,
                        'topico_id'=>$request->topico,
                        'asunto'=>$request->asunto,
                        'prioridad'=>$request->prioridad,
                        'asignado_a'=>$asignacion,
                        'actividad_actual'=>0,
                        'a_a0'=>$asignacion,
                        'n_actividades'=>$n_actividades,
                        'n_minutos'=>$n_minutos,
                        ]);

        if(isset($request->adjunto))
        {
            $upload_path = public_path('archivos');
            $file_name = $request->adjunto->getClientOriginalName();
            $generated_new_name = $ticket->id.'.'. $request->adjunto->getClientOriginalExtension();
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
                                    'descripcion'=>$actividad_estructura->secuencia=='0'?$request->descripcion:$actividad_estructura->descripcion,
                                    'sla'=>$actividad_estructura->sla,
                                    'grupo_id'=>$actividad_estructura->grupo_id,
                                    'tipo_asignacion'=>$actividad_estructura->tipo_asignacion,
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
        }

        if(isset($request->invitados))
        {
            foreach($request->invitados as $invitado)
            {
                InvitadoTicket::create([
                    'ticket_id'=>$ticket->id,
                    'user_id'=>$invitado['id'],
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
                    $upload_path = public_path('archivos');
                    $file_name = $campos['valor']->getClientOriginalName();
                    $generated_new_name = $ticket->id.'_'.$actividad_principal.'_'.$campos['referencia'].'.'. $campos['valor']->getClientOriginalExtension();
                    $campos['valor']->move($upload_path, $generated_new_name);
                    $valor=$generated_new_name;
                }
                ActividadTicketCampos::where('actividad_ticket_id',$actividad_principal)
                                    ->where('referencia',$campos['referencia'])
                                    ->update(['valor'=>$valor]);
            }
        }
        return $request->all();
    }
    private function obtenerAsignacion()
    {
        return 1;
    }
    public function show(Request $request)
    {
        $asignados_a_mi=Ticket::with('solicitante')
                            ->where('asignado_a',Auth::user()->id)
                            ->orWhere(function($query) {
                                $query->where('time_to', '-1')
                                      ->where('de_id', Auth::user()->id);
                            })
                            ->get();
        return (view('tickets',[
                'asignados_a_mi'=>$asignados_a_mi,
            ]));
    }
    private function update_tiempos($ticket)
    {
        $ultima_actualizacion = new \Carbon\Carbon($ticket->updated_at);
        $actual_actualizacion = new \Carbon\Carbon(now()->toDateTimeString());
        $minutesDiff=$ultima_actualizacion->diffInMinutes($actual_actualizacion);
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
            $upload_path = public_path('archivos');
            $file_name = $request->adjunto->getClientOriginalName();
            $generated_new_name = $request->id.'.'. $request->adjunto->getClientOriginalExtension();
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
}
