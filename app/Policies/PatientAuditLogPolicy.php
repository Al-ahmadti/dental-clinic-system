<?php

namespace App\Policies;

use App\Models\PatientAuditLog;
use App\Models\User;

class PatientAuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PatientAuditLog $patientAuditLog): bool
    {
        return true;
    }
}
