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
        Schema::create('alarms', function (Blueprint $table) {
            $table->integer('alarm_id')->autoIncrement();
            $table->integer('printer_id');
            $table->foreign('printer_id')->references('printer_id')->on('printers')->cascadeOnDelete();
            $table->string('name', 50);
            $table->tinyInteger('status');
            $table->timestamp('occured_at')->nullable();
            $table->timestamp('solved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alarms');
    }
};
