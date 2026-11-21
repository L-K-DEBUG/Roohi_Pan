<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Roohi Pan House')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="business-app">
    <aside class="sidebar no-print">
        <a class="brand" href="{{ route('pos.index') }}">Roohi Pan</a>
        <nav>
            <a @class(['active' => request()->routeIs('pos.*', 'orders.*')]) href="{{ route('pos.index') }}">New Order</a>
            <a @class(['active' => request()->routeIs('credit.*')]) href="{{ route('credit.index') }}">Credit Book</a>
            <a @class(['active' => request()->routeIs('reports.*')]) href="{{ route('reports.index') }}">Reports</a>
            @if (auth()->user()->isOwner())
                <a @class(['active' => request()->routeIs('staff.*')]) href="{{ route('staff.index') }}">Staff</a>
                <a @class(['active' => request()->routeIs('inventory.*')]) href="{{ route('inventory.index') }}">Inventory</a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <span>{{ auth()->user()->username ?: auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sidebar-signout" type="submit">Sign Out</button>
            </form>
        </div>
    </aside>
    <main class="main-content">
        @if (! request()->routeIs('orders.receipt', 'credit.index') && $lowStockItems->isNotEmpty())
            <section class="stock-alert" role="status" aria-label="Low stock notice">
                <div class="stock-alert-heading">
                    <span class="stock-alert-icon" aria-hidden="true">!</span>
                    <div>
                        <strong>Stock running low</strong>
                        <p>Please restock these items soon.</p>
                    </div>
                </div>
                <ul class="stock-alert-list">
                    @foreach ($lowStockItems as $item)
                        <li>
                            <span>{{ $item->item_name }}</span>
                            <strong class="stock-alert-quantity">{{ rtrim(rtrim(number_format((float) $item->quantity, 2, '.', ''), '0'), '.') }} remaining</strong>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
        @if (session('status'))
            <div class="notice notice-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="notice notice-error">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
