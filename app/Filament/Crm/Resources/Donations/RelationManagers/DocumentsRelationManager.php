<?php

namespace App\Filament\Crm\Resources\Donations\RelationManagers;

use App\Models\DocumentTemplate;
use App\Models\DonationDocument;
use App\Support\Crm\DonationDocumentGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
                    ->action(function (DonationDocument $record) {
                        if (! Storage::disk('public')->exists($record->pdf_path)) {
                            Notification::make()
                                ->title('PDF dosyası bulunamadı')
                                ->body('Belgeyi yeniden oluşturmayı deneyin.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $filename = $record->type . '-' . $record->verification_code . '.pdf';

                        return Storage::disk('public')->download($record->pdf_path, $filename);
                    }),
                Action::make('verify')
                    ->label('Doğrulama')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn ($record): string => $record->verification_url)
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->label('Sil')
                    ->visible(fn (): bool => $this->canManageDocuments())
                    ->requiresConfirmation()
                    ->modalHeading('Belgeyi sil')
                    ->modalDescription('Bu belge ve PDF dosyası kalıcı olarak silinecek. Emin misiniz?')
                    ->successNotificationTitle('Belge silindi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Seçilen belgeleri sil')
                        ->visible(fn (): bool => $this->canManageDocuments())
                        ->requiresConfirmation()
                        ->modalHeading('Seçilen belgeleri sil')
                        ->modalDescription('Seçili belgeler ve PDF dosyaları kalıcı olarak silinecek.')
                        ->successNotificationTitle('Belgeler silindi'),
                ]),
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
