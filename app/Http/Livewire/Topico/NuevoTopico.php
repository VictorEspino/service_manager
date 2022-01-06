<?php

namespace App\Http\Livewire\Topico;

use Livewire\Component;
use App\Models\Topico;
use App\Models\Grupo;
use App\Models\User;
use App\Models\Invitado;
use App\Models\ActividadTopico;
use App\Models\ActividadCampos;
use App\Models\TipoAsignaciones;

class NuevoTopico extends Component
{
    public $open=false;
    public $tipo_asignaciones;
    public $grupos;

    public $invitados_buscar;
    public $invitados_disponibles;

    public $nombre,$descripcion;
    public $sla,$grupo,$tipo_asignacion;
    public $campos_principal=[];    
    public $invitados_principal=[];

    public function mount()
    {
        $this->tipo_asignaciones=TipoAsignaciones::all();
        $this->grupos=Grupo::all();
    }
    public function render()
    {
        return view('livewire.topico.nuevo-topico');
    }

    public function guardar()
    {
        $nuevo_topico=Topico::create([
            'nombre'=>$this->nombre,
            'descripcion'=>$this->descripcion
        ]);
        
        $actividad_principal=ActividadTopico::create([
            'sla'=>$this->sla,
            'grupo_id'=>$this->grupo,
            'tipo_asignacion'=>$this->tipo_asignacion,
            'topico_id'=>$nuevo_topico->id,
        ]);

        foreach($this->campos_principal as $index => $campo)
        {
            ActividadCampos::create([
                'actividad_id'=>$actividad_principal->id,
                'etiqueta'=>$this->campos_principal[$index]['etiqueta'],
                'tipo_control'=>$this->campos_principal[$index]['tipo_control'],
                'requerido'=>$this->campos_principal[$index]['requerido'],
                'lista_id'=>$this->campos_principal[$index]['lista'],
            ]);
        }
        foreach($this->invitados_principal as $index => $invitado)
        {
            Invitado::create([
                'topico_id'=>$nuevo_topico->id,
                'user_id'=>$invitado['id'],
            ]);
        }


        $this->reset(['open','nombre','descripcion','sla','grupo','tipo_asignacion','campos_principal','invitados_principal','invitados_disponibles','invitados_buscar']);
        $this->emit('topicoAgregado');
        $this->emit('alert_ok','El topico se creo satisfactoriamente');
    }
    public function nuevo_campo_principal()
    {
        $this->campos_principal[]=[
                                    'etiqueta'=>'',
                                    'tipo_control'=>'Texto',
                                    'requerido'=>1,
                                    'lista'=>0
                                  ];
    }
    public function borrar_campo_principal($id)
    {
        unset($this->campos_principal[$id]);
        $this->campos_principal=array_values($this->campos_principal);
    }
    public function updatedInvitadosBuscar()
    {
        if(strlen($this->invitados_buscar)>1)
        {
        $this->invitados_disponibles=User::where('name','like','%'.$this->invitados_buscar.'%')
                                        ->get()
                                        ->take(5);
        }
    }
    public function agregar_invitado_principal($id,$empleado,$nombre)
    {
        $this->invitados_principal[]=[
            'id'=>$id,
            'empleado'=>$empleado,
            'name'=>$nombre,
        ];
    }
    public function eliminar_invitado_principal($id)
    {
        unset($this->invitados_principal[$id]);
        $this->invitados_principal=array_values($this->invitados_principal);
    }
}
