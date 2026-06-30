<?php

namespace App\Providers\Filament;

use App\Filament\Crm\Auth\Login;
use App\Filament\Crm\Pages\CrmDashboard;
use App\Models\Setting;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CrmPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $canDelete = fn (): bool => auth('crm')->check()
            && (auth('crm')->user()?->canDeleteRecords() ?? false);

        DeleteAction::configureUsing(
            fn (DeleteAction $action): DeleteAction => $action->visible($canDelete),
        );

        DeleteBulkAction::configureUsing(
            fn (DeleteBulkAction $action): DeleteBulkAction => $action->visible($canDelete),
        );

        return $panel
            ->id('crm')
            ->path('crm')
            ->login(Login::class)
            ->authGuard('crm')
            ->brandName('BKD Bağış Yönetimi')
            ->brandLogo(fn (): string => Setting::current()->logo
                ? asset('storage/' . Setting::current()->logo)
                : asset('images/default-logo.svg'))
            ->favicon(fn (): string => Setting::current()->favicon
                ? asset('storage/' . Setting::current()->favicon)
                : asset('images/default-logo.svg'))
            ->colors([
                'primary' => Color::Teal,
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
            ])
            ->discoverResources(in: app_path('Filament/Crm/Resources'), for: 'App\Filament\Crm\Resources')
            ->discoverPages(in: app_path('Filament/Crm/Pages'), for: 'App\Filament\Crm\Pages')
            ->pages([
                CrmDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Crm/Widgets'), for: 'App\Filament\Crm\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.crm.partials.login-style')->render(),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.crm.partials.poster-assets')->render(),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => view('filament.crm.partials.login-hero')->render(),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): string => view('filament.crm.partials.login-footer')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => request()->routeIs('filament.crm.auth.login')
                    ? view('filament.crm.partials.login-clock')->render()
                    : '',
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
