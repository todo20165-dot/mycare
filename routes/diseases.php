<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiseaseController;

// مسارات اختيار المرض للمريض
Route::middleware(['auth'])->group(function () {
    Route::get('/select-disease', [DiseaseController::class, 'selectDisease'])->name('patient.select-disease');
    Route::post('/select-disease', [DiseaseController::class, 'storeDisease'])->name('patient.store-disease');
    Route::get('/search-doctors-by-disease', [DiseaseController::class, 'searchDoctorsByDisease'])->name('patient.search-doctors-by-disease');
});

// مسارات AJAX
Route::middleware(['auth'])->group(function () {
    Route::get('/api/doctors-by-disease', [DiseaseController::class, 'getDoctorsByDisease'])->name('api.doctors-by-disease');
    Route::post('/api/validate-doctor-disease', [DiseaseController::class, 'validateDoctorForDisease'])->name('api.validate-doctor-disease');
});

// مسارات إدارة الأمراض (للإدارة)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('diseases', DiseaseController::class);
});
