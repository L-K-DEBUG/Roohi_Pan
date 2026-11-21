@extends('layouts.business')

@section('title', 'Receipt #' . $order->id)

@section('content')
<div class="receipt-wrap">
    <article class="receipt">
        <header class="receipt-header">
            <h1>ROOHI PAN HOUSE</h1>
            <p class="muted"><em>Fed your body, fed your soul - thank you!</em></p>
            <hr>
            <p>Order #{{ $order->id }} | {{ $order->order_type }}</p>
            <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
            @if ($order->table_no)<p>Table: {{ $order->table_no }}</p>@endif
            @if ($order->customer_name)<p>Customer: {{ $order->customer_name }}</p>@endif
            <p>Served by: {{ $order->user->username ?: $order->user->name }}</p>
            <p class="{{ $order->payment_status === 'paid' ? 'paid-label' : 'unpaid-label' }}">
                {{ $order->payment_status === 'paid' ? 'PAID' : 'UNPAID - CREDIT' }}
            </p>
            @if ($order->payment_status === 'paid' && $order->payment_method)
                <p>Payment method: {{ $order->payment_method === 'mpesa' ? 'M-Pesa' : 'Cash' }}</p>
            @endif
            @if ($order->status === 'cancelled')
                <p class="unpaid-label">CANCELLED</p>
            @endif
        </header>
        <hr>
        @foreach ($order->items as $line)
            <div class="receipt-line">
                <span>{{ $line->item->name }} x{{ $line->quantity }}</span>
                <span>KSh {{ number_format($line->price * $line->quantity) }}</span>
            </div>
        @endforeach
        <div class="receipt-total">
            <strong>TOTAL</strong>
            <strong>KSh {{ number_format($order->total_amount) }}</strong>
        </div>
        <p class="receipt-thanks">Thank you for dining with us!</p>
        <div class="receipt-actions no-print">
            <button class="button button-primary" type="button" onclick="window.print()">Print Receipt</button>
            @if ($order->status === 'completed')
                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order and return menu stock?')">
                    @csrf
                    <button class="button button-danger" type="submit">Cancel Order</button>
                </form>
            @endif
            <a href="{{ route('pos.index') }}">Back to POS</a>
        </div>
    </article>
</div>
@endsection
