<?php

namespace App\Providers;

use App\Models\Car;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

/******* Don't use this, runs before migration *******/
class CheckDatabaseContentProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
			if(!Schema::hasTable('clients') or !Schema::hasTable('services') or !Schema::hasTable('cars')) {
				Artisan::call('migrate');
			}
      if(Client::query()->count() == 0) {
				$clients = File::json(database_path("json/clients.json"));
				
				foreach($clients as $client_data) {
					$validator = Validator::make($client_data, [
						"id" => "required|numeric",
						"name" => "required|string",
						"idcard" => "required|alpha_num",
					]);
					
					if(!$validator->fails()) {
						$validated = $validator->validated();
						Client::forceCreate([
							"id" => $validated["id"],
							"name" => $validated["name"],
							"card_number" => $validated["idcard"],
						]);
					} else {
						Log::channel("client_import")->info("Client import failed with data: \n". json_encode($validated)."\n\n");
					}
				}
      }
	    
	    if(Car::query()->count() == 0) {
		    $cars = File::json(database_path("json/cars.json"));
		    
		    foreach($cars as $car_data) {
			    $validator = Validator::make($car_data, [
				    "id" => "required|numeric",
				    "client_id" => "required|numeric|exists:clients,id",
				    "car_id" => "required|numeric",
				    "type" => "required|string",
				    "registered" => "required|date",
				    "ownbrand" => "required|boolean",
				    "accident" => "required|integer",
			    ]);
			    
			    if(!$validator->fails()) {
				    $validated = $validator->validated();
				    Car::forceCreate([
					    "id" => $validated["id"],
					    "client_id" => $validated["client_id"],
					    "car_id" => $validated["car_id"],
					    "type" => $validated["type"],
					    "registered" => $validated["registered"],
					    "ownbrand" => $validated["ownbrand"],
					    "accidents" => $validated["accident"],
				    ]);
			    } else {
				    Log::channel("car_import")->info("Car import failed with data: \n". json_encode($validated)."\n\n");
			    }
		    }
	    }
	    
	    if(Service::query()->count() == 0) {
		    $services = File::json(database_path("json/services.json"));
		    
		    foreach($services as $service_data) {
			    $validator = Validator::make($service_data, [
				    "id" => "required|numeric",
				    "client_id" => "required|numeric|exists:clients,id",
				    "car_id" => "required|numeric",
				    "lognumber" => "required|string",
				    "event" => "required|string",
				    "eventtime" => "date",
				    "document_id" => "required|string",
			    ]);
			    
			    if(!$validator->fails()) {
				    $validated = $validator->validated();
				    Car::forceCreate([
					    "id" => $validated["id"],
					    "client_id" => $validated["client_id"],
					    "car_id" => $validated["car_id"],
					    "lognumber" => $validated["lognumber"],
					    "event" => $validated["event"],
					    "eventtime" => $validated["eventtime"],
					    "document_id" => $validated["document_id"],
				    ]);
			    } else {
				    Log::channel("car_import")->info("Car import failed with data: \n". json_encode($validated)."\n\n");
			    }
		    }
	    }
    }
}
