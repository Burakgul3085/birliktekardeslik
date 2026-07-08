@php
    use Illuminate\Support\Carbon;

    $isTr = app()->getLocale() === 'tr';
    $kvkkText = $isTr
        ? (trim((string) ($siteSettings->kvkk_text ?? '')) ?: __('app.legal.kvkk_content'))
        : __('app.legal.kvkk_content');

    $carouselItems = $testimonials->map(function ($item) {
        $date = $item->approved_at ?? $item->created_at;

        return [
            'name' => $item->display_name,
            'city' => $item->city,
            'rating' => (int) $item->rating,
            'comment' => $item->comment,
            'date' => $date instanceof Carbon
                ? $date->locale(app()->getLocale())->translatedFormat('d F Y')
                : '',
            'is_volunteer' => (bool) $item->is_volunteer,
            'is_donor' => (bool) $item->is_donor,
        ];
    })->values()->all();

    $modalConfig = [
        'submitUrl' => route('testimonials.store'),
        'openOnLoad' => $errors->any(),
        'initialRating' => (int) old('rating', 0),
        'labels' => [
            'rating_aria' => __('app.home.testimonials_form_rating_aria'),
        ],
        'kvkkText' => $kvkkText,
    ];
@endphp

<section
    id="destekci-deneyimleri"
    class="pt-16"
    aria-labelledby="testimonials-heading"
    x-data="testimonialModal(@js($modalConfig))"
    @keydown.window="handleKeydown($event)"
>
    <div class="mx-auto max-w-7xl px-4 md:px-6">
        @if (session('testimonial_success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900" role="status">
                {{ __('app.home.testimonials_success') }}
            </div>
        @endif

        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
                    <span aria-hidden="true">❤️</span>
                    {{ __('app.home.testimonials_badge') }}
                </span>
                <h2 id="testimonials-heading" class="mt-3 text-3xl font-bold tracking-tight text-slate-900 md:text-5xl">
                    {{ __('app.home.testimonials_title') }}
                </h2>
                <p class="mt-3 text-base leading-relaxed text-slate-600 md:text-lg">
                    {{ __('app.home.testimonials_subtitle') }}
                </p>
            </div>

            @if ($testimonialStats['count'] > 0)
                <div class="rounded-2xl border border-cyan-100 bg-gradient-to-br from-cyan-50/80 to-white px-5 py-4 text-center shadow-sm lg:min-w-[220px]">
                    <div class="flex items-center justify-center gap-0.5 text-amber-400" aria-hidden="true">
                        @for ($star = 1; $star <= 5; $star++)
                            <svg class="h-4 w-4 {{ $star <= round($testimonialStats['average']) ? 'fill-current' : 'fill-slate-200 text-slate-200' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.31 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                        @endfor
                    </div>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ number_format($testimonialStats['average'], 1, ',', '.') }} / 5
                    </p>
                    <p class="mt-1 text-sm font-medium text-slate-600">
                        {{ trans_choice('app.home.testimonials_count', $testimonialStats['count'], ['count' => $testimonialStats['count']]) }}
                    </p>
                </div>
            @endif
        </div>

        @if ($testimonials->isNotEmpty())
            <div
                class="relative mt-8"
                x-data="testimonialsCarousel({ items: @js($carouselItems) })"
                @mouseenter="pause()"
                @mouseleave="resume()"
                role="region"
                aria-roledescription="carousel"
                aria-label="{{ __('app.home.testimonials_title') }}"
            >
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out" :style="trackStyle">
                        @foreach ($testimonials as $testimonial)
                            @php
                                $date = $testimonial->approved_at ?? $testimonial->created_at;
                            @endphp
                            <div class="flex-shrink-0 px-2 w-full md:w-1/2 lg:w-1/3">
                                <x-testimonial-card
                                    :name="$testimonial->display_name"
                                    :city="$testimonial->city"
                                    :rating="$testimonial->rating"
                                    :comment="$testimonial->comment"
                                    :date="$date->locale(app()->getLocale())->translatedFormat('d F Y')"
                                    :is-volunteer="$testimonial->is_volunteer"
                                    :is-donor="$testimonial->is_donor"
                                    class="testimonial-fade-up"
                                />
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($testimonials->count() > 1)
                    <div class="mt-5 flex items-center justify-center gap-3">
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-cyan-300 hover:text-cyan-700 disabled:opacity-40"
                            @click="prev()"
                            :disabled="!canPrev"
                            aria-label="{{ __('app.home.testimonials_prev') }}"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <div class="flex items-center gap-2">
                            <template x-for="index in Array.from({ length: maxIndex + 1 }, (_, i) => i)" :key="index">
                                <button
                                    type="button"
                                    class="h-2.5 rounded-full transition-all"
                                    :class="current === index ? 'w-6 bg-cyan-600' : 'w-2.5 bg-slate-300'"
                                    @click="goTo(index)"
                                    :aria-label="'{{ __('app.home.testimonials_go_to') }} ' + (index + 1)"
                                ></button>
                            </template>
                        </div>

                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-cyan-300 hover:text-cyan-700"
                            @click="next()"
                            aria-label="{{ __('app.home.testimonials_next') }}"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                @endif
            </div>

            @if ($testimonialStats['count'] > 0)
                <script type="application/ld+json">
                    {!! json_encode([
                        '@context' => 'https://schema.org',
                        '@type' => 'Organization',
                        'name' => $siteSettings->site_title ?? 'Birlikte Kardeşlik Derneği',
                        'url' => url('/'),
                        'aggregateRating' => [
                            '@type' => 'AggregateRating',
                            'ratingValue' => (string) $testimonialStats['average'],
                            'bestRating' => '5',
                            'worstRating' => '1',
                            'ratingCount' => (string) $testimonialStats['count'],
                        ],
                        'review' => $testimonials->take(10)->map(fn ($item) => [
                            '@type' => 'Review',
                            'author' => [
                                '@type' => 'Person',
                                'name' => $item->display_name,
                            ],
                            'reviewRating' => [
                                '@type' => 'Rating',
                                'ratingValue' => (string) $item->rating,
                                'bestRating' => '5',
                            ],
                            'reviewBody' => $item->comment,
                            'datePublished' => optional($item->approved_at ?? $item->created_at)->toDateString(),
                        ])->values()->all(),
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
                </script>
            @endif
        @else
            <div class="mt-8 rounded-[20px] border border-dashed border-cyan-200 bg-gradient-to-br from-cyan-50/40 to-white px-6 py-12 text-center shadow-sm">
                <p class="text-4xl" aria-hidden="true">❤️</p>
                <p class="mt-4 text-lg font-semibold text-slate-800">{{ __('app.home.testimonials_empty_title') }}</p>
                <p class="mt-2 text-sm text-slate-600">{{ __('app.home.testimonials_empty_desc') }}</p>
            </div>
        @endif

        <div class="mt-8 flex justify-center">
            <button
                type="button"
                @click="openModal()"
                class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-7 py-3 text-sm font-semibold text-rose-800 shadow-sm transition hover:-translate-y-0.5 hover:border-rose-300 hover:bg-rose-100"
            >
                <span aria-hidden="true">❤️</span>
                {{ __('app.home.testimonials_share_cta') }}
            </button>
        </div>
    </div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 px-4 py-8 backdrop-blur-sm"
        @click="handleBackdrop($event)"
        role="dialog"
        aria-modal="true"
        aria-labelledby="testimonial-modal-title"
    >
        <div class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-[20px] border border-slate-100 bg-white p-6 shadow-2xl md:p-8" @click.stop>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">{{ __('app.home.testimonials_badge') }}</p>
                    <h3 id="testimonial-modal-title" class="mt-1 text-xl font-bold text-slate-900">{{ __('app.home.testimonials_form_title') }}</h3>
                </div>
                <button type="button" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="closeModal()" aria-label="{{ __('app.footer.close') }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('testimonials.store') }}" class="mt-6 space-y-4">
                @csrf
                <input type="text" name="company_website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">{{ __('app.home.testimonials_form_name') }} *</span>
                        <input type="text" name="name" value="{{ old('name') }}" required maxlength="120" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">{{ __('app.home.testimonials_form_city') }} *</span>
                        <input type="text" name="city" value="{{ old('city') }}" required maxlength="120" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">{{ __('app.home.testimonials_form_email') }} *</span>
                    <span class="block text-xs text-slate-500">{{ __('app.home.testimonials_form_email_hint') }}</span>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="190" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                </label>

                <div>
                    <span class="text-sm font-medium text-slate-700">{{ __('app.home.testimonials_form_rating') }} *</span>
                    <div class="mt-2 flex items-center gap-1">
                        @for ($star = 1; $star <= 5; $star++)
                            <button
                                type="button"
                                class="rounded p-1 transition"
                                @mouseenter="hoverRating = {{ $star }}"
                                @mouseleave="hoverRating = 0"
                                @click="setRating({{ $star }})"
                                :aria-label="'{{ $star }}'"
                            >
                                <svg class="h-7 w-7" :class="(hoverRating || rating) >= {{ $star }} ? 'text-amber-400 fill-amber-400' : 'text-slate-300'" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.31 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" :value="rating" required>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">{{ __('app.home.testimonials_form_comment') }} *</span>
                    <textarea name="comment" rows="4" required minlength="20" maxlength="500" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-100">{{ old('comment') }}</textarea>
                </label>

                <div class="space-y-2 text-sm text-slate-700">
                    <label class="flex items-start gap-2">
                        <input type="checkbox" name="is_anonymous" value="1" class="mt-1 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(old('is_anonymous'))>
                        <span>{{ __('app.home.testimonials_form_anonymous') }}</span>
                    </label>
                    <label class="flex items-start gap-2">
                        <input type="checkbox" name="is_volunteer" value="1" class="mt-1 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(old('is_volunteer'))>
                        <span>{{ __('app.home.testimonials_form_volunteer') }}</span>
                    </label>
                    <label class="flex items-start gap-2">
                        <input type="checkbox" name="is_donor" value="1" class="mt-1 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(old('is_donor'))>
                        <span>{{ __('app.home.testimonials_form_donor') }}</span>
                    </label>
                    <label class="flex items-start gap-2">
                        <input type="checkbox" name="kvkk_consent" value="1" required class="mt-1 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(old('kvkk_consent'))>
                        <span>
                            <button type="button" class="font-semibold text-cyan-700 underline underline-offset-2" @click.prevent="showKvkk = !showKvkk">{{ __('app.legal.kvkk_label') }}</button>
                            {{ __('app.home.testimonials_form_kvkk_read') }}
                        </span>
                    </label>
                </div>

                <div x-show="showKvkk" x-cloak class="max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs leading-relaxed text-slate-600">
                    {!! nl2br(e($kvkkText)) !!}
                </div>

                @if ($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <ul class="list-disc space-y-1 ps-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="submit" class="btn-primary w-full rounded-xl px-6 py-3 text-sm font-semibold shadow-md" :disabled="rating < 1">
                    {{ __('app.home.testimonials_form_submit') }}
                </button>
            </form>
        </div>
    </div>

    <style>
        .testimonial-fade-up {
            animation: testimonialFadeUp 400ms ease both;
        }
        @keyframes testimonialFadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</section>
