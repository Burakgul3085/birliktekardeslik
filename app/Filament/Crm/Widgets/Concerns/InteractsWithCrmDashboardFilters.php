<?php

namespace App\Filament\Crm\Widgets\Concerns;

use App\Models\Donation;
use App\Support\Crm\DashboardFilterResolver;
use App\Support\Crm\DonationDateFilter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

trait InteractsWithCrmDashboardFilters
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $crmDashboardFilters = null;

    #[On('crm-dashboard-filters-updated')]
    public function refreshDashboardWidgets(array $filters = []): void
    {
        if ($filters !== []) {
            $this->crmDashboardFilters = DashboardFilterResolver::normalize($filters);
        }

        if (property_exists($this, 'cachedStats')) {
            $this->cachedStats = null;
        }

        if (property_exists($this, 'cachedData')) {
            $this->cachedData = null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function dashboardFilters(): array
    {
        if (is_array($this->crmDashboardFilters)) {
            return $this->crmDashboardFilters;
        }

        return DashboardFilterResolver::get();
    }

    protected function filteredDonationsQuery(): Builder
    {
        return DonationDateFilter::applyDashboardFilters(
            Donation::query(),
            $this->dashboardFilters(),
        );
    }
}
