/*
 | BKD Afiş Motoru
 | --------------------------------------------------------------------------
 | Fabric.js tabanlı afiş tasarımcısı + stüdyosu + sessiz (offscreen) üretici.
 | Tek modül; Fabric dinamik import ile yalnızca gerektiğinde yüklenir.
 |
 | Modlar:
 |  - design:  Afiş şablonu tasarımı (arka plan + yer tutuculu yazı katmanları)
 |  - studio:  Üretilmiş afişi elle düzenleme (font/renk/boyut/sürükle)
 |  - generate: Görünmez canvas'ta otomatik üretim (buton -> otomatik afiş)
 */

const GOOGLE_FONTS = [
    'Inter', 'Lora', 'Montserrat', 'Roboto', 'Open+Sans',
    'Poppins', 'Playfair+Display', 'Merriweather', 'Oswald',
];

let fontsInjected = false;

function injectGoogleFonts() {
    if (fontsInjected) {
        return;
    }
    fontsInjected = true;

    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?'
        + GOOGLE_FONTS.map((f) => `family=${f}:ital,wght@0,400;0,600;0,700;1,400`).join('&')
        + '&display=swap';
    document.head.appendChild(link);
}

async function ensureFontsLoaded(families) {
    injectGoogleFonts();
    try {
        await document.fonts.ready;
        await Promise.all(
            (families || []).map((f) => document.fonts.load(`700 24px "${f}"`).catch(() => {})),
        );
        await document.fonts.ready;
    } catch (e) {
        /* yoksay */
    }
}

let fabricModulePromise = null;
function loadFabric() {
    if (!fabricModulePromise) {
        fabricModulePromise = import('fabric');
    }
    return fabricModulePromise;
}

function dataUrlToBlob(dataUrl) {
    const [meta, b64] = dataUrl.split(',');
    const mime = /:(.*?);/.exec(meta)?.[1] || 'image/png';
    const bin = atob(b64);
    const len = bin.length;
    const bytes = new Uint8Array(len);
    for (let i = 0; i < len; i += 1) {
        bytes[i] = bin.charCodeAt(i);
    }
    return new Blob([bytes], { type: mime });
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

/* ---------------------------------------------------------------------------
 | PosterCanvas
 | ------------------------------------------------------------------------- */

class PosterCanvas {
    constructor(fabric, canvasEl, config) {
        this.fabric = fabric;
        this.config = config;
        this.mode = config.mode || 'document';
        this.naturalW = config.canvasWidth || 0;
        this.naturalH = config.canvasHeight || 0;
        this.scale = 1;

        this.canvas = new fabric.Canvas(canvasEl, {
            preserveObjectStacking: true,
            backgroundColor: '#ffffff',
            enableRetinaScaling: false,
            selection: this.mode !== 'generate',
        });
    }

    async init(displayWidth) {
        let bgImg = null;

        if (this.config.backgroundUrl) {
            try {
                bgImg = await this.fabric.FabricImage.fromURL(
                    this.config.backgroundUrl,
                    { crossOrigin: 'anonymous' },
                );
                if (!this.naturalW || !this.naturalH) {
                    this.naturalW = bgImg.width;
                    this.naturalH = bgImg.height;
                }
            } catch (e) {
                bgImg = null;
            }
        }

        if (!this.naturalW || !this.naturalH) {
            this.naturalW = 720;
            this.naturalH = 1080;
        }

        this.applyDisplaySize(displayWidth);

        if (bgImg) {
            bgImg.set({
                left: 0,
                top: 0,
                originX: 'left',
                originY: 'top',
                selectable: false,
                evented: false,
                scaleX: 1,
                scaleY: 1,
            });
            this.canvas.backgroundImage = bgImg;
        }

        (this.config.layout || []).forEach((layer) => this.addLayer(layer, false));

        await ensureFontsLoaded(this.usedFonts());
        this.textboxes().forEach((tb) => this.applyAutofit(tb));
        this.canvas.requestRenderAll();
    }

    applyDisplaySize(displayWidth) {
        const maxW = displayWidth || this.naturalW;
        this.scale = Math.min(1, maxW / this.naturalW);
        this.canvas.setDimensions({
            width: Math.round(this.naturalW * this.scale),
            height: Math.round(this.naturalH * this.scale),
        });
        this.canvas.setZoom(this.scale);
    }

    resolveText(layer) {
        if (this.mode === 'design') {
            return layer.binding ? `{${layer.binding}}` : (layer.text ?? 'Metin');
        }
        // Stüdyo: snapshot metni (gerekirse elle düzenlenmiş) korunur
        if (this.mode === 'studio') {
            return layer.text ?? '';
        }
        // generate / document: bağlı alanlar veriden doldurulur
        if (layer.binding) {
            const value = (this.config.data || {})[layer.binding];
            return value === undefined || value === null ? '' : String(value);
        }
        return layer.text ?? '';
    }

    addLayer(layer, select = true) {
        const fabric = this.fabric;
        const tb = new fabric.Textbox(this.resolveText(layer) || ' ', {
            left: layer.left ?? 40,
            top: layer.top ?? 40,
            width: layer.width ?? Math.round(this.naturalW * 0.7),
            fontSize: layer.fontSize ?? 42,
            fontFamily: layer.fontFamily ?? 'Inter',
            fill: layer.fill ?? '#1d4ed8',
            fontWeight: layer.fontWeight ?? 'normal',
            fontStyle: layer.fontStyle ?? 'normal',
            underline: layer.underline ?? false,
            textAlign: layer.textAlign ?? 'center',
            lineHeight: layer.lineHeight ?? 1.16,
            angle: layer.angle ?? 0,
            originX: 'left',
            originY: 'top',
            editable: this.mode !== 'generate',
            selectable: this.mode !== 'generate',
        });

        tb.bkdBinding = layer.binding ?? null;
        tb.bkdBaseFontSize = layer.fontSize ?? 42;
        const longField = tb.bkdBinding === 'tesekkur_metni' || tb.bkdBinding === 'not';
        tb.bkdAutofit = layer.autofit ?? longField;
        tb.bkdBoxHeight = layer.boxHeight ?? null;
        this.canvas.add(tb);

        if (select && this.mode !== 'generate') {
            this.canvas.setActiveObject(tb);
        }
        return tb;
    }

    /**
     * Bir yazı katmanının satır genişliklerinden en genişini döndürür.
     */
    maxLineWidth(tb) {
        try {
            const n = tb.textLines ? tb.textLines.length : 0;
            let max = 0;
            for (let i = 0; i < n; i += 1) {
                const w = tb.getLineWidth(i);
                if (w > max) {
                    max = w;
                }
            }
            return max || tb.width;
        } catch (e) {
            return tb.width;
        }
    }

    /**
     * Metni katman genişliğine sarar; taşarsa yazı boyutunu kutuya sığana
     * kadar otomatik küçültür (yatay + dikey taşmayı önler).
     */
    applyAutofit(tb) {
        if (!tb || tb.type !== 'textbox' || !tb.bkdAutofit) {
            return;
        }

        const maxW = tb.width;
        const maxH = tb.bkdBoxHeight || (this.naturalH * 0.92 - tb.top);

        let size = tb.bkdBaseFontSize || tb.fontSize;
        const minSize = Math.max(8, Math.round(size * 0.35));
        const recalc = () => {
            if (typeof tb.initDimensions === 'function') {
                tb.initDimensions();
            }
        };

        tb.set('fontSize', size);
        recalc();

        let guard = 0;
        const overflowing = () => (maxH > 0 && tb.height > maxH) || (this.maxLineWidth(tb) > maxW + 0.5);

        while (overflowing() && size > minSize && guard < 600) {
            size -= 1;
            tb.set('fontSize', size);
            recalc();
            guard += 1;
        }

        tb.setCoords();
    }

    textboxes() {
        return this.canvas.getObjects().filter((o) => o.type === 'textbox');
    }

    usedFonts() {
        const set = new Set(['Inter']);
        this.textboxes().forEach((o) => {
            if (o.fontFamily) {
                set.add(o.fontFamily);
            }
        });
        return [...set];
    }

    /**
     * bakeText=false (tasarım): bağlı katmanlarda metin boş kaydedilir (veriden gelir).
     * bakeText=true  (snapshot): ekrandaki metin de saklanır (tekrar düzenlemek için).
     */
    serialize(bakeText = false) {
        return this.textboxes().map((o) => ({
            type: 'text',
            binding: o.bkdBinding ?? null,
            text: o.bkdBinding && !bakeText ? '' : o.text,
            left: Math.round(o.left),
            top: Math.round(o.top),
            width: Math.round(o.width),
            // tasarımda temel (küçültülmemiş) boyut, snapshot'ta gerçek boyut saklanır
            fontSize: Math.round(this.mode === 'design' ? (o.bkdBaseFontSize ?? o.fontSize) : o.fontSize),
            fontFamily: o.fontFamily,
            fill: o.fill,
            fontWeight: o.fontWeight,
            fontStyle: o.fontStyle,
            underline: !!o.underline,
            textAlign: o.textAlign,
            lineHeight: o.lineHeight,
            angle: Math.round(o.angle || 0),
            autofit: !!o.bkdAutofit,
            boxHeight: o.bkdBoxHeight ?? null,
        }));
    }

    async exportDataUrl() {
        await ensureFontsLoaded(this.usedFonts());
        this.textboxes().forEach((tb) => this.applyAutofit(tb));
        this.canvas.discardActiveObject();
        this.canvas.requestRenderAll();
        return this.canvas.toDataURL({
            format: 'png',
            multiplier: this.scale ? 1 / this.scale : 1,
        });
    }

    dispose() {
        try {
            this.canvas.dispose();
        } catch (e) {
            /* yoksay */
        }
    }
}

/* ---------------------------------------------------------------------------
 | Interaktif editör arayüzü (design + studio)
 | ------------------------------------------------------------------------- */

function el(tag, attrs = {}, children = []) {
    const node = document.createElement(tag);
    Object.entries(attrs).forEach(([k, v]) => {
        if (k === 'class') node.className = v;
        else if (k === 'style') node.setAttribute('style', v);
        else if (k.startsWith('on') && typeof v === 'function') node.addEventListener(k.slice(2), v);
        else if (v !== null && v !== undefined) node.setAttribute(k, v);
    });
    (Array.isArray(children) ? children : [children]).forEach((c) => {
        if (c == null) return;
        node.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
    });
    return node;
}

const BTN = 'display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;color:#0f172a;font-weight:600;font-size:13px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.05);';
const BTN_PRIMARY = 'display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;border:1px solid #0d9488;background:#0d9488;color:#fff;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.08);';
const FIELD = 'width:100%;padding:7px 9px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;background:#fff;color:#0f172a;';
const LABEL = 'display:block;font-size:12px;font-weight:600;color:#475569;margin:10px 0 4px;';

function initEditor(root) {
    if (root.dataset.posterInit === '1') {
        return;
    }
    root.dataset.posterInit = '1';

    const configEl = root.querySelector('[data-poster-config]');
    if (!configEl) {
        return;
    }

    let config;
    try {
        config = JSON.parse(configEl.textContent);
    } catch (e) {
        return;
    }

    const stage = root.querySelector('[data-poster-stage]');
    const toolbar = root.querySelector('[data-poster-toolbar]');
    const propsPanel = root.querySelector('[data-poster-props]');
    if (!stage || !toolbar || !propsPanel) {
        return;
    }

    const canvasEl = document.createElement('canvas');
    stage.appendChild(canvasEl);

    loadFabric().then(async (fabric) => {
        const displayWidth = stage.clientWidth || 700;
        const pc = new PosterCanvas(fabric, canvasEl, config);
        await pc.init(displayWidth);

        buildToolbar(toolbar, pc, config);
        const refreshProps = buildPropsPanel(propsPanel, pc, config);

        pc.canvas.on('selection:created', refreshProps);
        pc.canvas.on('selection:updated', refreshProps);
        pc.canvas.on('selection:cleared', refreshProps);

        // genişlik değişince (yan tutamaç) veya metin düzenlenince yeniden sığdır
        pc.canvas.on('object:modified', (e) => {
            if (e.target && e.target.type === 'textbox') {
                pc.applyAutofit(e.target);
                pc.canvas.requestRenderAll();
                refreshProps();
            }
        });
        pc.canvas.on('text:changed', (e) => {
            if (e.target) {
                pc.applyAutofit(e.target);
                pc.canvas.requestRenderAll();
            }
        });

        refreshProps();

        // pencere yeniden boyutlanınca ölçeği koru (basit)
        window.addEventListener('resize', () => {
            const w = stage.clientWidth || displayWidth;
            pc.applyDisplaySize(w);
            pc.canvas.requestRenderAll();
        });
    });
}

function buildToolbar(toolbar, pc, config) {
    toolbar.innerHTML = '';

    const addBtn = el('button', { type: 'button', style: BTN, onclick: () => {
        pc.addLayer({
            binding: null,
            text: 'Yeni metin',
            left: Math.round(pc.naturalW * 0.15),
            top: Math.round(pc.naturalH * 0.4),
            width: Math.round(pc.naturalW * 0.7),
            fontSize: 44,
            fontFamily: 'Inter',
            fill: '#1d4ed8',
            textAlign: 'center',
        });
        pc.canvas.requestRenderAll();
    } }, '+ Yazı ekle');
    toolbar.appendChild(addBtn);

    if (config.mode === 'design') {
        const saveBtn = el('button', { type: 'button', style: BTN_PRIMARY, onclick: () => {
            const layout = pc.serialize(false);
            window.dispatchEvent(new CustomEvent('poster-save', {
                detail: { layout, width: pc.naturalW, height: pc.naturalH },
            }));
        } }, 'Şablonu kaydet');
        toolbar.appendChild(saveBtn);
    }

    if (config.mode === 'studio') {
        const saveBtn = el('button', { type: 'button', style: BTN_PRIMARY, onclick: async (e) => {
            const btn = e.currentTarget;
            btn.disabled = true;
            btn.textContent = 'Kaydediliyor...';
            try {
                const dataUrl = await pc.exportDataUrl();
                const fd = new FormData();
                fd.append('layout_snapshot', JSON.stringify(pc.serialize(true)));
                fd.append('image', dataUrlToBlob(dataUrl), 'poster.png');
                const res = await fetch(config.saveUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
                    body: fd,
                });
                if (!res.ok) throw new Error('Kaydedilemedi');
                if (config.returnUrl) {
                    window.location.href = config.returnUrl;
                } else {
                    btn.textContent = 'Kaydedildi';
                }
            } catch (err) {
                btn.disabled = false;
                btn.textContent = 'Kaydet';
                alert('Afiş kaydedilemedi: ' + err.message);
            }
        } }, 'Kaydet');
        toolbar.appendChild(saveBtn);

        if (config.returnUrl) {
            toolbar.appendChild(el('a', { href: config.returnUrl, style: BTN }, 'Geri dön'));
        }
    }
}

function buildPropsPanel(panel, pc, config) {
    const placeholders = config.placeholders || {};
    const fonts = config.fonts || ['Inter'];

    const empty = el('div', { style: 'color:#64748b;font-size:13px;padding:8px 0;' },
        'Düzenlemek için bir yazı katmanına tıklayın. Sürükleyerek taşıyabilir, köşelerden boyutlandırabilirsiniz.');

    const wrap = el('div', { style: 'display:none;' });

    // Bağlama (yalnızca tasarım modunda)
    let bindingSel = null;
    if (config.mode === 'design') {
        const opts = [el('option', { value: '' }, 'Statik metin (sabit)')];
        Object.entries(placeholders).forEach(([key, label]) => {
            opts.push(el('option', { value: key }, `${label}  {${key}}`));
        });
        bindingSel = el('select', { style: FIELD }, opts);
        wrap.appendChild(el('label', { style: LABEL }, 'İçerik kaynağı'));
        wrap.appendChild(bindingSel);
    }

    const textArea = el('textarea', { style: FIELD + 'min-height:64px;resize:vertical;' });
    wrap.appendChild(el('label', { style: LABEL }, 'Metin'));
    wrap.appendChild(textArea);

    const fontSel = el('select', { style: FIELD }, fonts.map((f) => el('option', { value: f }, f)));
    wrap.appendChild(el('label', { style: LABEL }, 'Yazı tipi'));
    wrap.appendChild(fontSel);

    const sizeInput = el('input', { type: 'number', min: '6', max: '400', style: FIELD });
    wrap.appendChild(el('label', { style: LABEL }, 'Yazı boyutu (px)'));
    wrap.appendChild(sizeInput);

    const colorInput = el('input', { type: 'color', style: 'width:100%;height:38px;border-radius:8px;border:1px solid #cbd5e1;background:#fff;cursor:pointer;' });
    wrap.appendChild(el('label', { style: LABEL }, 'Renk'));
    wrap.appendChild(colorInput);

    // stil butonları
    const styleRow = el('div', { style: 'display:flex;gap:6px;margin-top:8px;' });
    const boldBtn = el('button', { type: 'button', style: BTN + 'font-weight:800;flex:1;' }, 'K');
    const italicBtn = el('button', { type: 'button', style: BTN + 'font-style:italic;flex:1;' }, 'İ');
    const underlineBtn = el('button', { type: 'button', style: BTN + 'text-decoration:underline;flex:1;' }, 'A');
    styleRow.append(boldBtn, italicBtn, underlineBtn);
    wrap.appendChild(el('label', { style: LABEL }, 'Stil'));
    wrap.appendChild(styleRow);

    // hizalama
    const alignRow = el('div', { style: 'display:flex;gap:6px;margin-top:8px;' });
    const alignBtns = ['left', 'center', 'right'].map((a) =>
        el('button', { type: 'button', 'data-align': a, style: BTN + 'flex:1;' },
            a === 'left' ? 'Sol' : a === 'center' ? 'Orta' : 'Sağ'));
    alignBtns.forEach((b) => alignRow.appendChild(b));
    wrap.appendChild(el('label', { style: LABEL }, 'Hizalama'));
    wrap.appendChild(alignRow);

    const lineInput = el('input', { type: 'number', min: '0.8', max: '3', step: '0.05', style: FIELD });
    wrap.appendChild(el('label', { style: LABEL }, 'Satır aralığı'));
    wrap.appendChild(lineInput);

    // Otomatik sığdırma (uzun metinler için)
    const fitWrap = el('div', { style: 'margin-top:14px;padding:12px;border:1px dashed #cbd5e1;border-radius:10px;background:#f8fafc;' });
    const fitToggle = el('input', { type: 'checkbox', style: 'width:16px;height:16px;cursor:pointer;' });
    const fitLabel = el('label', { style: 'display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:#0f172a;cursor:pointer;' },
        [fitToggle, document.createTextNode('Kutuya sığdır (uzun metni otomatik küçült)')]);
    fitWrap.appendChild(fitLabel);
    const boxHeightInput = el('input', { type: 'number', min: '20', style: FIELD + 'margin-top:8px;' });
    fitWrap.appendChild(el('label', { style: LABEL }, 'Kutu yüksekliği (px)'));
    fitWrap.appendChild(boxHeightInput);
    fitWrap.appendChild(el('div', { style: 'font-size:11px;color:#64748b;margin-top:6px;line-height:1.4;' },
        'Genişliği yazının yan tutamaçlarını sürükleyerek ayarlayın. Metin bu genişliğe sarılır ve yüksekliğe sığana kadar küçülür.'));
    wrap.appendChild(fitWrap);

    const delBtn = el('button', { type: 'button', style: BTN + 'margin-top:14px;width:100%;border-color:#fecaca;color:#b91c1c;background:#fef2f2;' }, 'Katmanı sil');
    wrap.appendChild(delBtn);

    panel.innerHTML = '';
    panel.append(empty, wrap);

    const getActive = () => pc.canvas.getActiveObject();

    function refresh() {
        const o = getActive();
        if (!o || o.type !== 'textbox') {
            wrap.style.display = 'none';
            empty.style.display = 'block';
            return;
        }
        empty.style.display = 'none';
        wrap.style.display = 'block';

        if (bindingSel) {
            bindingSel.value = o.bkdBinding || '';
            textArea.disabled = !!o.bkdBinding;
            textArea.value = o.bkdBinding ? `{${o.bkdBinding}}` : (o.text || '');
        } else {
            textArea.value = o.text || '';
        }
        fontSel.value = o.fontFamily || 'Inter';
        sizeInput.value = Math.round(o.bkdBaseFontSize ?? o.fontSize);
        colorInput.value = toHex(o.fill);
        lineInput.value = o.lineHeight ?? 1.16;
        fitToggle.checked = !!o.bkdAutofit;
        boxHeightInput.value = o.bkdBoxHeight ?? '';
        boxHeightInput.disabled = !o.bkdAutofit;
        boldBtn.style.background = o.fontWeight === 'bold' ? '#e2e8f0' : '#fff';
        italicBtn.style.background = o.fontStyle === 'italic' ? '#e2e8f0' : '#fff';
        underlineBtn.style.background = o.underline ? '#e2e8f0' : '#fff';
        alignBtns.forEach((b) => {
            b.style.background = b.getAttribute('data-align') === o.textAlign ? '#e2e8f0' : '#fff';
        });
    }

    const apply = (fn) => {
        const o = getActive();
        if (!o) return;
        fn(o);
        o.setCoords();
        pc.canvas.requestRenderAll();
    };

    if (bindingSel) {
        bindingSel.addEventListener('change', () => apply((o) => {
            const val = bindingSel.value || null;
            o.bkdBinding = val;
            if (val === 'tesekkur_metni' || val === 'not') {
                o.bkdAutofit = true;
            }
            o.set('text', val ? `{${val}}` : 'Metin');
            pc.applyAutofit(o);
            refresh();
        }));
    }
    textArea.addEventListener('input', () => apply((o) => {
        if (o.bkdBinding) return;
        o.set('text', textArea.value);
        pc.applyAutofit(o);
    }));
    fontSel.addEventListener('change', () => apply((o) => { o.set('fontFamily', fontSel.value); pc.applyAutofit(o); }));
    sizeInput.addEventListener('input', () => apply((o) => {
        const v = parseInt(sizeInput.value, 10) || 12;
        o.bkdBaseFontSize = v;
        o.set('fontSize', v);
        pc.applyAutofit(o);
    }));
    colorInput.addEventListener('input', () => apply((o) => o.set('fill', colorInput.value)));
    lineInput.addEventListener('input', () => apply((o) => { o.set('lineHeight', parseFloat(lineInput.value) || 1.16); pc.applyAutofit(o); }));
    boldBtn.addEventListener('click', () => { apply((o) => { o.set('fontWeight', o.fontWeight === 'bold' ? 'normal' : 'bold'); pc.applyAutofit(o); }); refresh(); });
    italicBtn.addEventListener('click', () => { apply((o) => { o.set('fontStyle', o.fontStyle === 'italic' ? 'normal' : 'italic'); pc.applyAutofit(o); }); refresh(); });
    underlineBtn.addEventListener('click', () => { apply((o) => o.set('underline', !o.underline)); refresh(); });
    fitToggle.addEventListener('change', () => { apply((o) => {
        o.bkdAutofit = fitToggle.checked;
        if (fitToggle.checked && !o.bkdBoxHeight) {
            o.bkdBoxHeight = Math.round(o.height);
        }
        pc.applyAutofit(o);
    }); refresh(); });
    boxHeightInput.addEventListener('input', () => apply((o) => {
        o.bkdBoxHeight = parseInt(boxHeightInput.value, 10) || null;
        pc.applyAutofit(o);
    }));
    alignBtns.forEach((b) => b.addEventListener('click', () => {
        apply((o) => o.set('textAlign', b.getAttribute('data-align')));
        refresh();
    }));
    delBtn.addEventListener('click', () => {
        const o = getActive();
        if (o) {
            pc.canvas.remove(o);
            pc.canvas.discardActiveObject();
            pc.canvas.requestRenderAll();
            refresh();
        }
    });

    return refresh;
}

function toHex(fill) {
    if (typeof fill !== 'string') return '#1d4ed8';
    if (fill.startsWith('#')) {
        if (fill.length === 4) {
            return '#' + fill.slice(1).split('').map((c) => c + c).join('');
        }
        return fill.slice(0, 7);
    }
    const m = /rgba?\((\d+),\s*(\d+),\s*(\d+)/.exec(fill);
    if (m) {
        return '#' + [m[1], m[2], m[3]].map((n) => parseInt(n, 10).toString(16).padStart(2, '0')).join('');
    }
    return '#1d4ed8';
}

/* ---------------------------------------------------------------------------
 | Sessiz (offscreen) üretim
 | ------------------------------------------------------------------------- */

async function runGenerateJob(job) {
    const fabric = await loadFabric();
    const holder = el('div', { style: 'position:fixed;left:-99999px;top:0;width:1px;height:1px;overflow:hidden;' });
    const canvasEl = document.createElement('canvas');
    holder.appendChild(canvasEl);
    document.body.appendChild(holder);

    const pc = new PosterCanvas(fabric, canvasEl, { ...job, mode: 'generate' });
    try {
        await pc.init(job.canvasWidth || null);
        const dataUrl = await pc.exportDataUrl();
        const fd = new FormData();
        fd.append('donation_id', job.donationId);
        fd.append('type', job.type);
        fd.append('template_id', job.templateId);
        fd.append('layout_snapshot', JSON.stringify(pc.serialize(true)));
        fd.append('image', dataUrlToBlob(dataUrl), 'poster.png');

        const res = await fetch(job.uploadUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': job.csrf || csrfToken(), Accept: 'application/json' },
            body: fd,
        });
        if (!res.ok) {
            throw new Error('Sunucu hatası (' + res.status + ')');
        }
        return await res.json();
    } finally {
        pc.dispose();
        holder.remove();
    }
}

async function handleGenerate(payload) {
    const jobs = Array.isArray(payload?.jobs) ? payload.jobs : (Array.isArray(payload) ? payload : []);
    if (!jobs.length) {
        return;
    }
    let ok = 0;
    for (const job of jobs) {
        try {
            // eslint-disable-next-line no-await-in-loop
            await runGenerateJob(job);
            ok += 1;
        } catch (e) {
            // eslint-disable-next-line no-console
            console.error('Afiş üretilemedi:', e);
        }
    }
    if (window.Livewire) {
        window.Livewire.dispatch('bkd-poster-saved', { ok });
    }
}

/* ---------------------------------------------------------------------------
 | Bootstrap
 | ------------------------------------------------------------------------- */

function bootEditors() {
    document.querySelectorAll('[data-poster-editor]').forEach(initEditor);
}

function registerLivewireListener() {
    if (window.__bkdPosterGenReg || !window.Livewire) {
        return;
    }
    window.__bkdPosterGenReg = true;
    window.Livewire.on('bkd-generate-poster', handleGenerate);
}

document.addEventListener('DOMContentLoaded', () => {
    bootEditors();
    registerLivewireListener();
});
document.addEventListener('livewire:init', registerLivewireListener);
document.addEventListener('livewire:navigated', () => {
    bootEditors();
    registerLivewireListener();
});

// Modül geç yüklenirse (Livewire zaten hazırsa) yine de bağlan
registerLivewireListener();
bootEditors();
