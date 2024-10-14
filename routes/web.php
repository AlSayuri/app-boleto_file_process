<?php

use App\Http\Controllers\Files\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('api/process-boleto', [FileController::class, 'processBoletoCSV']);
