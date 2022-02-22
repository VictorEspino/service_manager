<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;

class CerradosRecientemente extends Component
{
    public $tickets;
    public function render()
    {
        return view('livewire.ticket.cerrados-recientemente');
    }
    public function mount()
    {
        $this->tickets=Ticket::where('estatus','>=','2')
                                ->orderBy('cierre_at','desc')
                                ->when(Auth::user()->perfil=='MIEMBRO',function ($query){
                                    $query->whereRaw('id in ('.getSQLUniverso(Auth::user()->id).')');
                                    })
                                ->get()
                                ->take(10);
    }
}
