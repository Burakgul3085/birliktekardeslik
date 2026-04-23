<x-layouts.app>
    <section class="mx-auto max-w-7xl px-4 pt-6 md:px-6" x-data="{ idx: 0 }">
        @if($heroSlides->isNotEmpty())
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-cyan-700 to-brand-500 p-8 text-white md:p-12">
                <template x-for="(slide, index) in {{ $heroSlides->values()->toJson() }}" :key="index">
                    <div x-show="idx === index" class="grid items-center gap-8 md:grid-cols-2">
                        <div>
                            <h1 class="text-3xl font-bold md:text-4xl" x-text="slide.headline"></h1>
                            <p class="mt-4 text-cyan-50" x-text="slide.subtext"></p>
                            <a x-show="slide.button_text" :href="slide.button_url" class="btn-primary mt-6" x-text="slide.button_text"></a>
                        </div>
                        <img :src="slide.image_path ? '/storage/' + slide.image_path : '/images/default-logo.svg'" alt="Hero" class="h-64 w-full rounded-2xl object-cover shadow-xl">
                    </div>
                </template>
                @if($heroSlides->count() > 1)
                    <div class="mt-6 flex gap-2">
                        @foreach($heroSlides as $index => $slide)
                            <button @click="idx={{ $index }}" class="h-2 w-8 rounded-full bg-white/50"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <x-empty-state title="Hero alanı hazır" description="Admin panelinden hero slide ekleyerek anasayfayı canlandırabilirsiniz." />
        @endif
    </section>

    <section class="mx-auto max-w-7xl px-4 pt-14 md:px-6" id="projeler">
        <h2 class="section-title">Projeler ve Faaliyetler</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($projects as $project)
                <article class="card-ui">
                    <img src="{{ $project->cover_image ? asset('storage/' . $project->cover_image) : asset('images/default-logo.svg') }}" class="h-44 w-full rounded-xl object-cover" alt="{{ $project->title }}">
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ $project->title }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $project->description }}</p>
                    <span class="mt-3 inline-flex rounded-full bg-cyan-50 px-3 py-1 text-xs font-medium text-cyan-700">{{ $project->status === 'devam-ediyor' ? 'Devam Ediyor' : 'Tamamlandı' }}</span>
                </article>
            @empty
                <x-empty-state title="Henüz proje yok" description="Projeler panelinden yeni faaliyetler ekleyebilirsiniz." />
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pt-14 md:px-6" id="haberler">
        <h2 class="section-title">Haberler ve Duyurular</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($newsItems as $news)
                <article class="card-ui">
                    <p class="text-xs text-slate-500">{{ optional($news->published_at)->format('d.m.Y') }}</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $news->title }}</h3>
                    <div class="prose prose-sm mt-3 max-w-none text-slate-600">{!! \Illuminate\Support\Str::limit(strip_tags($news->content), 180) !!}</div>
                </article>
            @empty
                <x-empty-state title="Henüz haber yok" description="Haberler panelinden duyuru ekleyebilirsiniz." />
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 pt-14 md:px-6" id="bagis-hesapları">
        <h2 class="section-title">Bağış Hesapları</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @forelse($bankAccounts as $account)
                <article class="card-ui" x-data="{ copied: false }">
                    <p class="text-sm font-medium text-slate-500">{{ $account->bank_name }} ({{ $account->currency }})</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $account->recipient_name }}</h3>
                    <p class="mt-1 font-mono text-sm text-slate-700">{{ $account->iban }}</p>
                    <button @click="navigator.clipboard.writeText('{{ $account->iban }}'); copied = true; setTimeout(() => copied = false, 2000)" class="btn-primary mt-4 w-full">IBAN Kopyala</button>
                    <p x-show="copied" class="mt-2 text-center text-xs text-emerald-600">Kopyalandı</p>
                </article>
            @empty
                <x-empty-state title="Hesap bilgisi yok" description="Banka hesapları panelinden aktif hesap eklenince burada otomatik gösterilir." />
            @endforelse
        </div>
    </section>
</x-layouts.app>
