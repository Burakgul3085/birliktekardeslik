<style>
    .fi-simple-layout {
        background:
            radial-gradient(circle at 20% 20%, rgba(47, 142, 163, 0.24), transparent 40%),
            radial-gradient(circle at 80% 10%, rgba(91, 175, 193, 0.22), transparent 45%),
            linear-gradient(140deg, #f4fbfc 0%, #edf6f8 45%, #e7f1f4 100%);
        min-height: 100vh;
    }

    .fi-simple-layout .fi-simple-main {
        border-radius: 1rem;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .fi-simple-layout .bkd-login-hero {
        margin-bottom: 1rem;
        border-radius: 0.9rem;
        padding: 0.9rem 1rem;
        background: linear-gradient(130deg, #2f8ea3, #5bafc1);
        color: #fff;
    }

    .fi-simple-layout .bkd-login-back {
        margin-top: 0.75rem;
        margin-bottom: 0.25rem;
        text-align: center;
    }

    .fi-simple-layout .fi-simple-main .bkd-login-back__btn {
        display: block;
        width: 100%;
        box-sizing: border-box;
        margin: 0;
        padding: 0.65rem 1rem;
        text-align: center;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.25;
        color: #fff !important;
        text-decoration: none;
        border: none;
        border-radius: 0.75rem;
        background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        cursor: pointer;
        transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
    }

    .fi-simple-layout .fi-simple-main .bkd-login-back__btn:hover {
        background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 6px 18px rgba(29, 78, 216, 0.4);
        transform: translateY(-1px);
    }

    .fi-simple-layout .fi-simple-main .bkd-login-back__btn:focus-visible {
        outline: 2px solid #93c5fd;
        outline-offset: 2px;
    }

    .fi-simple-layout .bkd-login-forgot__btn {
        display: inline-flex;
        margin-top: 0.6rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #0e7490;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .fi-simple-layout .bkd-login-forgot__btn:hover {
        color: #0f172a;
        text-decoration: underline;
    }

    .fi-simple-layout .bkd-values {
        margin-top: 1rem;
        border-radius: 0.9rem;
        border: 1px solid rgba(15, 23, 42, 0.06);
        background: rgba(255, 255, 255, 0.9);
        padding: 0.9rem 1rem;
    }

    .fi-simple-layout .bkd-values h4 {
        margin: 0 0 0.4rem;
        color: #0f172a;
        font-size: 0.92rem;
        font-weight: 700;
    }

    .fi-simple-layout .bkd-values ul {
        margin: 0;
        padding-left: 1rem;
        color: #334155;
        font-size: 0.82rem;
        line-height: 1.5;
    }
</style>
