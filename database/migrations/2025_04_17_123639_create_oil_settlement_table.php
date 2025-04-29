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
        Schema::create('oil_settlements', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('oil_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('settlement_date');
            $table->foreign('member_id')
                ->references('id')->on('members')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('oil_settlements');
    }
};
