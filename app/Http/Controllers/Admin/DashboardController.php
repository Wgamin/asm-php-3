<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $previousMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $previousMonthStart->copy()->endOfMonth();

        $latestUsers = User::where('role', 'user')
            ->latest()
            ->limit(5)
            ->get();

        $latestOrders = Order::with(['user', 'payment'])
            ->latest()
            ->limit(5)
            ->get();

        $completedOrdersThisMonth = Order::with(['items.product', 'items.variant'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->get();

        $completedOrdersPreviousMonth = Order::with(['items.product', 'items.variant'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->get();

        $userRegistrationsThisMonth = User::where('role', 'user')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $userRegistrationsPreviousMonth = User::where('role', 'user')
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->count();

        $ordersToday = Order::whereDate('created_at', $now->toDateString())->count();
        $ordersYesterday = Order::whereDate('created_at', $now->copy()->subDay()->toDateString())->count();

        $monthlyRevenue = (float) $completedOrdersThisMonth->sum(fn ($order) => $order->payable_total);
        $previousMonthlyRevenue = (float) $completedOrdersPreviousMonth->sum(fn ($order) => $order->payable_total);

        $monthlyProfit = (float) $completedOrdersThisMonth->sum(fn ($order) => $order->gross_profit_amount);
        $previousMonthlyProfit = (float) $completedOrdersPreviousMonth->sum(fn ($order) => $order->gross_profit_amount);

        $orderStatusCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipping' => Order::where('status', 'shipping')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        $revenueSeries = collect(range(6, 0))
            ->map(function ($daysAgo) use ($now) {
                $date = $now->copy()->subDays($daysAgo);

                return [
                    'date' => $date->toDateString(),
                    'label' => $date->format('d/m'),
                    'day_label' => $date->locale('vi')->translatedFormat('D'),
                    'revenue' => 0.0,
                ];
            });

        $revenueByDate = Order::query()
            ->selectRaw('DATE(created_at) as order_date, SUM(payable_amount) as revenue_amount')
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $now->copy()->subDays(6)->toDateString())
            ->groupBy('order_date')
            ->pluck('revenue_amount', 'order_date');

        $revenueSeries = $revenueSeries
            ->map(function ($day) use ($revenueByDate) {
                $day['revenue'] = (float) ($revenueByDate[$day['date']] ?? 0);

                return $day;
            })
            ->values();

        $maxRevenue = max((float) $revenueSeries->max('revenue'), 1);
        $revenueSeries = $revenueSeries
            ->map(function ($day) use ($maxRevenue) {
                $day['height'] = max(12, (int) round(($day['revenue'] / $maxRevenue) * 220));

                return $day;
            })
            ->values();

        $topProducts = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as sold_quantity, SUM(price * quantity) as revenue_amount, SUM(cost_price * quantity) as cost_amount')
            ->whereHas('order', fn ($query) => $query->where('status', 'completed'))
            ->with('product.category')
            ->groupBy('product_id')
            ->orderByDesc('sold_quantity')
            ->limit(5)
            ->get()
            ->filter(fn ($row) => $row->product !== null)
            ->map(function ($row) {
                return [
                    'product' => $row->product,
                    'sold_quantity' => (int) $row->sold_quantity,
                    'revenue_amount' => (float) $row->revenue_amount,
                    'profit_amount' => (float) $row->revenue_amount - (float) $row->cost_amount,
                ];
            })
            ->values();

        return view('admin.dashboard', [
            'latestUsers' => $latestUsers,
            'latestOrders' => $latestOrders,
            'stats' => [
                'total_users' => User::where('role', 'user')->count(),
                'users_growth' => $this->makeGrowth($userRegistrationsThisMonth, $userRegistrationsPreviousMonth),
                'orders_today' => $ordersToday,
                'orders_growth' => $this->makeGrowth($ordersToday, $ordersYesterday),
                'monthly_revenue' => $monthlyRevenue,
                'revenue_growth' => $this->makeGrowth($monthlyRevenue, $previousMonthlyRevenue),
                'monthly_profit' => $monthlyProfit,
                'profit_growth' => $this->makeGrowth($monthlyProfit, $previousMonthlyProfit),
            ],
            'orderStatusCounts' => $orderStatusCounts,
            'revenueSeries' => $revenueSeries,
            'topProducts' => $topProducts,
        ]);
    }

    protected function makeGrowth(float|int $current, float|int $previous): array
    {
        $delta = $current - $previous;

        if ((float) $previous === 0.0) {
            $percent = (float) $current > 0 ? 100.0 : 0.0;
        } else {
            $percent = round(($delta / $previous) * 100, 1);
        }

        return [
            'delta' => $delta,
            'percent' => $percent,
            'is_positive' => $delta >= 0,
        ];
    }
}
