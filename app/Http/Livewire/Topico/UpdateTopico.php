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
use App\Models\MiembroGrupo;
use App\Models\Lista;

class UpdateTopico extends Component
{
    public $id_topico;

    public $campo_actualizado;

    public $open=false;
    public $tipo_asignaciones;
    public $grupos=[];

    public $invitados_buscar;
    public $invitados_disponibles=[];

    public $nombre,$descripcion;
    public $sla,$grupo,$tipo_asignacion,$estatus;
    public $emite_autorizacion='NO';
    public $campos_principal=[];    
    public $invitados_principal=[];

    public $actividades_adicionales=[];
    public $numero_actividades_adicionales;

    public $user_id_automatico=null;
    public $enable_automatico=false;
    public $usuarios_grupo_disponibles=[];

    public $listas_valores_disponibles=[];

    public function render()
    {
        return view('livewire.topico.update-topico');
    }
    public function mount($id_topico)
    {
        $this->id_topico=$id_topico;
        $this->tipo_asignaciones=TipoAsignaciones::all();
        $this->grupos=Grupo::all();
        $this->listas_valores_disponibles=Lista::where('estatus','1')->orderBy('nombre','asc')->get();
        $this->numero_actividades_adicionales=0;
    }

    public function edit_open()
    {
        $this->open=true;
        $topico=Topico::find($this->id_topico);
        $this->nombre=$topico->nombre;
        $this->descripcion=$topico->descripcion;
        $this->emite_autorizacion=$topico->emite_autorizacion=='0'?'NO':'SI';

        $actividades_topico=ActividadTopico::where('topico_id',$this->id_topico)
                                            ->orderBy('secuencia','asc')
                                            ->get();
        $this->numero_actividades_adicionales=0;
        $this->estatus=$topico->estatus;
        $this->campos_principal=[];
        $this->invitados_principal=[];
        $this->actividades_adicionales=[];

        foreach($actividades_topico as $actividad)
        {
            if($actividad->secuencia=='0') //LLENA LA ACTIVIDAD PRINCIPAL
            {
                $this->sla=$actividad->sla;
                $this->grupo=$actividad->grupo_id;
                $this->tipo_asignacion=$actividad->tipo_asignacion;
                
                if($actividad->tipo_asignacion=='2')
                {
                    $this->enable_automatico=true;
                    $this->usuarios_grupo_disponibles=MiembroGrupo::with('user')
                                                    ->where('grupo_id',$actividad->grupo_id)
                                                    ->get();
                    $this->user_id_automatico=$actividad->user_id_automatico;
                }
                $campos_actividad_principal=ActividadCampos::where('actividad_id',$actividad->id)
                                                            ->orderBy('id','asc')
                                                            ->get();
                foreach($campos_actividad_principal as $campo)
                {
                    $this->campos_principal[]=[
                        'etiqueta'=>$campo->etiqueta,
                        'tipo_control'=>$campo->tipo_control,
                        'requerido'=>$campo->requerido,
                        'lista'=>$campo->lista_id
                      ];
                }

                $invitados_actividad_principal=Invitado::with('user')
                                                        ->where('actividad_id',$actividad->id)
                                                        ->orderBy('id','asc')
                                                        ->get();
                foreach($invitados_actividad_principal as $invitado)
                {
                    $this->invitados_principal[]=[
                        'id'=>$invitado->user->id,
                        'empleado'=>$invitado->user->user,
                        'name'=>$invitado->user->name,
                    ];
                }

            }
            else //LLENA LAS ACTIVIDADES POSTERIORES
            {
                $this->numero_actividades_adicionales=$this->numero_actividades_adicionales+1;
                if($actividad->tipo_asignacion=='2')
                {
                    $lista_automatica=MiembroGrupo::with('user')
                                                ->where('grupo_id',$actividad->grupo_id)
                                                ->get();
                }
                else
                {   
                    $lista_automatica=[];
                }
                $campos_actividad_adicional=ActividadCampos::where('actividad_id',$actividad->id)
                                                            ->orderBy('id','asc')
                                                            ->get();
                $campos_a_integrar=[];
                foreach ($campos_actividad_adicional as $campo)
                {
                    $campos_a_integrar[]=[
                        'etiqueta'=>$campo->etiqueta,
                        'tipo_control'=>$campo->tipo_control,
                        'requerido'=>$campo->requerido,
                        'lista'=>$campo->lista_id
                    ];
                }

                $invitados_actividad_adicional=Invitado::with('user')
                                                        ->where('actividad_id',$actividad->id)
                                                        ->orderBy('id','asc')
                                                        ->get();
                $invitados_a_integrar=[];
                foreach($invitados_actividad_adicional as $invitado)
                {
                        $invitados_a_integrar[]=[
                        'id'=>$invitado->user->id,
                        'empleado'=>$invitado->user->user,
                        'name'=>$invitado->user->name,
                    ];
                }

                $this->actividades_adicionales[]=[
                    'nombre'=>$actividad->nombre,
                    'descripcion'=>$actividad->descripcion,
                    'sla'=>$actividad->sla,
                    'grupo'=>$actividad->grupo_id,
                    'tipo_asignacion'=>$actividad->tipo_asignacion,
                    'secuencia'=>$this->numero_actividades_adicionales,
                    'campos'=>$campos_a_integrar,
                    'invitados_buscar'=>'',
                    'invitados_disponibles'=>null,
                    'invitados'=>$invitados_a_integrar,
                    'user_id_automatico'=>$actividad->tipo_asignacion=='2'?$actividad->user_id_automatico:null,
                    'enable_automatico'=>$actividad->tipo_asignacion=='2'?true:false,
                    'usuarios_grupo_disponibles'=>$lista_automatica
                ];
            }
        }
    }

    public function nuevo_campo_principal()
    {
        $this->campos_principal[]=[
                                    'etiqueta'=>'',
                                    'tipo_control'=>'Texto',
                                    'requerido'=>1,
                                    'lista'=>null
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
                                        ->where('visible',1)
                                        ->where('estatus',1)
                                        ->get()
                                        ->take(5);
        }
        else
        {
            $this->invitados_disponibles=null; 
        }
    }

    public function agregar_invitado_principal($id,$empleado,$nombre)
    {
        $ya_esta="NO";
        foreach($this->invitados_principal as $index=>$invitado)
        {
            if(intval($invitado['id'])==intval($id))
            {
                $ya_esta="SI";
            }
        }
        if($ya_esta=="NO")
        {
            $this->invitados_principal[]=[
                'id'=>$id,
                'empleado'=>$empleado,
                'name'=>$nombre,
            ];
        }
        $this->invitados_buscar='';
        $this->invitados_disponibles=null; 
    }
    public function eliminar_invitado_principal($id)
    {
        unset($this->invitados_principal[$id]);
        $this->invitados_principal=array_values($this->invitados_principal);
    }

    public function cancelar()
    {
        $this->open=false;
        $this->campos_principal=[];    
        $this->invitados_principal=[];
        $this->actividades_adicionales=[];
        $this->numero_actividades_adicionales=0;
        $this->invitados_buscar=null;
        $this->sla='';
        $this->grupo=null;
        $this->tipo_asignacion=null;
        $this->nombre='';
        $this->descripcion='';
        $this->reset(['user_id_automatico','enable_automatico','usuarios_grupo_disponibles','open','nombre','descripcion','sla','grupo','tipo_asignacion','campos_principal','invitados_principal','invitados_disponibles','invitados_buscar','actividades_adicionales','numero_actividades_adicionales']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function agregar_actividad_adicional()
    {
        if($this->numero_actividades_adicionales<=8)
        {
            $this->numero_actividades_adicionales=$this->numero_actividades_adicionales+1;
            $this->actividades_adicionales[]=[
                'nombre'=>'',
                'descripcion'=>'',
                'sla'=>'',
                'grupo'=>null,
                'tipo_asignacion'=>null,
                'secuencia'=>$this->numero_actividades_adicionales,
                'campos'=>[],
                'invitados_buscar'=>'',
                'invitados_disponibles'=>null,
                'invitados'=>[],
                'user_id_automatico'=>null,
                'enable_automatico'=>false,
                'usuarios_grupo_disponibles'=>[]
            ];
        }
    }

    public function eliminar_actividad_adicional($id)
    {
        unset($this->actividades_adicionales[$id]);
        $this->actividades_adicionales=array_values($this->actividades_adicionales);
        $this->numero_actividades_adicionales=$this->numero_actividades_adicionales-1;
        foreach($this->actividades_adicionales as $index => $actividad)
        {
            $this->actividades_adicionales[$index]['secuencia']=$index+1;
        }
    }
    public function nuevo_campo_actividad_adicional($id)
    {
        $this->actividades_adicionales[$id]['campos'][]=[
                                    'etiqueta'=>'',
                                    'tipo_control'=>'Texto',
                                    'requerido'=>1,
                                    'lista'=>null
                                  ];
    }
    public function borrar_campo_actividad_adicional($id,$id_campo)
    {
        unset($this->actividades_adicionales[$id]['campos'][$id_campo]);
        $this->actividades_adicionales[$id]['campos']=array_values($this->actividades_adicionales[$id]['campos']);
    }

    public function updatedActividadesAdicionales($valor,$anidado)
    {
        $this->campo_actualizado=$valor."-".$anidado;
        $datos_campo = explode(".", $anidado);
        $indice=$datos_campo[0];
        $propiedad=$datos_campo[1];
        if($propiedad=='invitados_buscar' && strlen($valor)>2)
        {
            $this->actividades_adicionales[$indice]['invitados_disponibles']=
                                            User::where('name','like','%'.$valor.'%')
                                            ->where('visible',1)
                                            ->where('estatus',1)
                                            ->get()
                                            ->take(5);
        }
        if($propiedad=='tipo_asignacion' && $valor==2)
        {
            $this->actividades_adicionales[$indice]['usuarios_grupo_disponibles']=
                                                MiembroGrupo::with('user')
                                                ->where('grupo_id',$this->actividades_adicionales[$indice]['grupo'])
                                                ->get();
            $this->actividades_adicionales[$indice]['enable_automatico']=true;
            $this->actividades_adicionales[$indice]['user_id_automatico']=null;
        }
        if($propiedad=='tipo_asignacion' && $valor!=2)
        {
            $this->actividades_adicionales[$indice]['usuarios_grupo_disponibles']=[]; 
            $this->actividades_adicionales[$indice]['enable_automatico']=false;  
            $this->actividades_adicionales[$indice]['user_id_automatico']=null;                                             
        }
        if($propiedad=='grupo' && $this->actividades_adicionales[$indice]['tipo_asignacion']==2)
        {
            $this->actividades_adicionales[$indice]['usuarios_grupo_disponibles']=
                                                MiembroGrupo::with('user')
                                                ->where('grupo_id',$this->actividades_adicionales[$indice]['grupo'])
                                                ->get();
            $this->actividades_adicionales[$indice]['enable_automatico']=true;
            $this->actividades_adicionales[$indice]['user_id_automatico']=null;
        }
        if($propiedad=='grupo' && $this->actividades_adicionales[$indice]['tipo_asignacion']!=2)
        {
            $this->actividades_adicionales[$indice]['usuarios_grupo_disponibles']=[];
            $this->actividades_adicionales[$indice]['enable_automatico']=false;
            $this->actividades_adicionales[$indice]['user_id_automatico']=null;
        }
    }

    public function agregar_invitado_actividad_adicional($indice,$id,$empleado,$nombre)
    {
        $ya_esta="NO";
        $this->actividades_adicionales[$indice]['invitados_disponibles']=null;
        $this->actividades_adicionales[$indice]['invitados_buscar']='';
        foreach($this->actividades_adicionales[$indice]['invitados'] as $index=>$invitado_actividad)
        {
            if(intval($invitado_actividad['id'])==intval($id))
            {
                $ya_esta="SI";
            }
        }
        if($ya_esta=="NO")
        {
            $this->actividades_adicionales[$indice]['invitados'][]=[
                'id'=>$id,
                'empleado'=>$empleado,
                'name'=>$nombre,
            ];
        }
    }
    public function eliminar_invitado_actividad_adicional($indice,$id)
    {
        unset($this->actividades_adicionales[$indice]['invitados'][$id]);
        $this->actividades_adicionales[$indice]['invitados']=array_values($this->actividades_adicionales[$indice]['invitados']);
    }

    public function updatedTipoAsignacion()
    {
        $this->user_id_automatico=null;
        if($this->tipo_asignacion=='2') //ASIGNACION AUTOMATICA
        {
            $this->enable_automatico=true; 
            $this->usuarios_grupo_disponibles=MiembroGrupo::with('user')
                                            ->where('grupo_id',$this->grupo)
                                            ->get();
        }
        else
        {
            $this->enable_automatico=false; 
            $this->usuarios_grupo_disponibles=[];
        }
    }
    public function updatedGrupo()
    {
        $this->user_id_automatico=null;
        if($this->tipo_asignacion=='2') //ASIGNACION AUTOMATICA
        {
            $this->enable_automatico=true; 
            $this->usuarios_grupo_disponibles=MiembroGrupo::with('user')
                                            ->where('grupo_id',$this->grupo)
                                            ->get();
        }
        else
        {
            $this->enable_automatico=false; 
            $this->usuarios_grupo_disponibles=[];
        }
    }
    public function validacion()
    {
        $reglas = [
            'nombre' => 'required',
            'descripcion' => 'required',
            'grupo'=>'required',
            'tipo_asignacion'=>'required',
            'sla'=>'required|numeric',
          ];
        if($this->tipo_asignacion=='2') //ASIGNACION AUTOMATICA
        {
          $reglas = array_merge($reglas, [
            'user_id_automatico' => 'required',
          ]);
        }
        foreach ($this->campos_principal as $index => $campos) 
          {
            $reglas = array_merge($reglas, [
              'campos_principal.'.$index.'.etiqueta' => 'required',
            ]);
            if($campos['tipo_control']=='Lista')
            {
                $reglas = array_merge($reglas, [
                    'campos_principal.'.$index.'.lista' => 'required',
                  ]);
            }
          }
        foreach($this->actividades_adicionales as $index => $actividad_adicional)
            {
                $reglas = array_merge($reglas, [
                    'actividades_adicionales.'.$index.'.nombre'=> 'required',
                    'actividades_adicionales.'.$index.'.descripcion'=> 'required',
                    'actividades_adicionales.'.$index.'.sla'=> 'required|numeric',
                    'actividades_adicionales.'.$index.'.grupo'=> 'required',
                    'actividades_adicionales.'.$index.'.tipo_asignacion'=> 'required',
                  ]);
                  if($actividad_adicional['tipo_asignacion']=='2') //ASIGNACION AUTOMATICA
                    {
                    $reglas = array_merge($reglas, [
                        'actividades_adicionales.'.$index.'.user_id_automatico'=> 'required',
                    ]);
                    }

                  foreach($actividad_adicional['campos'] as $index_campo =>$campo)
                  {
                    $reglas = array_merge($reglas, [
                        'actividades_adicionales.'.$index.'.campos.'.$index_campo.'.etiqueta'=> 'required',
                      ]);
                    if($campo['tipo_control']=='Lista')
                    {
                        $reglas = array_merge($reglas, [
                            'actividades_adicionales.'.$index.'.campos.'.$index_campo.'.lista'=> 'required',
                        ]);
                    }
                  }

                
            }
        //dd($reglas);
        $this->validate($reglas,
            [
                'required' => 'Campo requerido.',
                'numeric'=>'Debe ser un numero'
            ],
          );
    }
    public function guardar()
    {
        
        $this->validacion();
        Topico::where('id',$this->id_topico)->update([
            'nombre'=>$this->nombre,
            'descripcion'=>$this->descripcion,
            'emite_autorizacion'=>$this->emite_autorizacion=='NO'?0:1
        ]);
        $this->resetDependencias(); //ELIMINARA TODO LO QUE TENGA EL TOPICO PARA DESPUES RECONSTRUIRLO
        $actividad_principal=ActividadTopico::create([
            'sla'=>$this->sla,
            'grupo_id'=>$this->grupo,
            'tipo_asignacion'=>$this->tipo_asignacion,
            'topico_id'=>$this->id_topico,
            'descripcion'=>'PRINCIPAL',
            'user_id_automatico'=>is_null($this->user_id_automatico)?0:$this->user_id_automatico,
        ]);

        foreach($this->campos_principal as $index => $campo)
        {
            ActividadCampos::create([
                'actividad_id'=>$actividad_principal->id,
                'etiqueta'=>$this->campos_principal[$index]['etiqueta'],
                'tipo_control'=>$this->campos_principal[$index]['tipo_control'],
                'requerido'=>$this->campos_principal[$index]['requerido'],
                'lista_id'=>is_null($this->campos_principal[$index]['lista'])?0:$this->campos_principal[$index]['lista'],
            ]);
        }
        foreach($this->invitados_principal as $index => $invitado)
        {
            Invitado::create([
                'actividad_id'=>$actividad_principal->id,
                'user_id'=>$invitado['id'],
            ]);
        }
        $siguiente_actividad=1;
        foreach($this->actividades_adicionales as $index => $actividad_adicional)
        {
            $actividad_adicional_creada=ActividadTopico::create([
                'nombre'=>$actividad_adicional['nombre'], 
                'descripcion'=>$actividad_adicional['descripcion'],
                'sla'=>$actividad_adicional['sla'],
                'grupo_id'=>$actividad_adicional['grupo'],
                'tipo_asignacion'=>$actividad_adicional['tipo_asignacion'],
                'topico_id'=>$this->id_topico,
                'secuencia'=>$siguiente_actividad,
                'user_id_automatico'=>is_null($actividad_adicional['user_id_automatico'])?0:$actividad_adicional['user_id_automatico'],
            ]);

            foreach($this->actividades_adicionales[$index]['campos'] as $campo)
            {
                ActividadCampos::create([
                    'actividad_id'=>$actividad_adicional_creada->id,
                    'etiqueta'=>$campo['etiqueta'],
                    'tipo_control'=>$campo['tipo_control'],
                    'requerido'=>$campo['requerido'],
                    'lista_id'=>is_null($campo['lista'])?0:$campo['lista'],
                ]);
            }
            foreach($this->actividades_adicionales[$index]['invitados'] as $invitado)
            {
                Invitado::create([
                    'actividad_id'=>$actividad_adicional_creada->id,
                    'user_id'=>$invitado['id'],
                ]);
            }



            $siguiente_actividad=$siguiente_actividad+1;
        }

        $this->reset(['user_id_automatico','enable_automatico','usuarios_grupo_disponibles','open','nombre','descripcion','sla','grupo','tipo_asignacion','campos_principal','invitados_principal','invitados_disponibles','invitados_buscar','actividades_adicionales','numero_actividades_adicionales']);
        $this->emit('topicoModificado');
    }
    private function resetDependencias()
    {
        $actividades=ActividadTopico::select('id')
                                ->where('topico_id',$this->id_topico)
                                ->get();
        $actividades=$actividades->pluck('id');

        Invitado::whereIn('actividad_id',$actividades)->delete();
        ActividadCampos::whereIn('actividad_id',$actividades)->delete();
        ActividadTopico::whereIn('id',$actividades)->delete();
    }
    public function cambiar_estatus()
    {
        Topico::where('id',$this->id_topico)
            ->update(['estatus'=>($this->estatus=='1'?0:1)]);
        $this->open=false;
        $this->emit('topicoModificado');
    }
}
