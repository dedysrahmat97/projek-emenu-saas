<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Models\Product;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProductResource;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        if (Auth::user()->role == 'admin') {
            return [
                Actions\CreateAction::make(),
            ];
        }

        $subscription = Subscription::where('user_id', auth()->user()->id)
            ->where('end_date', '>', now())
            ->where('is_active', true)
            ->latest()
            ->first();
        $countProduct = Product::where('user_id', auth()->user()->id)->count();

        return [
            Actions\Action::make('alert')
                ->label('Produk Kamu Melebihi Batas Penggunaan Gratis, Silahkan Berlangganan')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle')
                ->visible(!$subscription && $countProduct >= 5),
            Actions\CreateAction::make()
        ];
    }
}