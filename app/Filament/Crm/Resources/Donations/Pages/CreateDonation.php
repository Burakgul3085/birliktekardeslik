<?php

namespace App\Filament\Crm\Resources\Donations\Pages;

use App\Filament\Crm\Resources\Donations\DonationResource;
use App\Support\Crm\DonationDocumentGenerator;
use App\Support\Crm\DonationNumberGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDonation extends CreateRecord
{
    protected static string $resource = DonationResource::class;

    public function mount(): void
    {
        parent::mount();

        $donorId = request()->integer('donor_id');
        if ($donorId > 0) {
            $this->form->fill([
                'donor_id' => $donorId,
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['donation_number'] = DonationNumberGenerator::next();
        $data['created_by'] = auth('crm')->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            app(DonationDocumentGenerator::class)->generate($this->record);
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Bağış kaydedildi, makbuz oluşturulamadı')
                ->body($exception->getMessage())
                ->warning()
                ->send();
        }
    }
}
