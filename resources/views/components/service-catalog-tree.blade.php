@props([
    'categories',
    'uncategorized',
    'variant' => 'radios',
    'lineIndex' => null,
    'selectedServiceId' => null,
])

<div class="max-h-80 space-y-2 overflow-y-auto pe-1 text-sm" {{ $attributes }}>
    @foreach ($categories as $root)
        <details class="rounded-lg border border-primary-100 bg-white open:shadow-sm dark:border-gray-600 dark:bg-gray-800" @if ($loop->first) open @endif>
            <summary class="cursor-pointer px-3 py-2.5 font-medium text-primary-900 dark:text-primary-100">{{ $root->name }}</summary>
            <div class="space-y-1 border-t border-primary-100 px-3 py-2 dark:border-gray-600">
                @foreach ($root->services as $service)
                    @if ($variant === 'radios')
                        <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 hover:bg-primary-50 dark:hover:bg-gray-700">
                            <input type="radio" wire:model.live="selectedServiceId" value="{{ $service->id }}" class="text-primary-600" />
                            <span class="flex-1">{{ \App\Support\ServiceCatalogTree::serviceListLabel($service) }}</span>
                            <span class="tabular-nums text-xs text-gray-500">{{ number_format((float) $service->price, 2) }}</span>
                        </label>
                    @else
                        <button
                            type="button"
                            wire:click="pickServiceFromModal({{ $service->id }})"
                            class="flex w-full cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-start hover:bg-primary-50 dark:hover:bg-gray-700 {{ (int) $selectedServiceId === (int) $service->id ? 'bg-primary-50 ring-1 ring-primary-500 dark:bg-primary-950/40' : '' }}"
                        >
                            <span class="flex-1">{{ \App\Support\ServiceCatalogTree::serviceListLabel($service) }}</span>
                            <span class="tabular-nums text-xs text-gray-500">{{ number_format((float) $service->price, 2) }}</span>
                        </button>
                    @endif
                @endforeach
                @foreach ($root->children as $child)
                    @include('components.service-catalog-tree-node', [
                        'category' => $child,
                        'variant' => $variant,
                        'selectedServiceId' => $selectedServiceId,
                    ])
                @endforeach
            </div>
        </details>
    @endforeach
    @if ($uncategorized->isNotEmpty())
        <details class="rounded-lg border border-dashed border-gray-200 dark:border-gray-600">
            <summary class="cursor-pointer px-3 py-2 text-sm text-gray-600 dark:text-gray-300">بدون تصنيف</summary>
            <div class="space-y-1 px-3 pb-2">
                @foreach ($uncategorized as $service)
                    @if ($variant === 'radios')
                        <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 hover:bg-primary-50 dark:hover:bg-gray-700">
                            <input type="radio" wire:model.live="selectedServiceId" value="{{ $service->id }}" class="text-primary-600" />
                            <span class="flex-1">{{ \App\Support\ServiceCatalogTree::serviceListLabel($service) }}</span>
                            <span class="tabular-nums text-xs text-gray-500">{{ number_format((float) $service->price, 2) }}</span>
                        </label>
                    @else
                        <button
                            type="button"
                            wire:click="pickServiceFromModal({{ $service->id }})"
                            class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-start hover:bg-primary-50 dark:hover:bg-gray-700 {{ (int) $selectedServiceId === (int) $service->id ? 'bg-primary-50 ring-1 ring-primary-500 dark:bg-primary-950/40' : '' }}"
                        >
                            <span class="flex-1">{{ \App\Support\ServiceCatalogTree::serviceListLabel($service) }}</span>
                            <span class="tabular-nums text-xs text-gray-500">{{ number_format((float) $service->price, 2) }}</span>
                        </button>
                    @endif
                @endforeach
            </div>
        </details>
    @endif
</div>
