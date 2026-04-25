<x-layouts.app>
    <x-page-hero :title="$page->title" />

    @if ($page->slug === 'hikayemiz')
        <section class="mx-auto max-w-7xl px-4 py-12 md:px-6 lg:py-16">
            @if (! empty($page->content))
                <div class="prose mx-auto mb-10 max-w-3xl text-center text-slate-600 prose-slate">{!! $page->content !!}</div>
            @endif

            @php
                $storyItems = collect($page->story_items ?? [])->filter(fn ($item) => filled($item['title'] ?? null) && filled($item['description'] ?? null));
            @endphp

            @if ($storyItems->isNotEmpty())
                <div class="relative mx-auto max-w-6xl">
                    <div class="story-timeline-line z-0 hidden lg:block" aria-hidden="true"></div>

                    <div class="space-y-12 md:space-y-14">
                        @foreach ($storyItems as $index => $item)
                            @php
                                $isLeft = $index % 2 === 0; // çift: görsel sol, metin sağ; tek: metin sol, görsel sağ
                                $textOnRight = $isLeft;
                                $imagePath = ! empty($item['image']) ? \Illuminate\Support\Facades\Storage::url($item['image']) : null;
                                $imgOrder = $isLeft ? 'order-1 lg:order-1' : 'order-2 lg:order-3';
                                $textOrder = $isLeft ? 'order-2 lg:order-3' : 'order-1 lg:order-1';
                            @endphp

                            <article class="group/story relative z-10 flex flex-col gap-5 lg:grid lg:grid-cols-[1fr_2.5rem_1fr] lg:items-start lg:gap-0 lg:px-0">
                                {{-- Görsel sütun --}}
                                <div class="{{ $imgOrder }}">
                                    <div
                                        class="h-full overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-[0_10px_32px_rgba(15,23,42,0.07)] transition-all duration-500 ease-out group-hover/story:shadow-[0_18px_34px_rgba(14,116,144,0.14)]"
                                    >
                                        @if ($imagePath)
                                            <div class="relative h-[240px] w-full overflow-hidden bg-white/5 p-2 sm:h-[260px] lg:h-[280px]">
                                                <img
                                                    src="{{ $imagePath }}"
                                                    alt="{{ $item['title'] }}"
                                                    class="h-full w-full object-contain transition-transform duration-700 ease-out group-hover/story:scale-[1.02]"
                                                    loading="lazy"
                                                >
                                            </div>
                                        @else
                                            <div class="grid h-[240px] place-items-center bg-slate-100 text-sm text-slate-500 sm:h-[260px] lg:h-[280px]">
                                                Görsel eklenmedi
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Zaman noktası (masaüstü) --}}
                                <div class="pointer-events-none relative z-20 hidden w-8 shrink-0 items-start justify-center pt-8 lg:flex lg:order-2" aria-hidden="true">
                                    <span class="story-dot transition-all duration-500 group-hover/story:scale-110 group-hover/story:bg-cyan-400 group-hover/story:ring-cyan-400/35"></span>
                                </div>

                                {{-- Metin kartı: sadece bu kutu hover (arka plan + metin rengi) --}}
                                <div class="{{ $textOrder }} flex items-start">
                                    <div
                                        @class([
                                            'group/storytext relative w-full rounded-2xl border border-slate-200/95 bg-white p-5 shadow-[0_12px_30px_rgba(15,23,42,0.10)]',
                                            'transition-all duration-500 ease-out md:p-7',
                                            'hover:-translate-y-1',
                                            'hover:border-cyan-300/80',
                                            'hover:shadow-[0_18px_38px_rgba(6,120,150,0.14)]',
                                        ])
                                    >
                                        @if ($textOnRight)
                                            <span class="story-link-line-left"></span>
                                        @else
                                            <span class="story-link-line-right"></span>
                                        @endif
                                        @if ($textOnRight)
                                            <span
                                                class="story-text-pointer--to-left hidden transition-colors duration-500 group-hover/storytext:border-r-cyan-100 lg:block"
                                                aria-hidden="true"
                                            ></span>
                                        @else
                                            <span
                                                class="story-text-pointer--to-right hidden transition-colors duration-500 group-hover/storytext:border-l-cyan-100 lg:block"
                                                aria-hidden="true"
                                            ></span>
                                        @endif
                                        <h2
                                            class="text-lg font-bold text-cyan-900 transition-colors duration-500 group-hover/storytext:text-cyan-700 md:text-xl"
                                        >
                                            {{ $item['title'] }}
                                        </h2>
                                        <p
                                            class="mt-3 text-sm leading-relaxed text-slate-600 transition-colors duration-500 group-hover/storytext:text-cyan-800 md:text-base"
                                        >
                                            {{ $item['description'] }}
                                        </p>
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
    @elseif ($page->slug === 'hakkimizda')
        @php
            $settings = \App\Models\Setting::current();
            $meta = is_array($page->page_meta ?? null) ? $page->page_meta : [];
            $aboutImage = ! empty($meta['about_image']) ? \Illuminate\Support\Facades\Storage::url($meta['about_image']) : asset('images/default-logo.svg');
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
        <section class="mx-auto max-w-5xl px-4 py-12 md:px-6 lg:py-16">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_34px_rgba(15,23,42,0.08)] md:p-8">
                <div class="mx-auto max-w-2xl">
                    <div class="mx-auto w-full max-w-md rounded-2xl border border-slate-200 bg-white/10 p-2 shadow-sm backdrop-blur-sm">
                        <div class="rounded-xl bg-white/5 p-2">
                            <img
                                src="{{ $aboutImage }}"
                                alt="{{ $page->title }}"
                                class="mx-auto block h-auto max-h-[520px] w-full rounded-lg object-contain"
                            >
                        </div>
                    </div>

                    <div class="group mt-8 rounded-2xl border border-slate-200 bg-slate-50/60 p-5 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-cyan-300 hover:bg-cyan-50/70 hover:shadow-[0_16px_30px_rgba(14,116,144,0.14)] md:p-6">
                        <h2 class="mb-3 text-center text-xl font-bold text-cyan-900 transition-colors duration-300 group-hover:text-cyan-700 md:text-2xl">Hakkımızda</h2>
                        <div class="prose mx-auto max-w-none text-center prose-slate prose-p:leading-8 prose-p:transition-colors prose-p:duration-300 group-hover:prose-p:text-cyan-900">
                            {!! $page->content ?: '<p>Birlikte Kardeşlik Derneği; yardımlaşma, dayanışma ve sosyal sorumluluk bilinciyle faaliyet gösteren bir sivil toplum oluşumudur.</p>' !!}
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
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
            </article>
        </section>
    @elseif ($page->slug === 'vizyon-misyon')
        @php
            $settings = \App\Models\Setting::current();
            $meta = is_array($page->page_meta ?? null) ? $page->page_meta : [];
            $visionText = $meta['vision_text'] ?? null;
            $missionText = $meta['mission_text'] ?? null;
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
        <section class="mx-auto max-w-4xl px-4 py-12 md:px-6 lg:py-16">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_34px_rgba(15,23,42,0.08)] md:p-8">
                <div class="space-y-5">
                    <div class="group rounded-2xl border border-slate-200 bg-slate-50/60 p-5 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-cyan-300 hover:bg-cyan-50/70 hover:shadow-[0_16px_30px_rgba(14,116,144,0.14)] md:p-6">
                        <h2 class="mb-3 text-center text-xl font-bold text-cyan-900 transition-colors duration-300 group-hover:text-cyan-700 md:text-2xl">Vizyonumuz</h2>
                        <div class="prose mx-auto max-w-none text-center prose-slate prose-p:leading-8 prose-p:transition-colors prose-p:duration-300 group-hover:prose-p:text-cyan-900">
                            {!! $visionText ?: '<p>Vizyon metni admin panelde Vizyon Misyon Sayfası Alanları bölümünden girilecektir.</p>' !!}
                        </div>
                    </div>

                    <div class="group rounded-2xl border border-slate-200 bg-slate-50/60 p-5 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-cyan-300 hover:bg-cyan-50/70 hover:shadow-[0_16px_30px_rgba(14,116,144,0.14)] md:p-6">
                        <h2 class="mb-3 text-center text-xl font-bold text-cyan-900 transition-colors duration-300 group-hover:text-cyan-700 md:text-2xl">Misyonumuz</h2>
                        <div class="prose mx-auto max-w-none text-center prose-slate prose-p:leading-8 prose-p:transition-colors prose-p:duration-300 group-hover:prose-p:text-cyan-900">
                            {!! $missionText ?: '<p>Misyon metni admin panelde Vizyon Misyon Sayfası Alanları bölümünden girilecektir.</p>' !!}
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
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
            </article>
        </section>
    @elseif (in_array($page->slug, ['dernek-tuzugu', 'faaliyet-belgesi', 'kurumsal-evrak-arsivi'], true))
        @php
            $meta = is_array($page->page_meta ?? null) ? $page->page_meta : [];
            $documentConfig = [
                'dernek-tuzugu' => [
                    'legacy_file_key' => 'charter_file',
                    'legacy_title_key' => 'charter_title',
                    'default_title' => 'Dernek Tüzüğü',
                    'empty_text' => 'Dernek tüzüğü henüz yüklenmedi. Admin panelden PDF, JPG, PNG, DOC veya DOCX formatında belge yükleyebilirsiniz.',
                ],
                'faaliyet-belgesi' => [
                    'legacy_file_key' => 'activity_doc_file',
                    'legacy_title_key' => 'activity_doc_title',
                    'default_title' => 'Faaliyet Belgesi',
                    'empty_text' => 'Faaliyet belgesi henüz yüklenmedi. Admin panelden PDF, JPG, PNG, DOC veya DOCX formatında belge yükleyebilirsiniz.',
                ],
                'kurumsal-evrak-arsivi' => [
                    'legacy_file_key' => 'archive_doc_file',
                    'legacy_title_key' => 'archive_doc_title',
                    'default_title' => 'Kurumsal Evrak Arşivi',
                    'empty_text' => 'Kurumsal evrak arşivi dosyası henüz yüklenmedi. Admin panelden PDF, JPG, PNG, DOC veya DOCX formatında belge yükleyebilirsiniz.',
                ],
            ];
            $currentDoc = $documentConfig[$page->slug] ?? $documentConfig['dernek-tuzugu'];
            $documentFile = $meta['document_file'] ?? ($meta[$currentDoc['legacy_file_key']] ?? null);
            $documentUrl = filled($documentFile) ? \Illuminate\Support\Facades\Storage::url($documentFile) : null;
            $documentTitle = trim((string) ($meta['document_title'] ?? ($meta[$currentDoc['legacy_title_key']] ?? ''))) ?: $currentDoc['default_title'];
            $documentExt = $documentFile ? strtolower(pathinfo((string) $documentFile, PATHINFO_EXTENSION)) : null;
            $isImagePreview = in_array($documentExt, ['jpg', 'jpeg', 'png'], true);
            $isPdfPreview = $documentExt === 'pdf';
        @endphp
        <section class="mx-auto max-w-5xl px-4 py-12 md:px-6 lg:py-16">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_34px_rgba(15,23,42,0.08)] md:p-8">
                @if (! empty($page->content))
                    <div class="prose mx-auto mb-6 max-w-none text-center prose-slate prose-p:leading-8">
                        {!! $page->content !!}
                    </div>
                @endif

                @if ($documentUrl)
                    <div class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-slate-50/60 p-4 md:p-6">
                        <h2 class="text-center text-xl font-bold text-cyan-900 md:text-2xl">{{ $documentTitle }}</h2>

                        @if ($isPdfPreview)
                            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                <iframe
                                    src="{{ $documentUrl }}"
                                    class="h-[560px] w-full"
                                    style="border:0;"
                                    loading="lazy"
                                    title="{{ $documentTitle }}"
                                ></iframe>
                            </div>
                        @elseif ($isImagePreview)
                            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                                <img
                                    src="{{ $documentUrl }}"
                                    alt="{{ $documentTitle }}"
                                    class="mx-auto block h-auto max-h-[620px] w-full rounded-lg object-contain"
                                >
                            </div>
                        @else
                            <div class="mt-4 rounded-xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                                <p class="text-sm leading-7 text-slate-600">
                                    Bu dosya türü tarayıcı içinde önizlenemeyebilir. Belgeyi yeni sekmede açabilir veya indirebilirsiniz.
                                </p>
                            </div>
                        @endif

                        <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                            <a
                                href="{{ $documentUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700"
                            >
                                Belgeyi Yeni Sekmede Aç
                            </a>
                            <a
                                href="{{ $documentUrl }}"
                                download
                                class="inline-flex items-center rounded-full border border-cyan-200 bg-cyan-50 px-5 py-2.5 text-sm font-semibold text-cyan-800 transition hover:border-cyan-300 hover:bg-cyan-100"
                            >
                                Belgeyi İndir
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-center text-sm text-slate-600">
                        {{ $currentDoc['empty_text'] }}
                    </p>
                @endif
            </article>
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
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_34px_rgba(15,23,42,0.08)] md:p-8">
                <div class="items-start gap-8 md:grid md:grid-cols-[300px_1fr] lg:grid-cols-[380px_1fr]">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white/10 p-2 shadow-sm backdrop-blur-sm">
                        <div class="h-[300px] w-full rounded-xl bg-white/5 p-2 sm:h-[360px] lg:h-[420px]">
                            <img src="{{ $presidentImage }}" alt="{{ $page->title }}" class="h-full w-full object-contain">
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
            $mapsDirectUrl = null;
            $mapsNeedsExternalOpen = false;
            if (filled($mapsEmbedUrl)) {
                $mapsEmbedUrl = trim((string) $mapsEmbedUrl);
                $mapsDirectUrl = $mapsEmbedUrl;
                if (\Illuminate\Support\Str::contains($mapsEmbedUrl, ['maps.app.goo.gl', 'goo.gl/maps'])) {
                    if (filled($settings->address)) {
                        $normalizedMapsUrl = 'https://www.google.com/maps?q=' . urlencode((string) $settings->address) . '&output=embed';
                    } else {
                        $mapsNeedsExternalOpen = true;
                    }
                } else {
                    $normalizedMapsUrl = \Illuminate\Support\Str::contains($mapsEmbedUrl, ['/maps/embed', 'output=embed'])
                        ? $mapsEmbedUrl
                        : 'https://www.google.com/maps?q=' . urlencode($mapsEmbedUrl) . '&output=embed';
                }
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
                    @elseif ($mapsNeedsExternalOpen && filled($mapsDirectUrl))
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm leading-7 text-slate-600">
                                Kısa Google Maps bağlantıları gömülü haritada kısıtlanabilir. Haritayı yeni sekmede açabilirsiniz.
                            </p>
                            <a
                                href="{{ $mapsDirectUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-3 inline-flex items-center rounded-full bg-cyan-700 px-4 py-2 text-xs font-semibold text-white transition hover:bg-cyan-800"
                            >
                                Haritayı Aç
                            </a>
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
                                            $displayPhoto = $memberPhoto ?: asset('images/default-logo.svg');
                                        @endphp
                                        <article class="group w-full sm:w-[calc(50%-10px)] lg:w-[calc(33.333%-14px)] xl:w-[calc(25%-15px)] max-w-[280px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_12px_28px_rgba(15,23,42,0.08)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_36px_rgba(6,78,100,0.2)]">
                                            <div class="h-[280px] w-full overflow-hidden bg-white/10 p-2 sm:h-[300px] lg:h-[320px]">
                                                <img
                                                    src="{{ $displayPhoto }}"
                                                    alt="{{ $member['name'] }}"
                                                    class="h-full w-full object-contain transition duration-500 group-hover:scale-105"
                                                    loading="lazy"
                                                >
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
                <div class="prose mt-6 max-w-none prose-slate">{!! $page->content !!}</div>
            </article>
        </section>
    @endif
</x-layouts.app>
