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
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('role_id')->primary();
            $table->string('code', 20)->unique();
            $table->string('name', 60);
            $table->text('address');
            $table->smallInteger('status')->default(6)->nullable();
            $table->foreign('status')->references('id')->on('languages')->cascadeOnUpdate()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->integer('role_type');
            $table->foreign('role_type')->references('user_group_id')->on('user_groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->uuid('parent_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->foreign('parent_id')->references('role_id')->on('roles')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
