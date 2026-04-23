<?php

namespace App\Filament\Resources\AdminActivityLogs\Tables;

use App\Filament\Exports\AdminActivityLogExporter;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
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
            ->columns([
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i:s')->sortable(),
                TextColumn::make('causer.name')->label('Kullanıcı')->searchable()->placeholder('-'),
                TextColumn::make('event_type')->label('Olay Tipi')->badge()->searchable(),
                TextColumn::make('description')->label('Aksiyon')->searchable()->wrap(),
                TextColumn::make('subject_type')->label('Modül')->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
                TextColumn::make('subject_id')->label('Modül ID')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('route_name')->label('Rota')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('method')->label('Yöntem')->badge(),
                TextColumn::make('ip_address')->label('IP')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ])
            ->recordActions([
                Action::make('detay')
                    ->label('Detay')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Log Detayı')
                    ->modalContent(function ($record) {
                        $properties = $record->properties ?? [];
                        $prettyJson = empty($properties)
                            ? '{}'
                            : json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        $html = '<div style="font-size:13px; line-height:1.6;">';
                        $html .= '<p><strong>Kullanıcı:</strong> ' . e(optional($record->causer)->name ?? '-') . '</p>';
                        $html .= '<p><strong>Olay:</strong> ' . e($record->event_type) . '</p>';
                        $html .= '<p><strong>Açıklama:</strong> ' . e($record->description) . '</p>';
                        $html .= '<p><strong>Rota:</strong> ' . e($record->route_name ?? '-') . '</p>';
                        $html .= '<p><strong>URL:</strong> ' . e($record->url ?? '-') . '</p>';
                        $html .= '<p><strong>Tarih:</strong> ' . e(optional($record->created_at)->format('d.m.Y H:i:s')) . '</p>';
                        $html .= '<p><strong>JSON Değişiklik:</strong></p>';
                        $html .= '<pre style="max-height:300px; overflow:auto; background:#0f172a; color:#e2e8f0; padding:12px; border-radius:8px;">' . e($prettyJson) . '</pre>';
                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false),
            ])
            ->toolbarActions([
                ExportAction::make('disa_aktar')
                    ->label('CSV Dışa Aktar')
                    ->exporter(AdminActivityLogExporter::class),
            ]);
    }
}
