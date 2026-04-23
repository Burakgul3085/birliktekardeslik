<x-layouts.app>
    <section class="mx-auto max-w-7xl px-4 py-10 md:px-6">
        <h1 class="text-3xl font-bold text-slate-900">Hesap Numaralarımız</h1>
        <p class="mt-2 text-slate-600">Aşağıdaki bilgiler üzerinden güvenle bağış yapabilirsiniz.</p>

        <div class="mt-6 space-y-5">
            @forelse($bankAccounts as $account)
                <article class="rounded-3xl border border-cyan-100 bg-gradient-to-br from-cyan-50/50 to-white p-6 shadow-sm" x-data="{ copied: false }">
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div class="space-y-3 lg:col-span-2">
                            <span class="inline-flex rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-semibold text-cyan-700">{{ $account->currency }}</span>

                            <p class="text-2xl font-bold text-slate-900">
                                Hesap Adı: <span class="font-semibold">{{ $account->recipient_name }}</span>
                            </p>

                            <p class="text-xl font-semibold text-slate-900">
                                Şube Adı:
                                <span class="font-medium">
                                    {{ $account->branch_name ?: ($account->bank_name . ' Şubesi') }}
                                </span>
                            </p>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <p class="text-lg font-semibold text-slate-900">IBAN: <span class="font-mono">{{ $account->iban }}</span></p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">Hesap No: <span class="font-medium">{{ $account->account_number ?: '-' }}</span></p>
                            </div>

                            <button @click="navigator.clipboard.writeText('{{ $account->iban }}'); copied = true; setTimeout(() => copied = false, 2000)" class="btn-primary w-full max-w-sm">
                                IBAN Kopyala
                            </button>
                            <p x-show="copied" class="text-xs text-emerald-600">IBAN panoya kopyalandı.</p>
                        </div>

                        <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="mb-3 text-sm font-semibold text-slate-700">IBAN'ı tarayabilirsiniz</p>
                            <img src="{{ asset('storage/' . $donationQrPath) }}" alt="Bağış QR Kodu" class="h-44 w-44 rounded-xl border border-slate-200 object-cover">
                            <p class="mt-3 text-center text-xs text-slate-500">QR kod her zaman bağış sayfasını açar.</p>
                        </div>
                    </div>
                </article>
            @empty
                <x-empty-state title="Aktif hesap bulunmuyor" description="Admin panelden aktif banka hesabı eklediğinizde burada görünecektir." />
            @endforelse
        </div>
    </section>
</x-layouts.app>

