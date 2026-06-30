<style>
    .poster-workspace {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        min-height: calc(100vh - 12rem);
        border-radius: 1rem;
        padding: 1.5rem;
    }
    .dark .poster-workspace {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    }
    .poster-canvas-wrap {
        max-width: min(100%, 720px);
        margin: 0 auto;
        box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        border-radius: 0.5rem;
        overflow: hidden;
    }
    #poster-canvas {
        position: relative;
        width: 100%;
    }
    .poster-field {
        position: absolute;
        border: 2px dashed transparent;
        border-radius: 4px;
        transition: border-color 0.15s, box-shadow 0.15s;
        touch-action: none;
        cursor: move;
        box-sizing: border-box;
    }
    .poster-field:hover {
        border-color: rgb(59 130 246 / 0.45);
    }
    .poster-field.is-selected {
        border-color: #2563eb;
        border-style: solid;
        box-shadow: 0 0 0 3px rgb(37 99 235 / 0.2);
        z-index: 20;
    }
    .poster-field-content {
        width: 100%;
        height: 100%;
        overflow: hidden;
        padding: 2px 4px;
        line-height: 1.35;
        word-break: break-word;
        pointer-events: none;
        box-sizing: border-box;
    }
    .poster-field.is-selected .poster-field-content {
        pointer-events: auto;
    }
    .poster-field-content[contenteditable="true"] {
        outline: none;
        background: rgb(255 255 255 / 0.85);
        border-radius: 2px;
    }
    .poster-handle {
        position: absolute;
        width: 10px;
        height: 10px;
        background: #2563eb;
        border: 2px solid #fff;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgb(0 0 0 / 0.3);
        z-index: 30;
    }
    .poster-handle.se { bottom: -5px; right: -5px; cursor: se-resize; }
    .poster-handle.sw { bottom: -5px; left: -5px; cursor: sw-resize; }
    .poster-handle.ne { top: -5px; right: -5px; cursor: ne-resize; }
    .poster-handle.nw { top: -5px; left: -5px; cursor: nw-resize; }
    .poster-toolbar {
        position: sticky;
        bottom: 1rem;
        z-index: 40;
        margin-top: 1rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: rgb(255 255 255 / 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15);
    }
    .dark .poster-toolbar {
        background: rgb(15 23 42 / 0.95);
        border-color: #334155;
    }
    .poster-toolbar-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        height: 2.25rem;
        padding: 0 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        font-size: 0.875rem;
        color: #334155;
        transition: all 0.15s;
        cursor: pointer;
    }
    .dark .poster-toolbar-btn {
        background: #1e293b;
        border-color: #475569;
        color: #e2e8f0;
    }
    .poster-toolbar-btn:hover { background: #f8fafc; }
    .poster-toolbar-btn.is-active {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }
    .poster-chip {
        border-radius: 9999px;
        padding: 0.35rem 0.85rem;
        font-size: 0.75rem;
        font-weight: 500;
        border: 1px solid #cbd5e1;
        background: #fff;
        transition: all 0.15s;
        cursor: pointer;
    }
    .poster-chip.is-active {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }
    .poster-page-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .poster-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgb(0 0 0 / 0.06);
    }
    .poster-page-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgb(0 0 0 / 0.12);
    }
    .poster-page-btn:disabled {
        opacity: 0.65;
        cursor: wait;
    }
    .poster-page-btn--info {
        background: #0ea5e9;
        color: #fff;
    }
    .poster-page-btn--warning {
        background: #f59e0b;
        color: #fff;
    }
    .poster-page-btn--primary {
        background: #0d9488;
        color: #fff;
    }
    .poster-page-btn--success {
        background: #10b981;
        color: #fff;
    }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Lora:wght@400;600;700&display=swap" rel="stylesheet">
