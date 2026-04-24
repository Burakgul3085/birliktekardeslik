<x-layouts.app>
    <section class="mx-auto max-w-7xl px-4 pt-10 md:px-6 md:pt-12">
        <div class="rounded-3xl bg-cyan-50/50 p-6 md:p-8">
            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-cyan-700 ring-1 ring-cyan-100">
                • Haber Detayı
            </span>
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 md:text-5xl">{{ $news->title }}</h1>
            <p class="mt-3 text-sm font-medium text-slate-500">
                Yayın Tarihi: {{ optional($news->published_at)->format('d.m.Y H:i') ?: 'Belirtilmedi' }}
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 pt-8 md:px-6">
        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <img
                src="{{ $news->cover_image ? asset('storage/' . $news->cover_image) : asset('images/default-logo.svg') }}"
                alt="{{ $news->title }}"
                class="h-[260px] w-full object-cover md:h-[420px]"
            >
            <div class="p-6 md:p-8">
                @if(!empty($news->summary))
                    <p class="rounded-2xl bg-slate-50 p-4 text-base leading-relaxed text-slate-700 md:text-lg">
                        {{ $news->summary }}
                    </p>
                @endif

                <div class="prose prose-slate mt-6 max-w-none">
                    {!! $news->content !!}
                </div>

                @if(!empty($news->gallery_images))
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Haber Galerisi</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($news->gallery_images as $image)
                                <a href="{{ asset('storage/' . $image) }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $news->title }} galeri görseli" class="h-52 w-full object-cover transition duration-300 hover:scale-[1.03]">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </article>

        <section class="mt-10">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold tracking-tight text-slate-900">Diğer Haberler</h3>
                <a href="{{ route('news.index') }}" class="text-sm font-semibold text-cyan-700 transition hover:text-cyan-800">Tümünü Gör</a>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @forelse($relatedNews as $item)
                    <a href="{{ route('news.show', ['news' => $item->id]) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-cyan-200 hover:shadow-lg">
                        <img src="{{ $item->cover_image ? asset('storage/' . $item->cover_image) : asset('images/default-logo.svg') }}" alt="{{ $item->title }}" class="h-40 w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                        <div class="p-4">
                            <p class="text-xs text-slate-500">{{ optional($item->published_at)->format('d.m.Y') }}</p>
                            <h4 class="mt-1 text-lg font-bold leading-tight text-slate-900">{{ $item->title }}</h4>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">Henüz başka haber bulunmuyor.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.app>
