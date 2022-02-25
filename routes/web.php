<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Topico\ShowTopicos;
use App\Http\Livewire\Grupo\ShowGrupos;
use App\Http\Livewire\GrupoComunicacion\ShowGruposComunicacion;
use App\Http\Livewire\Usuario\ShowUsuarios;
use App\Http\Livewire\Ticket\TicketDetalle;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\BusquedaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {return view('dashboard');})->middleware('auth');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/topicos',ShowTopicos::class)->name('topicos')->middleware('auth');
Route::get('/usuarios',ShowUsuarios::class)->name('usuarios')->middleware('auth');
Route::get('/grupos',ShowGrupos::class)->name('grupos')->middleware('auth');
Route::get('/grupos_comunicacion',ShowGruposComunicacion::class)->name('grupos_comunicacion')->middleware('auth');
Route::get('/tickets',[TicketController::class,'show'])->name('tickets')->middleware('auth');
Route::get('/reportes',function (){return view ('reporte-tickets');})->name('reportes')->middleware('auth');
Route::post('/reportes',[ReportesController::class,'listado'])->name('reportes')->middleware('auth');

Route::post('/save_ticket',[TicketController::class,'save'])->middleware('auth')->name('save_ticket');
Route::get('/ticket/{id}',[TicketController::class,'ticket'])->name('ticket')->middleware('auth');
Route::post('/save_avance',[TicketController::class,'save_avance'])->middleware('auth')->name('save_avance');
Route::post('/avanzar_etapa',[TicketController::class,'avanzar_etapa'])->middleware('auth')->name('avanzar_etapa');

Route::get('/busqueda',[BusquedaController::class,'busqueda'])->middleware('auth')->name('busqueda');
Route::get('/busqueda_simple',[BusquedaController::class,'busqueda_simple'])->middleware('auth')->name('busqueda_simple');
