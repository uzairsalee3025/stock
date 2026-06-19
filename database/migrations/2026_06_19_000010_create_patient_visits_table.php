<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each visit is one row, giving repeat patients a full separate visit history.
     */
    public function up(): void
    {
        Schema::create('patient_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date');
            $table->string('doctor_name')->nullable();
            $table->string('disease')->nullable();
            $table->text('notes')->nullable();
            // Prescription / medicine slip (image or PDF) stored on the public disk.
            $table->string('prescription_path')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['active', 'follow_up', 'completed', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index('visit_date');
            $table->index('doctor_name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_visits');
    }
};
