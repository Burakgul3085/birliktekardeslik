<?php

namespace App\Filament\Crm\Pages;

use App\Models\Project;
use App\Support\Crm\DashboardFilterResolver;
use App\Support\Crm\DonationDateFilter;
use App\Support\Crm\LookupDeletionGuard;
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

    protected static ?string $navigationLabel = 'Genel Bakış';

    protected static ?string $title = 'Genel Bakış';

    public function mount(): void
    {
        $this->filters = DashboardFilterResolver::store($this->filters ?? []);
    }

    public function getFiltersForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersForm')) {
            return $this->getSchema('filtersForm');
        }

        $schema = $this->makeSchema()
            ->columns(1)
            ->extraAttributes(['wire:partial' => 'table-filters-form', 'class' => 'crm-dashboard-filters'])
            ->statePath('filters');

        return $this->filtersForm($schema);
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filtreler')
                    ->description('Özet kartlar, grafikler ve tablolar seçtiğiniz dönem ve faaliyete göre güncellenir.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            Select::make('period')
                                ->label('Zaman aralığı')
                                ->options(DonationDateFilter::dashboardPeriodOptions())
                                ->default('this_month')
                                ->live()
                                ->native(false),
                            Select::make('project_id')
                                ->label('Proje / Faaliyet')
                                ->options(fn (): array => app(LookupDeletionGuard::class)->activeOptions(Project::class, 'title'))
                                ->searchable()
                                ->nullable()
                                ->placeholder('Tüm faaliyetler')
                                ->live()
                                ->native(false),
                            TextInput::make('relative_amount')
                                ->label('Son')
                                ->numeric()
                                ->minValue(1)
                                ->default(3)
                                ->live(onBlur: true)
                                ->visible(fn ($get): bool => $get('period') === 'relative'),
                            Select::make('relative_unit')
                                ->label('Birim')
                                ->options(DonationDateFilter::relativeUnitOptions())
                                ->default('weeks')
                                ->live()
                                ->native(false)
                                ->visible(fn ($get): bool => $get('period') === 'relative'),
                            DateTimePicker::make('from')
                                ->label('Başlangıç')
                                ->seconds(false)
                                ->live(onBlur: true)
                                ->visible(fn ($get): bool => $get('period') === 'custom_range'),
                            DateTimePicker::make('until')
                                ->label('Bitiş')
                                ->seconds(false)
                                ->live(onBlur: true)
                                ->visible(fn ($get): bool => $get('period') === 'custom_range'),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public function updatedFilters(): void
    {
        $this->filters = DashboardFilterResolver::normalize($this->filters ?? []);

        if ($this->persistsFiltersInSession()) {
            session()->put($this->getFiltersSessionKey(), $this->filters);
        }
    }
}
