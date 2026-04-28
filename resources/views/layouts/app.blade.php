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
        /* Banner'ı gizle, body kaymasını önle */
        .goog-te-banner-frame { display: none !important; }
        body { top: 0 !important; }
        /* GT element ekran dışında ama DOM'da — select oluşsun */
        #google_translate_element { position:fixed; left:-9999px; top:0; }
        [dir="rtl"] { text-align: right; }
    </style>

    <!-- Dil seçici — head'de, her şeyden önce yükle -->
    <script>
        var langData = {
            tr: { code:'TR', dir:'ltr', img:'https://flagcdn.com/w40/tr.png' },
            en: { code:'EN', dir:'ltr', img:'https://flagcdn.com/w40/gb.png' },
            ar: { code:'AR', dir:'rtl', img:'https://flagcdn.com/w40/sa.png' },
            ru: { code:'RU', dir:'ltr', img:'https://flagcdn.com/w40/ru.png' },
        };

        /* RTL flash önleme */
        if (localStorage.getItem('bkd_lang') === 'ar')
            document.documentElement.setAttribute('dir', 'rtl');

        window.switchLang = function(lang) {
            localStorage.setItem('bkd_lang', lang);
            updateLangUI(lang);

            if (lang === 'tr') {
                /* Türkçe: orijinal siteye dön */
                var orig = location.href
                    .replace(/^https?:\/\/[^.]+\.translate\.goog/, 'https://birliktekardeslik.org')
                    .replace(/[?&]_x_tr_[^&]*/g, '');
                location.href = orig.replace(/[?&]$/, '') || 'https://birliktekardeslik.org/';
                return;
            }

            /* Google Translate proxy — her zaman çalışır, eklenti/kısıt yok */
            var currentUrl = location.href;
            /* Zaten proxy üzerindeyse sadece dili değiştir */
            if (currentUrl.indexOf('.translate.goog') !== -1) {
                location.href = currentUrl.replace(/_x_tr_tl=[a-z]+/, '_x_tr_tl=' + lang);
                return;
            }
            /* İlk kez proxy'ye yönlendir */
            location.href = 'https://translate.google.com/translate?sl=tr&tl=' + lang + '&hl=tr&u=' + encodeURIComponent(currentUrl);
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

    <!-- Google Translate -->
    <script>
        function updateLangUI(lang) {
            var info = langData[lang] || langData.tr;
            document.querySelectorAll('[data-lang-flag]').forEach(function(el){ el.src=info.img; el.alt=info.code; });
            document.querySelectorAll('[data-lang-code]').forEach(function(el){ el.textContent=info.code; });
            document.querySelectorAll('[data-lang-btn]').forEach(function(btn){
                var active = btn.getAttribute('data-lang-btn') === lang;
                btn.style.background = active ? '#e0f2fe' : '';
                btn.style.color      = active ? '#0369a1' : '';
                btn.style.fontWeight = active ? '700'    : '';
            });
        }

        function googleTranslateElementInit() {
            new google.translate.TranslateElement({ pageLanguage: 'tr', autoDisplay: false }, 'google_translate_element');
        }

        document.addEventListener('DOMContentLoaded', function() {
            var saved = localStorage.getItem('bkd_lang') || 'tr';
            updateLangUI(saved);
            document.documentElement.setAttribute('dir', saved === 'ar' ? 'rtl' : 'ltr');

            /* Event listener'ları ekle */
            document.querySelectorAll('[data-lang-btn]').forEach(function(btn) {
                var lang = btn.getAttribute('data-lang-btn');
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
