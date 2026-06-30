<?php

namespace App\Filament\Crm\Pages;

use App\Models\Project;
use App\Support\Crm\DashboardFilterResolver;
use App\Support\Crm\DonationDateFilter;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CrmDashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationLabel = 'Dashboard';

    public function mount(): void
    {
        if (blank($this->filters)) {
            $this->filters = DashboardFilterResolver::defaults();
        }
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filtreler')
                    ->description('Özet kartlar, grafikler ve tablolar seçtiğiniz dönem ve faaliyete göre güncellenir.')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                            'xl' => 4,
                        ])->schema([
                            Select::make('period')
                                ->label('Zaman aralığı')
                                ->options(DonationDateFilter::dashboardPeriodOptions())
                                ->default('this_month')
                                ->live()
                                ->columnSpan([
                                    'default' => 1,
                                    'md' => 2,
                                ]),
                            Select::make('project_id')
                                ->label('Proje / Faaliyet')
                                ->options(fn (): array => Project::query()
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->all())
                                ->searchable()
                                ->placeholder('Tüm faaliyetler')
                                ->live(),
                            TextInput::make('relative_amount')
                                ->label('Son')
                                ->numeric()
                                ->minValue(1)
                                ->default(3)
                                ->visible(fn ($get): bool => $get('period') === 'relative'),
                            Select::make('relative_unit')
                                ->label('Birim')
                                ->options(DonationDateFilter::relativeUnitOptions())
                                ->default('weeks')
                                ->visible(fn ($get): bool => $get('period') === 'relative'),
                            DateTimePicker::make('from')
                                ->label('Başlangıç')
                                ->seconds(false)
                                ->visible(fn ($get): bool => $get('period') === 'custom_range'),
                            DateTimePicker::make('until')
                                ->label('Bitiş')
                                ->seconds(false)
                                ->visible(fn ($get): bool => $get('period') === 'custom_range'),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public function updatedFilters(): void
    {
        parent::updatedFilters();

        $this->dispatch('crm-dashboard-filters-updated');
    }
}
