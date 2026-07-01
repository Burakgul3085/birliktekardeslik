<?php

namespace App\Filament\Crm\Resources\Notes\Schemas;

use App\Models\CrmNote;
use App\Models\Donation;
use App\Models\Donor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NoteForm
{
    /**
     * @param  'resource'|'donor'|'donation'  $context
     */
    public static function configure(Schema $schema, string $context = 'resource'): Schema
    {
        $isResource = $context === 'resource';

        return $schema->components([
            Section::make('Not içeriği')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('scope')
                            ->label('Not türü')
                            ->options(CrmNote::SCOPES)
                            ->default($context === 'donor' ? 'donor' : ($context === 'donation' ? 'donation' : 'general'))
                            ->required()
                            ->live()
                            ->native(false)
                            ->visible($isResource)
                            ->disabled(! $isResource),
                        Select::make('category')
                            ->label('Kategori')
                            ->options(CrmNote::CATEGORIES)
                            ->default('other')
                            ->required()
                            ->native(false),
                        Select::make('visibility')
                            ->label('Görünürlük')
                            ->options(CrmNote::VISIBILITIES)
                            ->default('team')
                            ->required()
                            ->native(false)
                            ->helperText('Kişisel notları yalnızca siz görürsünüz.'),
                        Toggle::make('is_pinned')
                            ->label('Üste sabitle')
                            ->default(false),
                    ]),
                    Select::make('donor_id')
                        ->label('Bağışçı')
                        ->options(fn (): array => Donor::query()
                            ->orderBy('last_name')
                            ->orderBy('first_name')
                            ->get()
                            ->mapWithKeys(fn (Donor $donor): array => [$donor->id => $donor->full_name])
                            ->all())
                        ->searchable()
                        ->required(fn ($get): bool => $isResource && $get('scope') === 'donor')
                        ->visible(fn ($get): bool => $isResource && $get('scope') === 'donor')
                        ->native(false),
                    Select::make('donation_id')
                        ->label('Bağış')
                        ->options(fn (): array => Donation::query()
                            ->with('donor')
                            ->latest('donated_at')
                            ->limit(200)
                            ->get()
                            ->mapWithKeys(fn (Donation $donation): array => [
                                $donation->id => trim($donation->donation_number . ' — ' . ($donation->donor?->full_name ?? '')),
                            ])
                            ->all())
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search): array {
                            return Donation::query()
                                ->with('donor')
                                ->where('donation_number', 'like', "%{$search}%")
                                ->orWhereHas('donor', fn ($q) => $q
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%"))
                                ->latest('donated_at')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn (Donation $donation): array => [
                                    $donation->id => trim($donation->donation_number . ' — ' . ($donation->donor?->full_name ?? '')),
                                ])
                                ->all();
                        })
                        ->required(fn ($get): bool => $isResource && $get('scope') === 'donation')
                        ->visible(fn ($get): bool => $isResource && $get('scope') === 'donation')
                        ->native(false),
                    TextInput::make('title')
                        ->label('Başlık')
                        ->maxLength(255)
                        ->required(fn ($get): bool => $isResource && $get('scope') === 'general')
                        ->columnSpanFull(),
                    Textarea::make('body')
                        ->label('Not')
                        ->required()
                        ->rows(8)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
