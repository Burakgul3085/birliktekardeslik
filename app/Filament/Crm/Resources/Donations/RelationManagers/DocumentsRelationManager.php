<?php

namespace App\Filament\Crm\Resources\Donations\RelationManagers;

use App\Models\DocumentTemplate;
use App\Support\Crm\DonationDocumentGenerator;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Belgeler';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('generated_at', 'desc')
            ->columns([
                TextColumn::make('type_label')->label('Belge Türü'),
                TextColumn::make('verification_code')->label('Doğrulama Kodu')->copyable(),
                TextColumn::make('generated_at')->label('Oluşturulma')->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([
                Action::make('generateReceipt')
                    ->label('Makbuz oluştur')
                    ->icon('heroicon-o-document-text')
                    ->visible(fn (): bool => auth('crm')->user()?->canWriteDonations() ?? false)
                    ->action(function (): void {
                        $this->generateDocument(DocumentTemplate::TYPE_RECEIPT);
                    }),
                Action::make('generateThanks')
                    ->label('Teşekkür belgesi')
                    ->icon('heroicon-o-heart')
                    ->visible(fn (): bool => auth('crm')->user()?->canWriteDonations() ?? false)
                    ->action(function (): void {
                        $this->generateDocument(DocumentTemplate::TYPE_THANKS_LETTER);
                    }),
                Action::make('generateAll')
                    ->label('Tüm belgeleri oluştur')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('primary')
                    ->visible(fn (): bool => auth('crm')->user()?->canWriteDonations() ?? false)
                    ->action(function (): void {
                        app(DonationDocumentGenerator::class)->generateAll($this->getOwnerRecord(), regenerate: true);

                        Notification::make()
                            ->title('Belgeler oluşturuldu')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('PDF İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record): string => route('crm.documents.download', $record))
                    ->openUrlInNewTab(),
                Action::make('verify')
                    ->label('Doğrulama')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn ($record): string => $record->verification_url)
                    ->openUrlInNewTab(),
            ]);
    }

    private function generateDocument(string $type): void
    {
        try {
            app(DonationDocumentGenerator::class)->generate($this->getOwnerRecord(), $type, regenerate: true);

            Notification::make()
                ->title(DocumentTemplate::TYPES[$type] . ' oluşturuldu')
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
