<?php

namespace App\Http\Livewire\Lista;

use Livewire\Component;
use App\Models\Lista;
use App\Models\ListaValores;

class NuevaLista extends Component
{
    public $valores=[];
    public $valor;
    public $nombre;
    public $descripcion;
    public $procesando=0;
    public $open=false;
    public $numero_de_valores=0;
    public function render()
    {
        return view('livewire.lista.nueva-lista');
    }
    public function abrir()
    {
        $this->open=true;
        $this->procesando=0;
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
        $lista_creada=Lista::create([
                'nombre'=>$this->nombre,
                'descripcion'=>$this->descripcion,
        ]);
        foreach($this->valores as $valor)
        {
            ListaValores::create([
                'lista_id'=>$lista_creada->id,
                'valor'=>$valor['texto'],
            ]);
        }
        $this->emit('listaAgregada');
        $this->open=false;
        $this->nombre=null;
        $this->descripcion=null;
        $this->valor=null;
        $this->valores=[];
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
