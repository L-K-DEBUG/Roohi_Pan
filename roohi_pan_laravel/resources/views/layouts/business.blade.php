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
