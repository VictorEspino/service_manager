<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Busqueda;
use Illuminate\Support\Facades\Auth;

class BusquedaController extends Controller
{
    public function busqueda(Request $request)
    {
        $query=$request->buscar;
        if(strlen($query)<=2) return view('busqueda',['ok'=>'NO']);
        $resultados=Busqueda::where('texto','like','%'.$query.'%')
                              ->orderBy('ticket_id','desc')
                              ->orderBy('created_at','desc')
                              ->when(Auth::user()->perfil=='MIEMBRO',function ($query){
                                    $query->whereRaw('ticket_id in ('.getSQLUniverso(Auth::user()->id).')');
                                    })
                              ->paginate(10);
        //return($resultados);
        $resultados->appends($request->all());
        return view('busqueda',['ok'=>'SI','registros'=>$resultados,'query'=>$query]);
    }
    public function busqueda_simple(Request $request)
    {
        $folio=$request->folio;
        $asunto=$request->asunto;
        //return($request->all());

        $resultados=Busqueda::where(function ($query) use ($folio,$asunto){
                                  if($folio!="" && $asunto=="")
                                  { 
                                    $query->where('ticket_id',$folio);
                                  }
                                  if($folio!="" && $asunto!="")
                                  {
                                    $query->where('ticket_id',$folio);
                                    $query->orWhere('texto','like','%'.$asunto.'%');
                                  }
                                  if($folio=="" && $asunto!="")
                                  {
                                    $query->where('texto','like','%'.$asunto.'%');
                                  }
                              })
                              ->where('campo','Asunto')
                              ->orderBy('ticket_id','desc')
                              ->orderBy('created_at','desc')
                              ->when(Auth::user()->perfil=='MIEMBRO',function ($query){
                                    $query->whereRaw('ticket_id in ('.getSQLUniverso(Auth::user()->id).')');
                                    })
                              ->paginate(10);
        //return($resultados);
        $resultados->appends($request->all());
        return view('busqueda',['ok'=>'SI','registros'=>$resultados,'query'=>$asunto]);
    }
}
