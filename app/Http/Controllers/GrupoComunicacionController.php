<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupoComunicacionPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrupoComunicacionController extends Controller
{
    public function grupo(Request $request)
    {
        if(Auth::user()->perfil=='MIEMBRO')
        {
            $grupos_autorizados=DB::select(DB::raw(getSQLGruposComunicacion(Auth::user()->id)));
            $grupos_autorizados=collect($grupos_autorizados);
            $universo=$grupos_autorizados->pluck('grupo_id','grupo_id');
            try
            {
                $universo[$request->id];
            } 
            catch(\Exception $e)
            {
                return(view('no_autorizado',['mensaje'=>'No cuenta con autorizacion para acceder a las publicaciones de este grupo']));
            }            
        }
        return(view('grupo',['id'=>$request->id]));
    }
    public function save_post(Request $request)
    {
        $post=GrupoComunicacionPost::create([
                        'grupo_id'=>$request->grupo_id,
                        'user_id'=>Auth::user()->id,
                        'nombre_usuario'=>Auth::user()->name,
                        'post'=>$request->post]);     

        if(isset($request->adjunto))
        {
            $upload_path = public_path('archivos');
            //$upload_path ='/home/icubecom/sm-bca.icube.com.mx/archivos';
            $file_name = $request->adjunto->getClientOriginalName();
            $generated_new_name = 'g_'.$post->id.'_'.time().'.'. $request->adjunto->getClientOriginalExtension();
            $request->adjunto->move($upload_path, $generated_new_name);
            $adjunto=$generated_new_name;
            
            GrupoComunicacionPost::where('id',$post->id)->update([
                'adjunto'=>1,
                'archivo_adjunto'=>$adjunto
            ]);
        }
        return(back());
    }
}
