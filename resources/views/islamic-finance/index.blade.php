<x-layouts.app>
    <x-page-hero :title="__('app.islamic_finance.page_title')" />

    <section class="mx-auto max-w-7xl px-4 py-10 md:px-6 md:py-12">
        <p class="mx-auto mb-10 max-w-3xl text-center text-base leading-relaxed text-slate-600 md:text-lg">
            {{ __('app.islamic_finance.intro') }}
        </p>

        <div class="grid gap-6 md:grid-cols-3">
            <a href="{{ route('zakat.index') }}" class="group rounded-[20px] border border-cyan-100 bg-gradient-to-br from-cyan-50/80 to-white p-6 shadow-sm transition hover:border-cyan-300 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-600 text-white shadow-md">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z"/></svg>
                </div>
                <h2 class="mt-5 text-xl font-bold text-slate-900 group-hover:text-cyan-800">{{ __('app.islamic_finance.zakat_title') }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('app.islamic_finance.zakat_desc') }}</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-cyan-700">{{ __('app.islamic_finance.zakat_cta') }} →</span>
            </a>

            <a href="{{ route('donations') }}" class="group rounded-[20px] border border-slate-100 bg-white p-6 shadow-sm transition hover:border-cyan-200 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500 text-white shadow-md">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>
                <h2 class="mt-5 text-xl font-bold text-slate-900 group-hover:text-cyan-800">{{ __('app.islamic_finance.donate_title') }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('app.islamic_finance.donate_desc') }}</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-cyan-700">{{ __('app.islamic_finance.donate_cta') }} →</span>
            </a>

            <a href="{{ route('activities.index') }}" class="group rounded-[20px] border border-slate-100 bg-white p-6 shadow-sm transition hover:border-cyan-200 hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-md">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5M10.5 13.5 21 3m0 0h-5.25M21 3v5.25"/></svg>
                </div>
                <h2 class="mt-5 text-xl font-bold text-slate-900 group-hover:text-cyan-800">{{ __('app.islamic_finance.activities_title') }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('app.islamic_finance.activities_desc') }}</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-cyan-700">{{ __('app.islamic_finance.activities_cta') }} →</span>
            </a>
        </div>
    </section>
</x-layouts.app>
