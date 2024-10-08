<?php

namespace App\Http\Middleware;

use App\Models\Car;
use App\Models\Client;
use App\Models\Service;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CheckDatabaseContent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
	    if(Client::query()->count() == 0) {
		    File::delete(storage_path("logs/client_import.log"));
		    $clients = File::json(database_path("json/clients.json"));
		    
		    foreach($clients as $client_data) {
			    $client_validator = Validator::make($client_data, [
				    "id" => "required|numeric",
				    "name" => "required|string",
				    "idcard" => "required|alpha_num",
			    ]);
			    
			    if(!$client_validator->fails()) {
				    $validated = $client_validator->validated();
				    Client::forceCreate([
					    "id" => $validated["id"],
					    "name" => $validated["name"],
					    "card_number" => $validated["idcard"],
				    ]);
			    } else {
				    Log::channel("client_import")->info("Client import failed with data: \n". json_encode($validated)."\n\n");
			    }
		    }
				unset($clients);
	    }
	    
	    if(Car::query()->count() == 0) {
		    File::delete(storage_path("logs/car_import.log"));
				$cars = File::json(database_path("json/cars.json"));
		    
		    foreach($cars as $car_data) {
			    $car_validator = Validator::make($car_data, [
				    "id" => "required|numeric",
				    "client_id" => "required|numeric|exists:clients,id",
				    "car_id" => "required|numeric",
				    "type" => "required|string",
				    "registered" => "required|date",
				    "ownbrand" => "required|boolean",
				    "accident" => "required|integer",
			    ]);
			    
			    if(!$car_validator->fails()) {
				    $validated = $car_validator->validated();
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
		    unset($cars);
	    }
	    
	    if(Service::query()->count() == 0) {
		    File::delete(storage_path("logs/service_import.log"));
		    $services = File::json(database_path("json/services.json"));
		    
		    foreach($services as $service_data) {
			    $service_validator = Validator::make($service_data, [
				    "id" => "required|numeric",
				    "client_id" => "required|numeric|exists:clients,id",
				    "car_id" => "required|numeric|exists:cars,car_id",
				    "lognumber" => "required|numeric",
				    "event" => "required|string",
				    "eventtime" => "nullable|date",
				    "document_id" => "required|string",
			    ]);
			    
			    if(!$service_validator->fails()) {
				    $validated = $service_validator->validated();
				    try {
					    Service::forceCreate([
						    "id" => $validated["id"],
						    "client_id" => $validated["client_id"],
						    "car_id" => $validated["car_id"],
						    "log_number" => $validated["lognumber"],
						    "event" => $validated["event"],
						    "event_time" => $validated["eventtime"],
						    "document_id" => $validated["document_id"],
					    ]);
				    } catch(QueryException $e) {
					    Log::channel("service_import")->info("Service import: failed insertion with data: \n". json_encode($validated)."\nErrors: ".$e->getMessage()."\n\n");
				    }
			    } else {
				    Log::channel("service_import")->info("Service import: failed validation with data: \n". json_encode($validated)."\nErrors: ".print_r($service_validator->errors()->getMessages(),true)."\n\n");
			    }
		    }
				unset($services);
	    }
      return $next($request);
    }
}
