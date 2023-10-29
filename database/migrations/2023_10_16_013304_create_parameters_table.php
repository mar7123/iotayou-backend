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
        Schema::create('parameters', function (Blueprint $table) {
            $table->integer('parameter_id')->autoIncrement();
            $table->integer('instrument_id')->nullable();
            $table->foreign('instrument_id')->references('instrument_id')->on('instruments')->nullOnDelete();
            $table->string('code', 20);
            $table->string('name', 50);
            $table->smallInteger('status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
