<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Services\Patients\PatientFinancialTotals;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class PatientAccountStatementPdfController extends Controller
{
    public function __invoke(Request $request, Patient $patient): Response
    {
        abort_unless(auth()->check(), 403);

        Gate::authorize('view', $patient);

        $from = Carbon::parse($request->query('from', now()->subMonths(3)->format('Y-m-d')))->startOfDay();
        $to = Carbon::parse($request->query('to', now()->format('Y-m-d')))->endOfDay();

        $financial = app(PatientFinancialTotals::class)->forPatient($patient, $from, $to);
        $clinic = ClinicSetting::current();

        $logoPath = null;
        if ($clinic?->logo_path && file_exists(storage_path('app/public/'.$clinic->logo_path))) {
            $logoPath = storage_path('app/public/'.$clinic->logo_path);
        }

        $pdf = Pdf::loadView('pdf.patient-account-statement', [
            'patient' => $patient,
            'clinic' => $clinic,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'financial' => $financial,
            'logoPath' => $logoPath,
        ])->setPaper('a4');

        $filename = 'كشف-حساب-'.$patient->getKey().'.pdf';

        return $pdf->download($filename);
    }
}
