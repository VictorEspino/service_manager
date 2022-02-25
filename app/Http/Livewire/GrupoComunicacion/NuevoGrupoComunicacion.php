<?php

namespace App\Http\Livewire\GrupoComunicacion;

use Livewire\Component;
use App\Models\User;
use App\Models\GrupoComunicacion;
use App\Models\MiembroGrupoComunicacion;

class NuevoGrupoComunicacion extends Component
{
    public $open=false;

    public $miembros=0;
    public $manager=0;

    public $procesando=1;

    public $miembros_buscar;
    public $usuarios_disponibles;
    
    public $nombre,$descripcion;
    public $usuarios_principal=[];

    public function render()
    {
        return view('livewire.grupo-comunicacion.nuevo-grupo-comunicacion');
    }
    public function updatedMiembrosBuscar()
    {
        if(strlen($this->miembros_buscar)>1)
        {
        $this->usuarios_disponibles=User::where('name','like','%'.$this->miembros_buscar.'%')
                                        ->where('visible',1)
                                        ->where('estatus',1)
                                        ->get()
                                        ->take(5);
        }
    }
    public function contar_miembros_manager()
    {
        $this->miembros=0;
        $this->manager=0;
        foreach($this->usuarios_principal as $miembro_grupo)
        {
            $this->miembros=$this->miembros+1;
            if($miembro_grupo['tipo']=='2')
            {
                $this->manager=$this->manager+1;
            }
        }
    }
    public function agregar_miembro_principal($id,$empleado,$nombre,$tipo_miembro)
    {
        $ya_esta="NO";
        foreach($this->usuarios_principal as $miembro_grupo)
        {
            if(intval($miembro_grupo['id'])==intval($id))
            {
                $ya_esta="SI";
            }
        }
        if($ya_esta=="NO")
        {
            $this->usuarios_principal[]=[
                'id'=>$id,
                'empleado'=>$empleado,
                'name'=>$nombre,
                'tipo'=>$tipo_miembro,
            ];
            $this->contar_miembros_manager();
        }
    }
    public function eliminar_miembro_principal($id)
    {
        unset($this->usuarios_principal[$id]);
        $this->usuarios_principal=array_values($this->usuarios_principal);
        $this->contar_miembros_manager();
    }
    public function validacion()
    {
        $reglas = [
            'nombre'=>'required',
            'descripcion'=>'required',
            'nombre' => 'required',
            'miembros'=>'numeric|min:1',
            'manager'=>'numeric|min:1',
          ];
        //dd($reglas);
        $this->validate($reglas,
            [
                'required' => 'Campo requerido.',
                'numeric'=>'Debe ser un numero',
                'miembros.min'=> 'El grupo debe tener al menos un miembro',
                'manager.min'=> 'El grupo debe tener al menos un manager'
            ],
          );

    }
    public function guardar()
    {
        $this->validacion();
        $this->procesando=1;
        $nuevo_grupo=GrupoComunicacion::create([
            'nombre'=>$this->nombre,
            'descripcion'=>$this->descripcion
        ]);        
        foreach($this->usuarios_principal as $index => $usuario)
        {
            MiembroGrupoComunicacion::create([
                'grupo_id'=>$nuevo_grupo->id,
                'user_id'=>$usuario['id'],
                'manager'=>$usuario['tipo']=='2'?1:0,
            ]);
        }


        $this->reset(['miembros','manager','open','nombre','descripcion','usuarios_principal','usuarios_disponibles','miembros_buscar']);
        $this->emit('grupoAgregado');
        $this->emit('alert_ok','El grupo se creo satisfactoriamente');
    }
    public function cancelar()
    {
        $this->reset(['miembros','manager','open','nombre','descripcion','usuarios_principal','usuarios_disponibles','miembros_buscar']);
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function abrir()
    {
        $this->procesando=0;
        $this->open=true;
    }
}
