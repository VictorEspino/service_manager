<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;

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
                        ->whereBetween($campo,[$desde,$hasta])
                        ->get();
        //return($listado);
        $usuarios=User::select('id','name')->get();
        $usuarios=$usuarios->pluck('name','id');
        return(view('export_listado',['listado'=>$listado,'usuarios'=>$usuarios]));
    }
}
