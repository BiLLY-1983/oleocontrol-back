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
            $table->unsignedBigInteger('entry_id')->unique(); 
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('oil_id')->nullable();
            $table->foreign('entry_id')
                ->references('id')->on('entries')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('member_id')
                ->references('id')->on('members')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('employee_id')
                ->references('id')->on('employees')
                ->onUpdate('cascade');
            $table->foreign('oil_id')
                ->references('id')->on('oils')
                ->onUpdate('cascade');
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
