<?php

namespace App\Livewire\Dashboard;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\LaundryService;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Page extends Component
{
    public function render()
    {
        // Summary Cards Data
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', OrderStatus::PENDING)->count();
        $completedOrders = Order::where('status', OrderStatus::COMPLETED)->count();
        $totalRevenue = Payment::where('status', PaymentStatus::PAID)->sum('amount');
        $monthlyRevenue = Payment::where('status', PaymentStatus::PAID)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Chart Data
        $monthlyOrdersData = $this->getMonthlyOrdersData();
        $orderStatusData = $this->getOrderStatusData();
        $revenueData = $this->getRevenueData();
        $topServicesData = $this->getTopServicesData();

        return view('livewire.dashboard.page', [
            'totalCustomers' => $totalCustomers,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyOrdersData' => $monthlyOrdersData,
            'orderStatusData' => $orderStatusData,
            'revenueData' => $revenueData,
            'topServicesData' => $topServicesData,
        ]);
    }

    private function getMonthlyOrdersData()
    {
        $monthlyOrders = Order::select(
                DB::raw("CAST(strftime('%m', created_at) AS INTEGER) as month"),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw("strftime('%m', created_at)"))
            ->orderBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = array_fill(0, 12, 0);

        foreach ($monthlyOrders as $order) {
            $data[$order->month - 1] = $order->count;
        }

        return [
            'categories' => $months,
            'data' => $data
        ];
    }

    private function getOrderStatusData()
    {
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];

        foreach ($statusCounts as $status) {
            $labels[] = ucfirst($status->status->value);
            $data[] = $status->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getRevenueData()
    {
        $revenueData = Payment::select(
                DB::raw("DATE(created_at) as date"),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', PaymentStatus::PAID)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date')
            ->get();

        $categories = [];
        $data = [];

        foreach ($revenueData as $revenue) {
            $categories[] = Carbon::parse($revenue->date)->format('M d');
            $data[] = (float) $revenue->total;
        }

        return [
            'categories' => $categories,
            'data' => $data
        ];
    }

    private function getTopServicesData()
    {
        $topServices = LaundryService::select('laundry_services.*')
            ->leftJoin('order_items', 'laundry_services.id', '=', 'order_items.laundry_service_id')
            ->selectRaw('laundry_services.*, COUNT(order_items.id) as order_items_count')
            ->groupBy('laundry_services.id')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($topServices as $service) {
            $labels[] = $service->name;
            $data[] = $service->order_items_count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getDailyOrdersData()
    {
        $dailyOrders = Order::select(
                DB::raw("DATE(created_at) as date"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date')
            ->get();

        $categories = [];
        $data = [];

        foreach ($dailyOrders as $order) {
            $categories[] = Carbon::parse($order->date)->format('M d');
            $data[] = $order->count;
        }

        return [
            'categories' => $categories,
            'data' => $data
        ];
    }
}