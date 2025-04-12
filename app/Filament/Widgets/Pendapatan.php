<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class Pendapatan extends BaseWidget
{
    protected static ?string $navigationLabel = 'Laporan Pendapatan';
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->heading('Laporan Pendapatan')
            ->query(
                Transaction::query()
                    ->where('status', 'success')
                    ->where('user_id', auth()->user()->id) // hanya transaksi sukses
            )
            ->columns([
                TextColumn::make('code')->label('Kode Transaksi')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('table_number')->label('Meja'),
                TextColumn::make('payment_method')->label('Pembayaran'),
                TextColumn::make('total_price')->label('Total')
                    ->money('IDR', true)
                    ->summarize([
                        Sum::make()
                            ->label('Total Pendapatan')
                            ->money('IDR', true)
                    ])
                    ->sortable(),
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y'),
                    ])
            ->filters([
                DateRangeFilter::make('created_at'),
            ]);
    }

}