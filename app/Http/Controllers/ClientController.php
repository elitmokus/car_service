<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ClientController extends Controller
{
    public function index(Request $request) {
			
			if($request->filled('search_by_name') and $request->filled('search_by_card_number')) {
				return back()->withErrors(["search_by_name" => "Csak az egyik mezőt töltse ki!"]);
			}
	    $client = null;
			if($request->filled('search_by_name') or $request->filled('search_by_card_number')) {
				$query = Client::query();
				
				//Search by name
				if($request->filled('search_by_name')) {
					$query->where("name", "like" , "%{$request->input('search_by_name')}%");
				}
				
				//Search by card number
				if($request->filled('search_by_card_number')) {
					$query->where("card_number", "=" , "{$request->input('search_by_card_number')}");
				}
				
				if($query->count() > 1) {
					return back()->withErrors(["search_by_name" => "Több mint 1 találat van az adott keresésre. Adjon meg pontosabb keresést!"]);
				}
				
				$client = $query->withCount([
					'cars',
					'cars as services_count' => function ($query) {
						$query->select(DB::raw('count(services.id)'))
							->join('services', 'cars.id', '=', 'services.car_id');
					}
				])->first();
			}
	    
	    //dd($request->all());
			
			return inertia("Client/Index", [
				"clients" => Client::query()
					->paginate(25)
					->withQueryString(),
				"client" => $client,
				"filters" => $request->only(["search_by_name", "search_by_card_number"])
				
			]);
    }
		
		public function getClientCars(Client $client) {
			$cars = $client->cars()
							->with(["services" => function ($query) {
							$query->orderBy('log_number', 'desc')->limit(1);
						}])->get();
			return response()->json($cars);
		}
		
		public function getCarServices(Client $client, Car $car) {
			$services = $car->services()->get();
			return response()->json($services);
		}
}
