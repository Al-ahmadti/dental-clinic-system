<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tooth_treatment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('fdi_number');
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 32);
            $table->text('doctor_notes')->nullable();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('performed_at');
            $table->timestamps();

            $table->index(['patient_id', 'fdi_number', 'performed_at'], 'tooth_hist_patient_fdi_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tooth_treatment_histories');
    }
};
