<?php

namespace App\Filament\Crm\Widgets\Concerns;

use App\Models\Donation;
use App\Support\Crm\DashboardFilterResolver;
use App\Support\Crm\DonationDateFilter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

trait InteractsWithCrmDashboardFilters
{
    #[On('crm-dashboard-filters-updated')]
    public function refreshDashboardWidgets(): void
    {
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
