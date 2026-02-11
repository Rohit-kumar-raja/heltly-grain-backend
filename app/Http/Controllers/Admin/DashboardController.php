<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // ── Stat Cards ──────────────────────────────────────────
        $totalOrders = Order::count();
        $lastMonthOrders = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $thisMonthOrders = Order::where('created_at', '>=', $startOfMonth)->count();
        $orderChange = $lastMonthOrders > 0
            ? round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : ($thisMonthOrders > 0 ? 100 : 0);

        $totalRevenue = Order::sum('total');
        $lastMonthRevenue = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total');
        $thisMonthRevenue = Order::where('created_at', '>=', $startOfMonth)->sum('total');
        $revenueChange = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($thisMonthRevenue > 0 ? 100 : 0);

        $totalCustomers = AppUser::count();
        $lastMonthCustomers = AppUser::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $thisMonthCustomers = AppUser::where('created_at', '>=', $startOfMonth)->count();
        $customerChange = $lastMonthCustomers > 0
            ? round((($thisMonthCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1)
            : ($thisMonthCustomers > 0 ? 100 : 0);

        $pendingOrders = Order::where('status', 'pending')->count();
        $lastMonthPending = Order::where('status', 'pending')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $pendingChange = $lastMonthPending > 0
            ? round((($pendingOrders - $lastMonthPending) / $lastMonthPending) * 100, 1)
            : ($pendingOrders > 0 ? 100 : 0);

        $stats = [
            [
                'label' => 'Total Orders',
                'value' => number_format($totalOrders),
                'change' => ($orderChange >= 0 ? '+' : '') . $orderChange . '%',
                'positive' => $orderChange >= 0,
                'icon' => 'pi-shopping-cart',
                'color' => 'primary',
            ],
            [
                'label' => 'Revenue',
                'value' => '₹' . number_format($totalRevenue),
                'change' => ($revenueChange >= 0 ? '+' : '') . $revenueChange . '%',
                'positive' => $revenueChange >= 0,
                'icon' => 'pi-wallet',
                'color' => 'emerald',
            ],
            [
                'label' => 'Customers',
                'value' => number_format($totalCustomers),
                'change' => ($customerChange >= 0 ? '+' : '') . $customerChange . '%',
                'positive' => $customerChange >= 0,
                'icon' => 'pi-users',
                'color' => 'blue',
            ],
            [
                'label' => 'Pending Orders',
                'value' => number_format($pendingOrders),
                'change' => ($pendingChange >= 0 ? '+' : '') . $pendingChange . '%',
                'positive' => $pendingChange <= 0, // fewer pending is positive
                'icon' => 'pi-clock',
                'color' => 'amber',
            ],
        ];

        // ── Recent Orders ───────────────────────────────────────
        $statusColorMap = [
            'delivered' => 'emerald',
            'processing' => 'blue',
            'pending' => 'amber',
            'shipped' => 'purple',
            'cancelled' => 'red',
        ];

        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) use ($statusColorMap) {
                return [
                    'id' => '#' . $order->order_number,
                    'customer' => $order->user?->name ?? 'Unknown',
                    'product' => $order->items->first()?->product_name ?? '—',
                    'amount' => '₹' . number_format($order->total),
                    'status' => ucfirst($order->status ?? 'pending'),
                    'statusColor' => $statusColorMap[strtolower($order->status ?? 'pending')] ?? 'amber',
                ];
            });

        // ── Top Products ────────────────────────────────────────
        $topProducts = OrderItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(quantity) as total_sales'),
            DB::raw('SUM(price * quantity) as total_revenue')
        )
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sales')
            ->take(4)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'sales' => (int) $item->total_sales,
                    'revenue' => '₹' . number_format($item->total_revenue),
                ];
            });

        // ── Sales Chart (monthly, current year) ─────────────────
        $yearStart = $now->copy()->startOfYear();
        $monthlyData = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as order_count')
        )
            ->where('created_at', '>=', $yearStart)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $salesChart = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'revenue' => [],
            'orders' => [],
        ];
        for ($m = 1; $m <= 12; $m++) {
            $salesChart['revenue'][] = (float) ($monthlyData[$m]->revenue ?? 0);
            $salesChart['orders'][] = (int) ($monthlyData[$m]->order_count ?? 0);
        }

        // ── Category Doughnut ───────────────────────────────────
        $categories = Category::withCount('products')->get();
        $categoryChart = [
            'labels' => $categories->pluck('name')->toArray(),
            'data' => $categories->pluck('products_count')->toArray(),
        ];

        // ── Weekly Orders Bar ───────────────────────────────────
        $weekStart = $now->copy()->startOfWeek();
        $dailyOrders = Order::select(
            DB::raw('DAYOFWEEK(created_at) as dow'),
            DB::raw('COUNT(*) as cnt')
        )
            ->where('created_at', '>=', $weekStart)
            ->groupBy(DB::raw('DAYOFWEEK(created_at)'))
            ->get()
            ->keyBy('dow');

        $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        // MySQL DAYOFWEEK: 1=Sun, 2=Mon, ... 7=Sat
        $dowMap = [2, 3, 4, 5, 6, 7, 1]; // Mon-Sun mapped to MySQL DAYOFWEEK
        $weeklyOrders = [
            'labels' => $dayLabels,
            'data' => array_map(fn($d) => (int) ($dailyOrders[$d]->cnt ?? 0), $dowMap),
            'total' => Order::where('created_at', '>=', $weekStart)->count(),
        ];

        // ── User Growth (last 4 weeks) ──────────────────────────
        $userGrowth = ['labels' => [], 'newUsers' => [], 'totalUsers' => []];
        $runningTotal = AppUser::where('created_at', '<', $now->copy()->subWeeks(4)->startOfWeek())->count();
        for ($w = 4; $w >= 1; $w--) {
            $wStart = $now->copy()->subWeeks($w)->startOfWeek();
            $wEnd = $now->copy()->subWeeks($w)->endOfWeek();
            $newCount = AppUser::whereBetween('created_at', [$wStart, $wEnd])->count();
            $runningTotal += $newCount;
            $userGrowth['labels'][] = 'Week ' . (5 - $w);
            $userGrowth['newUsers'][] = $newCount;
            $userGrowth['totalUsers'][] = $runningTotal;
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'salesChart' => $salesChart,
            'categoryChart' => $categoryChart,
            'weeklyOrders' => $weeklyOrders,
            'userGrowth' => $userGrowth,
        ]);
    }
}
