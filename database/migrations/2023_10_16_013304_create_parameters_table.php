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
            $table->uuid('parameter_id')->primary();
            $table->uuid('instrument_id')->nullable();
            $table->foreign('instrument_id')->references('instrument_id')->on('instruments')->cascadeOnUpdate()->nullOnDelete();
            $table->string('code', 20)->unique();
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
