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
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->integer('quantity');
            $table->enum('analysis_status', ['Pendiente', 'Completo'])->default('Pendiente');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')
                ->references('id')->on('members')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
