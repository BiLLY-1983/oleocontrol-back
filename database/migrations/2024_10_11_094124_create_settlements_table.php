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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->decimal('price', 10, 2);
            $table->enum('settlement_status', ['Pendiente', 'Aceptada', 'Cancelada'])->default('Pendiente');
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->foreign('member_id')
                ->references('id')->on('members')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('worker_id')->
                references('id')->on('workers')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
