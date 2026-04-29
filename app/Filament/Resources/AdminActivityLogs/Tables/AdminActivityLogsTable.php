<?php

namespace App\Filament\Resources\AdminActivityLogs\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class AdminActivityLogsTable
{
    private static array $eventTypeLabels = [
        'login'         => 'Giriş Yapıldı',
        'logout'        => 'Çıkış Yapıldı',
        'navigation'    => 'Gezinme',
        'model_created' => 'Kayıt Oluşturuldu',
        'model_updated' => 'Kayıt Güncellendi',
        'model_deleted' => 'Kayıt Silindi',
    ];

    private static array $eventTypeColors = [
        'login'         => 'success',
        'logout'        => 'warning',
        'navigation'    => 'gray',
        'model_created' => 'info',
        'model_updated' => 'primary',
        'model_deleted' => 'danger',
    ];

    private static array $methodColors = [
        'GET'    => 'gray',
        'POST'   => 'success',
        'PUT'    => 'warning',
        'PATCH'  => 'warning',
        'DELETE' => 'danger',
    ];

    private static array $moduleLabels = [
        'Setting'               => 'Ayarlar',
        'Project'               => 'Proje/Faaliyet',
        'News'                  => 'Haber',
        'BankAccount'           => 'Banka Hesabı',
        'Page'                  => 'Sayfa',
        'HeroSlide'             => 'Hero Slider',
        'MenuItem'              => 'Menü Öğesi',
        'ContactMessage'        => 'İletişim Mesajı',
        'VolunteerApplication'  => 'Gönüllü Başvurusu',
        'NewsletterSubscriber'  => 'E-Bülten Abonesi',
        'User'                  => 'Kullanıcı',
        'ActivitySectionSetting'=> 'Faaliyet Bölümü',
    ];

    private static array $routeLabels = [
        'filament.admin.resources.projects.index'                   => 'Projeler → Liste',
        'filament.admin.resources.projects.create'                  => 'Projeler → Yeni Ekle',
        'filament.admin.resources.projects.edit'                    => 'Projeler → Düzenle',
        'filament.admin.resources.news.index'                       => 'Haberler → Liste',
        'filament.admin.resources.news.create'                      => 'Haberler → Yeni Ekle',
        'filament.admin.resources.news.edit'                        => 'Haberler → Düzenle',
        'filament.admin.resources.contact-messages.index'           => 'İletişim Mesajları → Liste',
        'filament.admin.resources.volunteer-applications.index'     => 'Gönüllü Başvuruları → Liste',
        'filament.admin.resources.bank-accounts.index'              => 'Banka Hesapları → Liste',
        'filament.admin.resources.bank-accounts.create'             => 'Banka Hesapları → Yeni Ekle',
        'filament.admin.resources.bank-accounts.edit'               => 'Banka Hesapları → Düzenle',
        'filament.admin.resources.pages.index'                      => 'Sayfalar → Liste',
        'filament.admin.resources.pages.create'                     => 'Sayfalar → Yeni Ekle',
        'filament.admin.resources.pages.edit'                       => 'Sayfalar → Düzenle',
        'filament.admin.resources.hero-slides.index'                => 'Hero Slider → Liste',
        'filament.admin.resources.hero-slides.create'               => 'Hero Slider → Yeni Ekle',
        'filament.admin.resources.hero-slides.edit'                 => 'Hero Slider → Düzenle',
        'filament.admin.resources.menu-items.index'                 => 'Menü Yönetimi → Liste',
        'filament.admin.resources.newsletter-subscribers.index'     => 'E-Bülten Aboneleri → Liste',
        'filament.admin.resources.users.index'                      => 'Kullanıcılar → Liste',
        'filament.admin.resources.users.create'                     => 'Kullanıcılar → Yeni Ekle',
        'filament.admin.resources.users.edit'                       => 'Kullanıcılar → Düzenle',
        'filament.admin.resources.admin-activity-logs.index'        => 'Admin Log Kayıtları → Liste',
        'filament.admin.resources.settings.index'                   => 'Genel Ayarlar → Liste',
        'filament.admin.resources.settings.edit'                    => 'Genel Ayarlar → Düzenle',
        'filament.admin.resources.mailer-settings.index'            => 'Mailer Ayarları',
        'filament.admin.resources.activity-section-settings.index'  => 'Faaliyet Bölümü → Liste',
        'filament.admin.auth.login'                                 => 'Giriş Sayfası',
        'filament.admin.pages.dashboard'                            => 'Dashboard',
    ];

    private static function translateDescription(string $description): string
    {
        // "Admin panel etkileşimi: some.route.name" → rota etiketi ile değiştir
        if (preg_match('/Admin panel etkileşimi:\s*(.+)/', $description, $m)) {
            $route = trim($m[1]);
            return self::$routeLabels[$route] ?? ('Panel → ' . $route);
        }

        // "{ModelName} kaydı oluşturuldu/güncellendi/silindi"
        foreach (self::$moduleLabels as $eng => $tr) {
            $description = preg_replace('/\b' . preg_quote($eng, '/') . '\b/', $tr, $description);
        }

        $description = str_replace('kaydı oluşturuldu', 'oluşturuldu', $description);
        $description = str_replace('kaydı güncellendi', 'güncellendi', $description);
        $description = str_replace('kaydı silindi', 'silindi', $description);

        return $description;
    }

    private static function translateEvent(string $event): string
    {
        return self::$eventTypeLabels[$event] ?? $event;
    }

    protected static function columns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Tarih')
                ->dateTime('d.m.Y H:i:s')
                ->sortable(),

            TextColumn::make('causer.name')
                ->label('Kullanıcı')
                ->searchable()
                ->placeholder('-'),

            TextColumn::make('event_type')
                ->label('Olay Tipi')
                ->badge()
                ->formatStateUsing(fn (?string $state): string => $state ? (self::$eventTypeLabels[$state] ?? $state) : '-')
                ->color(fn (?string $state): string => self::$eventTypeColors[$state] ?? 'gray')
                ->searchable(),

            TextColumn::make('description')
                ->label('Aksiyon')
                ->formatStateUsing(fn (?string $state): string => $state ? self::translateDescription($state) : '-')
                ->searchable()
                ->wrap(),

            TextColumn::make('subject_type')
                ->label('Modül')
                ->formatStateUsing(fn (?string $state): string => $state ? (self::$moduleLabels[class_basename($state)] ?? class_basename($state)) : '-'),

            TextColumn::make('subject_id')
                ->label('Modül ID')
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('route_name')
                ->label('Rota')
                ->formatStateUsing(fn (?string $state): string => $state ? (self::$routeLabels[$state] ?? $state) : '-')
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('method')
                ->label('Yöntem')
                ->badge()
                ->color(fn (?string $state): string => self::$methodColors[$state] ?? 'gray'),

            TextColumn::make('ip_address')
                ->label('IP')
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected static function filters(): array
    {
        return [
            SelectFilter::make('causer_id')
                ->label('Kullanıcıya Göre')
                ->options(User::query()->orderBy('name')->pluck('name', 'id')),
            SelectFilter::make('event_type')
                ->label('Olay Tipi')
                ->options([
                    'login' => 'Giriş',
                    'logout' => 'Çıkış',
                    'navigation' => 'Tıklama/Gezinme',
                    'model_created' => 'Kayıt Oluşturma',
                    'model_updated' => 'Kayıt Güncelleme',
                    'model_deleted' => 'Kayıt Silme',
                ]),
            SelectFilter::make('subject_type')
                ->label('Modül')
                ->options([
                    'App\Models\Setting' => 'Ayarlar',
                    'App\Models\Project' => 'Projeler',
                    'App\Models\News' => 'Haberler',
                    'App\Models\BankAccount' => 'Banka Hesapları',
                    'App\Models\Page' => 'Sayfalar',
                    'App\Models\HeroSlide' => 'Hero Slider',
                    'App\Models\MenuItem' => 'Menü',
                    'App\Models\ContactMessage' => 'İletişim Mesajları',
                    'App\Models\VolunteerApplication' => 'Gönüllü Başvuruları',
                    'App\Models\NewsletterSubscriber' => 'E-Bülten Aboneleri',
                    'App\Models\User' => 'Kullanıcılar',
                ]),
            SelectFilter::make('method')
                ->label('HTTP Yöntemi')
                ->options([
                    'GET' => 'GET',
                    'POST' => 'POST',
                    'PUT' => 'PUT',
                    'PATCH' => 'PATCH',
                    'DELETE' => 'DELETE',
                ]),
            Filter::make('date_range')
                ->label('Tarih Aralığı')
                ->form([
                    DatePicker::make('from')->label('Başlangıç'),
                    DatePicker::make('until')->label('Bitiş'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                }),
            Filter::make('advanced')
                ->label('Gelişmiş Filtre')
                ->form([
                    TextInput::make('ip')->label('IP adresi'),
                    TextInput::make('route')->label('Rota adi'),
                    TextInput::make('keyword')->label('Aksiyon anahtar kelime'),
                    TextInput::make('subject_id')->label('Modül ID')->numeric(),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['ip'] ?? null, fn (Builder $q, $ip) => $q->where('ip_address', 'like', "%{$ip}%"))
                        ->when($data['route'] ?? null, fn (Builder $q, $route) => $q->where('route_name', 'like', "%{$route}%"))
                        ->when($data['keyword'] ?? null, fn (Builder $q, $keyword) => $q->where('description', 'like', "%{$keyword}%"))
                        ->when($data['subject_id'] ?? null, fn (Builder $q, $subjectId) => $q->where('subject_id', $subjectId));
                }),
            Filter::make('changed_records_only')
                ->label('Sadece Değişiklik İşlemleri')
                ->query(fn (Builder $query): Builder => $query->whereIn('event_type', ['model_created', 'model_updated', 'model_deleted'])),
        ];
    }

    protected static function recordActions(): array
    {
        return [
            Action::make('detay')
                ->label('Detay')
                ->icon('heroicon-o-eye')
                ->modalHeading('Log Detayı')
                ->modalContent(function ($record) {
                    $properties = $record->properties ?? [];
                    $prettyJson = empty($properties)
                        ? 'Değişiklik kaydı yok'
                        : json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    $olay    = self::$eventTypeLabels[$record->event_type] ?? $record->event_type;
                    $aciklama = $record->description ? self::translateDescription($record->description) : '-';
                    $modul   = $record->subject_type ? (self::$moduleLabels[class_basename($record->subject_type)] ?? class_basename($record->subject_type)) : '-';
                    $rota    = $record->route_name ? (self::$routeLabels[$record->route_name] ?? $record->route_name) : '-';

                    $html  = '<div style="font-size:13px; line-height:1.8;">';
                    $html .= '<table style="width:100%; border-collapse:collapse;">';
                    $html .= '<tr><td style="padding:4px 8px; font-weight:600; width:140px;">Kullanıcı</td><td style="padding:4px 8px;">' . e(optional($record->causer)->name ?? '-') . '</td></tr>';
                    $html .= '<tr style="background:#f8fafc;"><td style="padding:4px 8px; font-weight:600;">Olay Tipi</td><td style="padding:4px 8px;">' . e($olay) . '</td></tr>';
                    $html .= '<tr><td style="padding:4px 8px; font-weight:600;">Aksiyon</td><td style="padding:4px 8px;">' . e($aciklama) . '</td></tr>';
                    $html .= '<tr style="background:#f8fafc;"><td style="padding:4px 8px; font-weight:600;">Modül</td><td style="padding:4px 8px;">' . e($modul) . ($record->subject_id ? ' (ID: ' . $record->subject_id . ')' : '') . '</td></tr>';
                    $html .= '<tr><td style="padding:4px 8px; font-weight:600;">Sayfa/Rota</td><td style="padding:4px 8px;">' . e($rota) . '</td></tr>';
                    $html .= '<tr style="background:#f8fafc;"><td style="padding:4px 8px; font-weight:600;">Yöntem</td><td style="padding:4px 8px;">' . e($record->method ?? '-') . '</td></tr>';
                    $html .= '<tr><td style="padding:4px 8px; font-weight:600;">IP Adresi</td><td style="padding:4px 8px;">' . e($record->ip_address ?? '-') . '</td></tr>';
                    $html .= '<tr style="background:#f8fafc;"><td style="padding:4px 8px; font-weight:600;">Tarih</td><td style="padding:4px 8px;">' . e(optional($record->created_at)->format('d.m.Y H:i:s')) . '</td></tr>';
                    $html .= '</table>';

                    if (!empty($properties) && is_array($properties)) {
                        $html .= '<p style="margin-top:12px; font-weight:600;">Değişiklik Detayı (JSON):</p>';
                        $html .= '<pre style="max-height:300px; overflow:auto; background:#0f172a; color:#e2e8f0; padding:12px; border-radius:8px; font-size:12px;">' . e($prettyJson) . '</pre>';
                    }

                    $html .= '</div>';

                    return new HtmlString($html);
                })
                ->modalSubmitAction(false),
            DeleteAction::make()
                ->label('Sil')
                ->requiresConfirmation(),
        ];
    }

    protected static function toolbarActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->label('Seçilenleri sil')
                    ->requiresConfirmation(),
            ]),
        ];
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query): Builder {
                $quick = request()->query('quick');

                return match ($quick) {
                    'suspicious' => $query->whereIn('causer_id', function ($subQuery) {
                        $subQuery->select('causer_id')
                            ->from('admin_activity_logs')
                            ->whereNotNull('causer_id')
                            ->where('created_at', '>=', now()->subMinutes(15))
                            ->groupBy('causer_id')
                            ->havingRaw('COUNT(*) >= 40');
                    }),
                    'changes' => $query->whereIn('event_type', ['model_created', 'model_updated', 'model_deleted']),
                    'today_logins' => $query
                        ->where('event_type', 'login')
                        ->whereDate('created_at', now()->toDateString()),
                    default => $query,
                };
            })
            ->columns(self::columns())
            ->filters(self::filters())
            ->recordActions(self::recordActions())
            ->toolbarActions(self::toolbarActions());
    }
}
