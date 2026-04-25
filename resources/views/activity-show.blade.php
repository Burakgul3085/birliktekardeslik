<x-layouts.app>
    <x-page-hero :title="$activity->title" />

    @php
        $accordionItems = [
            [
                'title' => $activity->detail_item_1_title ?: 'Hızlı Müdahale',
                'text' => $activity->detail_item_1_text ?: 'Kriz anlarında hızlı müdahale ederek ihtiyaç sahiplerine sıcak yemek ve temel gıda desteği sağlıyoruz.',
            ],
            [
                'title' => $activity->detail_item_2_title ?: 'Uzun Vadeli Çözümler',
                'text' => $activity->detail_item_2_text ?: 'Bölgede kalıcı gıda güvenliği için sürdürülebilir dağıtım ve yerel işbirliği modelleri geliştiriyoruz.',
            ],
            [
                'title' => $activity->detail_item_3_title ?: 'Toplum Desteği',
                'text' => $activity->detail_item_3_text ?: 'Ailelerin düzenli beslenme ihtiyacına katkı sağlayan insani yardım faaliyetleri yürütüyoruz.',
            ],
        ];
    @endphp
    <section class="mx-auto max-w-7xl px-4 py-10 md:px-6">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-6">
                <img
                    src="{{ $activity->cover_image ? asset('storage/' . $activity->cover_image) : asset('images/default-logo.svg') }}"
                    alt="{{ $activity->title }}"
                    class="h-72 w-full rounded-xl object-cover md:h-96"
                >
                <h2 class="mt-6 text-3xl font-bold text-slate-900">{{ $activity->title }}</h2>
                <p class="mt-3 text-lg leading-relaxed text-slate-700">{{ $activity->description }}</p>
                <div class="prose prose-slate mt-6 max-w-none text-base leading-relaxed">
                    {!! $activity->content ?: nl2br(e((string) $activity->description)) !!}
                </div>

                <div class="mt-8 space-y-3" x-data="{ openIdx: 1 }">
                    @foreach($accordionItems as $idx => $acc)
                        <div class="overflow-hidden rounded-2xl border border-cyan-200/80 bg-white">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between px-5 py-4 text-left"
                                @click="openIdx = openIdx === {{ $idx }} ? -1 : {{ $idx }}"
                            >
                                <span class="text-xl font-semibold text-slate-900">{{ $acc['title'] }}</span>
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-cyan-500 text-white text-lg"
                                      x-text="openIdx === {{ $idx }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="openIdx === {{ $idx }}" x-collapse class="border-t border-cyan-100 px-5 py-4">
                                <p class="text-base leading-relaxed text-slate-700">{{ $acc['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <aside class="space-y-4">
                @foreach($bankAccounts as $account)
                    <a href="{{ route('donations') }}" class="block rounded-2xl border border-cyan-100 bg-cyan-50/40 p-4 shadow-sm transition hover:border-cyan-300 hover:shadow-md">
                        <p class="text-xl font-bold uppercase text-slate-900">Bağış Yap | IBAN ({{ $account->currency }})</p>
                        <div class="mt-3 overflow-hidden rounded-xl border border-slate-200 bg-white p-3">
                            <img src="{{ asset('storage/' . $donationQrPath) }}" alt="Bağış QR Kodu" class="h-56 w-full rounded-lg object-contain">
                        </div>
                    </a>
                @endforeach

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Diğer Faaliyetler</h3>
                    <div class="mt-4 space-y-3">
                        @forelse($relatedActivities as $related)
                            <a href="{{ route('activities.show', ['slug' => $related->slug]) }}" class="block rounded-xl border border-slate-100 px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-cyan-200 hover:bg-cyan-50/40 hover:text-cyan-800">
                                {{ $related->title }}
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">Henüz başka faaliyet bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
