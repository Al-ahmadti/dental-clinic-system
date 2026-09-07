<x-filament-widgets::widget>
    <x-filament::section :heading="$this->getHeading()">
        @php($rows = $this->rows)
        @if (count($rows) === 0)
            <p class="text-sm text-gray-500 dark:text-gray-400">لا يوجد متبقٍ على المرضى حالياً.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-start text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-3 py-2 font-semibold">المريض</th>
                            <th class="px-3 py-2 font-semibold">الهاتف</th>
                            <th class="px-3 py-2 font-semibold">المتبقي</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            @php($p = $row['patient'])
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-3 py-2">{{ $p->name }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $p->phone ?? '—' }}</td>
                                <td class="px-3 py-2 font-medium tabular-nums text-red-600 dark:text-red-400">{{ number_format($row['balance'], 2) }}</td>
                                <td class="px-3 py-2 text-end">
                                    <a href="{{ $this->patientUrl($p->id) }}" class="text-primary-700 underline dark:text-primary-300" wire:navigate>ملف المريض</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
