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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
           // Relation avec l'établissement (avec suppression en cascade si l'établissement est supprimé)
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            // Jour de la semaine : entier de 0 à 6 (unsignedTinyInteger est parfait et économe pour ça)
            $table->unsignedTinyInteger('day_of_week');
            // Format HH:MM:SS (type TIME en SQL)
            $table->time('start_time');
            $table->time('end_time');

            // Capacité d'accueil du créneau
            $table->unsignedInteger('capacity');

            // Index pour accélérer les recherches par établissement et jour de la semaine
            $table->index(['establishment_id', 'day_of_week']);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
