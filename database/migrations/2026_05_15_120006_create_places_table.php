<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('address');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('rating', 3, 1)->default(0);
            $table->text('description')->nullable();
            $table->boolean('wheelchair')->default(false);
            $table->boolean('ramp')->default(false);
            $table->boolean('elevator')->default(false);
            $table->boolean('adapted_toilet')->default(false);
            $table->boolean('pmr_parking')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};