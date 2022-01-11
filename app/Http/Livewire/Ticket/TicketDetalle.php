<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;

use App\Models\Ticket;

class TicketDetalle extends Component
{
    public $ticket_id;
    
    public $asunto;
    public $topico_nombre;
    public $solicitante;

    public $file_include=false;

    public $avance;
    public $cerrar_al_responder;
    public $esperando_respuesta;

    public function render()
    {
        $ticket=Ticket::with('topico','solicitante')->find($this->ticket_id);
        $this->asunto=$ticket->asunto;
        $this->topico_nombre=$ticket->topico->nombre;
        $this->solicitante=$ticket->solicitante->name;

        return view('livewire.ticket.ticket-detalle');
    }
    public function mount($id)
    {
        $this->ticket_id=$id;
    }
}
