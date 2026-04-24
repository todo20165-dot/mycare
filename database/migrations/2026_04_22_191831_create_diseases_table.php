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
        Schema::create('diseases', function (Blueprint $table) {
                        $table->id();
            $table->string('name')->unique()->comment('اسم المرض');
            $table->string('specialization')->comment('التخصص الطبي المطلوب لعلاج هذا المرض');
            $table->text('description')->nullable()->comment('وصف موجز للمرض');
            $table->timestamps();

            $table->index('specialization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};
