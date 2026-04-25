<x-layouts.app>
    <x-home-hero-slider :slides="$heroSlidesPayload" />

    @php
        $focusCards = [
            [
                'title' => $siteSettings->home_focus_1_title ?: 'Acil Gıda Desteği',
                'text' => $siteSettings->home_focus_1_text ?: 'Afrika’da açlık riski altındaki ailelere temel gıda kolileri ulaştırıyoruz.',
                'icon' => 'food',
            ],
            [
                'title' => $siteSettings->home_focus_2_title ?: 'Temiz Su Erişimi',
                'text' => $siteSettings->home_focus_2_text ?: 'Susuzlukla mücadele eden bölgelerde temiz suya erişimi destekliyoruz.',
                'icon' => 'water',
            ],
            [
                'title' => $siteSettings->home_focus_3_title ?: 'Beslenme Dayanışması',
                'text' => $siteSettings->home_focus_3_text ?: 'Yemek ve içme suyu odağında düzenli insani yardım çalışmaları yürütüyoruz.',
                'icon' => 'solidarity',
            ],
        ];
        $aboutItems = collect(preg_split('/[\r\n,]+/', (string) ($siteSettings->home_about_items ?? '')))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();
        if (empty($aboutItems)) {
            $aboutItems = [
                'Acil gıda kolisi dağıtımları',
                'Temiz su erişimi ve hijyen desteği',
                'Yerel mutfak ve yemek desteği',
                'Sürdürülebilir beslenme projeleri',
            ];
        }
        $aboutImage = $siteSettings->home_about_image ? asset('storage/' . $siteSettings->home_about_image) : asset('images/default-logo.svg');
    @endphp
    <section class="mx-auto max-w-7xl px-4 pt-6 md:px-6">
        <div class="grid gap-4 md:grid-cols-3">
            @foreach($focusCards as $card)
                <article class="group flex items-start gap-4 rounded-2xl border border-slate-100 border-t-4 border-t-cyan-600 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-cyan-200 hover:shadow-xl hover:shadow-cyan-900/10">
                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-cyan-600 text-white transition duration-300 group-hover:scale-110 group-hover:bg-cyan-700">
                        @if($card['icon'] === 'food')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 2a1 1 0 0 1 1 1v7a3 3 0 0 1-6 0V3a1 1 0 1 1 2 0v3h1V3a1 1 0 1 1 2 0v3h1V3a1 1 0 0 1 1-1Zm8 0a1 1 0 0 1 1 1v18a1 1 0 1 1-2 0v-7h-2a1 1 0 0 1-.97-1.24l1.5-6A5.5 5.5 0 0 1 18 2h-3Z"/></svg>
                        @elseif($card['icon'] === 'water')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2s6 7 6 11a6 6 0 1 1-12 0c0-4 6-11 6-11Zm-2 12a1 1 0 0 0-1 1 3 3 0 0 0 6 0 1 1 0 1 0-2 0 1 1 0 0 1-2 0 1 1 0 0 0-1-1Z"/></svg>
                        @else
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3a5 5 0 0 1 5 5v1h1a4 4 0 0 1 4 4v6a2 2 0 0 1-2 2h-4v-5a2 2 0 1 0-4 0v5H8a2 2 0 0 1-2-2v-6a4 4 0 0 1 4-4h1V8a5 5 0 0 1 1-5Zm0 2a3 3 0 0 0-3 3v1h6V8a3 3 0 0 0-3-3Z"/></svg>
                        @endif
                    </span>
                    <div>
                        <h3 class="text-2xl font-semibold tracking-tight text-slate-900 transition duration-300 group-hover:text-cyan-700">{{ $card['title'] }}</h3>
                        <p class="mt-2 text-lg leading-relaxed text-slate-600 transition duration-300 group-hover:text-slate-700">{{ $card['text'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pt-10 md:px-6 md:pt-12">
        <div class="grid items-center gap-10 lg:grid-cols-[1fr_1.1fr]">
            <div class="relative mx-auto w-full max-w-md lg:max-w-lg">
                <div class="about-float-orb absolute -left-4 -top-4 h-16 w-16 rounded-full bg-cyan-500/20 blur-[1px]"></div>
                <div class="about-float-orb-delayed absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-cyan-300/30 blur-[1px]"></div>
                <div class="relative aspect-square overflow-hidden rounded-3xl border border-slate-200/70 bg-transparent p-2 shadow-xl">
                    <div class="h-full w-full rounded-2xl p-2">
                        <img src="{{ $aboutImage }}" alt="Biz Kimiz" class="h-full w-full object-contain" />
                    </div>
                </div>
            </div>

            <div>
                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                    • {{ $siteSettings->home_about_badge ?: 'Birlikte Kardeşlik Derneği' }}
                </span>
                <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 md:text-5xl">
                    {{ $siteSettings->home_about_title ?: 'Biz Kimiz!' }}
                </h2>
                <p class="mt-4 text-lg font-medium leading-relaxed text-slate-700">
                    {{ $siteSettings->home_about_intro ?: 'Afrika\'da açlık ve susuzlukla mücadele eden kardeşlerimize gıda ve temiz su desteği sağlıyoruz.' }}
                </p>
                <p class="mt-4 text-base leading-relaxed text-slate-600">
                    {{ $siteSettings->home_about_body ?: 'Derneğimiz, Afrika bölgesinde yeme-içme ve temel insani ihtiyaçlar odağında çalışan gönüllü bir dayanışma hareketidir.' }}
                </p>

                <div class="mt-6 grid gap-2 sm:grid-cols-2">
                    @foreach($aboutItems as $item)
                        <div class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-cyan-100 text-cyan-700">✓</span>
                            <span>{{ $item }}</span>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('pages.show', ['slug' => 'hikayemiz']) }}" class="btn-primary mt-7">
                    {{ $siteSettings->home_about_button_text ?: 'Hakkımızda' }}
                </a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl scroll-mt-24 px-4 pt-10 md:px-6 md:pt-12" id="projeler">
        <div class="rounded-[28px] bg-cyan-50/45 p-5 md:p-8">
            <div class="grid items-start gap-6 lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-10">
                <div class="space-y-3">
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[11px] font-semibold tracking-wide text-cyan-700 ring-1 ring-cyan-100">
                        • YKD | {{ $activitySection->badge_text ?: 'Birlikte Kardeşlik Derneği' }}
                    </span>
                    <h2 class="max-w-[280px] text-[44px] font-bold leading-[1.08] tracking-tight text-slate-900">
                        {{ $activitySection->title ?: 'Faaliyetlerimiz' }}
                    </h2>
                </div>
                <p class="max-w-4xl pt-1 text-base leading-relaxed text-slate-600 md:text-lg lg:pt-2">
                    {{ $activitySection->description ?: 'Afrika’da açlık ve susuzlukla mücadele için yürüttüğümüz gıda, temiz su ve acil yardım faaliyetleri.' }}
                </p>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse($projects as $project)
                    <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 ease-out hover:-translate-y-1 hover:border-cyan-200 hover:shadow-xl hover:shadow-cyan-900/10">
                        <a href="{{ route('activities.show', ['slug' => $project->slug]) }}" class="block">
                            <div class="relative">
                                <img
                                    src="{{ $project->cover_image ? asset('storage/' . $project->cover_image) : asset('images/default-logo.svg') }}"
                                    class="h-52 w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                                    alt="{{ $project->title }}"
                                >
                                <span class="absolute bottom-3 right-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-cyan-600 text-white shadow-md">
                                    @if($loop->index % 3 === 0)
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 11a1 1 0 0 1 1-1h5V5a1 1 0 1 1 2 0v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 0 1-1-1Z"/></svg>
                                    @elseif($loop->index % 3 === 1)
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2s6 7 6 11a6 6 0 1 1-12 0c0-4 6-11 6-11Z"/></svg>
                                    @else
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.3 3.02a1 1 0 0 1 1.4 0l2.01 1.93a2 2 0 0 0 1.13.54l2.77.4a1 1 0 0 1 .55 1.71l-2 1.95a2 2 0 0 0-.58 1.76l.47 2.75a1 1 0 0 1-1.45 1.05L13 14.08a2 2 0 0 0-1.86 0l-2.48 1.3a1 1 0 0 1-1.45-1.05l.47-2.75a2 2 0 0 0-.58-1.76l-2-1.95a1 1 0 0 1 .55-1.71l2.77-.4a2 2 0 0 0 1.13-.54l2.01-1.93Z"/></svg>
                                    @endif
                                </span>
                            </div>
                        </a>
                        <div class="p-5 transition-colors duration-300 group-hover:bg-cyan-50/30">
                            <h3 class="text-2xl font-extrabold uppercase leading-tight tracking-tight text-slate-900 transition-colors duration-300 group-hover:text-cyan-700">{{ $project->title }}</h3>
                            <p class="mt-2 text-lg leading-relaxed text-slate-600 transition-colors duration-300 group-hover:text-slate-700">
                                {{ \Illuminate\Support\Str::limit($project->description ?: strip_tags((string) $project->content), 95) }}
                            </p>
                            <a href="{{ route('activities.show', ['slug' => $project->slug]) }}" class="mt-4 inline-flex items-center gap-2 text-base font-semibold text-cyan-700 transition duration-300 group-hover:translate-x-1 hover:text-cyan-800">
                                Devam
                                <span class="text-lg transition-transform duration-300 group-hover:translate-x-0.5">+</span>
                            </a>
                        </div>
                    </article>
                @empty
                    <x-empty-state title="Henüz faaliyet yok" description="Admin panelinden Projeler ve Faaliyetler bölümünden yeni kayıt ekleyebilirsiniz." />
                @endforelse
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pt-12 md:px-6 md:pt-14">
        <div class="rounded-3xl border border-cyan-100 bg-white p-6 shadow-sm md:p-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <article class="group relative rounded-2xl bg-slate-50 p-4 text-center ring-1 ring-transparent transition-all duration-500 ease-out hover:-translate-y-1.5 hover:bg-white hover:shadow-xl hover:shadow-cyan-900/10 hover:ring-cyan-100">
                    <span class="absolute left-3 top-3 inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-white transition-transform duration-500 group-hover:scale-110">01</span>
                    <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-cyan-600 text-white shadow-sm transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-cyan-700 group-hover:shadow-lg group-hover:shadow-cyan-700/30">
                        <svg class="h-7 w-7 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21a1 1 0 0 1-.7-.3l-6-6a5 5 0 1 1 7.1-7.1l.6.6.6-.6a5 5 0 1 1 7.1 7.1l-6 6a1 1 0 0 1-.7.3Z"/></svg>
                    </span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900 transition-colors duration-500 group-hover:text-cyan-700">Umudu Büyüt</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600 transition-colors duration-500 group-hover:text-slate-700">Küçük bir destek, ihtiyaç sahipleri için büyük bir umuda dönüşür.</p>
                </article>
                <article class="group relative rounded-2xl bg-slate-50 p-4 text-center ring-1 ring-transparent transition-all duration-500 ease-out hover:-translate-y-1.5 hover:bg-white hover:shadow-xl hover:shadow-cyan-900/10 hover:ring-cyan-100">
                    <span class="absolute left-3 top-3 inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-white transition-transform duration-500 group-hover:scale-110">02</span>
                    <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-cyan-600 text-white shadow-sm transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-cyan-700 group-hover:shadow-lg group-hover:shadow-cyan-700/30">
                        <svg class="h-7 w-7 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 11a1 1 0 0 1 1-1h5V5a1 1 0 1 1 2 0v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 0 1-1-1Z"/></svg>
                    </span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900 transition-colors duration-500 group-hover:text-cyan-700">Destek Ulaştır</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600 transition-colors duration-500 group-hover:text-slate-700">Bağışın gıda, su ve hijyen desteği olarak sahada hızlıca dağıtılır.</p>
                </article>
                <article class="group relative rounded-2xl bg-slate-50 p-4 text-center ring-1 ring-transparent transition-all duration-500 ease-out hover:-translate-y-1.5 hover:bg-white hover:shadow-xl hover:shadow-cyan-900/10 hover:ring-cyan-100">
                    <span class="absolute left-3 top-3 inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-white transition-transform duration-500 group-hover:scale-110">03</span>
                    <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-cyan-600 text-white shadow-sm transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-cyan-700 group-hover:shadow-lg group-hover:shadow-cyan-700/30">
                        <svg class="h-7 w-7 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2s6 7 6 11a6 6 0 1 1-12 0c0-4 6-11 6-11Z"/></svg>
                    </span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900 transition-colors duration-500 group-hover:text-cyan-700">Hayata Dokun</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600 transition-colors duration-500 group-hover:text-slate-700">Aileler temiz suya ve temel beslenme imkanına daha güvenli şekilde erişir.</p>
                </article>
                <article class="group relative rounded-2xl bg-slate-50 p-4 text-center ring-1 ring-transparent transition-all duration-500 ease-out hover:-translate-y-1.5 hover:bg-white hover:shadow-xl hover:shadow-cyan-900/10 hover:ring-cyan-100">
                    <span class="absolute left-3 top-3 inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-white transition-transform duration-500 group-hover:scale-110">04</span>
                    <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-cyan-600 text-white shadow-sm transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-cyan-700 group-hover:shadow-lg group-hover:shadow-cyan-700/30">
                        <svg class="h-7 w-7 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 12a1 1 0 0 1 1-1h3.28l1.36-4.08a1 1 0 0 1 1.91.03L12.72 11H16a1 1 0 0 1 .8.4l1.45 1.93 1.78-3.56a1 1 0 0 1 1.9.45V18a1 1 0 1 1-2 0v-3.76l-.9 1.8a1 1 0 0 1-1.7.12L15.5 13H12a1 1 0 0 1-.95-.68l-1.1-3.3-.95 2.86A1 1 0 0 1 8 13H4a1 1 0 0 1-1-1Z"/></svg>
                    </span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900 transition-colors duration-500 group-hover:text-cyan-700">Dayanışmayı Güçlendir</h3>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600 transition-colors duration-500 group-hover:text-slate-700">Sürekli destekle bölgede kalıcı iyileşmeye katkı sağlanır.</p>
                </article>
            </div>

            <div class="mt-7 flex justify-center">
                <a href="{{ route('donations') }}" class="group inline-flex min-w-[220px] items-center justify-center gap-2 rounded-full bg-cyan-700 px-8 py-3 text-sm font-semibold text-white shadow-md transition-all duration-500 hover:-translate-y-1 hover:scale-[1.02] hover:bg-cyan-800 hover:shadow-xl hover:shadow-cyan-700/30">
                    Bağış Yap
                    <span class="text-base transition-transform duration-500 group-hover:translate-x-1 group-hover:scale-110">+</span>
                </a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pt-14 md:px-6" id="haberler">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">
                    • Son Haberler
                </span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 md:text-5xl">Haberler ve Duyurular</h2>
            </div>
        </div>
        <div
            x-data="{
                scrollByAmount() {
                    return this.$refs.track ? Math.round(this.$refs.track.clientWidth * 0.9) : 360;
                },
                prev() {
                    this.$refs.track?.scrollBy({ left: -this.scrollByAmount(), behavior: 'smooth' });
                },
                next() {
                    this.$refs.track?.scrollBy({ left: this.scrollByAmount(), behavior: 'smooth' });
                }
            }"
            class="mt-7"
        >
            <div class="mb-3 flex justify-end gap-2">
                <button type="button" @click="prev()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-cyan-200 hover:text-cyan-700" aria-label="Önceki haberler">
                    <span aria-hidden="true">‹</span>
                </button>
                <button type="button" @click="next()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-cyan-200 hover:text-cyan-700" aria-label="Sonraki haberler">
                    <span aria-hidden="true">›</span>
                </button>
            </div>
            <div x-ref="track" class="no-scrollbar flex snap-x snap-mandatory gap-5 overflow-x-auto pb-2">
            @forelse($newsItems as $news)
                <article class="group w-[86%] shrink-0 snap-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-cyan-200 hover:shadow-xl hover:shadow-cyan-900/10 sm:w-[48%] xl:w-[32%]">
                    <div class="relative">
                        <img
                            src="{{ $news->cover_image ? asset('storage/' . $news->cover_image) : asset('images/default-logo.svg') }}"
                            alt="{{ $news->title }}"
                            class="h-52 w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                        >
                        <span class="absolute left-3 top-3 inline-flex rounded-full bg-cyan-700 px-3 py-1 text-xs font-semibold text-white shadow-sm">
                            {{ optional($news->published_at)->format('d M') ?: 'Yeni' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="text-2xl font-bold leading-tight text-slate-900">{{ $news->title }}</h3>
                        <p class="mt-3 text-base leading-relaxed text-slate-600">
                            {{ \Illuminate\Support\Str::limit($news->summary ?: strip_tags((string) $news->content), 130) }}
                        </p>
                        <a href="{{ route('news.show', ['news' => $news->id]) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-cyan-700 transition hover:text-cyan-800">
                            Detaylar
                            <span>+</span>
                        </a>
                    </div>
                </article>
            @empty
                <x-empty-state title="Henüz haber yok" description="Haberler panelinden duyuru ekleyebilirsiniz." />
            @endforelse
            </div>
        </div>
    </section>

</x-layouts.app>
