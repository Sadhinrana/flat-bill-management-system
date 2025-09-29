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
        Schema::create('flats', function (Blueprint $table) {
            $table->id();
            $table->string('flat_number');
            $table->string('owner_name');
            $table->string('owner_contact');
            $table->string('owner_email')->nullable();
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['building_id', 'flat_number']);
            $table->index('building_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flats');
    }
};
