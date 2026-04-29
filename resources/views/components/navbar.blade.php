@php
    /* Menü etiketini mevcut dile göre çeviren yardımcı */
    function navMenuLabel(string $label): string {
        $key = 'app.menu.' . $label;
        return __($key) !== $key ? __($key) : $label;
    }
    $currentLocale = app()->getLocale();
    $langList = [
        ['code' => 'tr', 'flag' => 'https://flagcdn.com/w40/tr.png', 'label' => 'Türkçe'],
        ['code' => 'en', 'flag' => 'https://flagcdn.com/w40/gb.png', 'label' => 'English'],
        ['code' => 'ar', 'flag' => 'https://flagcdn.com/w40/sa.png', 'label' => 'العربية'],
        ['code' => 'ru', 'flag' => 'https://flagcdn.com/w40/ru.png', 'label' => 'Русский'],
    ];
    $flagMap = [
        'tr' => 'https://flagcdn.com/w40/tr.png',
        'en' => 'https://flagcdn.com/w40/gb.png',
        'ar' => 'https://flagcdn.com/w40/sa.png',
        'ru' => 'https://flagcdn.com/w40/ru.png',
    ];
    $currentFlag = $flagMap[$currentLocale] ?? $flagMap['tr'];
@endphp

<header
    x-data="{
        contactOpen: false,
        init() {
            this.$watch('contactOpen', (v) => {
                document.documentElement.classList.toggle('overflow-hidden', v);
            });
        }
    }"
    class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 shadow-sm backdrop-blur"
>
    <div class="hidden border-b border-cyan-800/60 bg-cyan-900 text-cyan-50 md:block">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-2 md:px-6">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-xs">
                @if(!empty($siteSettings->email))
                    <span class="inline-flex items-center gap-1.5">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M2.94 5.5A2 2 0 0 1 4.8 4h10.4a2 2 0 0 1 1.86 1.5L10 9.88 2.94 5.5Z" /><path d="M2.8 7.25V14a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V7.25l-6.69 4.15a1 1 0 0 1-1.02 0L2.8 7.25Z" /></svg>
                        <a href="mailto:{{ $siteSettings->email }}" class="hover:text-white">{{ $siteSettings->email }}</a>
                    </span>
                @endif
                @if(!empty($siteSettings->address))
                    <span class="inline-flex items-center gap-1.5">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 2.5a5.5 5.5 0 0 0-5.5 5.5c0 4.3 4.65 8.76 5.03 9.12a.7.7 0 0 0 .94 0c.38-.36 5.03-4.82 5.03-9.12A5.5 5.5 0 0 0 10 2.5Zm0 7.25a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Z" clip-rule="evenodd" /></svg>
                        <span>{{ $siteSettings->address }}</span>
                    </span>
                @endif
                @if(!empty($siteSettings->phone))
                    <span class="inline-flex items-center gap-1.5">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M2 3.75A1.75 1.75 0 0 1 3.75 2h2.31c.83 0 1.54.58 1.7 1.39l.39 1.98a1.75 1.75 0 0 1-.5 1.57l-1.1 1.1a13.13 13.13 0 0 0 5.4 5.4l1.1-1.1a1.75 1.75 0 0 1 1.57-.5l1.98.4A1.75 1.75 0 0 1 18 13.94v2.31A1.75 1.75 0 0 1 16.25 18h-.75C8.6 18 2 11.4 2 3.75Z" /></svg>
                        <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="hover:text-white">{{ $siteSettings->phone }}</a>
                    </span>
                @endif
            </div>

            <div class="flex flex-wrap items-center justify-end gap-1.5 text-xs sm:gap-2">
                @php
                    $topBarSocialMap = [
                        'instagram_url' => 'instagram',
                        'youtube_url'   => 'youtube',
                        'tiktok_url'    => 'tiktok',
                        'facebook_url'  => 'facebook',
                        'x_url'         => 'x',
                        'linkedin_url'  => 'linkedin',
                        'whatsapp_url'  => 'whatsapp',
                        'telegram_url'  => 'telegram',
                        'website_url'   => 'website',
                    ];
                    $topBarAria = [
                        'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'facebook' => 'Facebook',
                        'x' => 'X (Twitter)', 'linkedin' => 'LinkedIn', 'whatsapp' => 'WhatsApp', 'telegram' => 'Telegram', 'website' => 'Web sitesi',
                    ];
                @endphp
                @foreach ($topBarSocialMap as $field => $platform)
                    @if (! empty($siteSettings->$field))
                        <a
                            href="{{ $siteSettings->$field }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex h-7 w-7 items-center justify-center rounded-full text-cyan-100/90 transition hover:bg-white/10 hover:text-white"
                            title="{{ $topBarAria[$platform] ?? $platform }}"
                            aria-label="{{ $topBarAria[$platform] ?? $platform }}"
                        >
                            <x-social-brand-icon :platform="$platform" icon-class="h-3.5 w-3.5" />
                        </a>
                    @endif
                @endforeach
                <a href="{{ route('donations') }}" class="ml-1 rounded-full bg-white/15 px-3 py-1.5 font-medium text-cyan-50 transition hover:bg-white/25 sm:ml-2">{{ __('app.nav.donate') }}</a>
                <a href="{{ route('volunteer') }}" class="rounded-full border border-cyan-100/50 px-3 py-1.5 font-medium text-cyan-50 transition hover:bg-white/10">{{ __('app.nav.volunteer') }}</a>
            </div>
        </div>
    </div>

    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-2.5 md:px-6 md:py-3">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5">
            <img src="{{ $siteSettings->logo ? asset('storage/' . $siteSettings->logo) : asset('images/default-logo.svg') }}" alt="Logo" class="h-11 w-11 shrink-0 rounded-full object-cover shadow-sm ring-1 ring-slate-200">
            <span class="max-w-[150px] truncate text-[15px] font-semibold tracking-tight text-slate-900 md:max-w-none md:text-[1.05rem]">{{ $siteSettings->site_title }}</span>
        </a>

        <div class="flex shrink-0 items-center gap-1.5 md:hidden">
            <div class="relative" x-data="{ mobileLangOpen: false }" @click.outside="mobileLangOpen = false">
                <button
                    type="button"
                    @click="mobileLangOpen = !mobileLangOpen"
                    class="inline-flex h-10 items-center gap-1 rounded-xl border border-slate-200 bg-white px-2 text-[10px] font-bold uppercase text-slate-700 shadow-sm"
                    aria-label="Dil seçici"
                >
                    <img src="{{ $currentFlag }}" alt="{{ strtoupper($currentLocale) }}" class="h-4 w-5 rounded object-cover">
                    {{ strtoupper($currentLocale) }}
                </button>
                <div
                    x-show="mobileLangOpen"
                    x-cloak
                    class="absolute right-0 top-full z-50 mt-2 w-36 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                >
                    @foreach($langList as $lang)
                        <a
                            href="{{ route('locale.switch', $lang['code']) }}"
                            class="flex items-center gap-2 border-b border-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 last:border-0 hover:bg-cyan-50 {{ $currentLocale === $lang['code'] ? 'bg-cyan-50' : '' }}"
                        >
                            <img src="{{ $lang['flag'] }}" alt="{{ strtoupper($lang['code']) }}" class="h-4 w-5 rounded object-cover">
                            <span class="flex-1">{{ $lang['label'] }}</span>
                            <span class="text-[10px] uppercase text-slate-400">{{ $lang['code'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <button
                type="button"
                @click="contactOpen = true"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-700 shadow-sm transition hover:border-cyan-300/60 hover:bg-cyan-50/80"
                :aria-expanded="contactOpen"
                aria-label="{{ __('app.nav.quick_contact') }}"
            >
                <span class="grid grid-cols-2 gap-0.5">
                    <span class="h-2 w-2 rounded-sm bg-cyan-700/90"></span>
                    <span class="h-2 w-2 rounded-sm bg-cyan-700/90"></span>
                    <span class="h-2 w-2 rounded-sm bg-cyan-700/90"></span>
                    <span class="h-2 w-2 rounded-sm bg-cyan-700/90"></span>
                </span>
            </button>
            <a href="{{ route('donations') }}" class="inline-flex min-h-[2.4rem] items-center rounded-full bg-cyan-600 px-3 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm transition hover:bg-cyan-700">{{ __('app.nav.donate_short') }}</a>
            <a href="{{ route('volunteer') }}" class="inline-flex min-h-[2.4rem] items-center rounded-full border border-cyan-200 bg-white px-3 text-[11px] font-bold uppercase tracking-wide text-cyan-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50">{{ __('app.nav.volunteer_short') }}</a>
        </div>

        @php
            $excludedMainLabels = ['ana sayfa', 'anasayfa', 'iletişim', 'iletisim', 'bağış', 'bagis', 'bağış yap', 'bagis yap', 'bağış hesapları', 'bagis hesaplari', 'medyada biz'];
            $headerTopItems = $menuItems
                ->whereNull('parent_id')
                ->filter(function ($item) use ($excludedMainLabels) {
                    $label = mb_strtolower(trim((string) $item->label));
                    return ! in_array($label, $excludedMainLabels, true);
                })
                ->values();
            $headerChildren = $menuItems
                ->whereNotNull('parent_id')
                ->groupBy('parent_id');
        @endphp
        <nav class="hidden items-center gap-0.5 md:flex">
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-[15px] font-semibold text-slate-800 transition hover:bg-slate-100 hover:text-cyan-700 lg:px-4">{{ __('app.nav.home') }}</a>

            @forelse($headerTopItems as $item)
                @php
                    $children = $headerChildren->get($item->id, collect());
                    $hasChildren = $children->isNotEmpty();
                    $itemLabel = navMenuLabel($item->label);
                @endphp
                @if ($hasChildren)
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-[15px] font-semibold text-slate-800 transition hover:bg-slate-100 hover:text-cyan-700 lg:px-4"
                            :aria-expanded="open"
                        >
                            <span>{{ $itemLabel }}</span>
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 8l4 4 4-4" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            class="absolute left-0 top-full z-50 min-w-[240px] pt-2"
                        >
                            <div class="rounded-xl border border-slate-200 bg-white py-2 shadow-xl">
                                @foreach($children as $child)
                                    <a
                                        href="{{ $child->url }}"
                                        target="{{ $child->open_in_new_tab ? '_blank' : '_self' }}"
                                        class="block border-b border-slate-100 px-4 py-2.5 text-[15px] font-medium text-slate-700 transition last:border-b-0 hover:bg-slate-50 hover:text-cyan-700"
                                    >{{ navMenuLabel($child->label) }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a
                        href="{{ $item->url }}"
                        target="{{ $item->open_in_new_tab ? '_blank' : '_self' }}"
                        class="rounded-lg px-3 py-2 text-[15px] font-semibold text-slate-800 transition hover:bg-slate-100 hover:text-cyan-700 lg:px-4"
                    >{{ $itemLabel }}</a>
                @endif
            @empty
                <span class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-500">{{ __('app.nav.menu_empty') }}</span>
            @endforelse

            <a href="{{ route('news.index') }}" class="rounded-lg px-3 py-2 text-[15px] font-semibold text-slate-800 transition hover:bg-slate-100 hover:text-cyan-700 lg:px-4">{{ __('app.nav.news') }}</a>

            <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2 text-[15px] font-semibold text-slate-800 transition hover:bg-slate-100 hover:text-cyan-700 lg:px-4">{{ __('app.nav.contact') }}</a>

            <div class="ml-1 flex items-center gap-1 pl-1">
                {{-- Galeri / Kamera ikonu --}}
                <a
                    href="{{ route('gallery') }}"
                    title="{{ __('app.nav.gallery_title') }}"
                    aria-label="{{ __('app.nav.gallery_title') }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50/90 text-slate-600 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50/90 hover:text-cyan-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                </a>
                <button
                    type="button"
                    @click="contactOpen = true"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50/90 text-slate-700 shadow-sm transition hover:border-cyan-200 hover:bg-cyan-50/90"
                    :aria-expanded="contactOpen"
                    aria-label="{{ __('app.nav.quick_contact') }}"
                >
                    <span class="grid grid-cols-2 gap-0.5" aria-hidden="true">
                        <span class="h-2 w-2 rounded-sm bg-cyan-700/90"></span>
                        <span class="h-2 w-2 rounded-sm bg-cyan-700/90"></span>
                        <span class="h-2 w-2 rounded-sm bg-cyan-700/90"></span>
                        <span class="h-2 w-2 rounded-sm bg-cyan-700/90"></span>
                    </span>
                </button>
                <a
                    href="{{ route('donations') }}"
                    class="inline-flex min-h-[2.5rem] items-center rounded-full bg-cyan-600 px-4 py-2 text-sm font-bold uppercase tracking-wide text-white shadow-sm transition hover:bg-cyan-700"
                >{{ __('app.nav.donate') }}</a>

                {{-- Dil Seçici --}}
                <div
                    class="relative"
                    x-data="{ langOpen: false }"
                    @click.outside="langOpen = false"
                >
                    <button
                        type="button"
                        @click="langOpen = !langOpen"
                        class="inline-flex h-10 items-center gap-1.5 rounded-2xl border border-slate-200 bg-slate-50/90 px-2.5 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50/90"
                        aria-label="Dil seçici"
                    >
                        <img
                            src="{{ $currentFlag }}"
                            alt="{{ strtoupper($currentLocale) }}"
                            class="h-5 w-7 rounded object-cover shadow-sm"
                        >
                        <span class="text-xs font-bold text-slate-600">{{ strtoupper($currentLocale) }}</span>
                        <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 8l4 4 4-4"/></svg>
                    </button>

                    <div
                        x-show="langOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-full z-50 mt-2 w-44 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                    >
                        @foreach($langList as $lang)
                        <a
                            href="{{ route('locale.switch', $lang['code']) }}"
                            class="flex w-full items-center gap-3 border-b border-slate-100 px-4 py-3 text-left transition last:border-0 hover:bg-cyan-50 {{ $currentLocale === $lang['code'] ? 'bg-cyan-50' : '' }}"
                        >
                            <img src="{{ $lang['flag'] }}" alt="{{ strtoupper($lang['code']) }}" class="h-5 w-7 rounded object-cover shadow-sm">
                            <span class="flex-1 text-sm font-semibold text-slate-700">{{ $lang['label'] }}</span>
                            <span class="text-xs font-bold text-slate-400">{{ strtoupper($lang['code']) }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="border-t border-slate-100 bg-white/95 md:hidden">
        <div class="no-scrollbar mx-auto flex max-w-7xl items-center gap-2 overflow-x-auto px-4 py-2.5">
            <a
                href="{{ route('home') }}"
                class="shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700"
            >{{ __('app.nav.home') }}</a>

            @foreach($headerTopItems as $item)
                @php
                    $children = $headerChildren->get($item->id, collect());
                @endphp
                @if ($children->isEmpty())
                    <a
                        href="{{ $item->url }}"
                        target="{{ $item->open_in_new_tab ? '_blank' : '_self' }}"
                        class="shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700"
                    >{{ navMenuLabel($item->label) }}</a>
                @endif
            @endforeach

            <a
                href="{{ route('news.index') }}"
                class="shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700"
            >{{ __('app.nav.news_short') }}</a>
            <a
                href="{{ route('gallery') }}"
                class="shrink-0 inline-flex items-center gap-1 rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-[11px] font-semibold text-cyan-700 transition hover:border-cyan-400 hover:bg-cyan-100"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                </svg>
                {{ __('app.nav.gallery') }}
            </a>
            <a
                href="{{ route('contact') }}"
                class="shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700"
            >{{ __('app.nav.contact') }}</a>

            {{-- Mobil dil seçici --}}
            @foreach($langList as $lang)
                @if($currentLocale !== $lang['code'])
                    <a
                        href="{{ route('locale.switch', $lang['code']) }}"
                        class="shrink-0 inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700"
                    >
                        <img src="{{ $lang['flag'] }}" alt="{{ $lang['code'] }}" class="h-3.5 w-5 rounded object-cover">
                        {{ strtoupper($lang['code']) }}
                    </a>
                @endif
            @endforeach
        </div>

        @php
            $mobileParentsWithChildren = $headerTopItems->filter(fn ($item) => $headerChildren->get($item->id, collect())->isNotEmpty());
        @endphp
        @if ($mobileParentsWithChildren->isNotEmpty())
            <div class="mx-auto max-w-7xl space-y-2 px-4 pb-3">
                @foreach($mobileParentsWithChildren as $item)
                    @php
                        $children = $headerChildren->get($item->id, collect());
                    @endphp
                    <details class="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <summary class="cursor-pointer list-none px-3 py-2.5 text-sm font-semibold text-slate-800 transition group-open:bg-cyan-50/70 group-open:text-cyan-800">
                            <span class="inline-flex items-center gap-1.5">
                                {{ navMenuLabel($item->label) }}
                                <svg class="h-4 w-4 text-slate-500 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 8l4 4 4-4" />
                                </svg>
                            </span>
                        </summary>
                        <div class="border-t border-slate-100 bg-slate-50/60 py-1.5">
                            @foreach($children as $child)
                                <a
                                    href="{{ $child->url }}"
                                    target="{{ $child->open_in_new_tab ? '_blank' : '_self' }}"
                                    class="block px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700"
                                >{{ navMenuLabel($child->label) }}</a>
                            @endforeach
                        </div>
                    </details>
                @endforeach
            </div>
        @endif
    </div>

    @include('components.header-contact-panel')
</header>
