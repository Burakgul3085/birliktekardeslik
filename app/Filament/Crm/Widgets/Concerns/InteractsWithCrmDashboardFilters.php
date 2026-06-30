<?php

namespace App\Filament\Crm\Widgets\Concerns;

use App\Models\Donation;
use App\Support\Crm\DashboardFilterResolver;
use App\Support\Crm\DonationDateFilter;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithCrmDashboardFilters
{
    use InteractsWithPageFilters;

    protected ?string $dashboardFiltersHash = null;

    public function updatedPageFilters(): void
    {
        $this->resetDashboardWidgetCaches();
    }

    protected function resetDashboardWidgetCaches(): void
    {
        if (property_exists($this, 'cachedStats')) {
            $this->cachedStats = null;
        }

        if (property_exists($this, 'cachedData')) {
            $this->cachedData = null;
        }

        if (property_exists($this, 'dataChecksum')) {
            $this->dataChecksum = null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function dashboardFilters(): array
    {
        $normalized = DashboardFilterResolver::normalize($this->pageFilters);
        $hash = md5(json_encode($normalized));

        if ($this->dashboardFiltersHash !== $hash) {
            $this->dashboardFiltersHash = $hash;
            $this->resetDashboardWidgetCaches();
        }

        return $normalized;
    }

    protected function filteredDonationsQuery(): Builder
    {
        return DonationDateFilter::applyDashboardFilters(
            Donation::query(),
            $this->dashboardFilters(),
        );
    }
}
