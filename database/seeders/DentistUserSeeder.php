<?php

namespace Database\Seeders;

use App\Models\ClinicSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DentistUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD', 'password');

        User::query()->firstOrCreate(
            ['email' => 'doctor@dental.local'],
            [
                'name' => 'طبيب العيادة',
                'password' => $password,
            ]
        );

        ClinicSetting::query()->firstOrCreate(
            [],
            [
                'clinic_name' => 'عيادة الأسنان',
                'primary_color' => '#2563EB',
                'secondary_color' => '#14B8A6',
            ]
        );
    }
}
