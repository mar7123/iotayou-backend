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
            $table->string('username', 30);
            $table->string('full_name', 60);
            $table->string('email', 100)->unique();
            $table->string('phone_num', 20);
            $table->string('pic', 20)->nullable();
            $table->text('address');
            $table->string('salt', 100)->nullable();
            $table->string('password', 100);

            // non-general field
            $table->string('picture', 100)->nullable();
            $table->string('reset_token', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->date('join_date')->nullable();
            $table->string('plant', 100)->default('javatech')->nullable();
            $table->string('site', 100)->nullable();
            $table->string('dash_suffix', 20)->default('_std');
            $table->tinyInteger('sendalmsms')->default(0);
            $table->tinyInteger('sendalmemail')->default(0);
            $table->tinyInteger('sendreport')->default(0);
            $table->tinyInteger('announcement')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('user_type');
            $table->foreign('user_type')->references('user_group_id')->on('user_groups');
            $table->smallInteger('status')->default(6);
            $table->text('notes')->nullable();
            // $table->smallInteger('created_by');
            // $table->smallInteger('updated_by')->default(0);
            // $table->smallInteger('deleted_by')->default(0);
            // $table->timestamp('deleted_at')->nullable();
            $table->rememberToken();
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
