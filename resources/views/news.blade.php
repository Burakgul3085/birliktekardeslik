<x-layouts.app>
    <x-page-hero title="Haberler ve Duyurular" />

    <section class="mx-auto max-w-7xl px-4 pt-10 md:px-6 md:pt-12">
        <div class="rounded-3xl bg-cyan-50/50 p-6 md:p-8">
            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-semibold text-cyan-700 ring-1 ring-cyan-100">
                • Son Haberler
            </span>
            <p class="mt-3 max-w-4xl text-base leading-relaxed text-slate-600 md:text-lg">
                Derneğimizin saha faaliyetlerinden güncel gelişmeleri, duyuruları ve bilgilendirmeleri bu alandan takip edebilirsiniz.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 pt-8 md:px-6">
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
                            Haber Detayı
                        </a>
                    </div>
                </article>
            @empty
                <x-empty-state title="Henüz haber yok" description="Admin panelinden Haberler ve Duyurular bölümünden yeni haber ekleyebilirsiniz." />
            @endforelse
        </div>

        <div class="mt-8">
            {{ $newsItems->links() }}
        </div>
    </section>
</x-layouts.app>
