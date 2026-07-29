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

    /* Menü bağlantısının aktif sayfayla eşleşip eşleşmediğini belirler */
    $navIsActive = function (?string $url): bool {
        if (blank($url)) {
            return false;
        }
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        return $path === ''
            ? request()->path() === '/'
            : (request()->is($path) || request()->is($path . '/*'));
    };
@endphp

<header
    x-data="{
        contactOpen: false,
        scrolled: false,
        init() {
            this.scrolled = window.scrollY > 8;
            this.$watch('contactOpen', (v) => {
                document.documentElement.classList.toggle('overflow-hidden', v);
            });
        }
    }"
    @scroll.window.passive="scrolled = window.scrollY > 8"
    class="sticky top-0 z-40 bg-white transition-shadow duration-300"
    :class="scrolled ? 'shadow-[0_6px_24px_-12px_rgba(15,23,42,0.28)]' : 'shadow-none'"
>
    {{-- ÜST BAR: iletişim bilgileri, sosyal medya ve hızlı aksiyonlar --}}
    <div class="bg-gradient-to-r from-cyan-950 via-cyan-900 to-teal-900 text-cyan-50">
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

        {{-- Mobil üst bar --}}
        <div class="mx-auto max-w-7xl px-3 py-2 md:hidden">
            <div class="mb-2 flex flex-wrap items-center gap-1.5 text-[11px] text-cyan-50/90">
                @if(!empty($siteSettings->email))
                    <a href="mailto:{{ $siteSettings->email }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-2.5 py-1 transition hover:bg-white/20">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 opacity-80"><path d="M2.94 5.5A2 2 0 0 1 4.8 4h10.4a2 2 0 0 1 1.86 1.5L10 9.88 2.94 5.5Z" /><path d="M2.8 7.25V14a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V7.25l-6.69 4.15a1 1 0 0 1-1.02 0L2.8 7.25Z" /></svg>
                        <span class="max-w-[180px] truncate">{{ $siteSettings->email }}</span>
                    </a>
                @endif
                @if(!empty($siteSettings->address))
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-2.5 py-1">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 opacity-80"><path fill-rule="evenodd" d="M10 2.5a5.5 5.5 0 0 0-5.5 5.5c0 4.3 4.65 8.76 5.03 9.12a.7.7 0 0 0 .94 0c.38-.36 5.03-4.82 5.03-9.12A5.5 5.5 0 0 0 10 2.5Zm0 7.25a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Z" clip-rule="evenodd" /></svg>
                        <span class="max-w-[140px] truncate">{{ $siteSettings->address }}</span>
                    </span>
                @endif
                @if(!empty($siteSettings->phone))
                    <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-2.5 py-1 transition hover:bg-white/20">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 opacity-80"><path d="M2 3.75A1.75 1.75 0 0 1 3.75 2h2.31c.83 0 1.54.58 1.7 1.39l.39 1.98a1.75 1.75 0 0 1-.5 1.57l-1.1 1.1a13.13 13.13 0 0 0 5.4 5.4l1.1-1.1a1.75 1.75 0 0 1 1.57-.5l1.98.4A1.75 1.75 0 0 1 18 13.94v2.31A1.75 1.75 0 0 1 16.25 18h-.75C8.6 18 2 11.4 2 3.75Z" /></svg>
                        <span>{{ $siteSettings->phone }}</span>
                    </a>
                @endif
            </div>
            <div class="flex items-center justify-between gap-2">
                <div class="no-scrollbar flex items-center gap-0.5 overflow-x-auto pr-1">
                    @foreach ($topBarSocialMap as $field => $platform)
                        @if (! empty($siteSettings->$field))
                            <a
                                href="{{ $siteSettings->$field }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-cyan-100/80 transition hover:bg-white/15 hover:text-white"
                                title="{{ $topBarAria[$platform] ?? $platform }}"
                                aria-label="{{ $topBarAria[$platform] ?? $platform }}"
                            >
                                <x-social-brand-icon :platform="$platform" icon-class="h-3.5 w-3.5" />
                            </a>
                        @endif
                    @endforeach
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <a href="{{ route('donations') }}" class="rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold text-white transition hover:bg-white/25">{{ __('app.nav.donate') }}</a>
                    <a href="{{ route('volunteer') }}" class="rounded-full border border-white/25 px-3 py-1 text-[11px] font-semibold text-cyan-50 transition hover:border-white/50 hover:bg-white/10">{{ __('app.nav.volunteer') }}</a>
                </div>
            </div>
        </div>

        {{-- Masaüstü üst bar --}}
        <div class="mx-auto hidden max-w-7xl items-center justify-between gap-6 px-4 py-2 text-[13px] md:flex md:px-6">
            <div class="flex flex-wrap items-center divide-x divide-white/15">
                @if(!empty($siteSettings->email))
                    <a href="mailto:{{ $siteSettings->email }}" class="inline-flex items-center gap-2 pr-5 text-cyan-50/85 transition hover:text-white">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-cyan-300/80"><path d="M2.94 5.5A2 2 0 0 1 4.8 4h10.4a2 2 0 0 1 1.86 1.5L10 9.88 2.94 5.5Z" /><path d="M2.8 7.25V14a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V7.25l-6.69 4.15a1 1 0 0 1-1.02 0L2.8 7.25Z" /></svg>
                        <span>{{ $siteSettings->email }}</span>
                    </a>
                @endif
                @if(!empty($siteSettings->address))
                    <span class="inline-flex items-center gap-2 px-5 text-cyan-50/85">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-cyan-300/80"><path fill-rule="evenodd" d="M10 2.5a5.5 5.5 0 0 0-5.5 5.5c0 4.3 4.65 8.76 5.03 9.12a.7.7 0 0 0 .94 0c.38-.36 5.03-4.82 5.03-9.12A5.5 5.5 0 0 0 10 2.5Zm0 7.25a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Z" clip-rule="evenodd" /></svg>
                        <span class="max-w-[420px] truncate">{{ $siteSettings->address }}</span>
                    </span>
                @endif
                @if(!empty($siteSettings->phone))
                    <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="inline-flex items-center gap-2 pl-5 font-medium text-cyan-50/90 transition hover:text-white">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-cyan-300/80"><path d="M2 3.75A1.75 1.75 0 0 1 3.75 2h2.31c.83 0 1.54.58 1.7 1.39l.39 1.98a1.75 1.75 0 0 1-.5 1.57l-1.1 1.1a13.13 13.13 0 0 0 5.4 5.4l1.1-1.1a1.75 1.75 0 0 1 1.57-.5l1.98.4A1.75 1.75 0 0 1 18 13.94v2.31A1.75 1.75 0 0 1 16.25 18h-.75C8.6 18 2 11.4 2 3.75Z" /></svg>
                        <span>{{ $siteSettings->phone }}</span>
                    </a>
                @endif
            </div>
            <div class="flex shrink-0 items-center gap-3">
                <div class="flex items-center gap-0.5">
                    @foreach ($topBarSocialMap as $field => $platform)
                        @if (! empty($siteSettings->$field))
                            <a
                                href="{{ $siteSettings->$field }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex h-7 w-7 items-center justify-center rounded-full text-cyan-100/70 transition hover:bg-white/15 hover:text-white"
                                title="{{ $topBarAria[$platform] ?? $platform }}"
                                aria-label="{{ $topBarAria[$platform] ?? $platform }}"
                            >
                                <x-social-brand-icon :platform="$platform" icon-class="h-3.5 w-3.5" />
                            </a>
                        @endif
                    @endforeach
                </div>
                <span class="h-4 w-px bg-white/20" aria-hidden="true"></span>
                <a href="{{ route('donations') }}" class="rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-white transition hover:bg-white/25">{{ __('app.nav.donate') }}</a>
                <a href="{{ route('volunteer') }}" class="rounded-full border border-white/25 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-cyan-50 transition hover:border-white/50 hover:bg-white/10">{{ __('app.nav.volunteer') }}</a>
            </div>
        </div>
    </div>

    {{-- ANA BAR: logo, ana menü ve aksiyon grubu --}}
    <div
        class="border-b border-slate-200/70 bg-white/95 backdrop-blur transition-all duration-300"
    >
        <div
            class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-3 sm:px-4 md:gap-6 md:px-6"
            :class="scrolled ? 'py-2 md:py-2.5' : 'py-2.5 md:py-4'"
        >
            {{-- Kurum kimliği --}}
            <a href="{{ route('home') }}" class="group flex min-w-0 items-center gap-2.5 sm:gap-3">
                <img
                    src="{{ $siteSettings->logo ? asset('storage/' . $siteSettings->logo) : asset('images/default-logo.svg') }}"
                    alt="{{ $siteSettings->site_title }}"
                    class="shrink-0 rounded-full object-cover ring-1 ring-slate-200/80 transition-all duration-300 group-hover:ring-cyan-300"
                    :class="scrolled ? 'h-9 w-9 md:h-10 md:w-10' : 'h-10 w-10 md:h-12 md:w-12'"
                >
                <span class="flex min-w-0 flex-col leading-tight">
                    <span class="max-w-[130px] truncate text-[0.95rem] font-bold tracking-tight text-slate-900 transition group-hover:text-cyan-800 sm:max-w-[190px] md:max-w-[260px] md:text-[1.1rem] lg:max-w-none">
                        {{ $siteSettings->site_title }}
                    </span>
                    @if(!empty($siteSettings->site_description))
                        <span class="hidden max-w-[320px] truncate text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400 lg:block">
                            {{ $siteSettings->site_description }}
                        </span>
                    @endif
                </span>
            </a>

            {{-- Mobil aksiyonlar --}}
            <div class="flex shrink-0 items-center gap-1 sm:gap-1.5 md:hidden">
                <div class="relative" x-data="{ mobileLangOpen: false }" @click.outside="mobileLangOpen = false">
                    <button
                        type="button"
                        @click="mobileLangOpen = !mobileLangOpen"
                        class="inline-flex h-9 items-center gap-1 rounded-xl border border-slate-200 bg-white px-2 text-[10px] font-bold uppercase text-slate-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50/60"
                        aria-label="Dil seçici"
                    >
                        <img src="{{ $currentFlag }}" alt="{{ strtoupper($currentLocale) }}" class="h-4 w-5 rounded object-cover">
                        {{ strtoupper($currentLocale) }}
                    </button>
                    <div
                        x-show="mobileLangOpen"
                        x-cloak
                        x-transition
                        class="absolute right-0 top-full z-50 mt-2 w-36 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                    >
                        @foreach($langList as $lang)
                            <a
                                href="{{ route('locale.switch', $lang['code']) }}"
                                class="flex items-center gap-2 border-b border-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition last:border-0 hover:bg-cyan-50 {{ $currentLocale === $lang['code'] ? 'bg-cyan-50 text-cyan-800' : '' }}"
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
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50/70 hover:text-cyan-700"
                    :aria-expanded="contactOpen"
                    aria-label="{{ __('app.nav.quick_contact') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <a href="{{ route('donations') }}" class="inline-flex h-9 items-center rounded-full bg-cyan-700 px-3 text-[10px] font-bold uppercase tracking-wide text-white shadow-sm transition hover:bg-cyan-800 sm:text-[11px]">{{ __('app.nav.donate_short') }}</a>
                <a href="{{ route('volunteer') }}" class="hidden h-9 items-center rounded-full border border-cyan-200 bg-white px-3 text-[10px] font-bold uppercase tracking-wide text-cyan-700 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-50 sm:inline-flex sm:text-[11px]">{{ __('app.nav.volunteer_short') }}</a>
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

            {{-- Masaüstü ana menü --}}
            <nav class="hidden items-center md:flex" aria-label="{{ __('app.nav.home') }}">
                <div class="flex items-center gap-0.5 lg:gap-1">
                    @php $homeActive = request()->routeIs('home'); @endphp
                    <a
                        href="{{ route('home') }}"
                        @class([
                            'relative rounded-lg px-2.5 py-2 text-[14.5px] font-semibold transition-colors lg:px-3',
                            'text-cyan-800' => $homeActive,
                            'text-slate-700 hover:text-cyan-800' => ! $homeActive,
                        ])
                    >
                        <span>{{ __('app.nav.home') }}</span>
                        <span @class([
                            'absolute inset-x-2.5 -bottom-0.5 h-[2px] rounded-full bg-cyan-600 transition-transform duration-300 lg:inset-x-3',
                            'scale-x-100' => $homeActive,
                            'scale-x-0' => ! $homeActive,
                        ])></span>
                    </a>

                    @forelse($headerTopItems as $item)
                        @php
                            $children = $headerChildren->get($item->id, collect());
                            $hasChildren = $children->isNotEmpty();
                            $itemLabel = navMenuLabel($item->label);
                            $itemActive = $navIsActive($item->url)
                                || $children->contains(fn ($child) => $navIsActive($child->url));
                        @endphp
                        @if ($hasChildren)
                            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                <button
                                    type="button"
                                    @class([
                                        'group relative inline-flex items-center gap-1 rounded-lg px-2.5 py-2 text-[14.5px] font-semibold transition-colors lg:px-3',
                                        'text-cyan-800' => $itemActive,
                                        'text-slate-700 hover:text-cyan-800' => ! $itemActive,
                                    ])
                                    :aria-expanded="open"
                                >
                                    <span>{{ $itemLabel }}</span>
                                    <svg class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 8l4 4 4-4" />
                                    </svg>
                                    <span
                                        class="absolute inset-x-2.5 -bottom-0.5 h-[2px] rounded-full bg-cyan-600 transition-transform duration-300 lg:inset-x-3"
                                        :class="(open || {{ $itemActive ? 'true' : 'false' }}) ? 'scale-x-100' : 'scale-x-0'"
                                    ></span>
                                </button>

                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1"
                                    class="absolute left-0 top-full z-50 min-w-[250px] pt-3"
                                >
                                    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-1.5 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.35)]">
                                        @foreach($children as $child)
                                            @php $childActive = $navIsActive($child->url); @endphp
                                            <a
                                                href="{{ $child->url }}"
                                                target="{{ $child->open_in_new_tab ? '_blank' : '_self' }}"
                                                @class([
                                                    'group flex items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-[14.5px] font-medium transition',
                                                    'bg-cyan-50/80 text-cyan-800' => $childActive,
                                                    'text-slate-700 hover:bg-slate-50 hover:text-cyan-800' => ! $childActive,
                                                ])
                                            >
                                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-500/40 transition group-hover:bg-cyan-600"></span>
                                                <span>{{ navMenuLabel($child->label) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a
                                href="{{ $item->url }}"
                                target="{{ $item->open_in_new_tab ? '_blank' : '_self' }}"
                                @class([
                                    'relative rounded-lg px-2.5 py-2 text-[14.5px] font-semibold transition-colors lg:px-3',
                                    'text-cyan-800' => $itemActive,
                                    'text-slate-700 hover:text-cyan-800' => ! $itemActive,
                                ])
                            >
                                <span>{{ $itemLabel }}</span>
                                <span @class([
                                    'absolute inset-x-2.5 -bottom-0.5 h-[2px] rounded-full bg-cyan-600 transition-transform duration-300 lg:inset-x-3',
                                    'scale-x-100' => $itemActive,
                                    'scale-x-0' => ! $itemActive,
                                ])></span>
                            </a>
                        @endif
                    @empty
                        <span class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-500">{{ __('app.nav.menu_empty') }}</span>
                    @endforelse

                    @php $newsActive = request()->routeIs('news.*'); @endphp
                    <a
                        href="{{ route('news.index') }}"
                        @class([
                            'relative rounded-lg px-2.5 py-2 text-[14.5px] font-semibold transition-colors lg:px-3',
                            'text-cyan-800' => $newsActive,
                            'text-slate-700 hover:text-cyan-800' => ! $newsActive,
                        ])
                    >
                        <span>{{ __('app.nav.news') }}</span>
                        <span @class([
                            'absolute inset-x-2.5 -bottom-0.5 h-[2px] rounded-full bg-cyan-600 transition-transform duration-300 lg:inset-x-3',
                            'scale-x-100' => $newsActive,
                            'scale-x-0' => ! $newsActive,
                        ])></span>
                    </a>

                    @php $contactActive = request()->routeIs('contact'); @endphp
                    <a
                        href="{{ route('contact') }}"
                        @class([
                            'relative rounded-lg px-2.5 py-2 text-[14.5px] font-semibold transition-colors lg:px-3',
                            'text-cyan-800' => $contactActive,
                            'text-slate-700 hover:text-cyan-800' => ! $contactActive,
                        ])
                    >
                        <span>{{ __('app.nav.contact') }}</span>
                        <span @class([
                            'absolute inset-x-2.5 -bottom-0.5 h-[2px] rounded-full bg-cyan-600 transition-transform duration-300 lg:inset-x-3',
                            'scale-x-100' => $contactActive,
                            'scale-x-0' => ! $contactActive,
                        ])></span>
                    </a>
                </div>

                {{-- Ayırıcı --}}
                <span class="mx-3 h-7 w-px bg-slate-200 lg:mx-4" aria-hidden="true"></span>

                {{-- Aksiyon grubu --}}
                <div class="flex shrink-0 items-center gap-1.5 lg:gap-2">
                    {{-- İkon grubu: galeri ve hızlı iletişim --}}
                    <div class="flex items-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50/70 shadow-sm">
                        <a
                            href="{{ route('gallery') }}"
                            title="{{ __('app.nav.gallery_title') }}"
                            aria-label="{{ __('app.nav.gallery_title') }}"
                            class="inline-flex h-9 w-9 items-center justify-center text-slate-500 transition hover:bg-white hover:text-cyan-700 lg:h-10 lg:w-10"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                        </a>
                        <span class="h-5 w-px bg-slate-200" aria-hidden="true"></span>
                        <button
                            type="button"
                            @click="contactOpen = true"
                            class="inline-flex h-9 w-9 items-center justify-center text-slate-500 transition hover:bg-white hover:text-cyan-700 lg:h-10 lg:w-10"
                            :aria-expanded="contactOpen"
                            aria-label="{{ __('app.nav.quick_contact') }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>

                    {{-- Dil seçici --}}
                    <div
                        class="relative"
                        x-data="{ langOpen: false }"
                        @click.outside="langOpen = false"
                    >
                        <button
                            type="button"
                            @click="langOpen = !langOpen"
                            class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50/70 px-2 shadow-sm transition hover:border-cyan-300 hover:bg-white lg:h-10 lg:px-2.5"
                            aria-label="Dil seçici"
                        >
                            <img
                                src="{{ $currentFlag }}"
                                alt="{{ strtoupper($currentLocale) }}"
                                class="h-4 w-6 rounded object-cover ring-1 ring-slate-200"
                            >
                            <span class="text-xs font-bold text-slate-600">{{ strtoupper($currentLocale) }}</span>
                            <svg class="h-3 w-3 text-slate-400 transition-transform duration-200" :class="langOpen && 'rotate-180'" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 8l4 4 4-4"/></svg>
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
                            class="absolute right-0 top-full z-50 mt-2 w-44 origin-top-right overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-1.5 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.35)]"
                        >
                            @foreach($langList as $lang)
                            <a
                                href="{{ route('locale.switch', $lang['code']) }}"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-slate-50 {{ $currentLocale === $lang['code'] ? 'bg-cyan-50/80' : '' }}"
                            >
                                <img src="{{ $lang['flag'] }}" alt="{{ strtoupper($lang['code']) }}" class="h-4 w-6 rounded object-cover ring-1 ring-slate-200">
                                <span class="flex-1 text-sm font-semibold {{ $currentLocale === $lang['code'] ? 'text-cyan-800' : 'text-slate-700' }}">{{ $lang['label'] }}</span>
                                <span class="text-[11px] font-bold text-slate-400">{{ strtoupper($lang['code']) }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Zekat hesaplama --}}
                    <a
                        href="{{ route('zakat.index') }}"
                        class="inline-flex h-9 items-center rounded-full border border-cyan-200 bg-cyan-50/70 px-3 text-[11px] font-bold uppercase tracking-wide text-cyan-800 shadow-sm transition hover:border-cyan-300 hover:bg-cyan-100 lg:h-10 lg:px-4 lg:text-xs"
                        title="{{ __('app.nav.zakat_calculate') }}"
                    >
                        <span class="lg:hidden">{{ __('app.nav.zakat_short') }}</span>
                        <span class="hidden lg:inline">{{ __('app.nav.zakat_calculate') }}</span>
                    </a>

                    {{-- Ana çağrı: bağış --}}
                    <a
                        href="{{ route('donations') }}"
                        class="inline-flex h-9 items-center rounded-full bg-gradient-to-r from-cyan-700 to-teal-700 px-4 text-[11px] font-bold uppercase tracking-wide text-white shadow-md shadow-cyan-900/20 transition hover:from-cyan-800 hover:to-teal-800 hover:shadow-lg hover:shadow-cyan-900/25 lg:h-10 lg:px-5 lg:text-xs"
                    >{{ __('app.nav.donate') }}</a>
                </div>
            </nav>
        </div>
    </div>

    {{-- MOBİL MENÜ ŞERİDİ --}}
    <div class="border-b border-slate-200/70 bg-white md:hidden">
        <div class="no-scrollbar mx-auto flex max-w-7xl items-center gap-2 overflow-x-auto px-4 py-2.5">
            <a
                href="{{ route('home') }}"
                class="shrink-0 rounded-full border px-3 py-1.5 text-[11px] font-semibold transition {{ request()->routeIs('home') ? 'border-cyan-300 bg-cyan-50 text-cyan-800' : 'border-slate-200 bg-white text-slate-700 hover:border-cyan-300 hover:text-cyan-700' }}"
            >{{ __('app.nav.home') }}</a>

            @foreach($headerTopItems as $item)
                @php
                    $children = $headerChildren->get($item->id, collect());
                @endphp
                @if ($children->isEmpty())
                    <a
                        href="{{ $item->url }}"
                        target="{{ $item->open_in_new_tab ? '_blank' : '_self' }}"
                        class="shrink-0 rounded-full border px-3 py-1.5 text-[11px] font-semibold transition {{ $navIsActive($item->url) ? 'border-cyan-300 bg-cyan-50 text-cyan-800' : 'border-slate-200 bg-white text-slate-700 hover:border-cyan-300 hover:text-cyan-700' }}"
                    >{{ navMenuLabel($item->label) }}</a>
                @endif
            @endforeach

            <a
                href="{{ route('news.index') }}"
                class="shrink-0 rounded-full border px-3 py-1.5 text-[11px] font-semibold transition {{ request()->routeIs('news.*') ? 'border-cyan-300 bg-cyan-50 text-cyan-800' : 'border-slate-200 bg-white text-slate-700 hover:border-cyan-300 hover:text-cyan-700' }}"
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
                class="shrink-0 rounded-full border px-3 py-1.5 text-[11px] font-semibold transition {{ request()->routeIs('contact') ? 'border-cyan-300 bg-cyan-50 text-cyan-800' : 'border-slate-200 bg-white text-slate-700 hover:border-cyan-300 hover:text-cyan-700' }}"
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

            <a
                href="{{ route('zakat.index') }}"
                class="shrink-0 rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-[11px] font-semibold text-cyan-800 transition hover:border-cyan-400 hover:bg-cyan-100"
            >{{ __('app.nav.zakat_short') }}</a>
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
                                    class="block px-3 py-2 text-sm font-medium transition hover:bg-cyan-50 hover:text-cyan-700 {{ $navIsActive($child->url) ? 'text-cyan-800' : 'text-slate-700' }}"
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
