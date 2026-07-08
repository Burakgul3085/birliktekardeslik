@props([
    'name',
    'city',
    'rating',
    'comment',
    'date',
    'isVolunteer' => false,
    'isDonor' => false,
])

<article {{ $attributes->merge(['class' => 'group flex h-full min-h-[300px] flex-col rounded-[20px] border border-slate-100 bg-white p-7 shadow-sm transition-all duration-400 hover:-translate-y-1.5 hover:scale-[1.02] hover:shadow-[0_18px_34px_rgba(14,116,144,0.14)]']) }}>
    <div class="flex items-center gap-0.5 text-amber-400" aria-label="{{ __('app.home.testimonials_rating_aria', ['rating' => $rating]) }}">
        @for ($star = 1; $star <= 5; $star++)
            <svg class="h-4 w-4 {{ $star <= $rating ? 'fill-current' : 'fill-slate-200 text-slate-200' }}" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.31 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/>
            </svg>
        @endfor
    </div>

    <blockquote class="mt-4 flex-1 text-sm leading-7 text-slate-700">
        <span class="text-cyan-600" aria-hidden="true">“</span>{{ $comment }}<span class="text-cyan-600" aria-hidden="true">”</span>
    </blockquote>

    <div class="mt-5 border-t border-slate-100 pt-4">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
            <span class="inline-flex items-center gap-1.5 font-semibold text-slate-900">
                <svg class="h-4 w-4 text-cyan-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0"/></svg>
                {{ $name }}
            </span>
            <span class="inline-flex items-center gap-1.5 text-slate-500">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 6.75-7.5 11.25-7.5 11.25S4.5 17.25 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                {{ $city }}
            </span>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1 rounded-full bg-cyan-50 px-2.5 py-1 text-[11px] font-semibold text-cyan-800">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('app.home.testimonials_verified') }}
            </span>
            @if ($isVolunteer)
                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">{{ __('app.home.testimonials_volunteer_badge') }}</span>
            @endif
            @if ($isDonor)
                <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-800">{{ __('app.home.testimonials_donor_badge') }}</span>
            @endif
        </div>

        <p class="mt-3 text-xs text-slate-400">{{ $date }}</p>
    </div>
</article>
