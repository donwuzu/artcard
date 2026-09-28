<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DecorOrder;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    /*------------------------------------------------------------------------
    | Auto-refresh every 60 seconds
    ------------------------------------------------------------------------*/

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();

        /*--------------------------------------------------------------------
        | Orders
        --------------------------------------------------------------------*/
        $totalOrders = Order::count() + DecorOrder::count();
        $todayOrders = Order::whereDate('created_at', $today)->count()
            + DecorOrder::whereDate('created_at', $today)->count();
        $pendingOrders = Order::where('status', 'unpaid')->count()
            + DecorOrder::where('status', 'unpaid')->count();

        /*--------------------------------------------------------------------
        | Revenue grouped by settlement currency
        --------------------------------------------------------------------*/
        $revenueToday = $this->currencyTotals(isPaid: true, date: $today);
        $paidOrdersToday = $this->orderCount(isPaid: true, date: $today);
        $paidOrdersYesterday = $this->orderCount(isPaid: true, date: $yesterday);
        $revenueMonth = $this->currencyTotals(isPaid: true, from: $thisMonth);

        /*--------------------------------------------------------------------
        | Combined (Portraits + Decor)
        --------------------------------------------------------------------*/
        $totalRevenue = $this->currencyTotals(isPaid: true);
        $unpaidBalance = $this->currencyTotals(isPaid: false, excludeCancelled: true);

        /*--------------------------------------------------------------------
        | 7-day sparkline data
        --------------------------------------------------------------------*/
        $last7 = collect(range(6, 0))->map(fn ($d) => $this->orderCount(date: Carbon::today()->subDays($d))
        )->toArray();

        $last7Paid = collect(range(6, 0))->map(fn ($d) => $this->orderCount(isPaid: true, date: Carbon::today()->subDays($d))
        )->toArray();

        return [
            Stat::make('Total Orders', number_format($totalOrders))
                ->description("{$todayOrders} new today")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart($last7)
                ->color('primary'),

            Stat::make('Unpaid Orders', number_format($pendingOrders))
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 10 ? 'danger' : 'warning'),

            Stat::make('Paid Today', $this->formatCurrencyTotals($revenueToday))
                ->description("{$paidOrdersToday} settled orders today")
                ->descriptionIcon($paidOrdersToday >= $paidOrdersYesterday
                    ? 'heroicon-m-arrow-trending-up'
                    : 'heroicon-m-arrow-trending-down')
                ->chart($last7Paid)
                ->color('success'),

            Stat::make('Month Revenue', $this->formatCurrencyTotals($revenueMonth))
                ->description('All paid orders this month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('All-Time Revenue', $this->formatCurrencyTotals($totalRevenue))
                ->description('Portraits + Decor combined')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Unpaid Balance', $this->formatCurrencyTotals($unpaidBalance))
                ->description('Outstanding receivables')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
        ];
    }

    protected function orderCount(?bool $isPaid = null, ?Carbon $date = null): int
    {
        return collect([Order::class, DecorOrder::class])
            ->sum(function (string $model) use ($isPaid, $date): int {
                $query = $model::query();

                if ($isPaid !== null) {
                    $query->where('is_paid', $isPaid);
                }

                if ($date !== null) {
                    $query->whereDate('created_at', $date);
                }

                return $query->count();
            });
    }

    protected function currencyTotals(
        bool $isPaid,
        ?Carbon $date = null,
        ?Carbon $from = null,
        bool $excludeCancelled = false
    ): array {
        $totals = [];

        foreach ([Order::class, DecorOrder::class] as $model) {
            $query = $model::query()
                ->selectRaw('currency, SUM(total_price) as total')
                ->where('is_paid', $isPaid)
                ->groupBy('currency');

            if ($date !== null) {
                $query->whereDate('created_at', $date);
            }

            if ($from !== null) {
                $query->where('created_at', '>=', $from);
            }

            if ($excludeCancelled) {
                $query->whereNotIn('status', ['cancelled']);
            }

            foreach ($query->get() as $row) {
                $currency = $row->currency ?: 'UGX';
                $totals[$currency] = ($totals[$currency] ?? 0) + (float) $row->total;
            }
        }

        ksort($totals);

        return $totals;
    }

    protected function formatCurrencyTotals(array $totals): string
    {
        if (empty($totals)) {
            return 'No settled funds';
        }

        return collect($totals)
            ->map(fn (float $amount, string $currency): string => $this->currencySymbol($currency).' '.number_format($amount, 2))
            ->join(' | ');
    }

    protected function currencySymbol(string $currency): string
    {
        return config("pricing.currencies.{$currency}.symbol", $currency);
    }
}
