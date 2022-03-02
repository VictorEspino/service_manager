<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;
use App\Models\ActividadTicket;
use App\Models\ActividadTicketCampos;
use App\Models\TicketAvance;
use App\Models\Ticket;
use App\Models\MiembroGrupo;

class SiguienteEtapa extends Component
{
    public $ticket_id;
    public $ticket;
    public $procesando=0;
    public $actividades_total;
    public $actividad_actual;
    public $open_avanzar=false;

    public $siguiente_etapa_id;
    public $siguiente_etapa_nombre;
    public $siguiente_etapa_descripcion;
    public $siguiente_etapa_campos=[];

    public $siguiente_etapa_asignacion_seleccionable=false;
    public $siguiente_etapa_atencion_seleccionada;
    public $siguiente_etapa_usuarios_disponibles=[];

    protected $listeners = ['etapa' => 'render'];

    public function render()
    {
        $ticket=Ticket::find($this->ticket_id);
        $this->ticket=$ticket;
        $this->actividades_total=$ticket->n_actividades;
        $this->actividad_actual=$ticket->actividad_actual;
        return view('livewire.ticket.siguiente-etapa');
    }
    public function open_avanzar_modal()
    {
        $actividad_avance=$this->actividad_actual+1;
        $actividad_ticket=ActividadTicket::where('ticket_id',$this->ticket_id)
        ->where('secuencia',$actividad_avance)
        ->get()
        ->first();

        $asignado_previo=0;
        if($actividad_avance==1){$asignado_previo=$this->ticket->a_a1;}
        if($actividad_avance==2){$asignado_previo=$this->ticket->a_a2;}
        if($actividad_avance==3){$asignado_previo=$this->ticket->a_a3;}
        if($actividad_avance==4){$asignado_previo=$this->ticket->a_a4;}
        if($actividad_avance==5){$asignado_previo=$this->ticket->a_a5;}
        if($actividad_avance==6){$asignado_previo=$this->ticket->a_a6;}
        if($actividad_avance==7){$asignado_previo=$this->ticket->a_a7;}
        if($actividad_avance==8){$asignado_previo=$this->ticket->a_a8;}
        if($actividad_avance==9){$asignado_previo=$this->ticket->a_a9;}

        if($actividad_ticket->tipo_asignacion=='6')
        {
            $this->siguiente_etapa_asignacion_seleccionable=true;
            $this->siguiente_etapa_usuarios_disponibles=MiembroGrupo::with('user')
                                    ->where('grupo_id',$actividad_ticket->grupo_id)
                                    ->get();

            if($asignado_previo!=0)
            {
                $this->siguiente_etapa_atencion_seleccionada=$asignado_previo;
            }
        }
        else
        {
            $this->siguiente_etapa_asignacion_seleccionable=false;
            $this->siguiente_etapa_usuarios_disponibles=[];
        }

        $this->siguiente_etapa_id=$actividad_ticket->id;
        $this->siguiente_etapa_nombre=$actividad_ticket->nombre;
        $this->siguiente_etapa_descripcion=$actividad_ticket->descripcion;
        $campos=ActividadTicketCampos::where('actividad_ticket_id',$actividad_ticket->id)->get();
        $this->siguiente_etapa_campos=[];
        foreach($campos as $campo)
        {
            $this->siguiente_etapa_campos[]=[
                'referencia'=>$campo->referencia,
                'etiqueta'=>$campo->etiqueta,
                'tipo_control'=>$campo->tipo_control,
                'requerido'=>$campo->requerido,
                'lista_id'=>$campo->lista_id,
                'valor'=>$campo->valor,
            ];
        }

        $this->open_avanzar=true;
        $this->procesando=0;
    }
    public function validacion()
    {
        $reglas = [
            'siguiente_etapa_descripcion' => 'required',
          ];
        foreach ($this->siguiente_etapa_campos as $index => $campos) 
          {
            if($campos['requerido']=='1')
            {
                $reglas = array_merge($reglas, [
                    'siguiente_etapa_campos.'.$index.'.valor' => 'required',
                  ]);
            }
          }
        if($this->siguiente_etapa_asignacion_seleccionable)
        {
            $reglas = array_merge($reglas, [
                'siguiente_etapa_atencion_seleccionada' => 'required',
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
    public function avanzar_etapa()
    {
        $this->validacion();
        $this->procesando=1;
        $this->emit('livewire_to_controller','cambio_etapa');
    }
    public function cancelar()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->open_avanzar=false;
    }
}
