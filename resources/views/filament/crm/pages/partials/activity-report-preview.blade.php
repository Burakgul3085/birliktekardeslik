@php
    $meta = $report['meta'] ?? [];
    $summary = $report['summary'] ?? [];
    $projectRows = $report['project_rows'] ?? [];
    $typeRows = $report['type_rows'] ?? [];
    $donorRows = $report['donor_rows'] ?? [];
    $detailRows = $report['detail_rows'] ?? [];
    $detailTotalCount = (int) ($report['detail_total_count'] ?? 0);
    $hasDetail = (bool) ($meta['has_detail_sheet'] ?? false);
    $showProject = (bool) ($meta['show_project_column'] ?? false);
    $detailColspan = $showProject ? 10 : 9;

    $money = static fn (float $amount): string => number_format($amount, 2, ',', '.');
@endphp

<div class="crm-activity-report">
    <div class="crm-activity-report__meta">
        <div>
            <span class="crm-activity-report__meta-label">Dönem</span>
            <strong>{{ $meta['period_label'] ?? '—' }}</strong>
        </div>
        <div>
            <span class="crm-activity-report__meta-label">Faaliyet</span>
            <strong>{{ $meta['project_label'] ?? '—' }}</strong>
        </div>
        <div>
            <span class="crm-activity-report__meta-label">Oluşturma</span>
            <strong>{{ $meta['generated_at'] ?? '—' }}</strong>
        </div>
        <div>
            <span class="crm-activity-report__meta-label">Hazırlayan</span>
            <strong>{{ $meta['generated_by'] ?? '—' }}</strong>
        </div>
    </div>

    <div class="crm-activity-report__stats">
        <div class="crm-activity-report__stat-card">
            <span class="crm-activity-report__stat-label">Toplam Bağış Adedi</span>
            <span class="crm-activity-report__stat-value">{{ number_format($summary['donation_count'] ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="crm-activity-report__stat-card">
            <span class="crm-activity-report__stat-label">Toplam Tutar (TRY)</span>
            <span class="crm-activity-report__stat-value">{{ $money((float) ($summary['total_amount'] ?? 0)) }}</span>
        </div>
        @if ($hasDetail)
            <div class="crm-activity-report__stat-card">
                <span class="crm-activity-report__stat-label">Detay Kayıt</span>
                <span class="crm-activity-report__stat-value">{{ number_format($detailTotalCount, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="crm-activity-report__section">
        <h3 class="crm-activity-report__section-title">Proje / Faaliyet Özeti</h3>
        <div class="crm-activity-report__table-wrap">
            <table class="crm-activity-report__table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Proje / Faaliyet</th>
                        <th>Bağış Adedi</th>
                        <th>Toplam Tutar</th>
                        <th>Ort. Bağış</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projectRows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ number_format($row['donation_count'], 0, ',', '.') }}</td>
                            <td class="is-amount">{{ $money((float) $row['total_amount']) }}</td>
                            <td class="is-amount">{{ $money((float) $row['average_amount']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="is-empty">Kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($projectRows !== [])
                    <tfoot>
                        <tr>
                            <td colspan="2">Genel Toplam</td>
                            <td>{{ number_format($summary['donation_count'] ?? 0, 0, ',', '.') }}</td>
                            <td class="is-amount">{{ $money((float) ($summary['total_amount'] ?? 0)) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="crm-activity-report__section">
        <h3 class="crm-activity-report__section-title">Bağışçı Bazlı Özet</h3>
        <div class="crm-activity-report__table-wrap">
            <table class="crm-activity-report__table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bağışçı</th>
                        <th>Bağış Adedi</th>
                        <th>Toplam Tutar</th>
                        <th>Ort. Bağış</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donorRows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ number_format($row['donation_count'], 0, ',', '.') }}</td>
                            <td class="is-amount">{{ $money((float) $row['total_amount']) }}</td>
                            <td class="is-amount">{{ $money((float) $row['average_amount']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="is-empty">Kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($donorRows !== [])
                    <tfoot>
                        <tr>
                            <td colspan="2">Genel Toplam</td>
                            <td>{{ number_format($summary['donation_count'] ?? 0, 0, ',', '.') }}</td>
                            <td class="is-amount">{{ $money((float) ($summary['total_amount'] ?? 0)) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="crm-activity-report__section">
        <h3 class="crm-activity-report__section-title">Bağış Türü Özeti <span class="crm-activity-report__hint">(bilgi)</span></h3>
        <div class="crm-activity-report__table-wrap">
            <table class="crm-activity-report__table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bağış Türü</th>
                        <th>Bağış Adedi</th>
                        <th>Toplam Tutar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($typeRows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ number_format($row['donation_count'], 0, ',', '.') }}</td>
                            <td class="is-amount">{{ $money((float) $row['total_amount']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="is-empty">Kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($hasDetail)
        <div class="crm-activity-report__section">
            <h3 class="crm-activity-report__section-title">
                Bağış Detayları <span class="crm-activity-report__hint">(kim, hangi bağışı yaptı)</span>
                <span class="crm-activity-report__hint">— ilk {{ count($detailRows) }} / {{ number_format($detailTotalCount, 0, ',', '.') }} kayıt, tamamı Excel’de</span>
            </h3>
            <div class="crm-activity-report__table-wrap">
                <table class="crm-activity-report__table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bağış No</th>
                            <th>Makbuz No</th>
                            <th>Bağışçı</th>
                            <th>Telefon</th>
                            @if ($showProject)
                                <th>Proje / Faaliyet</th>
                            @endif
                            <th>Tutar</th>
                            <th>Para Birimi</th>
                            <th>Bağış Tarihi</th>
                            <th>Bağış Türü</th>
                            <th>Açıklama</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($detailRows as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row['donation_number'] }}</td>
                                <td>{{ $row['receipt_number'] }}</td>
                                <td>{{ $row['donor_name'] }}</td>
                                <td>{{ $row['phone'] }}</td>
                                @if ($showProject)
                                    <td>{{ $row['project'] }}</td>
                                @endif
                                <td class="is-amount">{{ $money((float) $row['amount']) }}</td>
                                <td>{{ $row['currency'] }}</td>
                                <td>{{ $row['donated_at'] }}</td>
                                <td>{{ $row['donation_type'] }}</td>
                                <td>{{ $row['description'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $detailColspan }}" class="is-empty">Kayıt bulunamadı.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
