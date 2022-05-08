<?php

namespace App\Http\Livewire\Usuario;

use Livewire\Component;
use App\Models\Area;
use App\Models\SubArea;
use App\Models\Puesto;

use App\Models\User;

class NuevoUsuario extends Component
{
    public $open=false;
    public $procesando;

    public $user;
    public $email;
    public $nombre;
    public $puesto;
    public $perfil='MIEMBRO';
    public $area;
    public $areas=[];
    public $puestos=[];
    public $sub_area;
    public $sub_areas=[];

    public function render()
    {
        return view('livewire.usuario.nuevo-usuario');
    }
    public function mount()
    {
        $this->areas=Area::where('estatus',1)
                        ->orderBy('nombre','asc')
                        ->get();
        $this->puestos=Puesto::where('estatus',1)->orderBy('puesto','asc')->get();
    }
    public function nuevo()
    {
        $this->open=true;
        $this->procesando=0;
    }
    public function cancelar()
    {
        $this->open=false;
        $this->email='';
        $this->user='';
        $this->nombre='';
        $this->puesto='';
        $this->perfil='MIEMBRO';
        $this->area='';
        $this->sub_area='';
        $this->sub_areas=[];
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function updatedArea()
    {
        $this->sub_areas=SubArea::where('area_id',$this->area)
                                ->where('estatus',1)
                                ->orderBy('nombre','asc')
                                ->get();
    }
    public function validacion()
    {
        $reglas = [
            'user'=>'required|unique:users,user',
            'email'=>'required|email|unique:users,email',
            'nombre' => 'required',
            'puesto' => 'required',
            'perfil'=>'required',
            'area'=>'required',
            'sub_area'=>'required',
          ];
        //dd($reglas);
        $this->validate($reglas,
            [
                'required' => 'Campo requerido.',
                'numeric'=>'Debe ser un numero',
                'unique'=>'El valor ya existe en la base de datos',
                'email'=>'Se requiere una direccion de correo valida'
            ],
          );
    }
    public function guardar()
    {
        $this->validacion();
        $this->procesando=1;

        User::create([
            'user'=>$this->user,
            'name'=>$this->nombre,
            'puesto'=> $this->puesto,
            'perfil'=> $this->perfil,
            'area'=> $this->area,
            'sub_area'=> $this->sub_area,
            'email'=>$this->email,
            'password'=>'$2y$10$3WETO/uYpSjxNmqa8w2IZexzOlTXpKGWv6MxD9RCyFPUEHalkloGi',
        ]);


        $this->emit('usuarioAgregado');
        $this->emit('alert_ok','El usuario se creo satisfactoriamente');
        $this->open=false;
        $this->email='';
        $this->user='';
        $this->nombre='';
        $this->puesto='';
        $this->perfil='MIEMBRO';
        $this->area='';
        $this->sub_area='';
        $this->sub_areas=[];
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
