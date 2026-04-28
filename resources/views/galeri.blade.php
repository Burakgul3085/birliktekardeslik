<x-layouts.app>
    {{-- GLightbox CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

    {{-- Hero başlık --}}
    <section class="bg-gradient-to-br from-cyan-900 via-cyan-800 to-teal-700 py-14 text-white md:py-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="flex flex-col items-start gap-3">
                <nav class="flex items-center gap-2 text-sm text-cyan-200/80" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-white">Ana Sayfa</a>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7l6 6-6 6" /></svg>
                    <span class="text-white">Medya Galerisi</span>
                </nav>
                <h1 class="text-3xl font-bold tracking-tight md:text-4xl">Medya Galerisi</h1>
                <p class="max-w-2xl text-cyan-100/80">Faaliyetlerimize ait fotoğraf ve videolar — her anın tanığı.</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 md:px-6 md:py-14">

        {{-- Filtre çubuğu --}}
        @if($allProjects->isNotEmpty())
        <div class="mb-10 flex flex-wrap items-center gap-2">
            <a
                href="{{ route('gallery') }}"
                class="rounded-full px-4 py-1.5 text-sm font-semibold transition
                    {{ $activeSlug === '' ? 'bg-cyan-600 text-white shadow' : 'border border-slate-200 bg-white text-slate-700 hover:border-cyan-300 hover:text-cyan-700' }}"
            >Tümü</a>
            @foreach($allProjects as $proj)
                <a
                    href="{{ route('gallery', ['activity' => $proj->slug]) }}"
                    class="rounded-full px-4 py-1.5 text-sm font-semibold transition
                        {{ $activeSlug === $proj->slug ? 'bg-cyan-600 text-white shadow' : 'border border-slate-200 bg-white text-slate-700 hover:border-cyan-300 hover:text-cyan-700' }}"
                >{{ $proj->title }}</a>
            @endforeach
        </div>
        @endif

        {{-- Medya yok uyarısı --}}
        @if($projects->isEmpty())
            <div class="flex flex-col items-center justify-center gap-4 py-24 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
                    <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                </div>
                <p class="text-lg font-semibold text-slate-500">Bu faaliyet için henüz medya eklenmemiş.</p>
                <a href="{{ route('gallery') }}" class="mt-2 rounded-full bg-cyan-600 px-5 py-2 text-sm font-bold text-white hover:bg-cyan-700">Tüm Galeriyi Gör</a>
            </div>
        @else
            @foreach($projects as $project)
                @php
                    $images = is_array($project->gallery_images) ? array_filter($project->gallery_images) : [];
                    $videos = is_array($project->gallery_videos) ? array_filter($project->gallery_videos) : [];
                @endphp
                @if(count($images) > 0 || count($videos) > 0)
                <div class="mb-14">
                    {{-- Faaliyet başlığı --}}
                    <div class="mb-5 flex items-center gap-3">
                        <div class="h-7 w-1 rounded-full bg-cyan-600"></div>
                        <h2 class="text-xl font-bold text-slate-800 md:text-2xl">{{ $project->title }}</h2>
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <div class="flex items-center gap-3 text-sm text-slate-500">
                            @if(count($images) > 0)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                    {{ count($images) }} fotoğraf
                                </span>
                            @endif
                            @if(count($videos) > 0)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" /></svg>
                                    {{ count($videos) }} video
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Fotoğraf grid --}}
                    @if(count($images) > 0)
                        <div class="mb-6 grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                            @foreach($images as $index => $image)
                                <a
                                    href="{{ asset('storage/' . $image) }}"
                                    class="glightbox group relative aspect-square overflow-hidden rounded-xl bg-slate-100 shadow-sm"
                                    data-gallery="photos-{{ $project->slug }}"
                                    data-title="{{ $project->title }}"
                                >
                                    <img
                                        src="{{ asset('storage/' . $image) }}"
                                        alt="{{ $project->title }} - Fotoğraf {{ $index + 1 }}"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                        loading="lazy"
                                    >
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 transition duration-300 group-hover:bg-black/20">
                                        <svg class="h-8 w-8 scale-0 text-white drop-shadow transition duration-300 group-hover:scale-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803zM10.5 7.5v6m3-3h-6" />
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Video grid --}}
                    @if(count($videos) > 0)
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($videos as $index => $video)
                                <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                                    <div class="relative aspect-video bg-slate-900">
                                        <video
                                            class="h-full w-full object-cover"
                                            controls
                                            preload="metadata"
                                            src="{{ asset('storage/' . $video) }}"
                                        ></video>
                                    </div>
                                    <div class="px-3 py-2.5">
                                        <p class="text-sm font-medium text-slate-700">{{ $project->title }} — Video {{ $index + 1 }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif
            @endforeach
        @endif
    </section>

    {{-- GLightbox JS --}}
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true,
        });
    </script>
</x-layouts.app>
