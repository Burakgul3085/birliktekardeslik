<x-layouts.app>
    <section class="mx-auto max-w-4xl px-4 py-12 md:px-6">
        <article class="card-ui">
            <h1 class="text-3xl font-bold text-slate-900">{{ $page->title }}</h1>
            <div class="prose mt-6 max-w-none prose-slate">{!! $page->content !!}</div>
        </article>
    </section>
</x-layouts.app>
