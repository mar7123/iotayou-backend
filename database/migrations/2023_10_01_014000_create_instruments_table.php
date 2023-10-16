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
        Schema::create('instruments', function (Blueprint $table) {
            $table->integer('instrument_id')->autoIncrement();
            $table->integer('printer_id');
            $table->foreign('printer_id')->references('printer_id')->on('printers')->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name', 100);
            $table->string('brand', 100);
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
        Schema::dropIfExists('instruments');
    }
};
