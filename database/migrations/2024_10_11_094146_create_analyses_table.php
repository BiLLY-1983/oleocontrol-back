<?php

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
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->date('analysis_date')->nullable(); 
            $table->decimal('acidity', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->decimal('yield', 5, 2)->nullable(); 
            $table->unsignedBigInteger('entry_id')->unique(); //? 
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->unsignedBigInteger('oil_id')->nullable();
            $table->decimal('oil_quantity', 10, 2)->nullable();
            $table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');
            $table->foreign('worker_id')->references('id')->on('workers');
            $table->foreign('oil_id')->references('id')->on('oils');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
