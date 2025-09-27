<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MonthlyOrdersStats extends StatsOverviewWidget
{
    protected ?string $heading = 'Resumen mensual de órdenes';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $ordersCount = Order::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $paidTotal = (float) Order::query()
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->where('status', Order::STATUS_PAID)
            ->sum('total_amount');

        $topUser = User::query()
            ->withCount([
                'createdOrders as orders_count_this_month' => function (Builder $query) use ($startOfMonth, $endOfMonth): void {
                    $query
                        ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                        ->where('status', Order::STATUS_PAID)
                        ->whereNotNull('paid_at');
                },
            ])
            ->withSum([
                'createdOrders as total_amount_this_month' => function (Builder $query) use ($startOfMonth, $endOfMonth): void {
                    $query
                        ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                        ->where('status', Order::STATUS_PAID)
                        ->whereNotNull('paid_at');
                },
            ], 'total_amount')
            ->whereHas('createdOrders', function (Builder $query) use ($startOfMonth, $endOfMonth): void {
                $query
                    ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                    ->where('status', Order::STATUS_PAID)
                    ->whereNotNull('paid_at');
            })
            ->orderByDesc('orders_count_this_month')
            ->first();

        return [
            Stat::make('Órdenes creadas', $this->formatNumber($ordersCount))
                ->description('Creadas este mes')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('info'),
            Stat::make('Total pagado', $this->formatCurrency($paidTotal))
                ->description('Pagadas este mes')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            $this->buildTopUserStat($topUser),
        ];
    }

    private function formatNumber(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }

    private function formatCurrency(float $value): string
    {
        return 'PEN ' . number_format($value, 2, ',', '.');
    }

    private function buildTopUserStat(?User $user): Stat
    {
        if (! $user) {
            return Stat::make('Top usuario', $this->formatCurrency(0))
                ->description('Sin órdenes pagadas este mes')
                ->icon('heroicon-o-user-group')
                ->color('gray');
        }

        $ordersCount = (int) ($user->orders_count_this_month ?? 0);
        $totalAmount = (float) ($user->total_amount_this_month ?? 0.0);

        return Stat::make('Top usuario: ' . $user->name, $this->formatCurrency($totalAmount))
            ->description('Órdenes pagadas: ' . $this->formatNumber($ordersCount))
            ->icon('heroicon-o-trophy')
            ->color('warning');
    }
}
