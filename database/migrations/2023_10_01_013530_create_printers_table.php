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
            $table->integer('printer_id')->autoIncrement();
            $table->integer('site_id')->nullable();
            $table->foreign('site_id')->references('site_id')->on('sites')->nullOnDelete();
            $table->integer('instrument_id')->nullable();
            $table->foreign('instrument_id')->references('instrument_id')->on('instruments')->nullOnDelete();
            $table->string('code', 100);
            $table->string('name', 100);
            $table->string('ip_addr', 15);
            $table->smallInteger('printer_port');
            $table->string('image', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('coordinate', 100)->nullable();
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
        Schema::dropIfExists('printers');
    }
};
