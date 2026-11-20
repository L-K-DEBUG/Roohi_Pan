<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'today');
        abort_unless(in_array($filter, ['today', 'week', 'month', 'year'], true), 404);

        $now = CarbonImmutable::now();
        [$start, $end] = match ($filter) {
            'week' => [$now->startOfWeek(), $now->endOfWeek()],
            'month' => [$now->startOfMonth(), $now->endOfMonth()],
            'year' => [$now->startOfYear(), $now->endOfYear()],
            default => [$now->startOfDay(), $now->endOfDay()],
        };

        $orders = Order::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end]);

        $stats = (clone $orders)
            ->selectRaw('COUNT(*) as orders')
            ->selectRaw("SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as revenue")
            ->selectRaw("SUM(CASE WHEN payment_status = 'unpaid' THEN total_amount ELSE 0 END) as credit")
            ->selectRaw("SUM(CASE WHEN order_type = 'Dine-in' THEN 1 ELSE 0 END) as dine_in")
            ->selectRaw("SUM(CASE WHEN order_type = 'Delivery' THEN 1 ELSE 0 END) as delivery")
            ->first();

        $mostUsed = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('items', 'items.id', '=', 'order_items.item_id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get(['items.name', DB::raw('SUM(order_items.quantity) as total_qty')]);

        $history = (clone $orders)
            ->with(['user', 'items.item'])
            ->latest()
            ->get();

        return view('reports.index', compact('filter', 'stats', 'mostUsed', 'history'));
    }
}
