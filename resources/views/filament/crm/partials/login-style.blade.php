@include('filament.admin.partials.login-style')

<style>
    .fi-simple-layout .bkd-login-hero {
        background: linear-gradient(130deg, #0d9488, #14b8a6);
    }

    /* Dashboard filtreleri */
    .crm-dashboard-filters .fi-fo-field-wrp,
    .crm-dashboard-filters .choices,
    .crm-dashboard-filters .fi-select-input {
        min-width: 0;
        width: 100%;
    }

    .crm-dashboard-filters .fi-section-content {
        overflow: visible;
    }

    /* Afiş tasarımcısı */
    .bkd-poster-layout {
        display: flex;
        gap: 18px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .bkd-poster-stage {
        flex: 1 1 520px;
        min-width: 0;
        width: 100%;
        background: #f1f5f9;
        border-radius: 14px;
        padding: 14px;
        overflow: auto;
        display: flex;
        justify-content: center;
        -webkit-overflow-scrolling: touch;
    }

    .bkd-poster-props {
        width: 300px;
        flex: 0 0 300px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
    }

    .bkd-poster-toolbar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    /* Genel CRM mobil düzen */
    .fi-ta-content {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .fi-header-actions,
    .fi-ta-header-actions,
    .fi-ta-actions {
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .fi-fo-tabs-tab {
        white-space: nowrap;
    }

    .fi-tabs {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 768px) {
        #bkd-live-clock {
            display: none !important;
        }

        .fi-simple-layout .fi-simple-main {
            margin: 0.75rem;
            max-width: calc(100vw - 1.5rem);
        }

        .bkd-poster-props {
            width: 100%;
            flex: 1 1 100%;
        }

        .bkd-poster-stage {
            flex: 1 1 100%;
        }

        .fi-page-header-heading {
            font-size: 1.125rem;
            line-height: 1.4;
            word-break: break-word;
        }

        .fi-ta-filters .fi-fo-component-ctn {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 480px) {
        .fi-simple-layout .bkd-login-hero {
            padding: 0.75rem;
            font-size: 0.9rem;
        }
    }
</style>
