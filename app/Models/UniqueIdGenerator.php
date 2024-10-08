<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UniqueIdGenerator extends Model
{
    use HasFactory;
		
		protected $fillable = [
			"prefix",
			"digits",
			"counter",
		];
		
		public function generateUniqueId(string $prefix, int $digits = 5, int $counter = 0): ?string {
			//Single transaction and lock for concurrency
			return DB::transaction(function () use ($prefix, $digits, $counter) {
				//Get generator
				$generator = UniqueIdGenerator::query()
					->where('prefix', "=", $prefix)
					->sharedLock()
					->first();
				
				//Create if not found
				$generator ??= UniqueIdGenerator::create([
					'prefix' => $prefix,
					'digits' => $digits,
					'counter' => $counter,
				]);
				
				//Generate new id
				$new_id = $generator->prefix."-".str_pad($generator->counter + 1, $generator->digits, "0", STR_PAD_LEFT);
				
				//Update counter
				$generator->counter++;
				$generator->save();
				
				//return
				return $new_id;
			});
		}
}
