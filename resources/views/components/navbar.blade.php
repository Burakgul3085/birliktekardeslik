<header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 backdrop-blur">
    <div class="hidden border-b border-cyan-800/60 bg-cyan-900 text-cyan-50 md:block">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-2 md:px-6">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-xs">
                @if(!empty($siteSettings->email))
                    <span class="inline-flex items-center gap-1.5">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M2.94 5.5A2 2 0 0 1 4.8 4h10.4a2 2 0 0 1 1.86 1.5L10 9.88 2.94 5.5Z"/><path d="M2.8 7.25V14a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V7.25l-6.69 4.15a1 1 0 0 1-1.02 0L2.8 7.25Z"/></svg>
                        <a href="mailto:{{ $siteSettings->email }}" class="hover:text-white">{{ $siteSettings->email }}</a>
                    </span>
                @endif
                @if(!empty($siteSettings->address))
                    <span class="inline-flex items-center gap-1.5">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 2.5a5.5 5.5 0 0 0-5.5 5.5c0 4.3 4.65 8.76 5.03 9.12a.7.7 0 0 0 .94 0c.38-.36 5.03-4.82 5.03-9.12A5.5 5.5 0 0 0 10 2.5Zm0 7.25a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Z" clip-rule="evenodd"/></svg>
                        <span>{{ $siteSettings->address }}</span>
                    </span>
                @endif
                @if(!empty($siteSettings->phone))
                    <span class="inline-flex items-center gap-1.5">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M2 3.75A1.75 1.75 0 0 1 3.75 2h2.31c.83 0 1.54.58 1.7 1.39l.39 1.98a1.75 1.75 0 0 1-.5 1.57l-1.1 1.1a13.13 13.13 0 0 0 5.4 5.4l1.1-1.1a1.75 1.75 0 0 1 1.57-.5l1.98.4A1.75 1.75 0 0 1 18 13.94v2.31A1.75 1.75 0 0 1 16.25 18h-.75C8.6 18 2 11.4 2 3.75Z"/></svg>
                        <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="hover:text-white">{{ $siteSettings->phone }}</a>
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-3 text-xs">
                @foreach([
                    'instagram_url' => 'Instagram',
                    'youtube_url' => 'YouTube',
                    'tiktok_url' => 'TikTok',
                    'facebook_url' => 'Facebook',
                    'x_url' => 'X',
                ] as $key => $label)
                    @if(!empty($siteSettings->$key))
                        <a href="{{ $siteSettings->$key }}" target="_blank" class="text-cyan-100 transition hover:text-white" aria-label="{{ $label }}">{{ $label }}</a>
                    @endif
                @endforeach
                <a href="{{ route('donations') }}" class="ml-2 rounded-full bg-white/15 px-3 py-1.5 font-medium text-cyan-50 transition hover:bg-white/25">Bağış Yap</a>
                <a href="{{ route('volunteer') }}" class="rounded-full border border-cyan-100/50 px-3 py-1.5 font-medium text-cyan-50 transition hover:bg-white/10">Gönüllü Ol</a>
            </div>
        </div>
    </div>

    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 md:px-6">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ $siteSettings->logo ? asset('storage/' . $siteSettings->logo) : asset('images/default-logo.svg') }}" alt="Logo" class="h-12 w-12 rounded-full object-cover shadow">
            <span class="text-lg font-semibold text-slate-800">{{ $siteSettings->site_title }}</span>
        </a>

        <nav class="hidden items-center gap-2 md:flex">
            @forelse($menuItems as $item)
                <a href="{{ $item->url }}" target="{{ $item->open_in_new_tab ? '_blank' : '_self' }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">{{ $item->label }}</a>
                @php
                    $normalizedLabel = mb_strtolower((string) $item->label);
                    $isAboutItem = \Illuminate\Support\Str::contains($normalizedLabel, [
                        'hakkımızda',
                        'hakkimizda',
                        'about',
                    ]);
                @endphp
                @if($isAboutItem)
                    <a href="{{ route('contact') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">İletişim</a>
                @endif
            @empty
                <span class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-500">Menü henüz eklenmedi</span>
                <a href="{{ route('contact') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">İletişim</a>
            @endforelse
            <a href="{{ route('donations') }}" class="ml-1 rounded-full bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700">Bağış Yap</a>
        </nav>
    </div>
</header>
