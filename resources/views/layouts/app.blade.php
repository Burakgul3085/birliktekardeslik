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

    <!-- Google Translate + Dil Seçici JS -->
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({ pageLanguage: 'tr', autoDisplay: false }, 'google_translate_element');
        }

        var langData = {
            tr: { label: 'Türkçe',  code: 'TR', dir: 'ltr', img: 'https://flagcdn.com/w40/tr.png' },
            en: { label: 'English', code: 'EN', dir: 'ltr', img: 'https://flagcdn.com/w40/gb.png' },
            ar: { label: 'العربية', code: 'AR', dir: 'rtl', img: 'https://flagcdn.com/w40/sa.png' },
            ru: { label: 'Русский', code: 'RU', dir: 'ltr', img: 'https://flagcdn.com/w40/ru.png' },
        };

        function setGoogCookie(val) {
            var host = window.location.hostname;
            var exp  = val ? '' : '; expires=Thu, 01 Jan 1970 00:00:00 UTC';
            var v    = val || 'googtrans=';
            document.cookie = v + exp + '; path=/';
            document.cookie = v + exp + '; path=/; domain=' + host;
            /* .domain.com formu */
            if (host.indexOf('.') !== -1) {
                document.cookie = v + exp + '; path=/; domain=.' + host;
            }
        }

        function switchLang(lang) {
            localStorage.setItem('bkd_lang', lang);
            if (lang === 'tr') {
                setGoogCookie(null);           /* cookie sil */
            } else {
                setGoogCookie('googtrans=/tr/' + lang);
            }
            location.reload();
        }

        /* Sayfa yüklenince UI'yı güncelle */
        document.addEventListener('DOMContentLoaded', function () {
            var saved = localStorage.getItem('bkd_lang') || 'tr';
            var info  = langData[saved] || langData.tr;

            /* RTL/LTR */
            document.documentElement.setAttribute('dir', info.dir);

            /* Bayrak resimlerini güncelle */
            document.querySelectorAll('[data-lang-flag]').forEach(function (el) {
                el.src = info.img;
                el.alt = info.code;
            });
            document.querySelectorAll('[data-lang-code]').forEach(function (el) {
                el.textContent = info.code;
            });

            /* Aktif satırı vurgula */
            document.querySelectorAll('[data-lang-btn]').forEach(function (btn) {
                if (btn.getAttribute('data-lang-btn') === saved) {
                    btn.style.background = '#e0f2fe';
                    btn.style.color      = '#0369a1';
                }
            });
        });
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
