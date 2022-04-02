<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function listado(Request $request)
    {
        $desde=$request->desde;
        $hasta=$request->hasta;
        $campo='created_at';
        if($request->concepto_fecha!='Creacion')
        {
            $campo='cierre_at';
        }
        $listado=Ticket::where('topico_id',$request->topico)
                        ->whereBetween($campo,[$desde.' 00:00:00',$hasta.' 23:59:59'])
                        ->get();
        //return($listado);
        $actividades_topico=Ticket::select(DB::raw('max(n_actividades) as n_actividades'))
                        ->where('topico_id',$request->topico)
                        ->whereBetween($campo,[$desde.' 00:00:00',$hasta.' 23:59:59'])
                        ->get()->first()->n_actividades;

        $usuarios=User::select('id','name')->get();
        $usuarios=$usuarios->pluck('name','id');
        return(view('export_listado',['listado'=>$listado,'usuarios'=>$usuarios,'n_actividades'=>$actividades_topico]));
    }
}
