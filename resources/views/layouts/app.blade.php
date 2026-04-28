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

    <!-- Google Translate: varsayılan araç çubuğunu gizle -->
    <style>
        .goog-te-banner-frame, .skiptranslate { display: none !important; }
        body { top: 0 !important; }
        .goog-te-gadget { display: none !important; }
        /* RTL modu için genel uyum */
        [dir="rtl"] .rtl-flip { transform: scaleX(-1); }
        [dir="rtl"] .flex { flex-direction: row-reverse; }
        [dir="rtl"] .text-left { text-align: right; }
        [dir="rtl"] .text-right { text-align: left; }
        [dir="rtl"] .ml-auto { margin-left: unset; margin-right: auto; }
        [dir="rtl"] .mr-auto { margin-right: unset; margin-left: auto; }
    </style>
    <script>
        /* Dil seçicisi: sayfa yüklenince kayıtlı dili uygula */
        (function() {
            var saved = localStorage.getItem('bkd_lang');
            if (saved && saved !== 'tr') {
                document.documentElement.setAttribute('dir', saved === 'ar' ? 'rtl' : 'ltr');
            }
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

    <!-- Google Translate API -->
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'tr',
                autoDisplay: false,
            }, 'google_translate_element');
        }

        var langs = {
            'tr': { label: 'Türkçe', flag: '🇹🇷', dir: 'ltr' },
            'en': { label: 'English', flag: '🇬🇧', dir: 'ltr' },
            'ar': { label: 'العربية', flag: '🇸🇦', dir: 'rtl' },
            'ru': { label: 'Русский', flag: '🇷🇺', dir: 'ltr' },
        };

        function switchLang(lang) {
            localStorage.setItem('bkd_lang', lang);

            /* RTL / LTR */
            var dir = langs[lang] ? langs[lang].dir : 'ltr';
            document.documentElement.setAttribute('dir', dir);
            document.documentElement.setAttribute('lang', lang);

            /* Aktif butonu güncelle */
            document.querySelectorAll('[data-lang-btn]').forEach(function(btn) {
                var isActive = btn.getAttribute('data-lang-btn') === lang;
                btn.style.background    = isActive ? 'rgba(255,255,255,0.25)' : 'transparent';
                btn.style.fontWeight    = isActive ? '700' : '500';
                btn.style.borderColor   = isActive ? 'rgba(255,255,255,0.5)' : 'transparent';
            });

            if (lang === 'tr') {
                /* Türkçe'ye dön: sayfayı sıfırla */
                var cookie = document.cookie.split(';');
                for (var i = 0; i < cookie.length; i++) {
                    var c = cookie[i].trim();
                    if (c.indexOf('googtrans') === 0) {
                        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname + ';';
                    }
                }
                location.reload();
                return;
            }

            /* Google Translate cookie ayarla ve sayfayı yenile */
            document.cookie = 'googtrans=/tr/' + lang + '; path=/';
            document.cookie = 'googtrans=/tr/' + lang + '; path=/; domain=' + window.location.hostname;
            location.reload();
        }

        /* Sayfa yüklenince aktif dili işaretle ve bayrağı güncelle */
        document.addEventListener('DOMContentLoaded', function() {
            var saved = localStorage.getItem('bkd_lang') || 'tr';
            var info = langs[saved] || langs['tr'];

            /* Bayrak & kod güncelle */
            ['topbar-active-flag','nav-active-flag'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.textContent = info.flag;
            });
            ['topbar-active-label','nav-active-code'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.textContent = saved.toUpperCase();
            });

            /* Aktif buton stili */
            document.querySelectorAll('[data-lang-btn]').forEach(function(btn) {
                var isActive = btn.getAttribute('data-lang-btn') === saved;
                if (isActive) {
                    btn.style.background = '#eff6ff';
                    btn.style.color = '#0891b2';
                }
            });
        });
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
