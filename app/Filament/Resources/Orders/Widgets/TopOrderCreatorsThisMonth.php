<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TopOrderCreatorsThisMonth extends StatsOverviewWidget
{
    protected ?string $heading = 'Ranking de usuarios del mes';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        [$startOfMonth, $endOfMonth] = $this->getCurrentMonthRange();

        $topUsers = User::query()
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
            ->limit(3)
            ->get();

        $stats = $topUsers
            ->map(function (User $user, int $index): Stat {
                $ordersCount = (int) ($user->orders_count_this_month ?? 0);
                $totalAmount = (float) ($user->total_amount_this_month ?? 0);
                $position = $index + 1;

                return Stat::make(sprintf('%d. %s', $position, $user->name), $this->formatCurrency($totalAmount))
                    ->description('Órdenes pagadas: ' . $this->formatNumber($ordersCount))
                    ->icon(match ($position) {
                        1 => 'heroicon-o-trophy',
                        2 => 'heroicon-o-star',
                        default => 'heroicon-o-user-circle',
                    })
                    ->color(match ($position) {
                        1 => 'success',
                        2 => 'warning',
                        default => 'info',
                    });
            })
            ->all();

        $nextPosition = count($stats) + 1;
        while (count($stats) < 3) {
            $stats[] = Stat::make(sprintf('%d. —', $nextPosition), $this->formatCurrency(0))
                ->description('Sin órdenes pagadas registradas')
                ->icon('heroicon-o-user-group')
                ->color('gray');

            $nextPosition++;
        }

        return $stats;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getCurrentMonthRange(): array
    {
        $now = Carbon::now();

        return [
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth(),
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
}
