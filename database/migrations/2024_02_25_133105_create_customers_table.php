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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('username')->unique()->default('NVL');
            $table->boolean('is_vip')->default(false);
            $table->boolean('is_ban')->default(false);
            $table->string('group')->default('nvl_group');
            $table->timestamps();
        });

        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('nvl_id')->nullable();
            $table->string('slug');
            $table->string('title');
            $table->string('seasons')->nullable();
            $table->string('episodes')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('serie_id')->constrained()->cascadeOnDelete();
            $table->integer('qty');
            $table->double('unit_price')->constrained()->cascadeOnDelete();
            $table->double('total')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
