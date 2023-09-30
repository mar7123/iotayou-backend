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
        Schema::create('parent_children', function (Blueprint $table) {
            $table->integer('parent_children_id')->autoIncrement();
            $table->uuid('parent_id')->nullable();
            $table->uuid('child_id')->nullable();
            $table->foreign('parent_id')->references('user_id')->on('users');
            $table->foreign('child_id')->references('user_id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_children');
    }
};
