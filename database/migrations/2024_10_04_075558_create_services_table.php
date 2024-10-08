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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
						//$table->foreignIdFor(Client::class)->constrained()->cascadeOnUpdate();
						
						$table->integer('client_id');
						$table->integer('car_id');
						$table->foreign(["client_id","car_id"])->references(["client_id","car_id"])->on("cars")->cascadeOnUpdate();
						
						$table->integer("log_number")->default(0);
						$table->string("event");
						$table->timestamp("event_time")->nullable();
						$table->string("document_id");
						
						
						$table->unique(["client_id","car_id","log_number"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
