<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::query()->exists()) {
            return;
        }

        $roots = [
            'ترميم' => ['حشو مؤقت', 'حشو دائم', 'حشو مركب'],
            'خلع' => ['خلع بسيط', 'خلع جراحي'],
            'تنظيف' => ['تنظيف أسنان', 'إزالة جير'],
            'تقويم' => ['زيارة تقويم'],
            'عصب' => ['علاج عصب'],
            'جراحة' => ['جراحة لثة'],
        ];

        $order = 0;
        foreach ($roots as $rootName => $children) {
            $root = ServiceCategory::query()->create([
                'parent_id' => null,
                'name' => $rootName,
                'sort_order' => $order++,
                'is_active' => true,
            ]);

            $childOrder = 0;
            foreach ($children as $childName) {
                $child = ServiceCategory::query()->create([
                    'parent_id' => $root->id,
                    'name' => $childName,
                    'sort_order' => $childOrder++,
                    'is_active' => true,
                ]);

                Service::query()->create([
                    'category_id' => $child->id,
                    'name' => $childName,
                    'price' => match ($rootName) {
                        'خلع' => 150,
                        'عصب' => 400,
                        default => 100,
                    },
                    'is_active' => true,
                ]);
            }
        }

        Service::query()->create([
            'category_id' => null,
            'name' => 'استشارة',
            'price' => 50,
            'is_active' => true,
        ]);
    }
}
