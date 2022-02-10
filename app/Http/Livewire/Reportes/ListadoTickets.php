<?php

namespace App\Http\Livewire\Reportes;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Topico;
use App\Models\ActividadTopico;

class ListadoTickets extends Component
{
    public $grupos;
    public $topicos_disponibles=[];
    public $grupo;
    public $topico;
    public $desde;
    public $hasta;
    public $concepto_fecha='Creacion';

    public function render()
    {
        return view('livewire.reportes.listado-tickets');
    }
    public function mount()
    {
        $this->grupos=Grupo::where('estatus',1)
                            ->orderBy('nombre','asc')
                            ->get();
    }
    public function updatedGrupo()
    {
        $this->topicos_disponibles=ActividadTopico::where('secuencia',0)
                            ->where('grupo_id',$this->grupo)
                            ->get();
    }
}
