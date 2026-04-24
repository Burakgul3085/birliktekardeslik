<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteSettings->site_title ?? 'Birlikte Kardeşlik Derneği' }}</title>
    <meta name="description" content="{{ $siteSettings->site_description }}">
    @if($siteSettings->favicon)
        <link rel="icon" href="{{ asset('storage/' . $siteSettings->favicon) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-navbar :menu-items="$menuItems" :site-settings="$siteSettings" />
    @if (session('newsletter_success'))
        <div class="border-b border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-900" role="status">
            {{ session('newsletter_success') }}
        </div>
    @endif
    @if (session('newsletter_info'))
        <div class="border-b border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm font-medium text-amber-900" role="status">
            {{ session('newsletter_info') }}
        </div>
    @endif
    @if ($errors->has('email'))
        <div class="border-b border-rose-200 bg-rose-50 px-4 py-3 text-center text-sm text-rose-800" role="alert">
            {{ $errors->first('email') }}
        </div>
    @endif
    <main class="min-h-[70vh]">{{ $slot }}</main>
    <x-footer />
</body>
</html>
