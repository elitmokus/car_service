<?php

use App\Models\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
						$table->foreignIdFor(Client::class)->constrained()->cascadeOnUpdate();
						$table->integer('car_id');
						$table->string('type');
						$table->timestamp("registered");
						$table->boolean("ownbrand")->default(0);
						$table->integer("accidents")->default(0);
						
						$table->unique(['client_id', 'car_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
