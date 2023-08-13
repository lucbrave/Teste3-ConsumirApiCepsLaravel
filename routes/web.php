<?php

use App\Http\Controllers\CepsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/ceps', [CepsController::class, 'index']);
Route::post('/ceps/escolher', [CepsController::class, 'escolherCaminho']);

Route::post('/ceps/exportar', [CepsController::class, 'exportarCsv']);



