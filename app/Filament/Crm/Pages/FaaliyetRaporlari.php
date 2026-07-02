<?php

namespace App\Filament\Crm\Pages;

use App\Models\Project;
use App\Support\Crm\ActivityReportBuilder;
use App\Support\Crm\ActivityReportFilterResolver;
use App\Support\Crm\ActivitySpreadsheetExporter;
use App\Support\Crm\DonationDateFilter;
use App\Support\Crm\LookupDeletionGuard;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class FaaliyetRaporlari extends Page
{
    protected static ?string $navigationLabel = 'Faaliyet Raporları';

    protected static ?string $title = 'Faaliyet Raporları';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static string | UnitEnum | null $navigationGroup = 'Raporlar';

    protected static ?int $navigationSort = 1;

    /**
     * @var array<string, mixed>
     */
    public array $filters = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $report = null;

    public function mount(): void
    {
        $this->filters = ActivityReportFilterResolver::get();
    }

    public function booted(): void
    {
        $this->cacheSchema('filtersForm', $this->getFiltersForm());
    }

    public function getFiltersForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersForm')) {
            return $this->getSchema('filtersForm');
        }

        $schema = $this->makeSchema()
            ->columns(1)
            ->extraAttributes(['wire:partial' => 'table-filters-form', 'class' => 'crm-dashboard-filters crm-activity-report-filters'])
            ->statePath('filters');

        return $this->filtersForm($schema);
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filtreler')
                    ->description('Dönem ve faaliyet seçimi rapor özetini belirler.')
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
                    ->collapsible(),
            ]);
    }

    public function updatedFilters(): void
    {
        $this->filters = ActivityReportFilterResolver::normalize($this->filters);
        session()->put(ActivityReportFilterResolver::sessionKey(), $this->filters);
        $this->report = null;
    }

    public function generateReport(): void
    {
        $this->filters = ActivityReportFilterResolver::store($this->filters);
        $result = app(ActivityReportBuilder::class)->build($this->filters);

        if ($result->summary['donation_count'] === 0) {
            $this->report = null;

            Notification::make()
                ->title('Seçilen dönemde bağış bulunamadı')
                ->body('Filtreleri değiştirip tekrar deneyin.')
                ->warning()
                ->send();

            return;
        }

        $this->report = $result->toArray();

        Notification::make()
            ->title('Rapor hazır')
            ->body('Önizleme aşağıda görüntüleniyor.')
            ->success()
            ->send();
    }

    public function downloadExcel(): mixed
    {
        $this->filters = ActivityReportFilterResolver::store($this->filters);
        $result = app(ActivityReportBuilder::class)->build($this->filters);

        if ($result->summary['donation_count'] === 0) {
            Notification::make()
                ->title('İndirilecek bağış bulunamadı')
                ->warning()
                ->send();

            return null;
        }

        return ActivitySpreadsheetExporter::download($result);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateReport')
                ->label('Raporu Göster')
                ->icon(Heroicon::OutlinedEye)
                ->color('primary')
                ->action('generateReport'),
            Action::make('downloadExcel')
                ->label('Excel İndir')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action('downloadExcel'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedSchema::make('filtersForm'),
                EmptyState::make('Rapor önizlemesi')
                    ->description('Filtreleri seçip üstteki "Raporu Göster" ile önizleme oluşturun veya doğrudan Excel indirin.')
                    ->icon(Heroicon::OutlinedDocumentChartBar)
                    ->visible(fn (): bool => blank($this->report)),
                View::make('filament.crm.pages.partials.activity-report-preview')
                    ->viewData(fn (): array => [
                        'report' => $this->report,
                    ])
                    ->visible(fn (): bool => filled($this->report)),
            ]);
    }
}
