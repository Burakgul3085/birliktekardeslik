{{-- FilePond: iptal alanı tıklanmıyor + Filament/ Alpine pond.removeFile ile kesin iptal --}}
<style>
    .fi-fo-file-upload .filepond--file {
        position: relative;
    }

    .fi-fo-file-upload .filepond--file-status,
    .fi-fo-file-upload .filepond--file-status-main,
    .fi-fo-file-upload .filepond--file-status-sub {
        position: relative;
        z-index: 20 !important;
        pointer-events: auto !important;
    }

    .fi-fo-file-upload .filepond--file-info-main,
    .fi-fo-file-upload .filepond--file-info-sub {
        position: relative;
        z-index: 15;
    }

    .fi-fo-file-upload .filepond--process-indicator,
    .fi-fo-file-upload .filepond--load-indicator {
        pointer-events: none !important;
    }

    .fi-fo-file-upload .filepond--image-preview-overlay,
    .fi-fo-file-upload .filepond--file .filepond--image-preview-wrapper {
        pointer-events: none !important;
    }

    .fi-fo-file-upload .filepond--action-abort-item-processing,
    .fi-fo-file-upload .filepond--action-abort-item-load {
        position: relative;
        z-index: 30 !important;
        pointer-events: auto !important;
    }
</style>
<script>
    (function () {
        function getFilePondIdFromItem(item) {
            if (!item || !item.id) {
                return null;
            }
            if (!item.id.startsWith('filepond--item-')) {
                return null;
            }
            return item.id.replace(/^filepond--item-/, '');
        }

        function getPondFromHost(host) {
            if (!host) {
                return null;
            }
            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                try {
                    const d = window.Alpine.$data(host);
                    if (d && d.pond) {
                        return d.pond;
                    }
                } catch (e) {
                    /* ignore */
                }
            }
            if (host._x_dataStack && host._x_dataStack[0] && host._x_dataStack[0].pond) {
                return host._x_dataStack[0].pond;
            }
            if (window.FilePond) {
                const root = host.querySelector('.filepond--root') || host.closest('.filepond--root');
                if (root) {
                    return window.FilePond.find(root);
                }
            }
            return null;
        }

        function isAbortableItem(item) {
            return (
                item.querySelector(
                    [
                        '.filepond--action-abort-item-processing',
                        '.filepond--action-abort-item-load',
                        '.filepond--load-indicator',
                        '.filepond--process-indicator',
                    ].join(','),
                ) !== null
            );
        }

        function isInFileRow(target) {
            return Boolean(target.closest('.filepond--file'));
        }

        function tryRemoveFile(item) {
            const fileId = getFilePondIdFromItem(item);
            if (!fileId) {
                return false;
            }
            const host = item.closest('.fi-fo-file-upload');
            const pond = getPondFromHost(host);
            if (pond && typeof pond.removeFile === 'function') {
                pond.removeFile(fileId);
                return true;
            }
            const btn =
                item.querySelector('.filepond--action-abort-item-processing') ||
                item.querySelector('.filepond--action-abort-item-load');
            if (btn) {
                btn.dispatchEvent(
                    new MouseEvent('click', { bubbles: true, cancelable: true, view: window }),
                );
                return true;
            }
            return false;
        }

        function handleEvent(e) {
            if (!e.target || !e.target.closest) {
                return;
            }
            if (!e.target.closest('.fi-fo-file-upload')) {
                return;
            }
            if (e.target.closest('.filepond--action-remove-item')) {
                return;
            }
            if (e.target.closest('.filepond--action-abort-item-processing, .filepond--action-abort-item-load')) {
                return;
            }

            const item = e.target.closest('.filepond--item');
            if (!item || !isAbortableItem(item)) {
                return;
            }
            if (!isInFileRow(e.target)) {
                return;
            }

            if (tryRemoveFile(item)) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            }
        }

        document.addEventListener('pointerdown', handleEvent, true);
        document.addEventListener('click', handleEvent, true);
    })();
</script>
