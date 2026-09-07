<?php

namespace App\Support;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Collection;

class ServiceCatalogTree
{
    /** @var array<int, string>|null */
    private static ?array $adminCategoryPathCache = null;

    /**
     * Root service categories with nested children and services (same shape as OdontogramEditor).
     *
     * @return Collection<int, ServiceCategory>
     */
    public static function rootCategories(): Collection
    {
        $serviceQuery = fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('name');

        return ServiceCategory::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with([
                'services' => $serviceQuery,
                'children' => fn ($q) => $q->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with([
                        'services' => $serviceQuery,
                        'children' => fn ($c) => $c->where('is_active', true)
                            ->orderBy('sort_order')
                            ->with([
                                'services' => $serviceQuery,
                                'children' => fn ($c2) => $c2->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->with(['services' => $serviceQuery]),
                            ]),
                    ]),
            ])
            ->get();
    }

    /**
     * Active services with no category (for pickers alongside the tree).
     *
     * @return Collection<int, Service>
     */
    public static function uncategorizedServices(): Collection
    {
        return Service::query()
            ->where('is_active', true)
            ->whereNull('category_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<int, string>
     */
    public static function flattenedServiceLabels(): array
    {
        $out = [];

        $walkCategory = function (ServiceCategory $cat, string $prefix) use (&$walkCategory, &$out): void {
            $path = $prefix === '' ? $cat->name : $prefix.' › '.$cat->name;
            foreach ($cat->services as $svc) {
                $label = self::serviceListLabel($svc);
                $out[(int) $svc->getKey()] = $path.' › '.$label;
            }
            foreach ($cat->children as $child) {
                $walkCategory($child, $path);
            }
        };

        foreach (self::rootCategories() as $root) {
            $walkCategory($root, '');
        }

        foreach (self::uncategorizedServices() as $svc) {
            $out[(int) $svc->getKey()] = self::serviceListLabel($svc);
        }

        asort($out, SORT_NATURAL | SORT_FLAG_CASE);

        return $out;
    }

    public static function serviceListLabel(Service $service): string
    {
        $code = $service->code ?? null;
        if (is_string($code) && $code !== '') {
            return '['.$code.'] '.$service->name;
        }

        return $service->name;
    }

    /**
     * All categories (admin): id => full path, sorted by path.
     *
     * @return array<int, string>
     */
    public static function flattenedAdminCategoryLabels(): array
    {
        $paths = self::adminCategoryPathsById();
        asort($paths, SORT_NATURAL | SORT_FLAG_CASE);

        return $paths;
    }

    /**
     * Category id => path using one query (cached per request).
     *
     * @return array<int, string>
     */
    public static function adminCategoryPathsById(): array
    {
        if (self::$adminCategoryPathCache !== null) {
            return self::$adminCategoryPathCache;
        }

        $rows = ServiceCategory::query()->get(['id', 'name', 'parent_id']);
        $byId = $rows->keyBy('id');
        $paths = [];
        foreach ($rows as $row) {
            $paths[(int) $row->id] = self::buildPathForRow((int) $row->id, $byId);
        }
        self::$adminCategoryPathCache = $paths;

        return $paths;
    }

    public static function forgetAdminCategoryPathCache(): void
    {
        self::$adminCategoryPathCache = null;
    }

    /**
     * @param  Collection<int, \stdClass|ServiceCategory>|Collection<int, ServiceCategory>  $byId
     */
    public static function buildPathForRow(int $id, Collection $byId): string
    {
        $parts = [];
        $currentId = $id;
        $guard = 0;
        while ($currentId !== null && $guard++ < 100) {
            $row = $byId->get($currentId);
            if (! $row) {
                break;
            }
            array_unshift($parts, $row->name);
            $currentId = $row->parent_id !== null ? (int) $row->parent_id : null;
        }

        return implode(' › ', $parts);
    }

    /**
     * Category id and all descendant category ids (includes root).
     *
     * @return array<int, int>
     */
    public static function descendantCategoryIdsIncluding(int $categoryId): array
    {
        $childrenByParent = [];
        foreach (ServiceCategory::query()->get(['id', 'parent_id']) as $c) {
            $pid = $c->parent_id;
            $key = $pid === null ? 0 : (int) $pid;
            $childrenByParent[$key][] = (int) $c->id;
        }

        $out = [];
        $stack = [$categoryId];
        while ($stack !== []) {
            $id = (int) array_pop($stack);
            if (isset($out[$id])) {
                continue;
            }
            $out[$id] = $id;
            foreach ($childrenByParent[$id] ?? [] as $childId) {
                $stack[] = $childId;
            }
        }

        return array_values($out);
    }

    /**
     * Options for assigning a service to a category (same paths as admin).
     *
     * @return array<int, string>
     */
    public static function flattenedAdminCategoryLabelsForServiceForm(): array
    {
        return self::flattenedAdminCategoryLabels();
    }
}
