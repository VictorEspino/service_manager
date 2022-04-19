<?php

namespace App\Http\Livewire\Topico;

use Livewire\Component;
use App\Models\TopicoPuesto;

class AutorizarPuesto extends Component
{

    public $id_topico;
    public $open=false;
    public $procesando=0;
    public $puestos_autorizados=[];

    public function render()
    {
        return view('livewire.topico.autorizar-puesto');
    }
    public function mount($id_topico)
    {
        $this->id_topico=$id_topico;
    }
    public function edit_open()
    {
        $this->open=true;
        $this->procesando=0;
        $puestos_aut_actual=TopicoPuesto::with('puesto')
                                    ->where('topico_id',$this->id_topico)
                                    ->get();

        $this->puestos_autorizados=[];
        foreach($puestos_aut_actual as $puesto_procesado)
        {
            $this->puestos_autorizados[]=[
                                'id'=>$puesto_procesado->puesto_id,
                                'puesto'=>$puesto_procesado->puesto->puesto,
                                'autorizado'=>$puesto_procesado->autorizado,
                            ];
        }        
    }
    public function cancelar()
    {
        $this->open=false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function guardar()
    {
        $this->procesando=1;
        foreach($this->puestos_autorizados as $puesto_procesado)
        {
            TopicoPuesto::where('puesto_id',$puesto_procesado['id']) 
                        ->where('topico_id',$this->id_topico)
                        ->update(['autorizado'=>$puesto_procesado['autorizado']]);
        }
        $this->open=false;
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function select_todo()
    {
        foreach($this->puestos_autorizados as $index=>$puesto_procesado)
        {
            $this->puestos_autorizados[$index]['autorizado']=true;
        } 
    }
    public function quitar_todo()
    {
        foreach($this->puestos_autorizados as $index=>$puesto_procesado)
        {
            $this->puestos_autorizados[$index]['autorizado']=false;
        } 
    }
}
