<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTransaction = 0;
        $totalAmount = 0;

        $totalTransaction = Transaction::where('user_id', auth()->user()->id)->where('status', 'success')->count();
        $totalAmount = Transaction::where('user_id', auth()->user()->id)->where('status', 'success')->sum('total_price');

        if (Auth::user()->role == 'admin') {
            return [
                Stat::make('Total Pengguna', User::count()),
                Stat::make('Total Pendapatan Langganan', 'Rp. ' . number_format(SubscriptionPayment::where('status', 'success')->count() * 5000)),
                Stat::make('Total Produk', Product::count()),
            ];
        } else {
            return [
                Stat::make('Total Transaksi', $totalTransaction),
                Stat::make('Total Pendapatan', 'Rp. ' . number_format($totalAmount)),
                Stat::make('Rata-Rata Pendapatan', $totalTransaction > 0 ? 'Rp. ' . number_format($totalAmount / $totalTransaction) : 'Rp. 0'),
            ];
        }
    }
}