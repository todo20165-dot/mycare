<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes for PWA offline functionality
Route::middleware('auth:sanctum')->group(function () {
    // Medications API
    Route::get('/medications', 'App\Http\Controllers\Api\MedicationController@index');
    Route::post('/medications', 'App\Http\Controllers\Api\MedicationController@store');
    Route::get('/medications/{id}', 'App\Http\Controllers\Api\MedicationController@show');
    Route::put('/medications/{id}', 'App\Http\Controllers\Api\MedicationController@update');
    Route::delete('/medications/{id}', 'App\Http\Controllers\Api\MedicationController@destroy');

    // Medication Logs API
    Route::get('/medication-logs', 'App\Http\Controllers\Api\MedicationLogController@index');
    Route::post('/medication-logs', 'App\Http\Controllers\Api\MedicationLogController@store');

    // Vital Signs API
    Route::get('/vital-signs', 'App\Http\Controllers\Api\VitalSignController@index');
    Route::post('/vital-signs', 'App\Http\Controllers\Api\VitalSignController@store');
    Route::get('/vital-signs/{id}', 'App\Http\Controllers\Api\VitalSignController@show');

    // Notifications API
    Route::get('/notifications', 'App\Http\Controllers\Api\NotificationController@index');
    Route::post('/notifications/{id}/read', 'App\Http\Controllers\Api\NotificationController@markAsRead');

    // Reports API
    Route::get('/reports', 'App\Http\Controllers\Api\ReportController@index');
    Route::post('/reports', 'App\Http\Controllers\Api\ReportController@store');
    Route::get('/reports/{id}', 'App\Http\Controllers\Api\ReportController@show');

    // Family Links API
    Route::get('/family-links', 'App\Http\Controllers\Api\FamilyLinkController@index');
    Route::post('/family-links', 'App\Http\Controllers\Api\FamilyLinkController@store');
    Route::post('/family-links/{id}/approve', 'App\Http\Controllers\Api\FamilyLinkController@approve');
    Route::post('/family-links/{id}/reject', 'App\Http\Controllers\Api\FamilyLinkController@reject');

    // Statistics API
    Route::get('/statistics', 'App\Http\Controllers\Api\StatisticsController@index');
    Route::get('/compliance-rate', 'App\Http\Controllers\Api\StatisticsController@complianceRate');
});
