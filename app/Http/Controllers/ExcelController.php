<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EmpleadosImport;
use App\Models\Puesto;
use App\Models\Topico;
use App\Models\Area;
use App\Models\SubArea;
use App\Models\CargaEmpleados;
use App\Models\LogEmpleados;
use App\Models\TopicoPuesto;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExcelController extends Controller
{
    public function empleados_import(Request $request) 
    {
        $request->validate(['file'=> 'required'],['required'=>'Archivo requerido']);
        $file=$request->file('file');

        $bytes = random_bytes(5);
        $carga_id=bin2hex($bytes);
       

        $import=new EmpleadosImport($carga_id);
        try{
        $import->import($file);
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->withFailures($e->failures());
        }  
        
        return($this->aplicar_carga($carga_id));

        return back()->withStatus('Archivo cargado con exito!');
    }
    
    public function aplicar_carga($carga_id)
    {
        $this->aplica_puestos($carga_id);
        $this->aplica_areas($carga_id);
        $this->aplica_subareas($carga_id);
        return($this->aplica_usuarios($carga_id));
    }
    private function aplica_puestos($carga_id)
    {
        $puestos=Puesto::all();
        $puestos=$puestos->pluck('puesto','puesto');
        $puestos_cargados=CargaEmpleados::select(DB::raw('distinct puesto as puesto'))
                        ->where('carga_id',$carga_id)
                        ->get();
        foreach($puestos_cargados as $puesto_cargado)
        {
            try //CON ESTO REVISA SI EL PUESTO EXISTE EN LOS INDICES
            {
                $valor=$puestos[$puesto_cargado->puesto];
            }
            catch(\Exception $e) //OBTENDREMOS EXCEPCION SI NO ESTA POR LO TANTO LO CREAMOS Y LIGAMOS A TOPICOS
            {
                $puesto_creado=Puesto::create([
                    'puesto'=>$puesto_cargado->puesto,
                    'estatus'=>1,
                ]);
                $this->log_empleados($carga_id,'PUESTO CREADO: '.$puesto_cargado->puesto);
                $topicos=Topico::select('id')->get();
                foreach($topicos as $topico)
                {
                    TopicoPuesto::create([
                        'topico_id'=>$topico->id,
                        'puesto_id'=>$puesto_creado->id
                    ]);
                }
                $this->log_empleados($carga_id,'PUESTO: '.$puesto_cargado->puesto.' LIGADO A TOPICOS SIN AUTORIZACION');
            }
        }        
    }
    private function log_empleados($carga_id,$mensaje)
    {
        LogEmpleados::create(['carga_id'=>$carga_id,'mensaje'=>$mensaje]);
    }
    private function aplica_areas($carga_id)
    {
        $areas=Area::all();
        $areas=$areas->pluck('nombre','nombre');
        $areas_cargadas=CargaEmpleados::select(DB::raw('distinct area as area'))
                        ->where('carga_id',$carga_id)
                        ->get();
        foreach($areas_cargadas as $area_cargada)
        {
            try //CON ESTO REVISA SI EL AREA EXISTE EN LOS INDICES
            {
                $valor=$areas[$area_cargada->area];
            }
            catch(\Exception $e) //OBTENDREMOS EXCEPCION SI NO ESTA POR LO TANTO LO CREAMOS
            {
                $area_creada=Area::create([
                    'nombre'=>$area_cargada->area,
                ]);
                $this->log_empleados($carga_id,'AREA CREADA: '.$area_cargada->area);
            }
        }        
    }
    private function aplica_subareas($carga_id)
    {
        $areas=Area::all();
        $areas=$areas->pluck('id','nombre');

        $subareas=SubArea::all();
        $subareas=$subareas->pluck('area_id','nombre');
        $subareas_cargadas=CargaEmpleados::select(DB::raw('distinct area,subarea'))
                        ->where('carga_id',$carga_id)
                        ->get();

        foreach($subareas_cargadas as $subarea_cargada)
        {
            $area_id_nueva=$areas[$subarea_cargada->area];
            try //CON ESTO REVISA SI EL SUBAREA EXISTE EN LOS INDICES y ACTUALIZA AREA
            {
                $area_id_actual=$subareas[$subarea_cargada->subarea];  
            
                if($area_id_actual!=$area_id_nueva)
                {
                    SubArea::where('nombre',$subarea_cargada->subarea)
                            ->update(['area_id'=>$area_id_nueva]);
                    $this->log_empleados($carga_id,'SUBAREA ACTUALIZADA: '.$subarea_cargada->subarea);
                }
            }
            catch(\Exception $e) //OBTENDREMOS EXCEPCION SI NO ESTA POR LO TANTO LO CREAMOS
            {
                $subarea_creada=SubArea::create([
                    'nombre'=>$subarea_cargada->subarea,
                    'area_id'=>$area_id_nueva
                ]);
                $this->log_empleados($carga_id,'SUBAREA CREADA: '.$subarea_cargada->subarea);
            }
        }        
    }
    private function aplica_usuarios($carga_id)
    {
        $areas=Area::all();
        $areas=$areas->pluck('id','nombre');

        $subareas=SubArea::all();
        $subareas=$subareas->pluck('id','nombre');

        $puestos=Puesto::all();
        $puestos=$puestos->pluck('id','puesto');

        $usuarios=User::all();
        $usuarios=$usuarios->pluck('id','user');

        $empleados_cargados=CargaEmpleados::select('numero_empleado','nombre','area','subarea','puesto','estatus')
                        ->where('carga_id',$carga_id)
                        ->get();
        
        return($usuarios);
    }
}
