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
            $table->date('settlement_date');
            $table->date('settlement_date_res')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('settlement_status', ['Pendiente', 'Aceptada', 'Cancelada'])->default('Pendiente');
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('oil_id');
            $table->foreign('member_id')
                ->references('id')->on('members')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('employee_id')->
                references('id')->on('employees')
                ->onUpdate('cascade');
            $table->foreign('oil_id')
                ->references('id')->on('oils')
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
        Schema::dropIfExists('settlements');
    }
};
