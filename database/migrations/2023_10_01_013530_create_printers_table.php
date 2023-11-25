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
        Schema::create('printers', function (Blueprint $table) {
            $table->uuid('printer_id')->primary();
            $table->uuid('site_id')->nullable();
            $table->foreign('site_id')->references('site_id')->on('sites')->cascadeOnUpdate()->nullOnDelete();
            $table->uuid('instrument_id')->nullable();
            $table->foreign('instrument_id')->references('instrument_id')->on('instruments')->cascadeOnUpdate()->nullOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->smallInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('languages')->cascadeOnUpdate()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
