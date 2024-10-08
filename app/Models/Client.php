<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;
		
		public $timestamps = false;
		
		protected const PREFIX = "CLNT";
		protected CONST DIGITS = 6;
		
		protected $fillable = [
			"name",
			"card_number",
		];
		
		public function cars(): HasMany {
			return $this->hasMany(Car::class);
		}
}
