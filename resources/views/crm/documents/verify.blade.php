<x-layouts.app>
    <section class="py-16 md:py-24 bg-gradient-to-b from-[#f4fbfc] to-white min-h-[70vh]">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-6 py-8 text-white text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h1 class="text-2xl font-bold">Belge Doğrulandı</h1>
                    <p class="text-teal-50 mt-2 text-sm">Bu belge Birlikte Kardeşlik Derneği sisteminde kayıtlıdır.</p>
                </div>

                <div class="p-6 md:p-8 space-y-4 text-sm text-slate-700">
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-slate-500">Belge Türü</span>
                        <span class="font-semibold text-slate-900">{{ $document->type_label }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-slate-500">Doğrulama Kodu</span>
                        <span class="font-mono font-semibold">{{ $document->verification_code }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-slate-500">Bağış No</span>
                        <span class="font-semibold">{{ $donation->donation_number }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-slate-500">Bağışçı</span>
                        <span class="font-semibold">{{ $donation->donor?->full_name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-slate-500">Tutar</span>
                        <span class="font-semibold text-teal-700">{{ number_format((float) $donation->amount, 2, ',', '.') }} {{ $donation->currency }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-3">
                        <span class="text-slate-500">Bağış Tarihi</span>
                        <span class="font-semibold">{{ $donation->donated_at?->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Oluşturulma</span>
                        <span class="font-semibold">{{ $document->generated_at?->format('d.m.Y H:i') }}</span>
                    </div>
                </div>

                <div class="px-6 pb-8 text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
