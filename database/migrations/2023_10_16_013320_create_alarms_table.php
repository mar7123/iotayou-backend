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
            $table->uuid('alarm_id')->primary();
            $table->uuid('printer_id');
            $table->foreign('printer_id')->references('printer_id')->on('printers')->cascadeOnDelete();
            $table->uuid('parameter_id');
            $table->foreign('parameter_id')->references('parameter_id')->on('parameters')->cascadeOnDelete();
            $table->string('name', 50);
            $table->string('condition', 50);
            $table->smallInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('languages')->cascadeOnUpdate()->nullOnDelete();
            $table->text('notes')->nullable();
            // $table->timestamp('occured_at')->nullable();
            // $table->timestamp('solved_at')->nullable();
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
