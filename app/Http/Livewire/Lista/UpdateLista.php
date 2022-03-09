<?php

namespace App\Http\Livewire\Lista;

use Livewire\Component;

use App\Models\Lista;
use App\Models\ListaValores;

class UpdateLista extends Component
{
    public $id_lista;
    public $estatus;

    public $valores=[];
    public $valor;
    public $nombre;
    public $descripcion;
    public $procesando=0;
    public $open=false;
    public $numero_de_valores=0;

    public function render()
    {
        return view('livewire.lista.update-lista');
    }
    public function mount($id_lista)
    {
        $this->id_lista=$id_lista;
    }
    public function cambiar_estatus()
    {
        Lista::where('id',$this->id_lista)
            ->update(['estatus'=>($this->estatus=='1'?0:1)]);
        $this->open=false;
        $this->emit('listaModificada');
    }
    public function edit_open()
    {
        $this->procesando=0;
        $lista_consultada=Lista::find($this->id_lista);
        $valores_actuales=ListaValores::where('lista_id',$this->id_lista)
                                        ->orderBy('id','asc')
                                        ->get();
        $this->valores=[];
        foreach($valores_actuales as $valor)
        {
            $this->valores[]=['texto'=>$valor->valor];
        }
        $this->nombre=$lista_consultada->nombre;
        $this->descripcion=$lista_consultada->descripcion;
        $this->estatus=$lista_consultada->estatus;
        $this->contar_valores();
        $this->open=true;
        $this->resetErrorBag();
        $this->resetValidation();

    }
    public function cancelar()
    {
        $this->open=false;
        $this->nombre=null;
        $this->descripcion=null;
        $this->valor=null;
        $this->valores=[];
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function agregar_valor()
    {
        $ya_esta="NO";
        foreach($this->valores as $valor)
        {
            if($valor['texto']==$this->valor)
            {
                $ya_esta="SI";
            }
        }
        if($ya_esta=="NO")
        {
            $this->valores[]=['texto'=>$this->valor];
        }
        $this->valor=null;
        $this->contar_valores();
    }
    public function eliminar_valor($id)
    {
        unset($this->valores[$id]);
        $this->valores=array_values($this->valores);
        $this->contar_valores();
    }
    public function contar_valores()
    {
        $this->numero_de_valores=0;
        foreach($this->valores as $valor)
        {
            $this->numero_de_valores=$this->numero_de_valores+1;
        }
    }
    public function validacion()
    {
        $reglas = [
            'nombre'=>'required',
            'descripcion'=>'required',
            'numero_de_valores'=>'numeric|min:2',
          ];
        //dd($reglas);
        $this->validate($reglas,
            [
                'required' => 'Campo requerido.',
                'numeric'=>'Debe ser un numero',
                'numero_de_valores.min'=> 'La lista debe tener al menos dos valores',
            ],
          );

    }
    public function guardar()
    {
        $this->validacion();
        $this->procesando=1;
        $lista_creada=Lista::where('id',$this->id_lista)
                ->update([
                    'nombre'=>$this->nombre,
                    'descripcion'=>$this->descripcion,
                    ]);
        ListaValores::where('lista_id',$this->id_lista)->delete();
        foreach($this->valores as $valor)
        {
            ListaValores::create([
                'lista_id'=>$this->id_lista,
                'valor'=>$valor['texto'],
            ]);
        }
        $this->emit('listaModificada');
        $this->open=false;
        $this->nombre=null;
        $this->descripcion=null;
        $this->valor=null;
        $this->valores=[];
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
