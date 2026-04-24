<x-layouts.app>
    <section class="bg-cyan-900/95 py-12 text-white">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <h1 class="text-4xl font-bold tracking-tight md:text-5xl">Faaliyetlerimiz</h1>
            <p class="mt-4 max-w-4xl text-base leading-relaxed text-cyan-100 md:text-lg">
                Afrika’da açlık ve susuzlukla mücadele eden kardeşlerimize yönelik yürüttüğümüz gıda ve temiz su çalışmalarımız.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 md:px-6">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse($activities as $activity)
                <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <a href="{{ route('activities.show', ['slug' => $activity->slug]) }}" class="block">
                        <img
                            src="{{ $activity->cover_image ? asset('storage/' . $activity->cover_image) : asset('images/default-logo.svg') }}"
                            alt="{{ $activity->title }}"
                            class="h-56 w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                        >
                    </a>
                    <div class="p-5">
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-slate-900">{{ $activity->title }}</h2>
                        <p class="mt-3 text-lg leading-relaxed text-slate-600">
                            {{ \Illuminate\Support\Str::limit($activity->description ?: strip_tags((string) $activity->content), 180) }}
                        </p>
                        <a href="{{ route('activities.show', ['slug' => $activity->slug]) }}" class="mt-5 inline-flex items-center gap-2 text-base font-semibold text-cyan-700 transition hover:text-cyan-800">
                            Devam
                            <span class="text-lg">+</span>
                        </a>
                    </div>
                </article>
            @empty
                <x-empty-state title="Henüz faaliyet yok" description="Admin panelinden Projeler ve Faaliyetler bölümünden yeni kayıt ekleyebilirsiniz." />
            @endforelse
        </div>
    </section>
</x-layouts.app>
