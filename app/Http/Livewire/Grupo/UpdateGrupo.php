<?php

namespace App\Http\Livewire\Grupo;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\User;
use App\Models\MiembroGrupo;

class UpdateGrupo extends Component
{
    public $id_grupo;

    public $nombre;
    public $descripcion;

    public $miembros;
    public $manager;

    public $miembros_buscar;
    public $usuarios_disponibles=[];
    public $usuarios_principal=[];

    public $procesando=0;
    public $open=false;

    public function render()
    {
        return view('livewire.grupo.update-grupo');
    }
    public function mount($id_grupo)
    {
        $this->id_grupo=$id_grupo;
    }
    public function edit_open()
    {
        $this->procesando=0;
        $grupo=Grupo::find($this->id_grupo);
        $this->nombre=$grupo->nombre;
        $this->descripcion=$grupo->descripcion;
        $this->usuarios_disponibles=[];
        $this->usuarios_principal=[];
        $miembros_guardados=MiembroGrupo::with('user')->where('grupo_id',$this->id_grupo)->get();
        $this->miembros=0;
        $this->manager=0;
        foreach($miembros_guardados as $miembro_actual)
        {
            $this->miembros=$this->miembros+1;
            if($miembro_actual->manager=='1'){$this->manager=$this->manager+1;}
            $this->usuarios_principal[]=[
                'id'=>$miembro_actual->user->id,
                'empleado'=>$miembro_actual->user->user,
                'name'=>$miembro_actual->user->name,
                'tipo'=>$miembro_actual->manager=='1'?2:1,
            ];    
        }
        $this->open=true;
    }

    public function cancelar()
    {
        $this->open=false;
        $this->resetErrorBag();
        $this->resetValidation();
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

        Grupo::where('id',$this->id_grupo)
            ->update([
                    'nombre'=>$this->nombre,
                    'descripcion'=>$this->descripcion
                    ]);
   
        MiembroGrupo::where('grupo_id',$this->id_grupo)->delete();

        foreach($this->usuarios_principal as $index => $usuario)
        {
            MiembroGrupo::create([
                'grupo_id'=>$this->id_grupo,
                'user_id'=>$usuario['id'],
                'manager'=>$usuario['tipo']=='2'?1:0,
            ]);
        }

        $this->reset(['miembros','manager','open','nombre','descripcion','usuarios_principal','usuarios_disponibles','miembros_buscar']);
        $this->emit('grupoModificado');
    }
}