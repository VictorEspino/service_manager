<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;
use App\Models\ActividadTicket;
use App\Models\ActividadTicketCampos;
use App\Models\TicketAvance;
use App\Models\Ticket;

class SiguienteEtapa extends Component
{
    public $ticket_id;
    public $procesando=0;
    public $actividades_total;
    public $actividad_actual;
    public $open_avanzar=false;

    public $siguiente_etapa_id;
    public $siguiente_etapa_nombre;
    public $siguiente_etapa_descripcion;
    public $siguiente_etapa_campos=[];

    protected $listeners = ['etapa' => 'render'];

    public function render()
    {
        $ticket=Ticket::find($this->ticket_id);
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
