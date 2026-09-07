@props([
    'toothStatuses' => [],
    'selectedFdi' => null,
    'wireClickMethod' => 'addLineForTooth',
    'chartId' => 'chart',
    'compact' => false,
])

@php
    use App\Support\Odontogram\LicensedReactOdontogramGlyphs;

    $glyphs = LicensedReactOdontogramGlyphs::glyphs();
    $quadrants = LicensedReactOdontogramGlyphs::quadrants();
    $labelAnchors = LicensedReactOdontogramGlyphs::labelAnchors();

    $statusColors = function (int $fdi) use ($toothStatuses): array {
        return match ($toothStatuses[$fdi] ?? 'healthy') {
            'healthy' => ['fill' => '#34d399', 'stroke' => '#047857', 'label' => '#064e3b'],
            'decayed' => ['fill' => '#f87171', 'stroke' => '#991b1b', 'label' => '#450a0a'],
            'treated' => ['fill' => '#fcd34d', 'stroke' => '#b45309', 'label' => '#78350f'],
            'missing' => ['fill' => '#cbd5e1', 'stroke' => '#475569', 'label' => '#1e293b'],
            default => ['fill' => '#e2e8f0', 'stroke' => '#64748b', 'label' => '#334155'],
        };
    };

    $glowId = 'odontogram-tooth-glow-'.$chartId;
@endphp

<div class="odontogram-svg-chart w-full text-start" wire:key="odontogram-chart-{{ $chartId }}">
    @if (! $compact)
        <div class="mb-3 flex flex-wrap items-center gap-3 rounded-xl border border-primary-200/80 bg-primary-50/50 px-3 py-2 text-xs text-primary-950 dark:border-primary-800 dark:bg-primary-950/30 dark:text-primary-50">
            <span class="font-semibold">مفتاح:</span>
            <span class="inline-flex items-center gap-1"><span class="inline-block h-3 w-3 rounded-sm ring-1 ring-emerald-800" style="background:#34d399"></span> سليم</span>
            <span class="inline-flex items-center gap-1"><span class="inline-block h-3 w-3 rounded-sm ring-1 ring-red-900" style="background:#f87171"></span> تسوس</span>
            <span class="inline-flex items-center gap-1"><span class="inline-block h-3 w-3 rounded-sm ring-1 ring-amber-900" style="background:#fcd34d"></span> معالج</span>
            <span class="inline-flex items-center gap-1"><span class="inline-block h-3 w-3 rounded-sm ring-1 ring-slate-600" style="background:#cbd5e1"></span> مفقود</span>
        </div>
    @endif

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-gradient-to-b from-white to-slate-50 p-4 shadow-sm dark:border-gray-700 dark:from-gray-900 dark:to-gray-950">
        <div class="mb-2 flex flex-wrap justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="font-medium text-gray-700 dark:text-gray-300">الفك العلوي</span>
            <span class="font-medium text-gray-700 dark:text-gray-300">الفك السفلي</span>
        </div>
        <svg
            viewBox="0 0 900 158"
            class="mx-auto block h-auto w-full min-h-[180px] max-w-[920px] select-none"
            xmlns="http://www.w3.org/2000/svg"
            aria-label="مخطط أسنان FDI"
        >
            <defs>
                <filter id="{{ $glowId }}" x="-30%" y="-30%" width="160%" height="160%">
                    <feDropShadow dx="0" dy="0.5" stdDeviation="0.8" flood-color="#2563EB" flood-opacity="0.35" />
                </filter>
            </defs>

            <rect width="900" height="158" rx="12" fill="rgb(248 250 252 / 0.85)" class="dark:fill-gray-900/80" />
            <line x1="450" y1="6" x2="450" y2="152" stroke="currentColor" stroke-width="1" stroke-dasharray="5 4" class="text-gray-200 dark:text-gray-700" opacity="0.85" />

            @foreach ($quadrants as $qi => $quad)
                <g transform="{{ $quad['transform'] }}" wire:key="quad-{{ $chartId }}-{{ $qi }}">
                    @foreach ($glyphs as $ti => $g)
                        @php
                            $fdi = $quad['fdiBase'] + $ti;
                            $c = $statusColors($fdi);
                            $selected = $selectedFdi === $fdi;
                            $anchor = $labelAnchors[$ti] ?? ['x' => 0, 'y' => 72];
                        @endphp
                        <g
                            class="cursor-pointer outline-none transition-opacity hover:opacity-95"
                            wire:click="{{ $wireClickMethod }}({{ $fdi }})"
                            wire:key="t-{{ $chartId }}-{{ $fdi }}"
                            role="button"
                            tabindex="0"
                            @keydown.enter.prevent="$wire.{{ $wireClickMethod }}({{ $fdi }})"
                            @keydown.space.prevent="$wire.{{ $wireClickMethod }}({{ $fdi }})"
                        >
                            <title>سن {{ $fdi }} — {{ $g['type'] }}</title>
                            <path
                                d="{{ $g['shadow'] }}"
                                fill="#0f172a"
                                fill-opacity="0.14"
                                stroke="none"
                                pointer-events="none"
                            />
                            <path
                                d="{{ $g['outline'] }}"
                                fill="{{ $c['fill'] }}"
                                stroke="{{ $c['stroke'] }}"
                                stroke-width="{{ $selected ? 2.8 : 1.65 }}"
                                stroke-linejoin="round"
                                @if ($selected) filter="url(#{{ $glowId }})" @endif
                            />
                            @foreach ($g['highlights'] as $hd)
                                <path
                                    d="{{ $hd }}"
                                    fill="none"
                                    stroke="rgba(255,255,255,0.62)"
                                    stroke-width="1.15"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    pointer-events="none"
                                />
                            @endforeach
                            @if ($selected)
                                <ellipse
                                    cx="{{ $anchor['x'] }}"
                                    cy="{{ $anchor['y'] - 8 }}"
                                    rx="22"
                                    ry="34"
                                    fill="none"
                                    stroke="#0d9488"
                                    stroke-width="2"
                                    stroke-dasharray="4 3"
                                    opacity="0.9"
                                    pointer-events="none"
                                />
                            @endif
                            <text
                                x="{{ $anchor['x'] }}"
                                y="{{ $anchor['y'] }}"
                                text-anchor="middle"
                                class="pointer-events-none text-[11px] font-bold"
                                fill="{{ $c['label'] }}"
                                stroke="rgba(255,255,255,0.35)"
                                stroke-width="0.35"
                                paint-order="stroke fill"
                            >{{ $fdi }}</text>
                        </g>
                    @endforeach
                </g>
            @endforeach
        </svg>
        <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">انقر على السن لإضافة سطر في جدول العلاج.</p>
    </div>
</div>
