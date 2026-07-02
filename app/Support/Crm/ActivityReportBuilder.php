<?php

namespace App\Support\Crm;

use App\Models\Donation;
use App\Models\DonationType;
use App\Models\Donor;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ActivityReportBuilder
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function build(array $filters): ActivityReportResult
    {
        $filters = ActivityReportFilterResolver::normalize($filters);
        $baseQuery = $this->baseQuery($filters);

        $donationCount = (int) (clone $baseQuery)->reorder()->count();
        $totalAmount = (float) (clone $baseQuery)->reorder()->sum('amount');

        $projectRows = $this->buildProjectRows($baseQuery);
        $typeRows = $this->buildTypeRows($baseQuery);
        $donorRows = $this->buildDonorRows($baseQuery);

        // Detay listesi artık her zaman üretilir (proje seçilmiş olsun olmasın).
        $showProjectColumn = ! filled($filters['project_id'] ?? null);

        $details = (clone $baseQuery)
            ->with(['donor', 'donationType', 'project'])
            ->orderByDesc('donated_at')
            ->get();

        $detailTotalCount = $details->count();
        $detailRows = $details
            ->take(10)
            ->map(fn (Donation $donation): array => $this->mapDetailRow($donation))
            ->values()
            ->all();

        $user = auth('crm')->user();

        return new ActivityReportResult(
            meta: [
                'period_label' => DonationDateFilter::dashboardPeriodLabel($filters),
                'project_label' => DonationDateFilter::projectLabel($filters),
                'generated_at' => now()->format('d.m.Y H:i'),
                'generated_by' => $user?->name ?? 'CRM',
                'project_id' => $filters['project_id'] ?? null,
                'has_detail_sheet' => $detailTotalCount > 0,
                'show_project_column' => $showProjectColumn,
                'project_slug' => $this->projectSlug($filters),
            ],
            summary: [
                'donation_count' => $donationCount,
                'total_amount' => $totalAmount,
            ],
            projectRows: $projectRows,
            typeRows: $typeRows,
            donorRows: $donorRows,
            detailRows: $detailRows,
            detailTotalCount: $detailTotalCount,
        );
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function baseQuery(array $filters): Builder
    {
        return DonationDateFilter::applyDashboardFilters(
            Donation::query(),
            ActivityReportFilterResolver::normalize($filters),
        );
    }

    /**
     * @return array<int, array{label: string, donation_count: int, total_amount: float, average_amount: float}>
     */
    private function buildProjectRows(Builder $baseQuery): array
    {
        $aggregates = (clone $baseQuery)
            ->reorder()
            ->select('project_id')
            ->selectRaw('COUNT(*) as donations_count')
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('project_id')
            ->orderByDesc('total_amount')
            ->get();

        $projectIds = $aggregates->pluck('project_id')->filter()->all();
        $titles = Project::query()
            ->whereIn('id', $projectIds)
            ->pluck('title', 'id');

        return $aggregates
            ->map(function ($row) use ($titles): array {
                $count = (int) $row->donations_count;
                $total = (float) $row->total_amount;
                $label = $row->project_id
                    ? ($titles[$row->project_id] ?? 'Bilinmeyen proje')
                    : 'Proje atanmamış';

                return [
                    'label' => $label,
                    'donation_count' => $count,
                    'total_amount' => $total,
                    'average_amount' => $count > 0 ? $total / $count : 0.0,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, donation_count: int, total_amount: float, average_amount: float}>
     */
    private function buildTypeRows(Builder $baseQuery): array
    {
        $aggregates = (clone $baseQuery)
            ->reorder()
            ->select('donation_type_id')
            ->selectRaw('COUNT(*) as donations_count')
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('donation_type_id')
            ->orderByDesc('total_amount')
            ->get();

        $typeIds = $aggregates->pluck('donation_type_id')->filter()->all();
        $names = DonationType::query()
            ->whereIn('id', $typeIds)
            ->pluck('name', 'id');

        return $aggregates
            ->map(function ($row) use ($names): array {
                $count = (int) $row->donations_count;
                $total = (float) $row->total_amount;
                $label = $row->donation_type_id
                    ? ($names[$row->donation_type_id] ?? 'Bilinmeyen tür')
                    : 'Tür atanmamış';

                return [
                    'label' => $label,
                    'donation_count' => $count,
                    'total_amount' => $total,
                    'average_amount' => $count > 0 ? $total / $count : 0.0,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, donation_count: int, total_amount: float, average_amount: float}>
     */
    private function buildDonorRows(Builder $baseQuery): array
    {
        $aggregates = (clone $baseQuery)
            ->reorder()
            ->select('donor_id')
            ->selectRaw('COUNT(*) as donations_count')
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('donor_id')
            ->orderByDesc('total_amount')
            ->get();

        $donorIds = $aggregates->pluck('donor_id')->filter()->all();
        $donors = Donor::query()
            ->whereIn('id', $donorIds)
            ->get(['id', 'first_name', 'last_name'])
            ->keyBy('id');

        return $aggregates
            ->map(function ($row) use ($donors): array {
                $count = (int) $row->donations_count;
                $total = (float) $row->total_amount;
                $donor = $donors[$row->donor_id] ?? null;
                $label = $donor
                    ? trim($donor->first_name . ' ' . $donor->last_name)
                    : 'Bilinmeyen bağışçı';

                return [
                    'label' => $label !== '' ? $label : 'Bilinmeyen bağışçı',
                    'donation_count' => $count,
                    'total_amount' => $total,
                    'average_amount' => $count > 0 ? $total / $count : 0.0,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{
     *     donation_number: string,
     *     receipt_number: string,
     *     donor_name: string,
     *     phone: string,
     *     project: string,
     *     amount: float,
     *     currency: string,
     *     donated_at: string,
     *     donation_type: string,
     *     description: string,
     * }
     */
    private function mapDetailRow(Donation $donation): array
    {
        $donorName = trim(($donation->donor?->first_name ?? '') . ' ' . ($donation->donor?->last_name ?? ''));

        return [
            'donation_number' => (string) ($donation->donation_number ?? ''),
            'receipt_number' => (string) ($donation->receipt_number ?? ''),
            'donor_name' => $donorName !== '' ? $donorName : 'Bilinmeyen bağışçı',
            'phone' => (string) ($donation->donor?->phone ?? ''),
            'project' => (string) ($donation->project?->title ?? 'Proje atanmamış'),
            'amount' => (float) $donation->amount,
            'currency' => (string) ($donation->currency ?? 'TRY'),
            'donated_at' => $donation->donated_at?->format('d.m.Y H:i') ?? '',
            'donation_type' => (string) ($donation->donationType?->name ?? ''),
            'description' => (string) ($donation->description ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function projectSlug(array $filters): string
    {
        if (! filled($filters['project_id'] ?? null)) {
            return 'tum-faaliyetler';
        }

        $title = Project::query()->whereKey($filters['project_id'])->value('title') ?? 'proje';

        return Str::slug($title) ?: 'proje';
    }

    /**
     * @return Collection<int, Donation>
     */
    public function detailDonations(array $filters): Collection
    {
        $filters = ActivityReportFilterResolver::normalize($filters);

        return $this->baseQuery($filters)
            ->with(['donor', 'donationType', 'project', 'paymentMethod'])
            ->orderByDesc('donated_at')
            ->get();
    }
}
