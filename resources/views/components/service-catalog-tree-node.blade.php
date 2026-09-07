@props(['category', 'variant' => 'radios', 'selectedServiceId' => null])

<details class="ms-1 rounded-md border border-gray-100 dark:border-gray-600">
    <summary class="cursor-pointer px-2 py-1.5 text-gray-800 dark:text-gray-200">{{ $category->name }}</summary>
    <div class="space-y-1 px-2 pb-2">
        @foreach ($category->services as $service)
            @if ($variant === 'radios')
                <label class="flex cursor-pointer items-center gap-2 rounded py-1 hover:bg-gray-50 dark:hover:bg-gray-800/80">
                    <input type="radio" wire:model.live="selectedServiceId" value="{{ $service->id }}" class="text-primary-600" />
                    <span class="flex-1">{{ \App\Support\ServiceCatalogTree::serviceListLabel($service) }}</span>
                    <span class="tabular-nums text-xs text-gray-500">{{ number_format((float) $service->price, 2) }}</span>
                </label>
            @else
                <button
                    type="button"
                    wire:click="pickServiceFromModal({{ $service->id }})"
                    class="flex w-full cursor-pointer items-center gap-2 rounded py-1 text-start hover:bg-gray-50 dark:hover:bg-gray-800/80 {{ (int) $selectedServiceId === (int) $service->id ? 'bg-primary-50 ring-1 ring-primary-500 dark:bg-primary-950/40' : '' }}"
                >
                    <span class="flex-1">{{ \App\Support\ServiceCatalogTree::serviceListLabel($service) }}</span>
                    <span class="tabular-nums text-xs text-gray-500">{{ number_format((float) $service->price, 2) }}</span>
                </button>
            @endif
        @endforeach
        @foreach ($category->children as $sub)
            @include('components.service-catalog-tree-node', [
                'category' => $sub,
                'variant' => $variant,
                'selectedServiceId' => $selectedServiceId,
            ])
        @endforeach
    </div>
</details>
