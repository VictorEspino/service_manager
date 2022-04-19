<?php

namespace App\Http\Livewire\Usuario;

use Livewire\Component;

use App\Models\User;
use App\Models\Area;
use App\Models\SubArea;
use App\Models\Puesto;

class UpdateUsuario extends Component
{
    public $id_user;
    public $debug;

    public $open=false;
    public $procesando=0;

    public $email_inicial;

    public $user;
    public $email;
    public $nombre;
    public $puesto;
    public $area;
    public $sub_area;
    public $estatus;

    public $areas=[];
    public $sub_areas=[];
    public $puestos=[];

    public function render()
    {
        return view('livewire.usuario.update-usuario');
    }
    public function mount($id_user)
    {
        $this->id_user=$id_user;
    }
    public function edit_open()
    {
        $this->open=true;
        $this->procesando=0;
        $user=User::find($this->id_user);
        $this->areas=Area::where('estatus',1)
                        ->orderBy('nombre','asc')
                        ->get();
        $this->puestos=Puesto::where('estatus',1)->orderBy('puesto','asc')
                        ->get();
        $this->sub_areas=SubArea::where('area_id',$user->area)
                                ->where('estatus',1)
                                ->orderBy('nombre','asc')
                                ->get();    
        $this->nombre=$user->name;
        $this->user=$user->user;
        $this->email_inicial=$user->email;
        $this->email=$user->email;
        $this->puesto=$user->puesto;
        $this->perfil=$user->perfil;
        $this->area=$user->area;
        $this->sub_area=$user->sub_area;
        $this->estatus=$user->estatus;
    }

    public function cancelar()
    {
        $this->open=false;
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

    public function guardar()
    {
        $this->validacion();
        $this->procesando=1;
        User::where('id',$this->id_user)
            ->update([
                        'email'=>$this->email,
                        'name'=>$this->nombre,
                        'puesto'=>$this->puesto,
                        'perfil'=>$this->perfil,
                        'area'=>$this->area,
                        'sub_area'=>$this->sub_area,
            ]);
        $this->open=false;
        $this->emit('usuarioModificado');
    }

    private function validacion()
    {
        $reglas = [
            'email'=>'required|email',
            'nombre' => 'required',
            'puesto' => 'required',
            'perfil'=>'required',
            'area'=>'required',
            'sub_area'=>'required',
          ];
        if($this->email_inicial!=$this->email)
        {
            $reglas = array_merge($reglas, [
                'email' => 'unique:users,email',
              ]);
        }
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
    public function cambiar_estatus()
    {
        User::where('id',$this->id_user)
            ->update(['estatus'=>($this->estatus=='1'?0:1)]);
        $this->open=false;
        $this->emit('usuarioModificado');
    }
    public function reset_password()
    {
        User::where('id',$this->id_user)
            ->update(['password'=>'$2y$10$jK3NdYnXIUxIx.svFw/9SOXkXub0.RcR7p7cpiqWn/6inDrXeXZsq']);
        $this->open=false;
        $this->emit('usuarioModificado');
    }
}
