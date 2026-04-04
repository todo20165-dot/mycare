<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة
     */
    public function up(): void
    {
        Schema::create('doctor_patient', function (Blueprint $table) {
            $table->id();
            
            // المفاتيح الأجنبية
            $table->foreignId('doctor_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->foreignId('patient_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // معلومات العلاقة
            $table->string('specialization')->nullable()->comment('تخصص الطبيب');
            $table->enum('status', ['pending', 'approved', 'rejected', 'inactive'])
                ->default('pending')
                ->comment('حالة العلاقة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // التواريخ
            $table->timestamp('assigned_at')->useCurrent()->comment('تاريخ الربط');
            $table->timestamp('approved_at')->nullable()->comment('تاريخ الموافقة');
            $table->timestamps();
            
            // الفهارس
            $table->unique(['doctor_id', 'patient_id']);
            $table->index('doctor_id');
            $table->index('patient_id');
            $table->index('status');
        });
    }

    /**
     * استرجاع الهجرة
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_patient');
    }
};
