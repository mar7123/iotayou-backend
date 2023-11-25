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
            $table->uuid('site_id')->primary();
            $table->uuid('customer_id')->nullable();
            $table->foreign('customer_id')->references('role_id')->on('roles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name', 50);
            $table->text('address', 100)->nullable();
            $table->string('location', 100);
            $table->smallInteger('status')->default(6)->nullable();
            $table->foreign('status')->references('id')->on('languages')->cascadeOnUpdate()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('deleted_at')->nullable();
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
