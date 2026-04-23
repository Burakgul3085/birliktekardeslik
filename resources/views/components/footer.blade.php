<footer class="mt-16 border-t border-slate-100 bg-white">
    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-10 md:grid-cols-3 md:px-6">
        <div>
            <h3 class="font-semibold text-slate-900">{{ $siteSettings->site_title }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ $siteSettings->site_description }}</p>
        </div>
        <div class="text-sm text-slate-600">
            <p>{{ $siteSettings->phone }}</p>
            <p>{{ $siteSettings->email }}</p>
            <p>{{ $siteSettings->address }}</p>
        </div>
        <div class="flex items-start gap-3 text-sm">
            @foreach(['facebook_url' => 'Facebook', 'instagram_url' => 'Instagram', 'youtube_url' => 'YouTube', 'x_url' => 'X'] as $key => $label)
                @if(!empty($siteSettings->$key))
                    <a href="{{ $siteSettings->$key }}" class="rounded-lg bg-slate-100 px-3 py-1.5 text-slate-700 transition hover:bg-slate-200" target="_blank">{{ $label }}</a>
                @endif
            @endforeach
        </div>
    </div>
</footer>
