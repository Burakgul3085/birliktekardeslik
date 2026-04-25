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
                <article id="haber-{{ $news->id }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="relative">
                        <div class="w-full overflow-hidden bg-white">
                            <img
                                src="{{ $news->cover_image ? asset('storage/' . $news->cover_image) : asset('images/default-logo.svg') }}"
                                alt="{{ $news->title }}"
                                class="mx-auto block h-auto max-h-28 w-auto max-w-full"
                            >
                        </div>
                        <span class="absolute left-3 top-3 inline-flex rounded-full bg-cyan-700 px-3 py-1 text-xs font-semibold text-white shadow-sm">
                            {{ optional($news->published_at)->format('d M') ?: 'Yeni' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h2 class="text-2xl font-bold leading-tight text-slate-900">{{ $news->title }}</h2>
                        <p class="mt-3 text-base leading-relaxed text-slate-600">
                            {{ \Illuminate\Support\Str::limit($news->summary ?: strip_tags((string) $news->content), 170) }}
                        </p>
                        <a href="{{ route('news.show', ['news' => $news->id]) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-cyan-700 transition hover:text-cyan-800">
                            Detaylar
                            <span>+</span>
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
