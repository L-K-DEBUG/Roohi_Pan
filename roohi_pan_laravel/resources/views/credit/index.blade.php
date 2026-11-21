@extends('layouts.business')

@section('title', 'Credit Book - Roohi Pan')

@section('content')
<header class="page-header">
    <div><p class="eyebrow">Payments</p><h1>Credit Book</h1><p class="muted">Unpaid completed orders</p></div>
    <button class="button button-primary no-print" type="button" onclick="window.print()">Print Credit List</button>
</header>
<section class="panel table-panel">
    <div class="table-scroll">
        <table>
            <thead><tr><th>Order</th><th>Date</th><th>Customer</th><th>Served by</th><th>Amount owed</th><th class="no-print">Action</th></tr></thead>
            <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td><a href="{{ route('orders.receipt', $order) }}">#{{ $order->id }}</a></td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->customer_name ?: 'Walk-in Customer' }}</td>
                    <td>{{ $order->user->username ?: $order->user->name }}</td>
                    <td><strong>KSh {{ number_format($order->total_amount) }}</strong></td>
                    <td class="no-print">
                        <form class="settle-form" method="POST" action="{{ route('credit.settle', $order) }}" onsubmit="return confirm('Confirm payment received?')">
                            @csrf
                            <select class="payment-method-select" name="payment_method" required aria-label="Payment method for order {{ $order->id }}">
                                <option value="">Payment method</option>
                                <option value="cash">Cash</option>
                                <option value="mpesa">M-Pesa</option>
                            </select>
                            <button class="button button-small button-soft" type="submit">Mark as Paid</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-state">No outstanding credit. All customers have paid.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
