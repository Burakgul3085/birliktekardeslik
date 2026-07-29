@php
    /* Tüm kartlarda ortak kullanılan etiketler ve takvim verileri */
    $liveSharedConfig = [
        'locale' => app()->getLocale(),
        'labels' => [
            'weather' => __('app.chad_live.weather'),
            'local_time' => __('app.chad_live.local_time'),
            'hijri' => __('app.chad_live.hijri'),
            'next_prayer' => __('app.chad_live.next_prayer'),
            'weather_error' => __('app.chad_live.weather_error'),
            'donate' => __('app.chad_live.donate'),
        ],
        'prayerNames' => __('app.chad_live.prayers'),
        'hijriMonths' => __('app.chad_live.hijri_months'),
        'donateUrl' => route('donations'),
    ];

    /* Faaliyet yürütülen ülkeler: koordinat, saat dilimi ve namaz hesaplama yöntemi */
    $liveCountries = [
        [
            'flag' => 'chad',
            'badge' => __('app.chad_live.badge'),
            'title' => __('app.chad_live.title'),
            'subtitle' => __('app.chad_live.subtitle'),
            'location' => [
                'cacheKey' => 'chad',
                'latitude' => 12.1067,
                'longitude' => 15.0444,
                'timezone' => 'Africa/Ndjamena',
                'prayerMethod' => 4,
            ],
        ],
        [
            'flag' => 'madagascar',
            'badge' => __('app.chad_live.madagascar_badge'),
            'title' => __('app.chad_live.madagascar_title'),
            'subtitle' => __('app.chad_live.madagascar_subtitle'),
            'location' => [
                'cacheKey' => 'madagascar',
                'latitude' => -18.8792,
                'longitude' => 47.5079,
                'timezone' => 'Indian/Antananarivo',
                'prayerMethod' => 3,
            ],
        ],
    ];
@endphp

<section
    class="mx-auto mb-10 mt-8 max-w-7xl px-4 md:px-6"
    aria-label="{{ __('app.chad_live.title') }}"
>
    <div class="grid gap-5 lg:grid-cols-2 lg:gap-6">
        @foreach ($liveCountries as $country)
            <div
                x-data="chadLiveInfo(@js(array_merge($liveSharedConfig, ['location' => $country['location']])))"
                x-init="init()"
                class="chad-live-card chad-live-enter flex flex-col overflow-hidden rounded-[18px] border border-slate-100 bg-white shadow-[0_8px_30px_rgba(15,23,42,0.06)] transition-shadow duration-300 hover:shadow-[0_14px_40px_rgba(15,23,42,0.1)]"
                :class="{ 'chad-live-enter--visible': ready || !loading }"
            >
                {{-- Kart başlığı: ülke kimliği ve canlı veri göstergesi --}}
                <div class="border-b border-slate-100 bg-gradient-to-r from-cyan-50/80 via-white to-white px-5 py-5 md:px-6 md:py-6">
                    <div class="flex items-start gap-3.5">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-hidden="true">
                            @if ($country['flag'] === 'chad')
                                <svg class="h-full w-full" viewBox="0 0 36 24" role="img" aria-label="{{ $country['badge'] }}">
                                    <rect width="12" height="24" fill="#002664"/>
                                    <rect x="12" width="12" height="24" fill="#FECB00"/>
                                    <rect x="24" width="12" height="24" fill="#C8102E"/>
                                </svg>
                            @else
                                <svg class="h-full w-full" viewBox="0 0 36 24" role="img" aria-label="{{ $country['badge'] }}">
                                    <rect width="12" height="24" fill="#FFFFFF"/>
                                    <rect x="12" width="24" height="12" fill="#FC3D32"/>
                                    <rect x="12" y="12" width="24" height="12" fill="#007E3A"/>
                                </svg>
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-700">{{ $country['badge'] }}</p>
                                {{-- Canlı veri göstergesi --}}
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700" aria-hidden="true">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    </span>
                                    Live
                                </span>
                            </div>
                            <h2 class="mt-1.5 text-lg font-bold tracking-tight text-slate-900 md:text-xl md:leading-snug">
                                {{ $country['title'] }}
                            </h2>
                            <p class="mt-1.5 text-[13px] leading-relaxed text-slate-600 md:text-sm">
                                {{ $country['subtitle'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-1 flex-col px-5 py-5 md:px-6 md:py-6">
                    {{-- Skeleton --}}
                    <div x-show="loading" x-cloak class="space-y-5" aria-hidden="true">
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach (range(1, 4) as $item)
                                <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                                    <div class="h-10 w-10 animate-pulse rounded-xl bg-slate-200/70"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-2.5 w-16 animate-pulse rounded bg-slate-200/70"></div>
                                        <div class="h-5 w-20 animate-pulse rounded bg-slate-300/70"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="h-12 w-full animate-pulse rounded-full bg-slate-200/70"></div>
                    </div>

                    {{-- İçerik --}}
                    <div x-show="!loading" x-cloak class="flex flex-1 flex-col justify-between gap-5">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            {{-- Hava --}}
                            <div class="chad-live-stat group rounded-2xl border border-slate-100 bg-slate-50/50 p-3.5 transition duration-300 hover:border-cyan-100 hover:bg-cyan-50/40">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-600 text-white shadow-sm shadow-cyan-900/15 transition duration-300 group-hover:scale-105 group-hover:bg-cyan-700">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <circle cx="12" cy="12" r="4"/>
                                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('app.chad_live.weather') }}</p>
                                        <p class="mt-0.5 text-lg font-bold tabular-nums tracking-tight text-slate-900" x-text="weatherDisplay"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Saat --}}
                            <div class="chad-live-stat group rounded-2xl border border-slate-100 bg-slate-50/50 p-3.5 transition duration-300 hover:border-cyan-100 hover:bg-cyan-50/40">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-600 text-white shadow-sm shadow-cyan-900/15 transition duration-300 group-hover:scale-105 group-hover:bg-cyan-700">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M12 7v5l3 2"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('app.chad_live.local_time') }}</p>
                                        <p class="mt-0.5 text-lg font-bold tabular-nums tracking-tight text-slate-900" x-text="localTime" aria-live="polite"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Hicri --}}
                            <div class="chad-live-stat group rounded-2xl border border-slate-100 bg-slate-50/50 p-3.5 transition duration-300 hover:border-cyan-100 hover:bg-cyan-50/40">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-600 text-white shadow-sm shadow-cyan-900/15 transition duration-300 group-hover:scale-105 group-hover:bg-cyan-700">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M21 14.5A8.5 8.5 0 1 1 12 6v2.2"/>
                                            <path d="M12 3v4M9.5 5.5 12 8l2.5-2.5"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('app.chad_live.hijri') }}</p>
                                        <p class="mt-0.5 text-[15px] font-bold leading-snug tracking-tight text-slate-900" x-text="hijri"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Namaz --}}
                            <div class="chad-live-stat group rounded-2xl border border-slate-100 bg-slate-50/50 p-3.5 transition duration-300 hover:border-cyan-100 hover:bg-cyan-50/40">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-600 text-white shadow-sm shadow-cyan-900/15 transition duration-300 group-hover:scale-105 group-hover:bg-cyan-700">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M4 20h16"/>
                                            <path d="M6 20V11l6-4 6 4v9"/>
                                            <path d="M9 20v-5h6v5"/>
                                            <path d="M12 7V4"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('app.chad_live.next_prayer') }}</p>
                                        <p class="mt-0.5 text-[15px] font-bold leading-snug tracking-tight text-slate-900">
                                            <span x-text="prayerName"></span>
                                            <span class="font-semibold text-slate-400" x-show="prayerTime !== '--'">·</span>
                                            <span class="tabular-nums" x-text="prayerTime"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-5">
                            <a
                                href="{{ route('donations') }}"
                                class="btn-primary inline-flex w-full items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-semibold shadow-md shadow-cyan-900/10 transition duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-cyan-900/15"
                            >
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                {{ __('app.chad_live.donate') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
