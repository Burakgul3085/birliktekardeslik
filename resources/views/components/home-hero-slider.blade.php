@props([
    'slides' => [],
])

<section
    class="relative z-10 w-full max-w-[100vw] overflow-x-hidden notranslate"
    aria-label="Ana tanıtım slider"
    translate="no"
    x-data="homeHeroSlider({ slides: @js($slides) })"
    @touchstart.passive="startTouch($event)"
    @touchend.passive="endTouch($event)"
>
    <template x-if="total === 0">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
            <x-empty-state title="Hero alanı hazır" description="Yönetim panelinden «Hero Slider» bölümünden slayt ekleyin. Her slayta yalnızca bir görsel yükleyebilirsiniz; birden fazla slayt ekleyerek kaydırmalı alanı oluşturursunuz." />
        </div>
    </template>

    <div
        x-show="total > 0"
        x-cloak
        class="relative w-full overflow-hidden bg-slate-100"
        role="region"
        aria-roledescription="carousel"
    >
        <div
            class="relative w-full h-[min(58svh,600px)] min-h-[260px] sm:h-[min(62svh,640px)] sm:min-h-[300px] md:h-[min(68svh,720px)] md:min-h-[360px] lg:h-[min(70vh,800px)]"
        >
            <div class="absolute inset-0 z-0">
                <img
                    :src="current.image"
                    :alt="'Hero slayt ' + (idx + 1)"
                    class="h-full w-full object-contain object-center"
                    sizes="100vw"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                />
            </div>

            {{-- Oklar (tam genişlik kenarları) --}}
            <template x-if="total > 1">
                <div
                    class="pointer-events-none absolute inset-y-0 left-0 right-0 z-20 flex max-h-[100%] items-center justify-between px-1 sm:px-2 md:px-4"
                >
                    <button
                        type="button"
                        class="pointer-events-auto flex h-10 w-10 items-center justify-center rounded-full border border-slate-200/90 bg-white text-slate-900 shadow-lg transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-400/70 md:h-12 md:w-12"
                        @click="prev()"
                        aria-label="Önceki slayt"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="pointer-events-auto flex h-10 w-10 items-center justify-center rounded-full border border-slate-200/90 bg-white text-slate-900 shadow-lg transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-400/70 md:h-12 md:w-12"
                        @click="next()"
                        aria-label="Sonraki slayt"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </template>

            <div
                class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2 md:bottom-5"
                x-show="total > 1"
                role="tablist"
                aria-label="Slayt seçimi"
            >
                <template x-for="(slide, i) in slides" :key="'hero-dot-' + i">
                    <button
                        type="button"
                        class="h-2.5 w-2.5 rounded-full transition focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        :class="idx === i ? 'scale-125 bg-blue-800 shadow-sm' : 'bg-blue-200/90 hover:bg-blue-300'"
                        @click="go(i)"
                        :aria-label="'Slayt ' + (i + 1)"
                        :aria-current="idx === i ? 'true' : 'false'"
                    ></button>
                </template>
            </div>
        </div>
    </div>
</section>
