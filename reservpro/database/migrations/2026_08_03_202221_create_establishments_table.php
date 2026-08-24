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
        Schema::create('establishments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete(); // assumer que l'utilisateur est le propriétaire de l'établissement
    $table->string('name');
    $table->string('category'); // ex: salon, sport, coworking
    $table->string('address')->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
