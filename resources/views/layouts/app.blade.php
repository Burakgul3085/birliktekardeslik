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

    <!-- Google Translate UI gizleme — gadget DOM'da kalır, select çalışır -->
    <style>
        .goog-te-banner-frame { display: none !important; }
        body { top: 0 !important; }
        /* Gadget'ı görünmez ama işlevsel tut */
        #google_translate_element,
        .goog-te-gadget { position:absolute; left:-9999px; top:-9999px; height:0; overflow:hidden; }
        /* RTL desteği */
        [dir="rtl"] { text-align: right; }
    </style>
    <script>
        (function() {
            var saved = localStorage.getItem('bkd_lang');
            if (saved === 'ar') document.documentElement.setAttribute('dir', 'rtl');
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

    <!-- Google Translate + Dil Seçici -->
    <script>
        var langData = {
            tr: { label:'Türkçe',  code:'TR', dir:'ltr', img:'https://flagcdn.com/w40/tr.png' },
            en: { label:'English', code:'EN', dir:'ltr', img:'https://flagcdn.com/w40/gb.png' },
            ar: { label:'العربية', code:'AR', dir:'rtl', img:'https://flagcdn.com/w40/sa.png' },
            ru: { label:'Русский', code:'RU', dir:'ltr', img:'https://flagcdn.com/w40/ru.png' },
        };

        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'tr',
                includedLanguages: 'tr,en,ar,ru',
                autoDisplay: false
            }, 'google_translate_element');

            /* Translate hazır olunca kayıtlı dili uygula */
            var saved = localStorage.getItem('bkd_lang');
            if (saved && saved !== 'tr') {
                setTimeout(function() { applyTranslation(saved); }, 800);
            }
        }

        function applyTranslation(lang) {
            /* Google Translate'in oluşturduğu gizli select'i bul */
            var select = document.querySelector('.goog-te-combo');
            if (!select) { return; }
            select.value = lang;
            var evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', true, true);
            select.dispatchEvent(evt);
        }

        function switchLang(lang) {
            localStorage.setItem('bkd_lang', lang);

            /* RTL / LTR hemen uygula */
            var dir = lang === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.setAttribute('dir', dir);

            /* Buton UI güncelle */
            updateLangUI(lang);

            if (lang === 'tr') {
                /* Orijinal dile dön */
                var restoreEl = document.querySelector('.goog-te-banner-frame');
                var select = document.querySelector('.goog-te-combo');
                if (select) {
                    select.value = '';
                    var evt = document.createEvent('HTMLEvents');
                    evt.initEvent('change', true, true);
                    select.dispatchEvent(evt);
                }
                /* Cookie temizle ve yenile */
                var exp = 'expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
                document.cookie = 'googtrans=; ' + exp;
                document.cookie = 'googtrans=; ' + exp + '; domain=' + location.hostname;
                document.cookie = 'googtrans=; ' + exp + '; domain=.' + location.hostname;
                setTimeout(function() { location.reload(); }, 200);
                return;
            }

            applyTranslation(lang);
        }

        function updateLangUI(lang) {
            var info = langData[lang] || langData.tr;
            document.querySelectorAll('[data-lang-flag]').forEach(function(el) {
                el.src = info.img; el.alt = info.code;
            });
            document.querySelectorAll('[data-lang-code]').forEach(function(el) {
                el.textContent = info.code;
            });
            document.querySelectorAll('[data-lang-btn]').forEach(function(btn) {
                var active = btn.getAttribute('data-lang-btn') === lang;
                btn.style.background = active ? '#e0f2fe' : '';
                btn.style.color      = active ? '#0369a1' : '';
            });
        }

        /* Sayfa yüklenince UI'yı hazırla */
        document.addEventListener('DOMContentLoaded', function() {
            var saved = localStorage.getItem('bkd_lang') || 'tr';
            updateLangUI(saved);
            if (saved === 'ar') document.documentElement.setAttribute('dir', 'rtl');
            else document.documentElement.setAttribute('dir', 'ltr');
        });
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
