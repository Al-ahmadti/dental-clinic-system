<?php

namespace App\Models;

use App\Support\ServiceCatalogTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ServiceCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ServiceCategory $category): void {
            if ($category->parent_id === null) {
                return;
            }

            $parentId = (int) $category->parent_id;
            if ($category->exists) {
                $blocked = ServiceCatalogTree::descendantCategoryIdsIncluding((int) $category->getKey());
                if (in_array($parentId, $blocked, true)) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'لا يمكن جعل التصنيف أباً لنفسه أو لفرع من فروعه.',
                    ]);
                }
            }

            $validator = Validator::make(
                ['parent_id' => $category->parent_id],
                ['parent_id' => ['exists:service_categories,id']],
            );
            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }
        });

        static::saved(fn () => ServiceCatalogTree::forgetAdminCategoryPathCache());

        static::deleted(fn () => ServiceCatalogTree::forgetAdminCategoryPathCache());
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id')->orderBy('sort_order')->orderBy('name');
    }
}
