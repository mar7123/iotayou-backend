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
        Schema::create('sites', function (Blueprint $table) {
            $table->integer('site_id')->autoIncrement();
            $table->uuid('customer_id')->nullable();
            $table->foreign('customer_id')->references('user_id')->on('users')->nullOnDelete();
            $table->string('code', 100);
            $table->string('name', 100);
            $table->text('address', 100);
            $table->smallInteger('sourceloc')->nullable()->default(80);
            $table->string('location', 100);
            $table->string('pic', 100)->nullable();
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
        Schema::dropIfExists('sites');
    }
};
