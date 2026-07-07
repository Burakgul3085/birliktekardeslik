@php
    $chadLiveConfig = [
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
@endphp

<section
    class="mx-auto mb-10 mt-8 max-w-7xl px-4 md:px-6"
    aria-label="{{ __('app.chad_live.title') }}"
>
    <div
        x-data="chadLiveInfo(@js($chadLiveConfig))"
        x-init="init()"
        class="chad-live-enter rounded-[18px] border border-slate-100 bg-white p-8 shadow-sm"
        :class="{ 'chad-live-enter--visible': ready || !loading }"
    >
        {{-- Başlık --}}
        <div class="text-center md:text-start">
            <h2 class="text-xl font-bold tracking-tight text-slate-900 md:text-2xl">
                {{ __('app.chad_live.title') }}
            </h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-600 md:text-base">
                {{ __('app.chad_live.subtitle') }}
            </p>
        </div>

        {{-- Skeleton --}}
        <div x-show="loading" x-cloak class="mt-8 space-y-6" aria-hidden="true">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (range(1, 4) as $item)
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 animate-pulse rounded-full bg-slate-100"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3 w-20 animate-pulse rounded bg-slate-100"></div>
                            <div class="h-5 w-16 animate-pulse rounded bg-slate-200"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mx-auto h-11 w-full max-w-xs animate-pulse rounded-xl bg-slate-100"></div>
        </div>

        {{-- İçerik --}}
        <div x-show="!loading" x-cloak class="mt-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-0">
                {{-- Hava --}}
                <div class="chad-live-stat group flex items-center gap-3 lg:px-5 lg:first:ps-0">
                    <span class="text-2xl" aria-hidden="true">☀️</span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('app.chad_live.weather') }}</p>
                        <p class="mt-0.5 text-lg font-semibold text-slate-900" x-text="weatherDisplay"></p>
                    </div>
                </div>

                <div class="hidden h-auto w-px self-stretch bg-slate-200 lg:block" aria-hidden="true"></div>

                {{-- Saat --}}
                <div class="chad-live-stat group flex items-center gap-3 lg:px-5">
                    <span class="text-2xl" aria-hidden="true">🕒</span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('app.chad_live.local_time') }}</p>
                        <p class="mt-0.5 text-lg font-semibold text-slate-900" x-text="localTime" aria-live="polite"></p>
                    </div>
                </div>

                <div class="hidden h-auto w-px self-stretch bg-slate-200 lg:block" aria-hidden="true"></div>

                {{-- Hicri --}}
                <div class="chad-live-stat group flex items-center gap-3 lg:px-5">
                    <span class="text-2xl" aria-hidden="true">🌙</span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('app.chad_live.hijri') }}</p>
                        <p class="mt-0.5 text-lg font-semibold text-slate-900" x-text="hijri"></p>
                    </div>
                </div>

                <div class="hidden h-auto w-px self-stretch bg-slate-200 lg:block" aria-hidden="true"></div>

                {{-- Namaz --}}
                <div class="chad-live-stat group flex items-center gap-3 lg:px-5 lg:last:pe-0">
                    <span class="text-2xl" aria-hidden="true">🕌</span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('app.chad_live.next_prayer') }}</p>
                        <p class="mt-0.5 text-lg font-semibold text-slate-900">
                            <span x-text="prayerName"></span>
                            <span class="text-slate-500" x-show="prayerTime !== '--'">·</span>
                            <span x-text="prayerTime"></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-center">
                <a href="{{ route('donations') }}" class="btn-primary w-full gap-2 sm:w-auto sm:min-w-[220px]">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    {{ __('app.chad_live.donate') }}
                </a>
            </div>
        </div>
    </div>
</section>
