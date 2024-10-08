<?php
	
	use App\Http\Controllers\ClientController;
	use App\Http\Middleware\CheckDatabaseContent;
use Illuminate\Support\Facades\Route;

Route::middleware(CheckDatabaseContent::class)->group(function () {
	
	Route::get('/', [ClientController::class, 'index']);
	Route::get('/search', [ClientController::class, 'search']);
	
	Route::get('/clients/{client}/cars', [ClientController::class, 'getClientCars']);
	Route::get('/clients/{client}/cars/{car}/services', [ClientController::class, 'getCarServices']);

});
