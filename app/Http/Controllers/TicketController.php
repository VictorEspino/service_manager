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
        $ticket=Ticket::create([
                        'creador_id'=>Auth::user()->id,
                        'de_id'=>$request->de_id,
                        'topico_id'=>$request->topico,
                        'asunto'=>$request->asunto,
                        'prioridad'=>$request->prioridad,
                        'asignado_a'=>$asignacion,
                        'actividad_actual'=>0
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

            return($adjunto);
        }
        $actividades_topico=ActividadTopico::where('topico_id',$request->topico)
                                            ->get();
        $actividad_principal=0;
        foreach($actividades_topico as $actividad_estructura)                
        {
            
            $actividad_ticket=ActividadTicket::create([
                                    'ticket_id'=>$ticket->id,
                                    'secuencia'=>$actividad_estructura->secuencia,
                                    'descripcion'=>$request->descripcion,
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
                            ->get();
        return (view('tickets',[
                'asignados_a_mi'=>$asignados_a_mi,
            ]));
    }
    public function save_avance(Request $request)
    {
        //return $request->all();

        $id_avance=TicketAvance::create([
            'ticket_id'=>$request->id,
            'user_id'=>Auth::user()->id,
            'nombre_usuario'=>Auth::user()->name,
            'avance'=>$request->avance,
            'tipo_avance'=>1,
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
    }
}
