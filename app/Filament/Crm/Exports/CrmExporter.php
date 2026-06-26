<?php

namespace App\Filament\Crm\Exports;

use Filament\Actions\Exports\Exporter;

abstract class CrmExporter extends Exporter
{
    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
