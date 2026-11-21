@extends('layouts.business')

@section('title', 'New Order - Roohi Pan')

@section('content')
<header class="page-header">
    <div><p class="eyebrow">Point of sale</p><h1>New Order</h1></div>
    <span class="user-chip">{{ auth()->user()->username ?: auth()->user()->name }}</span>
</header>

<div class="pos-layout">
    <section class="menu-panel">
        @forelse ($items as $category => $categoryItems)
            <h2 class="category-title">{{ $category }}</h2>
            <div class="menu-grid">
                @foreach ($categoryItems as $item)
                    <button class="menu-item" type="button"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}"
                        data-price="{{ $item->price }}"
                        data-stock="{{ $item->stock }}">
                        <strong>{{ $item->name }}</strong>
                        <span>KSh {{ number_format($item->price) }}</span>
                    </button>
                @endforeach
            </div>
        @empty
            <div class="panel empty-state">No in-stock menu items are available.</div>
        @endforelse
    </section>

    <section class="panel cart-panel">
        <h2>Current Order</h2>
        <form id="order-form" method="POST" action="{{ route('orders.store') }}">
            @csrf
            <label for="order_type">Order type</label>
            <select id="order_type" name="order_type" required>
                <option value="Dine-in">Dine-in</option>
                <option value="Delivery">Delivery</option>
            </select>
            <label for="table_no">Table number <span class="muted">(optional)</span></label>
            <input id="table_no" name="table_no" maxlength="20">
            <label for="customer_name">Customer name</label>
            <input id="customer_name" name="customer_name" maxlength="100" required value="{{ old('customer_name') }}">
            <label for="payment_status">Payment status</label>
            <select id="payment_status" name="payment_status" required>
                <option value="paid" @selected(old('payment_status', 'paid') === 'paid')>Paid</option>
                <option value="unpaid" @selected(old('payment_status') === 'unpaid')>Credit (unpaid)</option>
            </select>
            <div id="payment-method-field">
                <label for="payment_method">Payment method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Cash</option>
                    <option value="mpesa" @selected(old('payment_method') === 'mpesa')>M-Pesa</option>
                </select>
            </div>

            <div id="cart-items" class="cart-items"><p class="muted">Tap menu items to add them to the order.</p></div>
            <div class="cart-total"><span>Total</span><strong id="cart-total">KSh 0</strong></div>
            <button id="submit-order" class="button button-primary button-wide" type="submit" disabled>Place Order &amp; Print</button>
        </form>
    </section>
</div>

<script>
(() => {
    const cart = new Map();
    const cartContainer = document.getElementById('cart-items');
    const totalDisplay = document.getElementById('cart-total');
    const submitButton = document.getElementById('submit-order');
    const form = document.getElementById('order-form');
    const paymentStatus = document.getElementById('payment_status');
    const paymentMethodField = document.getElementById('payment-method-field');
    const paymentMethod = document.getElementById('payment_method');
    const money = amount => new Intl.NumberFormat('en-KE').format(amount);

    function updatePaymentMethodVisibility() {
        const isPaid = paymentStatus.value === 'paid';
        paymentMethodField.hidden = !isPaid;
        paymentMethod.disabled = !isPaid;
        paymentMethod.required = isPaid;
    }

    paymentStatus.addEventListener('change', updatePaymentMethodVisibility);
    updatePaymentMethodVisibility();

    function renderCart() {
        cartContainer.replaceChildren();
        let total = 0;

        if (cart.size === 0) {
            const empty = document.createElement('p');
            empty.className = 'muted';
            empty.textContent = 'Tap menu items to add them to the order.';
            cartContainer.append(empty);
        }

        for (const [id, item] of cart) {
            const line = document.createElement('div');
            line.className = 'cart-line';
            const label = document.createElement('span');
            label.textContent = `${item.name} x${item.quantity}`;
            const price = document.createElement('strong');
            price.textContent = `KSh ${money(item.price * item.quantity)}`;
            const remove = document.createElement('button');
            remove.className = 'button button-danger button-small';
            remove.type = 'button';
            remove.textContent = 'Remove';
            remove.addEventListener('click', () => {
                cart.delete(id);
                renderCart();
            });
            line.append(label, price, remove);
            cartContainer.append(line);
            total += item.price * item.quantity;
        }

        totalDisplay.textContent = `KSh ${money(total)}`;
        submitButton.disabled = cart.size === 0;
    }

    document.querySelectorAll('.menu-item').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const item = cart.get(id) || {
                name: button.dataset.name,
                price: Number(button.dataset.price),
                stock: Number(button.dataset.stock),
                quantity: 0,
            };
            if (item.quantity < item.stock) {
                item.quantity++;
                cart.set(id, item);
                renderCart();
            }
        });
    });

    form.addEventListener('submit', event => {
        if (cart.size === 0) {
            event.preventDefault();
            return;
        }
        for (const [id, item] of cart) {
            for (const [field, value] of [['id', id], ['quantity', item.quantity]]) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `cart[${id}][${field}]`;
                input.value = value;
                form.append(input);
            }
        }
    });
})();
</script>
@endsection
