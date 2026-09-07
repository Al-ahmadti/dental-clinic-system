<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_teeth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('fdi_number');
            $table->string('current_status', 32)->default('healthy');
            $table->timestamps();

            $table->unique(['patient_id', 'fdi_number']);
            $table->index('fdi_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_teeth');
    }
};
