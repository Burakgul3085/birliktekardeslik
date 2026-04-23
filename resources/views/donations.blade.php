<x-layouts.app>
    <section class="mx-auto max-w-7xl px-4 py-10 md:px-6">
        <h1 class="text-3xl font-bold text-slate-900">Bağış Hesapları</h1>
        <p class="mt-2 text-slate-600">Aşağıdaki hesaplardan derneğimize güvenle bağış yapabilirsiniz.</p>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @forelse($bankAccounts as $account)
                <article class="card-ui" x-data="{ copied: false }">
                    <p class="text-sm font-medium text-slate-500">{{ $account->bank_name }} ({{ $account->currency }})</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $account->recipient_name }}</h3>
                    <p class="mt-2 rounded-xl bg-slate-50 px-3 py-2 font-mono text-sm text-slate-700">{{ $account->iban }}</p>
                    <button @click="navigator.clipboard.writeText('{{ $account->iban }}'); copied = true; setTimeout(() => copied = false, 2000)" class="btn-primary mt-4 w-full">
                        IBAN Kopyala
                    </button>
                    <p x-show="copied" class="mt-2 text-center text-xs text-emerald-600">Kopyalandı</p>
                </article>
            @empty
                <x-empty-state title="Aktif hesap bulunmuyor" description="Admin panelden aktif banka hesabı eklediğinizde burada görünecektir." />
            @endforelse
        </div>
    </section>
</x-layouts.app>

