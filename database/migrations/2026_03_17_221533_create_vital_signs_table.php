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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['blood_pressure', 'blood_sugar', 'temperature', 'weight', 'heart_rate', 'oxygen_saturation'])->default('blood_pressure');
            $table->decimal('value_1', 8, 2)->nullable();
            $table->decimal('value_2', 8, 2)->nullable();
            $table->string('unit');
            $table->text('notes')->nullable();
            $table->boolean('is_abnormal')->default(false);
            $table->dateTime('measured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
