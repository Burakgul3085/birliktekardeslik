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

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-R2X10WDTGW"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date()); gtag('config', 'G-R2X10WDTGW');
    </script>

    <style>
        /* Google Translate banner'ını gizle, body kaymasını önle */
        .goog-te-banner-frame.skiptranslate { display: none !important; }
        body { top: 0 !important; }
        /* Widget kutucuğu — sağ alt köşede küçük */
        #bkd-gt-wrap {
            position: fixed; bottom: 70px; right: 16px; z-index: 9990;
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: 4px 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,.10);
            opacity: 0; pointer-events: none; /* başlangıçta görünmez */
            transition: opacity .3s;
        }
        #bkd-gt-wrap.ready { opacity: 1; pointer-events: auto; }
        [dir="rtl"] { text-align: right; }
    </style>

    <!-- Dil değişkenleri — her şeyden önce tanımlanır -->
    <script>
        var BKD_LANGS = {
            tr: { code:'TR', img:'https://flagcdn.com/w40/tr.png', dir:'ltr' },
            en: { code:'EN', img:'https://flagcdn.com/w40/gb.png', dir:'ltr' },
            ar: { code:'AR', img:'https://flagcdn.com/w40/sa.png', dir:'rtl' },
            ru: { code:'RU', img:'https://flagcdn.com/w40/ru.png', dir:'ltr' },
        };
        /* Arapça flash önleme */
        if (localStorage.getItem('bkd_lang') === 'ar')
            document.documentElement.setAttribute('dir', 'rtl');
    </script>
</head>
<body>
    <!-- Google Translate widget kapsayıcı -->
    <div id="bkd-gt-wrap">
        <div id="google_translate_element"></div>
    </div>

    <div id="page-transition"
        class="pointer-events-none fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/88 opacity-0 transition-opacity duration-300"
        aria-hidden="true">
        <div class="flex items-center gap-3">
            <span class="page-transition-dot page-transition-dot--1"></span>
            <span class="page-transition-dot page-transition-dot--2"></span>
            <span class="page-transition-dot page-transition-dot--3"></span>
        </div>
    </div>

    <x-navbar :menu-items="$menuItems" :site-settings="$siteSettings" />
    <main class="min-h-[70vh]">{{ $slot }}</main>
    <x-footer :site-settings="$siteSettings" />

    <script>
        /* ── UI: bayrak + kod + aktif stil ── */
        function bkdUpdateUI(lang) {
            var info = BKD_LANGS[lang] || BKD_LANGS.tr;
            document.querySelectorAll('[data-lang-flag]').forEach(function(el){ el.src = info.img; el.alt = info.code; });
            document.querySelectorAll('[data-lang-code]').forEach(function(el){ el.textContent = info.code; });
            document.querySelectorAll('[data-lang-btn]').forEach(function(btn){
                var active = btn.getAttribute('data-lang-btn') === lang;
                btn.style.background = active ? '#e0f2fe' : '';
                btn.style.color      = active ? '#0369a1' : '';
                btn.style.fontWeight = active ? '700'    : '';
            });
            document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
        }

        /* ── Google Translate event tetikleyici ── */
        function bkdFire(el, ev) {
            try {
                var e = document.createEvent('HTMLEvents');
                e.initEvent(ev, true, true);
                el.dispatchEvent(e);
            } catch(x) {}
        }

        /* ── select.goog-te-combo'yu bul ve dili uygula ── */
        function bkdApplyLang(lang, tries) {
            tries = tries || 0;
            if (lang === 'tr') { location.reload(); return; }
            var sel = document.querySelector('select.goog-te-combo');
            if (sel && sel.options.length > 1) {
                sel.value = lang;
                bkdFire(sel, 'change');
                bkdFire(sel, 'change');
            } else if (tries < 40) {
                setTimeout(function(){ bkdApplyLang(lang, tries + 1); }, 300);
            }
        }

        /* ── Google Translate init callback ── */
        window.googleTranslateElementInit = function() {
            new google.translate.TranslateElement({
                pageLanguage: 'tr',
                includedLanguages: 'tr,en,ar,ru',
                autoDisplay: false
            }, 'google_translate_element');

            /* Widget hazır — kaydedilen dili uygula */
            var saved = localStorage.getItem('bkd_lang');
            if (saved && saved !== 'tr') {
                bkdApplyLang(saved);
            }
        };

        /* ── Sayfa yüklenince UI güncelle ── */
        document.addEventListener('DOMContentLoaded', function() {
            var lang = localStorage.getItem('bkd_lang') || 'tr';
            bkdUpdateUI(lang);
        });
    </script>

    <!-- Google Translate — widget veya proxy fallback -->
    <script>
        (function() {
            var ORIGIN  = 'birliktekardeslik.org';
            var PROXY   = 'birliktekardeslik-org.translate.goog';

            /* Proxy modu: dil butonlarını proxy URL'ye yönlendir */
            function enableProxyMode() {
                window.switchLang = function(lang) {
                    localStorage.setItem('bkd_lang', lang);
                    if (typeof bkdUpdateUI === 'function') bkdUpdateUI(lang);
                    if (lang === 'tr') {
                        window.location.href = 'https://' + ORIGIN + location.pathname;
                        return;
                    }
                    var path = location.pathname;
                    var qs   = location.search.replace(/[?&]_x_tr_[^&]*/g,'');
                    var sep  = qs ? '&' : '?';
                    window.location.href = 'https://' + PROXY + path + qs + sep +
                        '_x_tr_sl=tr&_x_tr_tl=' + lang + '&_x_tr_hl=tr&_x_tr_pto=wapp';
                };
            }

            /* Proxy URL'deyse switchLang'ı proxy moduna al */
            if (location.hostname === PROXY) {
                enableProxyMode();
            }

            /* Google Translate widget script — yüklenemezse proxy moduna geç */
            var s = document.createElement('script');
            s.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
            s.onerror = function() { enableProxyMode(); };
            document.head.appendChild(s);
        })();
    </script>
</body>
</html>
