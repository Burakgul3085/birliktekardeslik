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
                <div class="relative aspect-square overflow-hidden rounded-3xl border border-white/40 bg-white/10 p-2 shadow-xl backdrop-blur-md">
                    <div class="h-full w-full rounded-2xl bg-white/5 p-2 backdrop-blur-sm">
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
            <div class="grid items-center gap-6 lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-10">
                <div class="space-y-2">
                    <h2 class="max-w-[280px] text-[44px] font-bold leading-[1.08] tracking-tight text-slate-900">
                        {{ $activitySection->title ?: 'Faaliyetlerimiz' }}
                    </h2>
                </div>
                <p class="max-w-3xl text-base leading-relaxed text-slate-600 md:text-lg">
                    {{ $activitySection->description ?: 'Afrika’da açlık ve susuzlukla mücadele için yürüttüğümüz gıda, temiz su ve acil yardım faaliyetleri.' }}
                </p>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse($projects as $project)
                    @php
                        $statusLabel = $project->status === 'tamamlandi' ? 'Tamamlandı' : 'Devam Ediyor';
                        $statusClass = $project->status === 'tamamlandi'
                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                            : 'border-amber-200 bg-amber-50 text-amber-700';
                    @endphp
                    <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-cyan-300 hover:shadow-[0_18px_34px_rgba(14,116,144,0.18)]">
                        <a href="{{ route('activities.show', ['slug' => $project->slug]) }}" class="block p-4">
                            <div class="w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <img
                                    src="{{ $project->cover_image ? asset('storage/' . $project->cover_image) : asset('images/default-logo.svg') }}"
                                    class="mx-auto block h-auto max-h-[250px] w-full object-contain transition-transform duration-500 group-hover:scale-[1.02]"
                                    alt="{{ $project->title }}"
                                >
                            </div>
                        </a>
                        <div class="px-5 pb-5">
                            <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                            <h3 class="text-xl font-bold text-slate-900 transition-colors duration-300 group-hover:text-cyan-700">{{ $project->title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600 transition-colors duration-300 group-hover:text-cyan-900">
                                {{ \Illuminate\Support\Str::limit($project->description ?: strip_tags((string) $project->content), 170) }}
                            </p>
                            @if (! is_null($project->donation_amount))
                                <p class="mt-4 text-lg font-extrabold text-cyan-800">
                                    {{ number_format((float) $project->donation_amount, 2, ',', '.') }} {{ $project->donation_currency ?: 'TL' }}
                                </p>
                            @endif
                            <div class="mt-5 flex items-center gap-3">
                                <a href="{{ route('donations') }}" class="inline-flex items-center rounded-full bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700">Bağış Yap</a>
                                <a href="{{ route('activities.show', ['slug' => $project->slug]) }}" class="inline-flex items-center text-sm font-semibold text-cyan-700 transition hover:text-cyan-900">Faaliyet Detayı</a>
                            </div>
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
                <article class="group w-[86%] shrink-0 snap-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-cyan-300 hover:shadow-[0_18px_34px_rgba(14,116,144,0.18)] sm:w-[48%] xl:w-[32%]">
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
                        <h3 class="text-xl font-bold text-slate-900 transition-colors duration-300 group-hover:text-cyan-700">{{ $news->title }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600 transition-colors duration-300 group-hover:text-cyan-900">
                            {{ \Illuminate\Support\Str::limit($news->summary ?: strip_tags((string) $news->content), 170) }}
                        </p>
                        <a href="{{ route('news.show', ['news' => $news->id]) }}" class="mt-5 inline-flex items-center text-sm font-semibold text-cyan-700 transition hover:text-cyan-900">
                            Haber Detayı
                        </a>
                    </div>
                </article>
            @empty
                <x-empty-state title="Henüz haber yok" description="Haberler panelinden duyuru ekleyebilirsiniz." />
            @endforelse
            </div>
        </div>
    </section>


    @php
        $waNumber = '';
        if (!empty($siteSettings->whatsapp_url)) {
            $waNumber = preg_replace('/[^0-9]/', '', $siteSettings->whatsapp_url);
        } elseif (!empty($siteSettings->phone)) {
            $waNumber = preg_replace('/[^0-9]/', '', $siteSettings->phone);
            if (str_starts_with($waNumber, '0')) {
                $waNumber = '90' . substr($waNumber, 1);
            }
        }
    @endphp

    @if($waNumber)
    <div
        x-data="{ visible: false, pulse: true }"
        x-init="setTimeout(() => visible = true, 1000)"
        class="fixed bottom-6 right-6 z-50"
    >
        {{-- Dış halka ping animasyonu --}}
        <span
            x-show="visible"
            class="absolute inset-0 rounded-full bg-green-400 opacity-75"
            style="animation: wa-ping 1.8s cubic-bezier(0,0,0.2,1) infinite;"
        ></span>

        {{-- Ana buton --}}
        <a
            x-show="visible"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-50"
            x-transition:enter-end="opacity-100 scale-100"
            href="https://wa.me/{{ $waNumber }}"
            target="_blank"
            rel="noopener noreferrer"
            title="WhatsApp ile iletişime geçin"
            aria-label="WhatsApp"
            class="relative flex h-14 w-14 items-center justify-center rounded-full bg-green-500 shadow-xl ring-2 ring-white transition duration-300 hover:scale-110 hover:bg-green-600 hover:shadow-green-400/50"
            style="box-shadow: 0 0 20px rgba(34,197,94,0.5);"
        >
            <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </a>

        {{-- Tooltip --}}
        <div
            x-show="visible"
            x-transition:enter="transition ease-out duration-500 delay-700"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="absolute bottom-1 right-16 whitespace-nowrap rounded-xl bg-slate-900/90 px-3 py-1.5 text-xs font-medium text-white shadow-lg backdrop-blur-sm"
        >
            Bize yazın 💬
            <div class="absolute right-[-6px] top-1/2 -translate-y-1/2 border-4 border-transparent border-l-slate-900/90"></div>
        </div>
    </div>

    <style>
        @keyframes wa-ping {
            0%   { transform: scale(1);   opacity: 0.75; }
            75%  { transform: scale(1.8); opacity: 0; }
            100% { transform: scale(1.8); opacity: 0; }
        }
    </style>
    @endif

</x-layouts.app>
