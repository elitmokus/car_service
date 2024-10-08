<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;
		
		public $timestamps = false;
		protected const PREFIX = "SERVICE";
		protected CONST DIGITS = 8;
		
		protected $fillable = [
			//"client_id",
			//"car_id",
			"log_number",
			"event",
			"event_time",
			"document_id",
		];
		
		public function car(): belongsTo {
			return $this->belongsTo(Car::class);
		}
	
		/*protected static function booted() {
			static::creating(function ($service) {
				$generator = new UniqueIdGenerator;
				$service->document_id = $generator->generateUniqueId(self::PREFIX, self::DIGITS);
			});
		}*/
}
