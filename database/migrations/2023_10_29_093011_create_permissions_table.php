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
        Schema::create('permissions', function (Blueprint $table) {
            $table->integer('permission_id')->autoIncrement();
            $table->integer('user_group');
            $table->foreign('user_group')->references('user_group_id')->on('user_groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->uuid('role');
            $table->foreign('role')->references('role_id')->on('roles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('role_permission', 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
