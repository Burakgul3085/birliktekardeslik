<x-layouts.app>
    <x-page-hero title="Faaliyetlerimiz" />

    <section class="mx-auto max-w-7xl px-4 py-10 md:px-6">
        <p class="mx-auto mb-8 max-w-4xl text-center text-base leading-relaxed text-slate-600 md:text-lg">
            Dernek olarak sürdürdüğümüz tüm faaliyetleri burada görebilir, detayları inceleyebilir ve bağış desteği sunabilirsiniz.
        </p>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse($activities as $activity)
                @php
                    $statusLabel = $activity->status === 'tamamlandi' ? 'Tamamlandı' : 'Devam Ediyor';
                    $statusClass = $activity->status === 'tamamlandi'
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                        : 'border-amber-200 bg-amber-50 text-amber-700';
                @endphp
                <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-cyan-300 hover:shadow-[0_18px_34px_rgba(14,116,144,0.18)]">
                    <a href="{{ route('activities.show', ['slug' => $activity->slug]) }}" class="block p-4">
                        <div class="w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <img
                                src="{{ $activity->cover_image ? asset('storage/' . $activity->cover_image) : asset('images/default-logo.svg') }}"
                                alt="{{ $activity->title }}"
                                class="mx-auto block h-auto max-h-[250px] w-full object-contain transition-transform duration-500 group-hover:scale-[1.02]"
                            >
                        </div>
                    </a>
                    <div class="px-5 pb-5">
                        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                        <h2 class="text-xl font-bold text-slate-900 transition-colors duration-300 group-hover:text-cyan-700">{{ $activity->title }}</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600 transition-colors duration-300 group-hover:text-cyan-900">
                            {{ \Illuminate\Support\Str::limit($activity->description ?: strip_tags((string) $activity->content), 180) }}
                        </p>
                        @if (! is_null($activity->donation_amount))
                            <p class="mt-4 text-lg font-extrabold text-cyan-800">
                                {{ number_format((float) $activity->donation_amount, 2, ',', '.') }} {{ $activity->donation_currency ?: 'TL' }}
                            </p>
                        @endif
                        <div class="mt-5 flex items-center gap-3">
                            <a href="{{ route('donations') }}" class="inline-flex items-center rounded-full bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700">Bağış Yap</a>
                            <a href="{{ route('activities.show', ['slug' => $activity->slug]) }}" class="inline-flex items-center text-sm font-semibold text-cyan-700 transition hover:text-cyan-900">Faaliyet Detayı</a>
                        </div>
                    </div>
                </article>
            @empty
                <x-empty-state title="Henüz faaliyet yok" description="Admin panelinden Projeler ve Faaliyetler bölümünden yeni kayıt ekleyebilirsiniz." />
            @endforelse
        </div>
    </section>
</x-layouts.app>
