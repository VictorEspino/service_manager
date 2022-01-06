<?php

namespace App\Http\Livewire\Grupo;

use Livewire\Component;
use App\Models\User;
use App\Models\Grupo;
use App\Models\MiembroGrupo;

class NuevoGrupo extends Component
{
    public $open=false;

    public $miembros_buscar;
    public $usuarios_disponibles;
    
    public $nombre,$descripcion;
    public $usuarios_principal=[];

    public function render()
    {
        return view('livewire.grupo.nuevo-grupo');
    }
    public function updatedMiembrosBuscar()
    {
        if(strlen($this->miembros_buscar)>1)
        {
        $this->usuarios_disponibles=User::where('name','like','%'.$this->miembros_buscar.'%')
                                        ->get()
                                        ->take(5);
        }
    }
    public function agregar_miembro_principal($id,$empleado,$nombre,$tipo_miembro)
    {
        $this->usuarios_principal[]=[
            'id'=>$id,
            'empleado'=>$empleado,
            'name'=>$nombre,
            'tipo'=>$tipo_miembro,
        ];
    }
    public function eliminar_miembro_principal($id)
    {
        unset($this->usuarios_principal[$id]);
        $this->usuarios_principal=array_values($this->usuarios_principal);
    }
    public function guardar()
    {
        $nuevo_grupo=Grupo::create([
            'nombre'=>$this->nombre,
            'descripcion'=>$this->descripcion
        ]);        
        foreach($this->usuarios_principal as $index => $usuario)
        {
            MiembroGrupo::create([
                'grupo_id'=>$nuevo_grupo->id,
                'user_id'=>$usuario['id'],
                'manager'=>$usuario['tipo']=='2'?1:0,
            ]);
        }


        $this->reset(['open','nombre','descripcion','usuarios_principal','usuarios_disponibles','miembros_buscar']);
        $this->emit('grupoAgregado');
        $this->emit('alert_ok','El grupo se creo satisfactoriamente');
    }
}
