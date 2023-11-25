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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('username', 40)->unique();
            $table->string('email', 100)->unique();
            $table->string('name', 50);
            $table->string('salt', 100)->nullable();
            $table->string('password', 100);
            $table->smallInteger('status')->default(6)->nullable();
            $table->foreign('status')->references('id')->on('languages')->cascadeOnUpdate()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('user_role_id');
            $table->foreign('user_role_id')->references('role_id')->on('roles')->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('phone_num', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('picture', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
