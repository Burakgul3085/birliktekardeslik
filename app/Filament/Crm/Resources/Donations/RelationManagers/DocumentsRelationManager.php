<?php

namespace App\Filament\Crm\Resources\Donations\RelationManagers;

use App\Filament\Crm\Resources\Donations\DonationResource;
use App\Models\DocumentTemplate;
use App\Models\DonationDocument;
use App\Support\Crm\DonationDocumentGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Belgeler';

    private function canManageDocuments(): bool
    {
        return auth('crm')->user()?->canWriteDonations() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('generated_at', 'desc')
            ->columns([
                TextColumn::make('type_label')->label('Belge Türü'),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === DonationDocument::STATUS_FINAL ? 'Onaylı' : 'Taslak')
                    ->color(fn (string $state): string => $state === DonationDocument::STATUS_FINAL ? 'success' : 'warning'),
                TextColumn::make('verification_code')->label('Doğrulama Kodu')->copyable(),
                TextColumn::make('generated_at')->label('Oluşturulma')->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([
                Action::make('generateReceipt')
                    ->label('Makbuz oluştur')
                    ->icon('heroicon-o-document-text')
                    ->visible(fn (): bool => $this->canManageDocuments())
                    ->action(fn () => $this->generateDocument(DocumentTemplate::TYPE_RECEIPT)),
                Action::make('generateDonationPoster')
                    ->label('Bağış afişi oluştur')
                    ->icon('heroicon-o-photo')
                    ->visible(fn (): bool => $this->canManageDocuments())
                    ->form(fn (): array => $this->descriptionFormFields())
                    ->action(function (array $data): void {
                        $this->generateDocument(
                            DocumentTemplate::TYPE_DONATION_POSTER,
                            $data['description'] ?? null,
                        );
                    }),
                Action::make('generateThanksPoster')
                    ->label('Teşekkür afişi oluştur')
                    ->icon('heroicon-o-heart')
                    ->visible(fn (): bool => $this->canManageDocuments())
                    ->action(fn () => $this->generateDocument(DocumentTemplate::TYPE_THANKS_POSTER)),
                Action::make('generateAll')
                    ->label('Tüm belgeleri oluştur')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('primary')
                    ->visible(fn (): bool => $this->canManageDocuments())
                    ->form(fn (): array => $this->descriptionFormFields())
                    ->action(function (array $data): void {
                        try {
                            app(DonationDocumentGenerator::class)->generateAll(
                                $this->getOwnerRecord(),
                                regenerate: true,
                                description: $data['description'] ?? null,
                            );

                            Notification::make()
                                ->title('3 belge oluşturuldu')
                                ->body('Makbuz, bağış afişi ve teşekkür afişi hazır.')
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Belgeler oluşturulamadı')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Görüntüle / Düzenle')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (DonationDocument $record): bool => $record->isPoster())
                    ->url(fn (DonationDocument $record): string => DonationResource::getUrl('preview-document', [
                        'record' => $this->getOwnerRecord(),
                        'document' => $record->id,
                    ])),
                Action::make('downloadPdf')
                    ->label('PDF İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (DonationDocument $record) {
                        if (! Storage::disk('public')->exists($record->pdf_path)) {
                            Notification::make()->title('PDF bulunamadı')->danger()->send();

                            return;
                        }

                        return Storage::disk('public')->download(
                            $record->pdf_path,
                            $record->type . '-' . $record->verification_code . '.pdf',
                        );
                    }),
                Action::make('downloadPng')
                    ->label('PNG İndir')
                    ->icon('heroicon-o-photo')
                    ->visible(fn (DonationDocument $record): bool => filled($record->png_path))
                    ->action(function (DonationDocument $record) {
                        if (! $record->png_path || ! Storage::disk('public')->exists($record->png_path)) {
                            Notification::make()->title('PNG bulunamadı')->danger()->send();

                            return;
                        }

                        return Storage::disk('public')->download(
                            $record->png_path,
                            $record->type . '-' . $record->verification_code . '.png',
                        );
                    }),
                Action::make('verify')
                    ->label('QR Doğrulama')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn ($record): string => $record->verification_url)
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->label('Sil')
                    ->visible(fn (): bool => $this->canManageDocuments())
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Seçilen belgeleri sil')
                        ->visible(fn (): bool => $this->canManageDocuments())
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    /**
     * @return array<int, Textarea>
     */
    private function descriptionFormFields(): array
    {
        $donation = $this->getOwnerRecord();

        if (filled($donation->description)) {
            return [];
        }

        return [
            Textarea::make('description')
                ->label('Bağış Afişi Notu')
                ->required()
                ->rows(4)
                ->helperText('Bağış afişinde görünecek metin.')
                ->placeholder('Örn: Açelya\'dan olma Kaan Zer\'in adaklık kurbanı...'),
        ];
    }

    private function generateDocument(string $type, ?string $description = null): void
    {
        try {
            app(DonationDocumentGenerator::class)->generate(
                $this->getOwnerRecord(),
                $type,
                regenerate: true,
                description: $description,
            );

            Notification::make()
                ->title((DocumentTemplate::ACTIVE_TYPES[$type] ?? $type) . ' oluşturuldu')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Belge oluşturulamadı')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
