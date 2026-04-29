<x-layouts.app>
    <x-page-hero :title="__('app.page.news_page_title')" />

    <section class="mx-auto max-w-7xl px-4 pt-10 md:px-6 md:pt-12">
        <div class="rounded-3xl bg-cyan-50/50 p-6 md:p-8">
            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-cyan-700 ring-1 ring-cyan-100">
                • {{ __('app.page.news_badge') }}
            </span>
            <p class="mt-3 max-w-4xl text-base leading-relaxed text-slate-600 md:text-lg">
                {{ __('app.page.news_intro') }}
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 pt-8 md:px-6">

        {{-- Filtre Alanı --}}
        <form method="GET" action="{{ route('news.index') }}" class="mb-8">
            <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:gap-4">

                {{-- Arama --}}
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ $filters['q'] }}"
                        placeholder="{{ __('app.page.news_search') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-4 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100"
                    >
                </div>

                {{-- Sıralama --}}
                <div class="flex gap-2">
                    @foreach (['newest' => __('app.page.news_sort_newest'), 'oldest' => __('app.page.news_sort_oldest')] as $val => $label)
                        <button
                            type="submit"
                            name="sort"
                            value="{{ $val }}"
                            class="rounded-full border px-4 py-2 text-xs font-semibold transition
                                {{ $filters['sort'] === $val
                                    ? 'border-cyan-500 bg-cyan-600 text-white shadow-sm'
                                    : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700' }}"
                        >{{ $label }}</button>
                    @endforeach
                </div>

                {{-- Filtreyi Temizle --}}
                @if($filters['q'] || $filters['sort'] !== 'newest')
                    <a
                        href="{{ route('news.index') }}"
                        class="flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 hover:text-rose-700"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('app.page.activities_clear') }}
                    </a>
                @endif
            </div>

            {{-- Sonuç sayısı --}}
            <p class="mt-3 text-sm text-slate-500">
                <span class="font-semibold text-cyan-700">{{ $newsItems->total() }}</span> {{ __('app.page.news_count') }}
            </p>
        </form>

        {{-- Haber Grid --}}
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($newsItems as $news)
                <article id="haber-{{ $news->id }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-cyan-300 hover:shadow-[0_18px_34px_rgba(14,116,144,0.18)]">
                    <a href="{{ route('news.show', ['news' => $news->id]) }}" class="block p-4">
                        <div class="relative w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <img
                                src="{{ $news->cover_image ? asset('storage/' . $news->cover_image) : asset('images/default-logo.svg') }}"
                                alt="{{ $news->title }}"
                                class="mx-auto block h-auto max-h-[250px] w-full object-contain transition-transform duration-500 group-hover:scale-[1.02]"
                            >
                            <span class="absolute left-3 top-3 inline-flex rounded-full bg-cyan-700 px-3 py-1 text-xs font-semibold text-white shadow-sm">
                                {{ optional($news->published_at)->format('d M') ?: 'Yeni' }}
                            </span>
                        </div>
                    </a>
                    <div class="px-5 pb-5">
                        <h2 class="text-xl font-bold text-slate-900 transition-colors duration-300 group-hover:text-cyan-700">{{ $news->title }}</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600 transition-colors duration-300 group-hover:text-cyan-900">
                            {{ \Illuminate\Support\Str::limit($news->summary ?: strip_tags((string) $news->content), 170) }}
                        </p>
                        <a href="{{ route('news.show', ['news' => $news->id]) }}" class="mt-5 inline-flex items-center text-sm font-semibold text-cyan-700 transition hover:text-cyan-900">
                            {{ __('app.page.news_detail') }}
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-3 py-16 text-center">
                    <svg class="mx-auto mb-4 h-14 w-14 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-lg font-semibold text-slate-500">{{ __('app.page.news_empty') }}</p>
                    <a href="{{ route('news.index') }}" class="mt-4 inline-flex items-center rounded-full bg-cyan-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700">{{ __('app.page.news_view_all') }}</a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $newsItems->links() }}
        </div>
    </section>
</x-layouts.app>
