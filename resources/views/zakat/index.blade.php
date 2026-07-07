@php
    $zakatConfig = [
        'locale' => app()->getLocale(),
        'settings' => $settings,
        'donateUrl' => route('donations'),
        'activitiesUrl' => route('activities.index'),
        'labels' => [
            'live_prices' => __('app.zakat.live_prices'),
            'prices_loading' => __('app.zakat.prices_loading'),
            'prices_error' => __('app.zakat.prices_error'),
            'source_forex' => __('app.zakat.source_forex'),
            'source_metals' => __('app.zakat.source_metals'),
            'source_note' => __('app.zakat.source_note'),
            'last_update' => __('app.zakat.last_update'),
            'nisap_label' => __('app.zakat.nisap_label'),
            'assets_title' => __('app.zakat.assets_title'),
            'gold_section' => __('app.zakat.gold_section'),
            'money_section' => __('app.zakat.money_section'),
            'other_section' => __('app.zakat.other_section'),
            'debts_section' => __('app.zakat.debts_section'),
            'gram' => __('app.zakat.gram'),
            'summary_title' => __('app.zakat.summary_title'),
            'total_assets' => __('app.zakat.total_assets'),
            'net_wealth' => __('app.zakat.net_wealth'),
            'zakat_amount' => __('app.zakat.zakat_amount'),
            'below_nisap' => __('app.zakat.below_nisap'),
            'hawl_label' => __('app.zakat.hawl_label'),
            'donate' => __('app.zakat.donate'),
            'activities' => __('app.zakat.activities'),
            'reset' => __('app.zakat.reset'),
            'legal_title' => __('app.zakat.legal_title'),
            'legal_text' => __('app.zakat.legal_text'),
        ],
    ];
@endphp

<x-layouts.app>
    <x-page-hero :title="__('app.zakat.page_title')" />

    <section class="mx-auto max-w-7xl px-4 py-10 md:px-6 md:py-12">
        <p class="mx-auto mb-8 max-w-3xl text-center text-base leading-relaxed text-slate-600 md:text-lg">
            {{ __('app.zakat.intro') }}
        </p>

        <div
            x-data="zakatCalculator(@js($zakatConfig))"
            x-init="init()"
            class="grid gap-8 lg:grid-cols-[320px_minmax(0,1fr)]"
        >
            {{-- Canlı fiyat paneli --}}
            <aside class="lg:sticky lg:top-28 lg:self-start">
                <div class="overflow-hidden rounded-[18px] border border-slate-100 bg-white shadow-[0_8px_30px_rgba(15,23,42,0.06)]">
                    <div class="border-b border-slate-100 bg-gradient-to-r from-cyan-50/80 to-white px-5 py-4">
                        <h2 class="text-sm font-bold uppercase tracking-[0.14em] text-cyan-800">{{ __('app.zakat.live_prices') }}</h2>
                    </div>

                    <div class="space-y-4 p-5" x-show="pricesLoading" x-cloak>
                        @foreach (range(1, 6) as $line)
                            <div class="h-10 animate-pulse rounded-lg bg-slate-100"></div>
                        @endforeach
                    </div>

                    <div class="space-y-4 p-5" x-show="!pricesLoading" x-cloak>
                        <template x-if="pricesError">
                            <p class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">{{ __('app.zakat.prices_error') }}</p>
                        </template>

                        <div class="space-y-3 text-sm">
                            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('app.zakat.gold_24') }}</p>
                                <p class="mt-1 text-lg font-bold text-slate-900" x-text="prices.gold_24_per_gram ? money(prices.gold_24_per_gram) : '—'"></p>
                                <p class="mt-1 text-[11px] leading-relaxed text-slate-500">
                                    <span class="font-medium text-slate-600">{{ __('app.zakat.source_label') }}:</span>
                                    <span x-text="labels.source_metals"></span>
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    {{ __('app.zakat.last_update') }}:
                                    <span x-text="formatFetchedAt(sources?.metals?.fetched_at)"></span>
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ __('app.zakat.silver') }}</p>
                                <p class="mt-1 text-lg font-bold text-slate-900" x-text="prices.silver_per_gram ? money(prices.silver_per_gram) : '—'"></p>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    <span class="font-medium text-slate-600">{{ __('app.zakat.source_label') }}:</span>
                                    <span x-text="labels.source_metals"></span>
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">USD / EUR / GBP</p>
                                <p class="mt-1 font-semibold text-slate-900">
                                    <span x-text="prices.usd_try ? money(prices.usd_try) : '—'"></span>
                                    <span class="text-slate-300"> · </span>
                                    <span x-text="prices.eur_try ? money(prices.eur_try) : '—'"></span>
                                    <span class="text-slate-300"> · </span>
                                    <span x-text="prices.gbp_try ? money(prices.gbp_try) : '—'"></span>
                                </p>
                                <p class="mt-1 text-[11px] leading-relaxed text-slate-500">
                                    <span class="font-medium text-slate-600">{{ __('app.zakat.source_label') }}:</span>
                                    <span x-text="labels.source_forex"></span>
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    {{ __('app.zakat.last_update') }}:
                                    <span x-text="formatFetchedAt(sources?.forex?.fetched_at)"></span>
                                </p>
                            </div>

                            <div class="rounded-xl border border-cyan-100 bg-cyan-50/60 p-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-cyan-700">{{ __('app.zakat.nisap_label') }}</p>
                                <p class="mt-1 text-lg font-bold text-cyan-900" x-text="prices.nisap_threshold_try ? money(prices.nisap_threshold_try) : '—'"></p>
                                <p class="text-[11px] text-cyan-800/80">{{ $settings['nisap_grams'] }} gr ({{ $settings['nisap_karat'] }} ayar)</p>
                            </div>
                        </div>

                        <p class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-[11px] leading-relaxed text-slate-500">
                            {{ __('app.zakat.source_note') }}
                        </p>
                    </div>
                </div>
            </aside>

            {{-- Hesaplama formu --}}
            <div class="space-y-6">
                <form @input="saveForm()" class="space-y-6">
                    <div class="rounded-[18px] border border-slate-100 bg-white p-6 shadow-sm md:p-8">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('app.zakat.gold_section') }}</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            @foreach ([24, 22, 18, 14] as $karat)
                                <label class="block">
                                    <span class="text-sm font-medium text-slate-700">{{ __('app.zakat.gold_karat', ['karat' => $karat]) }}</span>
                                    <div class="relative mt-1.5">
                                        <input type="number" min="0" step="0.01" x-model="form.gold{{ $karat }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                                        <span class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-xs text-slate-400">{{ __('app.zakat.gram') }}</span>
                                    </div>
                                </label>
                            @endforeach
                            <label class="block sm:col-span-2">
                                <span class="text-sm font-medium text-slate-700">{{ __('app.zakat.silver') }}</span>
                                <div class="relative mt-1.5">
                                    <input type="number" min="0" step="0.01" x-model="form.silver" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                                    <span class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-xs text-slate-400">{{ __('app.zakat.gram') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="rounded-[18px] border border-slate-100 bg-white p-6 shadow-sm md:p-8">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('app.zakat.money_section') }}</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">{{ __('app.zakat.cash') }}</span>
                                <input type="number" min="0" step="0.01" x-model="form.cash" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">{{ __('app.zakat.bank') }}</span>
                                <input type="number" min="0" step="0.01" x-model="form.bank" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">USD</span>
                                <input type="number" min="0" step="0.01" x-model="form.usd" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">EUR</span>
                                <input type="number" min="0" step="0.01" x-model="form.eur" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">GBP</span>
                                <input type="number" min="0" step="0.01" x-model="form.gbp" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                            </label>
                        </div>
                    </div>

                    <div class="rounded-[18px] border border-slate-100 bg-white p-6 shadow-sm md:p-8">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('app.zakat.other_section') }}</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">{{ __('app.zakat.trade') }}</span>
                                <input type="number" min="0" step="0.01" x-model="form.trade" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">{{ __('app.zakat.receivables') }}</span>
                                <input type="number" min="0" step="0.01" x-model="form.receivables" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                            </label>
                        </div>
                    </div>

                    <div class="rounded-[18px] border border-slate-100 bg-white p-6 shadow-sm md:p-8">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('app.zakat.debts_section') }}</h2>
                        <label class="mt-4 block">
                            <span class="text-sm font-medium text-slate-700">{{ __('app.zakat.debts') }}</span>
                            <input type="number" min="0" step="0.01" x-model="form.debts" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100" placeholder="0">
                        </label>
                    </div>

                    <label class="flex items-start gap-3 rounded-[18px] border border-slate-200 bg-slate-50/80 p-4">
                        <input type="checkbox" x-model="form.hawl" class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="text-sm leading-relaxed text-slate-700">{{ __('app.zakat.hawl_label') }}</span>
                    </label>
                </form>

                {{-- Özet --}}
                <div class="rounded-[18px] border border-cyan-100 bg-gradient-to-br from-cyan-50/80 to-white p-6 shadow-sm md:p-8">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('app.zakat.summary_title') }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="text-slate-600">{{ __('app.zakat.total_assets') }}</dt>
                            <dd class="font-bold text-slate-900" x-text="money(result.totalAssets)"></dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="text-slate-600">{{ __('app.zakat.net_wealth') }}</dt>
                            <dd class="font-bold text-slate-900" x-text="money(result.netWealth)"></dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-600">{{ __('app.zakat.zakat_amount') }}</dt>
                            <dd class="text-2xl font-extrabold text-cyan-700" x-text="money(result.zakatAmount)"></dd>
                        </div>
                    </dl>
                    <p class="mt-3 text-xs text-slate-500" x-show="!result.meetsNisap && result.totalAssets > 0" x-cloak>{{ __('app.zakat.below_nisap') }}</p>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-center">
                        <a
                            :href="donateLink"
                            class="btn-primary inline-flex w-full items-center justify-center gap-2 rounded-full px-8 py-3 text-sm font-semibold shadow-md sm:w-auto"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            {{ __('app.zakat.donate') }}
                        </a>
                        <a
                            href="{{ route('activities.index') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-cyan-200 bg-white px-8 py-3 text-sm font-semibold text-cyan-800 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50 sm:w-auto"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5M10.5 13.5 21 3m0 0h-5.25M21 3v5.25"/></svg>
                            {{ __('app.zakat.activities') }}
                        </a>
                        <button type="button" @click="resetForm()" class="inline-flex w-full items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-50 sm:w-auto">
                            {{ __('app.zakat.reset') }}
                        </button>
                    </div>
                </div>

                <div class="rounded-[18px] border border-amber-100 bg-amber-50/50 p-5 text-sm leading-relaxed text-amber-950">
                    <p class="font-semibold">{{ __('app.zakat.legal_title') }}</p>
                    <p class="mt-2">{{ __('app.zakat.legal_text') }}</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
