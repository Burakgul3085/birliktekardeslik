<x-layouts.app>
    <x-page-hero :title="$news->title" />

    <section class="mx-auto max-w-7xl px-4 pt-10 md:px-6 md:pt-12">
        <div class="rounded-3xl bg-cyan-50/50 p-6 md:p-8">
            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-cyan-700 ring-1 ring-cyan-100">
                • {{ __('app.page.news_detail') }}
            </span>
            <p class="mt-3 text-sm font-medium text-slate-500">
                {{ __('app.page.news_published_at') }} {{ optional($news->published_at)->format('d.m.Y H:i') ?: __('app.page.news_not_specified') }}
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 pt-8 md:px-6">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-6">
                <div class="w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-3">
                <img
                    src="{{ $news->cover_image ? asset('storage/' . $news->cover_image) : asset('images/default-logo.svg') }}"
                    alt="{{ $news->title }}"
                    class="mx-auto block h-auto max-h-[460px] w-full object-contain"
                >
            </div>
            <div class="mt-6">
                <h2 class="text-3xl font-bold text-slate-900">{{ $news->title }}</h2>
                <p class="mt-2 text-sm font-medium text-slate-500">
                    {{ __('app.page.news_published_at') }} {{ optional($news->published_at)->format('d.m.Y H:i') ?: __('app.page.news_not_specified') }}
                </p>
                @if(!empty($news->summary))
                    <p class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-base leading-relaxed text-slate-700 md:text-lg">
                        {{ $news->summary }}
                    </p>
                @endif

                <div class="prose prose-slate mt-6 max-w-none">
                    {!! $news->content !!}
                </div>

                @php
                    $galleryImages = collect($news->gallery_images ?? [])->filter()->values();
                    $galleryVideos = collect($news->gallery_videos ?? [])->filter()->values();
                @endphp
                @if($galleryImages->isNotEmpty() || $galleryVideos->isNotEmpty())
                    <div class="mt-8" x-data="{ previewOpen: false, previewType: null, previewSrc: '' }">
                        <h2 class="text-xl font-bold text-slate-900">{{ __('app.page.news_gallery') }}</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($galleryImages as $image)
                                <a href="{{ asset('storage/' . $image) }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-2">
                                    <div class="w-full overflow-hidden rounded-lg bg-white p-2">
                                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $news->title }} {{ __('app.page.news_gallery_image') }}" class="mx-auto block h-auto max-h-[220px] w-full object-contain">
                                    </div>
                                </a>
                            @endforeach
                            @foreach($galleryVideos as $video)
                                <button
                                    type="button"
                                    class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-2 text-left transition hover:border-cyan-300 hover:shadow-md"
                                    @click="previewOpen = true; previewType = 'video'; previewSrc = '{{ asset('storage/' . $video) }}'"
                                >
                                    <video controls class="h-auto max-h-[220px] w-full rounded-lg bg-black/90 object-contain">
                                        <source src="{{ asset('storage/' . $video) }}">
                                        {{ __('app.page.video_not_supported') }}
                                    </video>
                                </button>
                            @endforeach
                        </div>

                        <div
                            x-show="previewOpen"
                            x-cloak
                            class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/75 p-4"
                            @click.self="previewOpen = false; previewSrc = ''; previewType = null"
                            @keydown.escape.window="previewOpen = false; previewSrc = ''; previewType = null"
                        >
                            <div class="relative w-full max-w-5xl rounded-2xl border border-slate-700 bg-slate-900 p-3 shadow-2xl">
                                <button
                                    type="button"
                                    class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25"
                                    @click="previewOpen = false; previewSrc = ''; previewType = null"
                                    aria-label="{{ __('app.page.close_preview') }}"
                                >✕</button>

                                <template x-if="previewType === 'video' && previewSrc">
                                    <video controls autoplay class="h-auto max-h-[78vh] w-full rounded-xl bg-black object-contain">
                                        <source :src="previewSrc">
                                    </video>
                                </template>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            </article>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('app.page.other_news') }}</h3>
                        <a href="{{ route('news.index') }}" class="text-sm font-semibold text-cyan-700 transition hover:text-cyan-800">{{ __('app.page.news_view_all_short') }}</a>
                    </div>
                    <div class="mt-4 space-y-3">
                @forelse($relatedNews as $item)
                    <a href="{{ route('news.show', ['news' => $item->id]) }}" class="block rounded-xl border border-slate-100 px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-cyan-200 hover:bg-cyan-50/40 hover:text-cyan-800">
                        {{ $item->title }}
                    </a>
                @empty
                    <p class="text-sm text-slate-500">{{ __('app.page.no_other_news') }}</p>
                @endforelse
                    </div>
                </div>
            </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
