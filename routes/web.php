<?php

use App\Http\Controllers\PatientAccountStatementPdfController;
use App\Http\Controllers\PatientMediaFileController;
use App\Models\ClinicSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $clinic = null;

    try {
        $clinic = Schema::hasTable('clinic_settings')
            ? ClinicSetting::query()->first()
            : null;
    } catch (\Throwable) {
        $clinic = null;
    }

    $logoUrl = null;
    if ($clinic?->logo_path && file_exists(storage_path('app/public/'.$clinic->logo_path))) {
        $logoUrl = asset('storage/'.$clinic->logo_path);
    }

    return view('landing', [
        'clinicName' => $clinic?->clinic_name ?: 'عيادة الأسنان',
        'phone' => $clinic?->phone,
        'address' => $clinic?->address,
        'logoUrl' => $logoUrl,
        'primary' => $clinic?->primary_color ?: '#2563EB',
        'secondary' => $clinic?->secondary_color ?: '#14B8A6',
    ]);
});

Route::middleware('auth')->group(function (): void {
    Route::get('/patients/{patient}/account-statement/pdf', PatientAccountStatementPdfController::class)
        ->name('patients.account-statement.pdf');
    Route::get('/patients/{patient}/media/{media}', PatientMediaFileController::class)
        ->name('patients.media.show');
});
