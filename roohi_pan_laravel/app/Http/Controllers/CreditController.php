<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CreditController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with('user')
            ->where('payment_status', 'unpaid')
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('credit.index', compact('orders'));
    }

    public function settle(Order $order): RedirectResponse
    {
        if ($order->status === 'completed' && $order->payment_status === 'unpaid') {
            $order->update(['payment_status' => 'paid']);
        }

        return redirect()->route('credit.index')->with('status', 'Payment recorded.');
    }
}
