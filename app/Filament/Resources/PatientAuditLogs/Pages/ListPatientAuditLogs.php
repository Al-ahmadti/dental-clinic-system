<?php

namespace App\Filament\Resources\PatientAuditLogs\Pages;

use App\Filament\Resources\PatientAuditLogs\PatientAuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPatientAuditLogs extends ListRecords
{
    protected static string $resource = PatientAuditLogResource::class;
}
