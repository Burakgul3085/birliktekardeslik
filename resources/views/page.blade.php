<x-layouts.app>
    @if ($page->slug === 'hikayemiz')
        <section class="mx-auto max-w-7xl px-4 py-12 md:px-6 lg:py-16">
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 md:text-4xl">{{ $page->title }}</h1>
                @if (! empty($page->content))
                    <div class="prose mx-auto mt-4 max-w-3xl prose-slate">{!! $page->content !!}</div>
                @endif
            </div>

            @php
                $storyItems = collect($page->story_items ?? [])->filter(fn ($item) => filled($item['title'] ?? null) && filled($item['description'] ?? null));
            @endphp

            @if ($storyItems->isNotEmpty())
                <div class="relative">
                    <div class="pointer-events-none absolute left-1/2 top-0 hidden h-full w-px -translate-x-1/2 bg-gradient-to-b from-cyan-500/0 via-cyan-500/60 to-cyan-500/0 lg:block"></div>

                    <div class="space-y-8 md:space-y-10">
                        @foreach ($storyItems as $index => $item)
                            @php
                                $isLeft = $index % 2 === 0;
                                $imagePath = ! empty($item['image']) ? \Illuminate\Support\Facades\Storage::url($item['image']) : null;
                            @endphp

                            <article class="group relative lg:grid lg:grid-cols-[1fr_auto_1fr] lg:items-center lg:gap-6">
                                <div class="{{ $isLeft ? 'lg:order-1' : 'lg:order-3' }}">
                                    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-[0_12px_30px_rgba(15,23,42,0.08)] transition duration-500 ease-out group-hover:-translate-y-1 group-hover:shadow-[0_24px_40px_rgba(6,78,100,0.2)]">
                                        @if ($imagePath)
                                            <div class="relative overflow-hidden">
                                                <img
                                                    src="{{ $imagePath }}"
                                                    alt="{{ $item['title'] }}"
                                                    class="h-56 w-full object-cover transition duration-700 ease-out group-hover:scale-105 md:h-72"
                                                    loading="lazy"
                                                >
                                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/20 to-transparent opacity-60 transition duration-500 group-hover:opacity-40"></div>
                                            </div>
                                        @else
                                            <div class="grid h-56 place-items-center bg-slate-100 text-sm text-slate-500 md:h-72">
                                                Görsel eklenmedi
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="relative hidden lg:order-2 lg:flex lg:justify-center">
                                    <span class="inline-flex h-4 w-4 rounded-full border-4 border-cyan-100 bg-cyan-500 shadow-[0_0_0_6px_rgba(6,182,212,0.16)] transition duration-500 group-hover:scale-110 group-hover:bg-cyan-400"></span>
                                </div>

                                <div class="{{ $isLeft ? 'lg:order-3' : 'lg:order-1' }}">
                                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.08)] transition duration-500 ease-out group-hover:-translate-y-1 group-hover:border-cyan-200/90 group-hover:bg-cyan-700 group-hover:shadow-[0_24px_40px_rgba(6,78,100,0.24)] md:p-7">
                                        <h2 class="text-xl font-semibold text-slate-900 transition-colors duration-500 group-hover:text-white">{{ $item['title'] }}</h2>
                                        <p class="mt-3 text-sm leading-7 text-slate-600 transition-colors duration-500 group-hover:text-cyan-50 md:text-base">{{ $item['description'] }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @else
                <article class="card-ui">
                    <h2 class="text-2xl font-semibold text-slate-900">Hikayemiz</h2>
                    <p class="mt-4 text-slate-600">Bu sayfa içeriği admin panelinden düzenlenecektir.</p>
                </article>
            @endif
        </section>
    @elseif ($page->slug === 'baskanin-mesaji')
        @php
            $settings = \App\Models\Setting::current();
            $meta = is_array($page->page_meta ?? null) ? $page->page_meta : [];
            $presidentImage = ! empty($meta['president_image']) ? \Illuminate\Support\Facades\Storage::url($meta['president_image']) : asset('images/default-logo.svg');
            $signatureTitle = $meta['signature_title'] ?? 'YKD | Yeryüzü Kalkınma Derneği Başkanı';
            $signatureName = $meta['signature_name'] ?? 'Yasir FETEN';
            $socialMap = [
                'instagram_url' => 'instagram',
                'youtube_url' => 'youtube',
                'tiktok_url' => 'tiktok',
                'facebook_url' => 'facebook',
                'x_url' => 'x',
                'linkedin_url' => 'linkedin',
                'whatsapp_url' => 'whatsapp',
                'telegram_url' => 'telegram',
            ];
            $socialAria = [
                'instagram' => 'Instagram',
                'youtube' => 'YouTube',
                'tiktok' => 'TikTok',
                'facebook' => 'Facebook',
                'x' => 'X',
                'linkedin' => 'LinkedIn',
                'whatsapp' => 'WhatsApp',
                'telegram' => 'Telegram',
            ];
        @endphp
        <section class="mx-auto max-w-6xl px-4 py-12 md:px-6 lg:py-16">
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 md:text-4xl">{{ $page->title }}</h1>
            </div>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_34px_rgba(15,23,42,0.08)] md:p-8">
                <div class="items-start gap-8 md:grid md:grid-cols-[300px_1fr] lg:grid-cols-[380px_1fr]">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm">
                        <div class="aspect-[4/5] w-full">
                            <img src="{{ $presidentImage }}" alt="{{ $page->title }}" class="h-full w-full object-cover object-center">
                        </div>
                    </div>

                    <div>
                        <div class="prose max-w-none prose-slate prose-p:leading-8">
                            {!! $page->content ?: '<p>Bu sayfa içeriği admin panelden düzenlenecektir.</p>' !!}
                        </div>

                        <div class="mt-8">
                            <p class="text-lg font-semibold text-slate-900">{{ $signatureTitle }}</p>
                            <p class="mt-1 text-2xl font-bold text-slate-950">{{ $signatureName }}</p>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-2">
                            @foreach ($socialMap as $field => $platform)
                                @if (! empty($settings->$field))
                                    <a
                                        href="{{ $settings->$field }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-cyan-200 bg-cyan-50 text-cyan-700 transition duration-300 hover:-translate-y-0.5 hover:border-cyan-300 hover:bg-cyan-600 hover:text-white"
                                        title="{{ $socialAria[$platform] ?? $platform }}"
                                        aria-label="{{ $socialAria[$platform] ?? $platform }}"
                                    >
                                        <x-social-brand-icon :platform="$platform" icon-class="h-4 w-4" />
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </article>
        </section>
    @elseif ($page->slug === 'resmi-bilgiler' || $page->slug === 'resmi-belgiler')
        @php
            $settings = $siteSettings ?? \App\Models\Setting::current();
            $accounts = collect($bankAccounts ?? []);
            $meta = is_array($page->page_meta ?? null) ? $page->page_meta : [];
            $mapsEmbedUrl = $meta['maps_embed_url'] ?? null;
            $donationPageUrl = $meta['donation_page_url'] ?? route('donations');
            $normalizedMapsUrl = null;
            if (filled($mapsEmbedUrl)) {
                $mapsEmbedUrl = trim((string) $mapsEmbedUrl);
                $normalizedMapsUrl = \Illuminate\Support\Str::contains($mapsEmbedUrl, ['/maps/embed', 'output=embed'])
                    ? $mapsEmbedUrl
                    : 'https://www.google.com/maps?q=' . urlencode($mapsEmbedUrl) . '&output=embed';
            }
            $socialMap = [
                'instagram_url' => 'instagram',
                'youtube_url' => 'youtube',
                'tiktok_url' => 'tiktok',
                'facebook_url' => 'facebook',
                'x_url' => 'x',
                'linkedin_url' => 'linkedin',
                'whatsapp_url' => 'whatsapp',
                'telegram_url' => 'telegram',
            ];
            $socialAria = [
                'instagram' => 'Instagram',
                'youtube' => 'YouTube',
                'tiktok' => 'TikTok',
                'facebook' => 'Facebook',
                'x' => 'X',
                'linkedin' => 'LinkedIn',
                'whatsapp' => 'WhatsApp',
                'telegram' => 'Telegram',
            ];
        @endphp

        <section
            class="relative isolate overflow-hidden py-10 text-white md:py-12"
            style="background-color:#0b5f79;background-image:linear-gradient(rgba(7,77,98,.86),rgba(7,77,98,.86)),url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22240%22 height=%22120%22 viewBox=%220 0 240 120%22%3E%3Cg fill=%22none%22 stroke=%22%23ffffff%22 stroke-opacity=%220.07%22 stroke-width=%222%22%3E%3Cpath d=%22M0 24h240M0 60h240M0 96h240%22/%3E%3Cpath d=%22M30 0v120M90 0v120M150 0v120M210 0v120%22/%3E%3C/g%3E%3C/svg%3E');background-size:cover,240px 120px;background-position:center,center;"
        >
            <div class="relative mx-auto max-w-7xl px-4 text-center md:px-6">
                <h1 class="text-3xl font-extrabold tracking-tight md:text-4xl">{{ $page->title }}</h1>
                <div class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-cyan-100">
                    <a href="{{ route('home') }}" class="transition hover:text-white">Anasayfa</a>
                    <span aria-hidden="true">›</span>
                    <span class="text-white">{{ $page->title }}</span>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 md:px-6 lg:py-14">
            <h2 class="mb-8 text-center text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">
                {{ $settings->site_title ?? 'Birlikte Kardeşlik Derneği' }}
            </h2>

            <div class="mx-auto grid max-w-6xl gap-6 md:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-7">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-cyan-800">Dernek Kimliği</h3>
                        <span class="text-2xl font-bold text-slate-200">01</span>
                    </div>
                    <p class="text-base leading-8 text-slate-600">
                        {{ $settings->site_description ?: 'Kurumsal kimlik ve resmi bilgiler bu alanda paylaşılır.' }}
                    </p>
                </article>

                @foreach ($accounts as $index => $account)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md md:p-7">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-xl font-bold text-cyan-800">{{ $account->currency }} Bağış Hesabı</h3>
                            <span class="text-2xl font-bold text-slate-200">{{ str_pad((string) ($index + 2), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="space-y-1.5 text-base text-slate-700">
                            <p><span class="font-semibold">Banka:</span> {{ $account->bank_name }}</p>
                            <p><span class="font-semibold">Hesap Adı:</span> {{ $account->recipient_name }}</p>
                            @if (filled($account->account_number))
                                <p><span class="font-semibold">Hesap No:</span> {{ $account->account_number }}</p>
                            @endif
                            <p class="break-all"><span class="font-semibold">IBAN:</span> {{ $account->iban }}</p>
                        </div>
                        @if (filled($account->qr_image))
                            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-2">
                                <img src="{{ asset('storage/' . $account->qr_image) }}" alt="{{ $account->bank_name }} QR" class="mx-auto h-28 w-28 object-contain">
                            </div>
                        @endif
                        @if (filled($donationPageUrl))
                            <div class="mt-4">
                                <a href="{{ $donationPageUrl }}" target="_blank" class="inline-flex items-center rounded-full bg-cyan-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-cyan-800">
                                    Bağışa Git
                                </a>
                            </div>
                        @endif
                    </article>
                @endforeach

                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-7">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-cyan-800">Sosyal Medya Hesaplarımız</h3>
                        <span class="text-2xl font-bold text-slate-200">{{ str_pad((string) ($accounts->count() + 2), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($socialMap as $field => $platform)
                            @if (! empty($settings->$field))
                                <a
                                    href="{{ $settings->$field }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-cyan-200 bg-cyan-50 text-cyan-700 transition hover:-translate-y-0.5 hover:bg-cyan-600 hover:text-white"
                                    title="{{ $socialAria[$platform] ?? $platform }}"
                                    aria-label="{{ $socialAria[$platform] ?? $platform }}"
                                >
                                    <x-social-brand-icon :platform="$platform" icon-class="h-4 w-4" />
                                </a>
                            @endif
                        @endforeach
                    </div>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-7">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-cyan-800">Bizi Ziyaret Edin</h3>
                        <span class="text-2xl font-bold text-slate-200">{{ str_pad((string) ($accounts->count() + 3), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    @if (filled($normalizedMapsUrl))
                        <div class="overflow-hidden rounded-xl border border-slate-200">
                            <iframe
                                src="{{ $normalizedMapsUrl }}"
                                class="h-64 w-full"
                                style="border:0;"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen
                            ></iframe>
                        </div>
                    @else
                        <p class="text-sm leading-7 text-slate-600">Google Maps bağlantısı admin panelden eklendiğinde bu alanda harita görünecektir.</p>
                    @endif
                </article>
            </div>
        </section>
    @elseif ($page->slug === 'basin-kiti')
        @php
            $meta = is_array($page->page_meta ?? null) ? $page->page_meta : [];
            $items = collect($meta['press_kit_items'] ?? [])
                ->filter(fn ($item) => filled($item['title'] ?? null) && filled($item['file'] ?? null))
                ->values();
            $siteSettings = \App\Models\Setting::current();
            $defaultLogo = $siteSettings->logo ? asset('storage/' . $siteSettings->logo) : asset('images/default-logo.svg');
        @endphp

        <section
            class="relative isolate overflow-hidden py-10 text-white md:py-12"
            style="background-color:#0b5f79;background-image:linear-gradient(rgba(7,77,98,.86),rgba(7,77,98,.86)),url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22240%22 height=%22120%22 viewBox=%220 0 240 120%22%3E%3Cg fill=%22none%22 stroke=%22%23ffffff%22 stroke-opacity=%220.07%22 stroke-width=%222%22%3E%3Cpath d=%22M0 24h240M0 60h240M0 96h240%22/%3E%3Cpath d=%22M30 0v120M90 0v120M150 0v120M210 0v120%22/%3E%3C/g%3E%3C/svg%3E');background-size:cover,240px 120px;background-position:center,center;"
        >
            <div class="relative mx-auto max-w-7xl px-4 text-center md:px-6">
                <h1 class="text-3xl font-extrabold tracking-tight md:text-4xl">{{ $page->title }}</h1>
                <div class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-cyan-100">
                    <a href="{{ route('home') }}" class="transition hover:text-white">Anasayfa</a>
                    <span aria-hidden="true">›</span>
                    <span class="text-white">{{ $page->title }}</span>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 md:px-6 lg:py-14">
            @if (! empty($page->content))
                <div class="mx-auto mb-8 max-w-5xl rounded-2xl border border-slate-200 bg-white px-5 py-4 text-center text-sm leading-7 text-slate-600 shadow-sm md:text-base">
                    {!! $page->content !!}
                </div>
            @endif

            @if ($items->isNotEmpty())
                <div class="mx-auto grid max-w-6xl gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $item)
                        @php
                            $filePath = (string) $item['file'];
                            $fileUrl = asset('storage/' . ltrim($filePath, '/'));
                            $fileExt = strtoupper(pathinfo($filePath, PATHINFO_EXTENSION) ?: 'DOSYA');
                            $formatLabel = filled($item['format_label'] ?? null) ? strtoupper((string) $item['format_label']) : $fileExt;
                            $logo = ! empty($item['logo']) ? asset('storage/' . ltrim((string) $item['logo'], '/')) : $defaultLogo;
                        @endphp
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <img src="{{ $logo }}" alt="{{ $item['title'] }}" class="mx-auto h-16 w-auto object-contain">
                            <p class="mt-4 text-sm font-medium text-slate-500">Dosya Formatı</p>
                            <p class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900">"{{ $formatLabel }}"</p>
                            <div class="my-5 h-px bg-slate-200"></div>
                            <a
                                href="{{ $fileUrl }}"
                                download
                                class="group inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:border-cyan-500 hover:bg-cyan-500 hover:text-white hover:shadow-[0_10px_20px_rgba(6,182,212,0.35)]"
                            >
                                İndir
                                <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-y-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10 2.5a.75.75 0 0 1 .75.75v7.69l2.22-2.22a.75.75 0 1 1 1.06 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 1 1 1.06-1.06l2.22 2.22V3.25A.75.75 0 0 1 10 2.5Z"/>
                                    <path d="M3.5 14.25a.75.75 0 0 1 .75.75v.5a1.25 1.25 0 0 0 1.25 1.25h9a1.25 1.25 0 0 0 1.25-1.25V15a.75.75 0 0 1 1.5 0v.5A2.75 2.75 0 0 1 14.5 18.25h-9A2.75 2.75 0 0 1 2.75 15.5V15a.75.75 0 0 1 .75-.75Z"/>
                                </svg>
                            </a>
                        </article>
                    @endforeach
                </div>
            @else
                <article class="card-ui">
                    <h2 class="text-2xl font-semibold text-slate-900">Basın Kiti</h2>
                    <p class="mt-4 text-slate-600">Bu sayfa içeriği ve dosyaları admin panelde Basın Kiti Sayfası Alanları bölümünden düzenlenecektir.</p>
                </article>
            @endif
        </section>
    @elseif ($page->slug === 'yonetim')
        @php
            $meta = is_array($page->page_meta ?? null) ? $page->page_meta : [];
            $sections = collect($meta['management_sections'] ?? [])
                ->filter(fn ($section) => filled($section['section_title'] ?? null))
                ->values();
        @endphp

        <section
            class="relative isolate overflow-hidden py-12 text-white shadow-inner md:py-14"
            style="background-color:#0b5f79;background-image:linear-gradient(rgba(7,77,98,.86),rgba(7,77,98,.86)),url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22240%22 height=%22120%22 viewBox=%220 0 240 120%22%3E%3Cg fill=%22none%22 stroke=%22%23ffffff%22 stroke-opacity=%220.07%22 stroke-width=%222%22%3E%3Cpath d=%22M0 24h240M0 60h240M0 96h240%22/%3E%3Cpath d=%22M30 0v120M90 0v120M150 0v120M210 0v120%22/%3E%3C/g%3E%3C/svg%3E');background-size:cover,240px 120px;background-position:center,center;"
        >
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-950/30 via-transparent to-cyan-950/30"></div>
            <div class="relative mx-auto max-w-7xl px-4 text-center md:px-6">
                <h1 class="text-3xl font-extrabold tracking-tight drop-shadow-sm md:text-4xl">{{ $page->title }}</h1>
                <div class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-cyan-100">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 2.5 2.5 8.2v9.3h5.1v-4.8h4.8v4.8h5.1V8.2L10 2.5Z"/>
                    </svg>
                    <a href="{{ route('home') }}" class="transition hover:text-white">Anasayfa</a>
                    <span aria-hidden="true">›</span>
                    <span class="text-white">{{ $page->title }}</span>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 md:px-6 lg:py-14">
            @if (! empty($page->content))
                <div class="prose mx-auto mb-10 max-w-3xl text-center prose-slate">{!! $page->content !!}</div>
            @endif

            @if ($sections->isNotEmpty())
                <div class="space-y-14 md:space-y-16">
                    @foreach ($sections as $section)
                        @php
                            $members = collect($section['members'] ?? [])
                                ->filter(fn ($member) => filled($member['name'] ?? null) && filled($member['role'] ?? null))
                                ->values();
                        @endphp

                        @if ($members->isNotEmpty())
                            <div>
                                <h2 class="mb-6 text-center text-2xl font-extrabold uppercase tracking-wide text-cyan-950 md:mb-8 md:text-3xl">
                                    {{ $section['section_title'] }}
                                </h2>

                                <div class="mx-auto flex max-w-6xl flex-wrap justify-center gap-5">
                                    @foreach ($members as $member)
                                        @php
                                            $memberPhoto = ! empty($member['photo']) ? \Illuminate\Support\Facades\Storage::url($member['photo']) : null;
                                        @endphp
                                        <article class="group w-full sm:w-[calc(50%-10px)] lg:w-[calc(33.333%-14px)] xl:w-[calc(25%-15px)] max-w-[280px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_12px_28px_rgba(15,23,42,0.08)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_36px_rgba(6,78,100,0.2)]">
                                            <div class="aspect-[3/4] w-full overflow-hidden bg-slate-100">
                                                @if ($memberPhoto)
                                                    <img
                                                        src="{{ $memberPhoto }}"
                                                        alt="{{ $member['name'] }}"
                                                        class="h-full w-full object-cover object-center transition duration-500 group-hover:scale-105"
                                                        loading="lazy"
                                                    >
                                                @else
                                                    <div class="relative flex h-full w-full items-center justify-center overflow-hidden bg-gradient-to-br from-slate-100 via-slate-200 to-cyan-100/70">
                                                        <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-cyan-200/40 blur-2xl"></div>
                                                        <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-slate-300/40 blur-2xl"></div>
                                                        <div class="relative flex h-24 w-24 items-center justify-center rounded-full border border-white/80 bg-white/70 shadow-lg backdrop-blur-sm">
                                                            <svg class="h-12 w-12 text-cyan-700/70" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/>
                                                            </svg>
                                                        </div>
                                                        <p class="absolute bottom-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Fotoğraf eklenecek</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $member['role'] }}</p>
                                                <h3 class="mt-1 text-lg font-bold text-slate-900">{{ $member['name'] }}</h3>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <article class="card-ui">
                    <h2 class="text-2xl font-semibold text-slate-900">Yönetim</h2>
                    <p class="mt-4 text-slate-600">Bu sayfayı admin panelde Yönetim Sayfası Alanları bölümünden oluşturabilirsiniz.</p>
                </article>
            @endif
        </section>
    @else
        <section class="mx-auto max-w-4xl px-4 py-12 md:px-6">
            <article class="card-ui">
                <h1 class="text-3xl font-bold text-slate-900">{{ $page->title }}</h1>
                <div class="prose mt-6 max-w-none prose-slate">{!! $page->content !!}</div>
            </article>
        </section>
    @endif
</x-layouts.app>
