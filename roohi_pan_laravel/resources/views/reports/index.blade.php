@extends('layouts.business')

@section('title', 'Reports - Roohi Pan')

@section('content')
<header class="page-header">
    <div><p class="eyebrow">Sales overview</p><h1>Reports</h1></div>
    <button class="button button-primary no-print" type="button" onclick="window.print()">Print Report</button>
</header>
<nav class="filter-tabs no-print" aria-label="Report period">
    @foreach (['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'] as $key => $label)
        <a class="{{ $filter === $key ? 'selected' : '' }}" href="{{ route('reports.index', ['filter' => $key]) }}">{{ $label }}</a>
    @endforeach
</nav>
<section class="stats-grid">
    <article class="stat-card stat-dark"><h2>Revenue (paid)</h2><p>KSh {{ number_format($stats->revenue ?? 0) }}</p></article>
    <article class="stat-card stat-credit"><h2>Outstanding credit</h2><p>KSh {{ number_format($stats->credit ?? 0) }}</p></article>
    <article class="stat-card"><h2>Orders</h2><p>{{ $stats->orders ?? 0 }}</p></article>
    <article class="stat-card"><h2>Dine-in</h2><p>{{ $stats->dine_in ?? 0 }}</p></article>
    <article class="stat-card"><h2>Delivery</h2><p>{{ $stats->delivery ?? 0 }}</p></article>
</section>
<section class="panel table-panel report-section">
    <h2>Most Used Items</h2>
    <div class="table-scroll"><table>
        <thead><tr><th>Item name</th><th>Quantity sold</th></tr></thead>
        <tbody>
        @forelse ($mostUsed as $item)
            <tr><td>{{ $item->name }}</td><td><strong>{{ $item->total_qty }}</strong></td></tr>
        @empty
            <tr><td colspan="2" class="empty-state">No items sold in this period.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>
<section class="panel table-panel report-section">
    <h2>Detailed Order History</h2>
    <p class="muted">Completed orders with the team member and menu items recorded.</p>
    <div class="table-scroll"><table>
        <thead><tr><th>Order</th><th>Date &amp; time</th><th>Customer</th><th>Employee</th><th>Type</th><th>Items sold</th><th>Total</th></tr></thead>
        <tbody>
        @forelse ($history as $order)
            <tr>
                <td><a href="{{ route('orders.receipt', $order) }}">#{{ $order->id }}</a></td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->customer_name ?: 'Walk-in Customer' }}</td>
                <td>{{ $order->user->username ?: $order->user->name }}</td>
                <td>{{ $order->order_type }}</td>
                <td>{{ $order->items->map(fn ($line) => $line->item->name . ' (' . $line->quantity . ')')->implode(', ') }}</td>
                <td><strong>KSh {{ number_format($order->total_amount) }}</strong></td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-state">No orders found for this period.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>
@endsection
