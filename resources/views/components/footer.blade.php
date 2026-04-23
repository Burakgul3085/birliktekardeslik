@props(['siteSettings' => \App\Models\Setting::current()])
@php
    $topBarSocialMap = [
        'instagram_url' => 'instagram',
        'youtube_url' => 'youtube',
        'tiktok_url' => 'tiktok',
        'facebook_url' => 'facebook',
        'x_url' => 'x',
        'linkedin_url' => 'linkedin',
        'whatsapp_url' => 'whatsapp',
        'telegram_url' => 'telegram',
        'website_url' => 'website',
    ];
    $topBarAria = [
        'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'facebook' => 'Facebook',
        'x' => 'X (Twitter)', 'linkedin' => 'LinkedIn', 'whatsapp' => 'WhatsApp', 'telegram' => 'Telegram', 'website' => 'Web sitesi',
    ];
    $logoSrc = $siteSettings->logo ? asset('storage/' . $siteSettings->logo) : asset('images/default-logo.svg');
@endphp

<footer class="mt-auto border-t border-slate-800/80 bg-slate-900 text-slate-200">
    <div class="mx-auto max-w-7xl px-4 py-12 md:px-6">
        <div class="grid gap-10 border-b border-slate-700/80 pb-12 lg:grid-cols-2 lg:gap-12 xl:grid-cols-4">
            {{-- Sütun 1: marka + e-bülten --}}
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <img src="{{ $logoSrc }}" alt="Logo" class="h-12 w-12 rounded-full object-cover ring-1 ring-slate-600">
                    <span class="text-left text-lg font-bold leading-tight text-white">{{ $siteSettings->site_title }}</span>
                </a>
                <p class="text-sm leading-relaxed text-slate-400">
                    {{ $siteSettings->site_description ?: 'Birlikte iyiliği büyütüyoruz. E-bültene kayıt olarak duyurulardan haberdar olabilirsiniz.' }}
                </p>
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-300/90">E-bülten</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="post" class="flex flex-col gap-2 sm:flex-row sm:items-stretch">
                        @csrf
                        <label class="sr-only" for="footer-newsletter-email">E-posta</label>
                        <input
                            id="footer-newsletter-email"
                            type="email"
                            name="email"
                            required
                            value="{{ old('email') }}"
                            placeholder="E-posta adresiniz"
                            class="min-h-[2.75rem] flex-1 rounded-2xl border border-slate-600 bg-slate-800/80 px-4 text-sm text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/30"
                        >
                        <button
                            type="submit"
                            class="inline-flex min-h-[2.75rem] min-w-[2.75rem] items-center justify-center rounded-2xl bg-cyan-500 text-white shadow-sm transition hover:bg-cyan-400"
                            aria-label="E-bültene kayıt ol"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </form>
                    <p class="mt-2 text-xs italic text-slate-500">Kaydınızı onay e-postası ve istediğiniz zaman iptal linki alırsınız.</p>
                </div>
            </div>

            {{-- Sütun 2: Hızlı erişim --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white">Hızlı erişim</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @forelse($footerMenuQuick as $item)
                        <li>
                            <a
                                href="{{ $item->url }}"
                                target="{{ $item->open_in_new_tab ? '_blank' : '_self' }}"
                                class="group inline-flex items-start gap-2 text-slate-300 transition hover:text-cyan-300"
                            >
                                <span class="mt-0.5 text-cyan-500/80 transition group-hover:text-cyan-300" aria-hidden="true">»</span>
                                <span>{{ $item->label }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-slate-500">Menü öğelerinde &quot;Footer sütunu&quot; olarak <strong class="text-slate-400">Hızlı erişim</strong> seçilen bağlantılar burada listelenir.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Sütun 3: Faaliyetlerimiz --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white">Faaliyetlerimiz</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @forelse($footerMenuActivities as $item)
                        <li>
                            <a
                                href="{{ $item->url }}"
                                target="{{ $item->open_in_new_tab ? '_blank' : '_self' }}"
                                class="group inline-flex items-start gap-2 text-slate-300 transition hover:text-cyan-300"
                            >
                                <span class="mt-0.5 text-cyan-500/80 transition group-hover:text-cyan-300" aria-hidden="true">»</span>
                                <span>{{ $item->label }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-slate-500">Menüde &quot;Footer sütunu&quot; olarak <strong class="text-slate-400">Faaliyetlerimiz</strong> atanan öğeler burada görünür.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Sütun 4: İletişim + sosyal --}}
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-white">İletişime geçin</h3>
                <ul class="space-y-2 text-sm text-slate-300">
                    @if (filled($siteSettings->email))
                        <li>
                            <span class="block text-xs font-medium text-slate-500">E-posta</span>
                            <a href="mailto:{{ $siteSettings->email }}" class="text-cyan-300 transition hover:text-cyan-200">{{ $siteSettings->email }}</a>
                        </li>
                    @endif
                    @if (filled($siteSettings->phone))
                        <li>
                            <span class="block text-xs font-medium text-slate-500">Telefon</span>
                            <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="text-cyan-300 transition hover:text-cyan-200">{{ $siteSettings->phone }}</a>
                        </li>
                    @endif
                    @if (filled($siteSettings->address))
                        <li>
                            <span class="block text-xs font-medium text-slate-500">Adres</span>
                            <span class="text-slate-200">{{ $siteSettings->address }}</span>
                        </li>
                    @endif
                </ul>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bizi takip edin</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach ($topBarSocialMap as $field => $platform)
                            @if (! empty($siteSettings->$field))
                                <a
                                    href="{{ $siteSettings->$field }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-600 bg-slate-800/50 text-cyan-100 transition hover:border-cyan-500/50 hover:bg-slate-700"
                                    title="{{ $topBarAria[$platform] ?? $platform }}"
                                    aria-label="{{ $topBarAria[$platform] ?? $platform }}"
                                >
                                    <x-social-brand-icon :platform="$platform" icon-class="h-3.5 w-3.5" />
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col items-center justify-between gap-4 border-t border-slate-800 pt-8 text-xs text-slate-500 sm:flex-row sm:gap-6">
            <p class="text-center sm:text-left">
                © {{ date('Y') }} — Tüm hakları saklıdır.
                <span class="text-slate-400">·</span>
                <span class="text-slate-300">{{ $siteSettings->site_title }}</span>
            </p>
            <nav class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-slate-400" aria-label="Yasal">
                @if (filled($siteSettings->legal_kvkk_url))
                    <a href="{{ $siteSettings->legal_kvkk_url }}" class="transition hover:text-cyan-300" target="_blank" rel="noopener">KVKK</a>
                @endif
                @if (filled($siteSettings->legal_terms_url))
                    <a href="{{ $siteSettings->legal_terms_url }}" class="transition hover:text-cyan-300" target="_blank" rel="noopener">Şartlar ve koşullar</a>
                @endif
                @if (filled($siteSettings->legal_privacy_url))
                    <a href="{{ $siteSettings->legal_privacy_url }}" class="transition hover:text-cyan-300" target="_blank" rel="noopener">Gizlilik politikası</a>
                @endif
            </nav>
        </div>
    </div>
</footer>
