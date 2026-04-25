<?php

namespace App\Filament\Resources\VolunteerApplications\Tables;

use App\Models\Setting;
use App\Support\Mailer;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use PHPMailer\PHPMailer\Exception;

class VolunteerApplicationsTable
{
    private static function preferenceOptions(): array
    {
        $items = Setting::current()->volunteerPreferenceOptions();

        return collect($items)
            ->mapWithKeys(fn (string $item): array => [$item => $item])
            ->all();
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('first_name')->label('Ad')->searchable(),
                TextColumn::make('last_name')->label('Soyad')->searchable(),
                TextColumn::make('email')->label('E-posta')->searchable()->copyable(),
                TextColumn::make('phone')->label('Telefon')->searchable(),
                TextColumn::make('preference')->label('Tercih')->badge(),
                TextColumn::make('about')->label('Kendinden Bahset')->limit(70)->wrap()->searchable(),
                IconColumn::make('is_replied')->label('Yanıtlandı')->boolean(),
            ])
            ->filters([
                SelectFilter::make('preference')
                    ->label('Gönüllülük Tercihi')
                    ->options(self::preferenceOptions()),
                TernaryFilter::make('is_replied')
                    ->label('Yanıt Durumu')
                    ->trueLabel('Yanıtlananlar')
                    ->falseLabel('Yanıtlanmayanlar')
                    ->placeholder('Tümü'),
            ])
            ->recordActions([
                Action::make('yanitla')
                    ->label('Cevap Ver')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->modalHeading('Gönüllü Başvurusuna Cevap Ver')
                    ->form([
                        TextInput::make('subject')
                            ->label('Konu')
                            ->required()
                            ->maxLength(180)
                            ->default('Gönüllülük Başvurunuz Hakkında'),
                        Textarea::make('body')
                            ->label('Mesaj')
                            ->rows(8)
                            ->required()
                            ->maxLength(10000),
                    ])
                    ->action(function ($record, array $data): void {
                        $settings = Setting::current();
                        $replyToEmail = $settings->mailer_notification_email ?: $settings->email;

                        try {
                            $html = view('emails.volunteer-reply', [
                                'application' => $record,
                                'subject' => $data['subject'],
                                'body' => $data['body'],
                                'siteTitle' => $settings->site_title ?? config('app.name'),
                            ])->render();

                            Mailer::send(
                                $record->email,
                                trim($record->first_name . ' ' . $record->last_name),
                                $data['subject'],
                                $html,
                                $replyToEmail,
                            );

                            $record->update([
                                'is_replied' => true,
                                'reply_subject' => $data['subject'],
                                'reply_message' => $data['body'],
                                'replied_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Yanıt e-postası gönderildi')
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('E-posta gönderilemedi')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                DeleteAction::make()->label('Sil'),
            ]);
    }
}

