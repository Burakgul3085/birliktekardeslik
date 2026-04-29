<!doctype html>
<html lang="tr" id="html-root">
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
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-R2X10WDTGW"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-R2X10WDTGW');
    </script>

    <!-- Çeviri stilleri -->
    <style>
        .goog-te-banner-frame { display: none !important; }
        body { top: 0 !important; }
        #google_translate_element { position:fixed; left:-9999px; top:0; opacity:0; pointer-events:none; }
        [dir="rtl"] { text-align: right; }
    </style>

    <!-- Dil sistemi (head'de tanımlanır, her şeyden önce hazır olur) -->
    <script>
        var BKD_LANGS = {
            tr: { code:'TR', img:'https://flagcdn.com/w40/tr.png', dir:'ltr' },
            en: { code:'EN', img:'https://flagcdn.com/w40/gb.png', dir:'ltr' },
            ar: { code:'AR', img:'https://flagcdn.com/w40/sa.png', dir:'rtl' },
            ru: { code:'RU', img:'https://flagcdn.com/w40/ru.png', dir:'ltr' },
        };

        /* RTL flash önleme */
        (function(){
            var l = localStorage.getItem('bkd_lang');
            if (l === 'ar') document.documentElement.setAttribute('dir','rtl');
        })();

        /* Dil geçiş fonksiyonu — GLOBAL, her yerden çağrılabilir */
        window.switchLang = function(lang) {
            if (!lang) return;
            localStorage.setItem('bkd_lang', lang);

            /* Cookie ayarla */
            var host = location.hostname;
            if (lang === 'tr') {
                var exp = 'expires=Thu, 01 Jan 1970 00:00:01 GMT';
                document.cookie = 'googtrans=; ' + exp + '; path=/';
                document.cookie = 'googtrans=; ' + exp + '; path=/; domain=' + host;
                document.cookie = 'googtrans=; ' + exp + '; path=/; domain=.' + host;
            } else {
                var val = '/tr/' + lang;
                document.cookie = 'googtrans=' + val + '; path=/';
                document.cookie = 'googtrans=' + val + '; path=/; domain=' + host;
                document.cookie = 'googtrans=' + val + '; path=/; domain=.' + host;
            }
            location.reload();
        };
    </script>
</head>
<body>
    <!-- Google Translate elementi — CSS ile ekran dışında, DOM'da tam -->
    <div id="google_translate_element"></div>

    <div
        id="page-transition"
        class="pointer-events-none fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/88 opacity-0 transition-opacity duration-300"
        aria-hidden="true"
    >
        <div class="flex items-center gap-3">
            <span class="page-transition-dot page-transition-dot--1"></span>
            <span class="page-transition-dot page-transition-dot--2"></span>
            <span class="page-transition-dot page-transition-dot--3"></span>
        </div>
    </div>
    <x-navbar :menu-items="$menuItems" :site-settings="$siteSettings" />
    <main class="min-h-[70vh]">{{ $slot }}</main>
    <x-footer :site-settings="$siteSettings" />

    <!-- UI güncelleme + Google Translate init -->
    <script>
        /* Aktif dili bayrak/kod olarak göster */
        document.addEventListener('DOMContentLoaded', function() {
            var lang = localStorage.getItem('bkd_lang') || 'tr';
            var info = BKD_LANGS[lang] || BKD_LANGS.tr;
            document.querySelectorAll('[data-lang-flag]').forEach(function(el){
                el.src = info.img; el.alt = info.code;
            });
            document.querySelectorAll('[data-lang-code]').forEach(function(el){
                el.textContent = info.code;
            });
            document.querySelectorAll('[data-lang-btn]').forEach(function(btn){
                var active = btn.getAttribute('data-lang-btn') === lang;
                btn.style.background  = active ? '#e0f2fe' : '';
                btn.style.color       = active ? '#0369a1' : '';
                btn.style.fontWeight  = active ? '700' : '';
            });
            document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
        });

        function googleTranslateElementInit() {
            new google.translate.TranslateElement({ pageLanguage: 'tr', autoDisplay: false }, 'google_translate_element');
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
