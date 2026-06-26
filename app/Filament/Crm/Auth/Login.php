<?php

namespace App\Filament\Crm\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function getTitle(): string | Htmlable
    {
        return 'CRM Girişi';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Birlikte Kardeşlik Derneği';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString('Bağış yönetim paneline erişmek için bilgilerinizi girin.');
    }
}
