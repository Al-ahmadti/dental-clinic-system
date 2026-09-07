<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientMediaFileController extends Controller
{
    public function __invoke(Request $request, Patient $patient, PatientMedia $media): StreamedResponse
    {
        abort_unless(auth()->check(), 403);

        Gate::authorize('view', $patient);

        abort_unless((int) $media->patient_id === (int) $patient->getKey(), 404);

        $disk = $media->storageDisk();
        abort_unless(Storage::disk($disk)->exists($media->path), 404);

        return Storage::disk($disk)->response(
            $media->path,
            $media->original_name ?? basename($media->path),
        );
    }
}
