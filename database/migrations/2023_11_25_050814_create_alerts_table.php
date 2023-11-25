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
        Schema::create('alerts', function (Blueprint $table) {
            $table->integer('alert_id')->autoIncrement();
            $table->string('code', 20);
            $table->string('name', 50);
            $table->string('site_name', 50);
            $table->string('printer_name', 50);
            $table->smallInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('languages')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('alerts');
    }
};
