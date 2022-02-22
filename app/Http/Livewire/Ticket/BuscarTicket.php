<?php

namespace App\Http\Livewire\Ticket;

use Livewire\Component;

class BuscarTicket extends Component
{
    public $folio,$asunto,$busqueda;

    public function render()
    {
        return view('livewire.ticket.buscar-ticket');
    }

    public function updated()
    {
        $this->busqueda=$this->folio."".$this->asunto;
    }
    public function buscar()
    {
        $reglas = [
            'busqueda' => 'required',
          ];
        $this->validate($reglas,
            [
                'required' => 'Especifique algun valor de busqueda.',
                'numeric'=>'Debe ser un numero'
            ],
          );
        if($this->folio!="")
            {
                $reglas = [
                    'folio' => 'numeric',
                  ];
                $this->validate($reglas,
                  [
                    'required' => 'Especifique algun valor de busqueda.',
                    'numeric'=>'Debe ser un numero, omita # en caso necesario'
                  ],
                );
            }
        $this->emit('livewire_to_controller','form_busqueda_simple');
    }
}
