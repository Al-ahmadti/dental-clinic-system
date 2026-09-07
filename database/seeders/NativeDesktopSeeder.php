<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * First-run seeder for NativePHP desktop installs (no demo patients).
 */
class NativeDesktopSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DentistUserSeeder::class,
            ServiceCatalogSeeder::class,
        ]);
    }
}
