<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    public function descargar(Request $request)
    {
        return response()->download('/var/www/sm-bca.icube.com.mx/dwh'.'/'.$request->archivo);
    }
}
