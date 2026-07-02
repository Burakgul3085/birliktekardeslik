<?php

namespace App\Support\Crm;

/**
 * @phpstan-type SummaryArray array{donation_count: int, total_amount: float}
 * @phpstan-type RowArray array{label: string, donation_count: int, total_amount: float, average_amount: float}
 * @phpstan-type DetailRowArray array{
 *     donation_number: string,
 *     receipt_number: string,
 *     donor_name: string,
 *     amount: float,
 *     currency: string,
 *     donated_at: string,
 *     donation_type: string,
 *     description: string,
 * }
 * @phpstan-type MetaArray array{
 *     period_label: string,
 *     project_label: string,
 *     generated_at: string,
 *     generated_by: string,
 *     project_id: int|null,
 *     has_detail_sheet: bool,
 * }
 */
class ActivityReportResult
{
    /**
     * @param  SummaryArray  $summary
     * @param  array<int, RowArray>  $projectRows
     * @param  array<int, RowArray>  $typeRows
     * @param  array<int, DetailRowArray>  $detailRows
     */
    public function __construct(
        public readonly array $meta,
        public readonly array $summary,
        public readonly array $projectRows,
        public readonly array $typeRows,
        public readonly array $detailRows,
        public readonly int $detailTotalCount,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'meta' => $this->meta,
            'summary' => $this->summary,
            'project_rows' => $this->projectRows,
            'type_rows' => $this->typeRows,
            'detail_rows' => $this->detailRows,
            'detail_total_count' => $this->detailTotalCount,
        ];
    }
}
