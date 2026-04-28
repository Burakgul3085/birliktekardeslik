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

    <!-- Google Translate stiller -->
    <style>
        .goog-te-banner-frame.skiptranslate { display: none !important; }
        body { top: 0 !important; }
        #google_translate_element { display: none !important; }
        [dir="rtl"] { text-align: right; }
    </style>

    <!-- Dil seçici: head'de tanımla, Alpine'dan önce yüklensin -->
    <script>
        var langData = {
            tr: { code:'TR', dir:'ltr', img:'https://flagcdn.com/w40/tr.png' },
            en: { code:'EN', dir:'ltr', img:'https://flagcdn.com/w40/gb.png' },
            ar: { code:'AR', dir:'rtl', img:'https://flagcdn.com/w40/sa.png' },
            ru: { code:'RU', dir:'ltr', img:'https://flagcdn.com/w40/ru.png' },
        };

        /* Global scope'a aç — onclick ve Alpine her ikisi de erişebilir */
        window.switchLang = function(lang) {
            localStorage.setItem('bkd_lang', lang);
            var past = 'Thu, 01 Jan 1970 00:00:00 GMT';
            var host = location.hostname;
            /* Eski cookie sil */
            document.cookie = 'googtrans=; expires=' + past + '; path=/';
            document.cookie = 'googtrans=; expires=' + past + '; path=/; domain=' + host;
            document.cookie = 'googtrans=; expires=' + past + '; path=/; domain=.' + host;
            if (lang !== 'tr') {
                var v = 'googtrans=/tr/' + lang;
                document.cookie = v + '; path=/';
                document.cookie = v + '; path=/; domain=' + host;
                document.cookie = v + '; path=/; domain=.' + host;
            }
            location.reload();
        };

        /* RTL flash önleme */
        (function() {
            if (localStorage.getItem('bkd_lang') === 'ar')
                document.documentElement.setAttribute('dir', 'rtl');
        })();
    </script>
</head>
<body>
    <!-- Google Translate gizli element -->
    <div id="google_translate_element" style="display:none;"></div>

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

    <!-- Google Translate başlatıcı -->
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({ pageLanguage: 'tr', autoDisplay: false }, 'google_translate_element');
        }

        /* Sayfa yüklenince: UI güncelle + event listener ekle */
        document.addEventListener('DOMContentLoaded', function() {
            var saved = localStorage.getItem('bkd_lang') || 'tr';
            var info  = langData[saved] || langData.tr;

            /* Bayrak ve kod güncelle */
            document.querySelectorAll('[data-lang-flag]').forEach(function(el) { el.src = info.img; el.alt = info.code; });
            document.querySelectorAll('[data-lang-code]').forEach(function(el) { el.textContent = info.code; });

            /* RTL */
            document.documentElement.setAttribute('dir', info.dir);

            /* Dil butonlarına event listener ekle (onclick yerine) */
            document.querySelectorAll('[data-lang-btn]').forEach(function(btn) {
                var lang = btn.getAttribute('data-lang-btn');
                if (lang === saved) {
                    btn.style.background = '#e0f2fe';
                    btn.style.fontWeight = '700';
                    btn.style.color      = '#0369a1';
                }
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    window.switchLang(lang);
                });
            });
        });
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
