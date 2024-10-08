<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    /** @use HasFactory<\Database\Factories\CarFactory> */
    use HasFactory;
	
		public $timestamps = false;
		protected const PREFIX = "CAR";
		protected CONST DIGITS = 6;
		
		protected $fillable = [
			"type",
			"registered",
			"own_brand",
			"accidents",
			//"client_id"
		];
		
		public function client(): belongsTo {
			return $this->belongsTo(Client::class);
		}
		
		public function services(): HasMany{
			return $this->hasMany(Service::class);
		}
}
