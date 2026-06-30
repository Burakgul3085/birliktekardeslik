@php
    $logoSrc = $siteSettings->logo ? asset('storage/' . $siteSettings->logo) : asset('images/default-logo.svg');
    $siteTitle = $siteSettings->site_title ?? 'Birlikte Kardeşlik Derneği';
    $description = trim((string) ($donation->description ?? ''));
    $descriptionLong = strlen($description) > 180;
@endphp

<x-layouts.app>
    <section class="py-12 md:py-20 bg-gradient-to-b from-[#f0fdfa] via-[#f4fbfc] to-white min-h-[70vh]">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                {{-- Üst: marka + doğrulama --}}
                <div class="bg-gradient-to-br from-teal-600 via-teal-600 to-cyan-600 px-6 py-8 md:px-10 md:py-10 text-white">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                        <img
                            src="{{ $logoSrc }}"
                            alt="{{ $siteTitle }}"
                            class="h-16 w-16 shrink-0 rounded-full object-cover ring-4 ring-white/25 shadow-lg"
                        >
                        <div class="flex-1 min-w-0">
                            <p class="text-teal-100 text-sm font-medium tracking-wide uppercase">{{ $siteTitle }}</p>
                            <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-sm font-semibold backdrop-blur-sm">
                                <svg class="h-5 w-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Makbuz Doğrulandı
                            </div>
                            <h1 class="mt-3 text-xl md:text-2xl font-bold leading-tight">Bağış makbuzu sistemde kayıtlıdır</h1>
                            <p class="mt-2 text-sm text-teal-50/90 max-w-xl">
                                Bu sayfa, taranan QR kodunun derneğimiz kayıtlarında geçerli bir makbuza ait olduğunu gösterir.
                            </p>
                            <p class="mt-3 text-xs text-teal-100/80">
                                Doğrulama zamanı: {{ now()->format('d.m.Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-8 lg:p-10 space-y-8">
                    {{-- Tutar vurgusu --}}
                    <div class="rounded-2xl border border-teal-100 bg-gradient-to-br from-teal-50 to-cyan-50 px-6 py-8 text-center shadow-sm">
                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Bağış Tutarı</p>
                        <p class="mt-2 text-4xl md:text-5xl font-bold text-teal-700 tabular-nums">
                            {{ number_format((float) $donation->amount, 2, ',', '.') }}
                            <span class="text-2xl md:text-3xl font-semibold text-teal-600">{{ $donation->currency }}</span>
                        </p>
                        <p class="mt-3 text-sm text-slate-600">
                            Bağış tarihi:
                            <span class="font-semibold text-slate-800">{{ $donation->donated_at?->format('d.m.Y') ?? '-' }}</span>
                        </p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Bağış bilgileri --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-5 md:p-6">
                            <h2 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-slate-500 mb-4">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-teal-100 text-teal-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                Bağış Bilgileri
                            </h2>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-3">
                                    <dt class="text-slate-500 shrink-0">Bağış No</dt>
                                    <dd class="font-semibold text-slate-900 text-right">{{ $donation->donation_number }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-3">
                                    <dt class="text-slate-500 shrink-0">Makbuz No</dt>
                                    <dd class="font-semibold text-slate-900 text-right">{{ $donation->receipt_number ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-3">
                                    <dt class="text-slate-500 shrink-0">Bağışçı</dt>
                                    <dd class="font-semibold text-slate-900 text-right">{{ $donation->donor?->full_name ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-3">
                                    <dt class="text-slate-500 shrink-0">Bağış Türü</dt>
                                    <dd class="font-semibold text-slate-900 text-right">{{ $donation->donationType?->name ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-3">
                                    <dt class="text-slate-500 shrink-0">Ödeme Türü</dt>
                                    <dd class="font-semibold text-slate-900 text-right">{{ $donation->paymentMethod?->name ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-500 shrink-0">Proje / Faaliyet</dt>
                                    <dd class="font-semibold text-slate-900 text-right">{{ $donation->project?->title ?? '-' }}</dd>
                                </div>
                            </dl>

                            @if ($description !== '')
                                <div class="mt-5 rounded-xl border border-slate-200 bg-white p-4" x-data="{ expanded: false }">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Açıklama</p>
                                    <p
                                        class="text-sm text-slate-700 leading-relaxed whitespace-pre-line"
                                        :class="expanded ? '' : 'line-clamp-4'"
                                    >{{ $description }}</p>
                                    @if ($descriptionLong)
                                        <button
                                            type="button"
                                            @click="expanded = !expanded"
                                            class="mt-2 text-sm font-semibold text-teal-600 hover:text-teal-700 transition"
                                            x-text="expanded ? 'Daha az göster' : 'Devamını gör'"
                                        ></button>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Belge bilgileri --}}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-5 md:p-6">
                            <h2 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-slate-500 mb-4">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-teal-100 text-teal-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </span>
                                Belge Bilgileri
                            </h2>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between gap-4 border-b border-slate-200/80 pb-3">
                                    <dt class="text-slate-500 shrink-0">Belge Türü</dt>
                                    <dd class="font-semibold text-slate-900 text-right">{{ $document->type_label }}</dd>
                                </div>
                                <div class="border-b border-slate-200/80 pb-3">
                                    <dt class="text-slate-500 mb-2">Doğrulama Kodu</dt>
                                    <dd class="flex items-center gap-2">
                                        <code class="flex-1 rounded-lg bg-white border border-slate-200 px-3 py-2 font-mono text-xs font-semibold text-slate-800 break-all">{{ $document->verification_code }}</code>
                                        <button
                                            type="button"
                                            onclick="navigator.clipboard.writeText('{{ $document->verification_code }}'); this.querySelector('[data-label]').textContent = 'Kopyalandı'; setTimeout(() => this.querySelector('[data-label]').textContent = 'Kopyala', 2000)"
                                            class="shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:border-teal-300 hover:text-teal-700 transition"
                                        >
                                            <span data-label>Kopyala</span>
                                        </button>
                                    </dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-500 shrink-0">Oluşturulma</dt>
                                    <dd class="font-semibold text-slate-900 text-right">{{ $document->generated_at?->format('d.m.Y H:i') ?? '-' }}</dd>
                                </div>
                            </dl>

                            <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50/80 p-4">
                                <p class="text-xs leading-relaxed text-emerald-800">
                                    Bu makbuz dijital olarak kayıt altına alınmıştır. Şüpheli bir durumda derneğimizle iletişime geçebilirsiniz.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Aksiyonlar --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 pt-2">
                        <a
                            href="{{ $document->public_download_url }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-6 py-3 text-sm font-semibold text-white shadow-md shadow-teal-600/20 hover:bg-teal-700 transition"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF İndir
                        </a>
                        <a
                            href="{{ route('home') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-800 hover:border-slate-300 hover:bg-slate-50 transition"
                        >
                            Ana Sayfaya Dön
                        </a>
                    </div>
                </div>

                {{-- İletişim alt şerit --}}
                @if (filled($siteSettings->phone) || filled($siteSettings->email))
                    <div class="border-t border-slate-100 bg-slate-50 px-6 py-5 md:px-10">
                        <p class="text-center text-xs text-slate-500 mb-2">Bu sayfa yalnızca makbuz doğrulama amacıyla kullanılır.</p>
                        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-slate-600">
                            @if (filled($siteSettings->phone))
                                <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="inline-flex items-center gap-1.5 hover:text-teal-700 transition">
                                    <svg class="h-4 w-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $siteSettings->phone }}
                                </a>
                            @endif
                            @if (filled($siteSettings->email))
                                <a href="mailto:{{ $siteSettings->email }}" class="inline-flex items-center gap-1.5 hover:text-teal-700 transition">
                                    <svg class="h-4 w-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ $siteSettings->email }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.app>
