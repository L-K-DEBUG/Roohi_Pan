@extends('layouts.business')

@section('title', 'Inventory - Roohi Pan')

@section('content')
<header class="page-header">
    <div><p class="eyebrow">Owner tools</p><h1>Inventory Tracker</h1></div>
    <button class="button button-primary no-print" type="button" onclick="window.print()">Print Inventory</button>
</header>

<p class="page-intro">Only ingredients and individually counted drinks are tracked here. Set each opening quantity; sales automatically deduct the recipe amount.</p>

@foreach ($groups as $group => $items)
    @if (count($items))
        <section class="inventory-section">
            <h2 class="category-title">{{ $group }}</h2>
            <div class="inventory-grid">
        @foreach ($items as $item)
            <article id="inventory-item-{{ $item->id }}" class="inventory-card">
                <h2>{{ $item->item_name }}</h2>
                <p class="inventory-quantity">{{ rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }}</p>
                <p class="inventory-unit">
                    @if (in_array($group, ['Canned Soft Drinks', 'Bottled Soft Drinks'], true))
                        individual drinks
                    @elseif ($group === 'Chicken Breast')
                        chicken breasts (half order = 0.5)
                    @elseif ($item->item_name === 'Drumsticks')
                        drumsticks
                    @elseif ($item->item_name === 'Wings')
                        wings
                    @elseif ($item->item_name === 'Chicken Nuggets')
                        nuggets
                    @elseif ($item->item_name === 'Fish Fingers')
                        fish fingers
                    @elseif ($item->item_name === 'Chicken')
                        chicken portions
                    @else
                        pieces
                    @endif
                </p>
                <form method="POST" action="{{ route('inventory.adjust', $item) }}" class="inventory-adjust no-print">
                    @csrf
                    @method('PATCH')
                    @if ($item->item_name === 'Chicken Breast')
                        <input type="number" name="quantity" min="0.5" step="0.5" value="1" aria-label="Quantity to adjust for {{ $item->item_name }}">
                    @else
                        <input type="number" name="quantity" min="1" step="1" value="1" aria-label="Quantity to adjust for {{ $item->item_name }}">
                    @endif
                    <button class="button button-small button-soft" name="action" value="subtract" type="submit">−</button>
                    <button class="button button-small button-soft" name="action" value="add" type="submit">+</button>
                </form>
            </article>
        @endforeach
            </div>
        </section>
    @endif
@endforeach
@endsection
