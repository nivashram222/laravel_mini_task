<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Store Billing & Order Management System — Create orders, manage inventory, and track low-stock products.">
    <title>Store Billing — New Order</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --bg-primary: #0f1117;
            --bg-secondary: #1a1d27;
            --bg-card: #1e2130;
            --bg-input: #252838;
            --border: #2d3148;
            --border-focus: #6366f1;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent: #6366f1;
            --accent-hover: #818cf8;
            --success: #22c55e;
            --success-bg: rgba(34, 197, 94, 0.12);
            --warning: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.12);
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --info: #06b6d4;
            --gradient-accent: linear-gradient(135deg, #6366f1, #8b5cf6);
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.5);
            --radius: 10px;
            --radius-sm: 6px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.5;
        }

        /* ── Header ── */
        .header {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
        }
        .header-title {
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .header-title .icon { font-size: 1.3rem; }
        .header-breadcrumb {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .header-breadcrumb span { color: var(--text-secondary); }
        .header-actions { display: flex; gap: 0.5rem; align-items: center; }

        /* ── Layout ── */
        .main-container {
            max-width: 1340px;
            margin: 0 auto;
            padding: 1.5rem 2rem 3rem;
        }
        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.5rem;
        }

        /* ── Cards ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.3s ease;
        }
        .card:hover { box-shadow: var(--shadow-md); }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .card-title {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-secondary);
        }
        .card-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            background: var(--accent);
            color: white;
        }

        /* ── Form Elements ── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group { display: flex; flex-direction: column; gap: 0.35rem; }
        .form-group label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secondary);
        }
        .form-control {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.6rem 0.85rem;
            font-size: 0.88rem;
            color: var(--text-primary);
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .form-control::placeholder { color: var(--text-muted); }
        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8.5L1 3.5h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2rem;
        }

        /* ── Product Table ── */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .product-table thead th {
            background: var(--bg-input);
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-secondary);
            padding: 0.65rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .product-table thead th:first-child { border-radius: var(--radius-sm) 0 0 0; }
        .product-table thead th:last-child { border-radius: 0 var(--radius-sm) 0 0; }
        .product-table tbody td {
            padding: 0.6rem 0.75rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        .product-table tbody tr { transition: background 0.15s; }
        .product-table tbody tr:hover { background: rgba(99, 102, 241, 0.04); }
        .product-table .qty-input {
            width: 60px;
            text-align: center;
            padding: 0.4rem;
        }
        .product-table .price-col, .product-table .total-col {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }
        .remove-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: var(--danger-bg);
            color: var(--danger);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .remove-btn:hover { background: var(--danger); color: white; }

        /* ── Buttons ── */
        .btn {
            padding: 0.55rem 1.1rem;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.25s ease;
        }
        .btn-primary {
            background: var(--gradient-accent);
            color: white;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }
        .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.5);
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-outline {
            background: transparent;
            color: var(--accent);
            border: 1px solid var(--border);
        }
        .btn-outline:hover {
            background: rgba(99, 102, 241, 0.08);
            border-color: var(--accent);
        }
        .btn-success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
        }
        .btn-success:hover { box-shadow: 0 4px 16px rgba(34, 197, 94, 0.5); transform: translateY(-1px); }
        .btn-block { width: 100%; justify-content: center; padding: 0.7rem; font-size: 0.9rem; }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ── Payment Summary ── */
        .payment-summary { margin-top: 1.25rem; }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 0.45rem 0;
            font-size: 0.88rem;
        }
        .payment-row.total {
            border-top: 2px solid var(--border);
            margin-top: 0.3rem;
            padding-top: 0.65rem;
            font-weight: 700;
            font-size: 1.05rem;
        }
        .payment-row .label { color: var(--text-secondary); }
        .payment-row .value { font-variant-numeric: tabular-nums; }
        .payment-row.total .value { color: var(--success); }

        /* ── Low Stock Alert ── */
        .alert-panel {
            margin-bottom: 1.5rem;
        }
        .alert-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--warning);
        }
        .alert-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background: var(--warning-bg);
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            border-left: 3px solid var(--warning);
        }
        .alert-item .stock-count {
            font-weight: 600;
            color: var(--warning);
            white-space: nowrap;
        }
        .alert-item.out-of-stock {
            background: var(--danger-bg);
            border-left-color: var(--danger);
        }
        .alert-item.out-of-stock .stock-count { color: var(--danger); }

        /* ── Orders History ── */
        .search-section { margin-top: 1.5rem; }
        .search-row {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .search-row .form-control { flex: 1; }
        .order-card {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.85rem 1rem;
            margin-bottom: 0.6rem;
            transition: border-color 0.2s;
        }
        .order-card:hover { border-color: var(--accent); }
        .order-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.4rem;
        }
        .order-id { font-weight: 700; font-size: 0.88rem; color: var(--accent); }
        .order-date { font-size: 0.72rem; color: var(--text-muted); }
        .order-items-list {
            font-size: 0.78rem;
            color: var(--text-secondary);
            margin-bottom: 0.35rem;
        }
        .order-total {
            text-align: right;
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--success);
        }

        /* ── Balance Return ── */
        .balance-section { margin-top: 1rem; }
        .balance-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .balance-result {
            text-align: right;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 0.5rem;
            background: var(--success-bg);
            border-radius: var(--radius-sm);
            color: var(--success);
        }

        /* ── Toast Notification ── */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .toast {
            padding: 0.85rem 1.2rem;
            border-radius: var(--radius-sm);
            font-size: 0.84rem;
            font-weight: 500;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideIn 0.35s ease;
            max-width: 400px;
        }
        .toast-success { background: #16a34a; color: white; }
        .toast-error { background: #dc2626; color: white; }
        .toast-info { background: #2563eb; color: white; }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        /* ── Spinner ── */
        .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .grid-layout { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .form-row { grid-template-columns: 1fr; }
            .main-container { padding: 1rem; }
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        .empty-state .icon { font-size: 2rem; margin-bottom: 0.5rem; }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <div>
        <div class="header-title">
            <span class="icon">🏪</span> Store Billing
        </div>
        <div class="header-breadcrumb">Dashboard / <span>New Order</span></div>
    </div>
    <div class="header-actions">
        <span style="font-size:0.78rem; color:var(--text-muted);" id="currentDate"></span>
    </div>
</header>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Main Content -->
<div class="main-container">
    <div class="grid-layout">

        <!-- Left Column — Order Form -->
        <div>
            <!-- Customer Section -->
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header">
                    <span class="card-title">👤 Customer</span>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="customerEmail">Email</label>
                        <input type="email" id="customerEmail" class="form-control" placeholder="e.g. john@example.com">
                    </div>
                    <div class="form-group">
                        <label for="customerName">Name</label>
                        <input type="text" id="customerName" class="form-control" placeholder="e.g. John Doe">
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header">
                    <span class="card-title">📦 Products</span>
                    <span class="card-badge" id="itemCount">0 items</span>
                </div>
                <table class="product-table" id="productTable">
                    <thead>
                        <tr>
                            <th style="width:40%">Product</th>
                            <th style="width:12%">Qty</th>
                            <th style="width:16%" class="price-col">Price</th>
                            <th style="width:16%" class="total-col">Line Total</th>
                            <th style="width:8%"></th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <!-- Rows added dynamically -->
                    </tbody>
                </table>
                <div style="margin-top:0.75rem; display:flex; justify-content:flex-end;">
                    <button class="btn btn-outline" id="addProductBtn" type="button">
                        + Add Product
                    </button>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title">💳 Payment</span>
                </div>
                <div class="payment-summary">
                    <div class="payment-row">
                        <span class="label">Subtotal</span>
                        <span class="value" id="subtotalDisplay">₹0.00</span>
                    </div>
                    <div class="payment-row">
                        <span class="label">Tax</span>
                        <span class="value" id="taxDisplay">₹0.00</span>
                    </div>
                    <div class="payment-row total">
                        <span class="label">Order Total</span>
                        <span class="value" id="grandTotalDisplay">₹0.00</span>
                    </div>
                </div>
                <div style="margin-top:1.25rem;">
                    <button class="btn btn-success btn-block" id="generateBillBtn" type="button" disabled>
                        ⚡ Generate Bill
                    </button>
                </div>

                <!-- Balance to Return -->
                <div class="balance-section">
                    <div class="card-title" style="margin-bottom:0.6rem; margin-top:1rem;">Balance to Return</div>
                    <div class="balance-row">
                        <input type="number" id="amountPaid" class="form-control" placeholder="Amount paid" style="flex:1;" step="0.01" min="0">
                    </div>
                    <div class="balance-result" id="balanceReturn">₹0.00 = (Paid) − (Order Total)</div>
                </div>
            </div>
        </div>

        <!-- Right Column — Sidebar -->
        <div>
            <!-- Low Stock Alert -->
            <div class="card alert-panel">
                <div class="alert-title">⚠️ Low Stock Alert</div>
                <ul class="alert-list" id="lowStockList">
                    <li class="empty-state"><div class="icon">📦</div>Loading...</li>
                </ul>
            </div>

            <!-- Search Orders -->
            <div class="card search-section">
                <div class="card-header">
                    <span class="card-title">🔍 Search Monthly Orders</span>
                </div>
                <div class="search-row">
                    <input type="email" id="searchEmail" class="form-control" placeholder="Customer email">
                    <button class="btn btn-primary" id="searchOrdersBtn" type="button">Search</button>
                </div>
                <div id="orderHistoryResults">
                    <div class="empty-state">
                        <div class="icon">📋</div>
                        Enter an email to search order history
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const API_BASE = '/api';
    let products = [];
    let currentOrderTotal = 0;

    // ── Utility ──
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => document.querySelectorAll(sel);
    const fmt = (n) => `₹${parseFloat(n).toFixed(2)}`;

    // Set current date
    const now = new Date();
    $('#currentDate').textContent = now.toLocaleDateString('en-IN', {
        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'
    });

    // ── Toast Notifications ──
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `<span>${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span> ${message}`;
        $('#toastContainer').appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.35s ease forwards';
            setTimeout(() => toast.remove(), 350);
        }, 4000);
    }

    // ── Fetch Products ──
    async function loadProducts() {
        try {
            const res = await fetch(`${API_BASE}/products`);
            const json = await res.json();
            products = json.data || [];
        } catch (e) {
            showToast('Failed to load products', 'error');
        }
    }

    // ── Fetch Low Stock ──
    async function loadLowStock() {
        try {
            const res = await fetch(`${API_BASE}/products/low-stock?threshold=10`);
            const json = await res.json();
            const items = json.data || [];
            const list = $('#lowStockList');

            if (items.length === 0) {
                list.innerHTML = '<li class="empty-state"><div class="icon">✅</div>All products are well stocked</li>';
                return;
            }

            list.innerHTML = items.map(p => `
                <li class="alert-item ${p.stock_on_hand === 0 ? 'out-of-stock' : ''}">
                    <span>${p.name}</span>
                    <span class="stock-count">${p.stock_on_hand === 0 ? 'Out of stock' : p.stock_on_hand + ' left'}</span>
                </li>
            `).join('');
        } catch (e) {
            showToast('Failed to load low stock data', 'error');
        }
    }

    // ── Product Rows ──
    function buildProductOptions(selectedId = '') {
        const usedIds = [...$$('.product-select')].map(s => s.value).filter(Boolean);
        let opts = '<option value="">— Choose product —</option>';
        products.forEach(p => {
            const disabled = usedIds.includes(String(p.id)) && String(p.id) !== selectedId ? 'disabled' : '';
            const stock = p.stock_on_hand === 0 ? ' [OUT OF STOCK]' : ` [${p.stock_on_hand} in stock]`;
            opts += `<option value="${p.id}" ${disabled} ${String(p.id) === selectedId ? 'selected' : ''}>${p.name} (${p.code})${stock}</option>`;
        });
        return opts;
    }

    function addProductRow() {
        const tbody = $('#productTableBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select class="form-control product-select">${buildProductOptions()}</select>
            </td>
            <td>
                <input type="number" class="form-control qty-input" value="1" min="1">
            </td>
            <td class="price-col price-display">₹0.00</td>
            <td class="total-col line-total-display">₹0.00</td>
            <td>
                <button class="remove-btn" type="button" title="Remove">×</button>
            </td>
        `;
        tbody.appendChild(row);

        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.qty-input');
        const removeBtn = row.querySelector('.remove-btn');

        select.addEventListener('change', () => { updateRow(row); refreshSelects(); });
        qtyInput.addEventListener('input', () => updateRow(row));
        removeBtn.addEventListener('click', () => { row.remove(); recalculate(); refreshSelects(); });

        updateItemCount();
    }

    function updateRow(row) {
        const select = row.querySelector('.product-select');
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const product = products.find(p => String(p.id) === select.value);

        if (product && qty > 0) {
            const price = parseFloat(product.price_per_unit);
            const taxPct = parseFloat(product.tax_percentage);
            const lineSubtotal = price * qty;
            const lineTax = lineSubtotal * (taxPct / 100);
            const lineTotal = lineSubtotal + lineTax;

            row.querySelector('.price-display').textContent = fmt(price);
            row.querySelector('.line-total-display').textContent = fmt(lineTotal);
        } else {
            row.querySelector('.price-display').textContent = '₹0.00';
            row.querySelector('.line-total-display').textContent = '₹0.00';
        }

        recalculate();
    }

    function refreshSelects() {
        $$('.product-select').forEach(select => {
            const currentVal = select.value;
            select.innerHTML = buildProductOptions(currentVal);
        });
    }

    function recalculate() {
        let subtotal = 0;
        let totalTax = 0;

        $$('#productTableBody tr').forEach(row => {
            const select = row.querySelector('.product-select');
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const product = products.find(p => String(p.id) === select.value);

            if (product && qty > 0) {
                const price = parseFloat(product.price_per_unit);
                const taxPct = parseFloat(product.tax_percentage);
                const lineSubtotal = price * qty;
                subtotal += lineSubtotal;
                totalTax += lineSubtotal * (taxPct / 100);
            }
        });

        const grandTotal = subtotal + totalTax;
        currentOrderTotal = grandTotal;

        $('#subtotalDisplay').textContent = fmt(subtotal);
        $('#taxDisplay').textContent = fmt(totalTax);
        $('#grandTotalDisplay').textContent = fmt(grandTotal);

        updateItemCount();
        updateBalance();
        validateForm();
    }

    function updateItemCount() {
        const count = $$('#productTableBody tr').length;
        $('#itemCount').textContent = `${count} item${count !== 1 ? 's' : ''}`;
    }

    function updateBalance() {
        const paid = parseFloat($('#amountPaid').value) || 0;
        const balance = paid - currentOrderTotal;
        $('#balanceReturn').textContent = `${fmt(balance)} = ${fmt(paid)} (Paid) − ${fmt(currentOrderTotal)} (Order Total)`;
        $('#balanceReturn').style.color = balance >= 0 ? 'var(--success)' : 'var(--danger)';
    }

    function validateForm() {
        const email = $('#customerEmail').value.trim();
        const name = $('#customerName').value.trim();
        const rows = $$('#productTableBody tr');
        let hasValidProduct = false;

        rows.forEach(row => {
            const select = row.querySelector('.product-select');
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            if (select.value && qty > 0) hasValidProduct = true;
        });

        $('#generateBillBtn').disabled = !(email && name && hasValidProduct);
    }

    // ── Generate Bill ──
    async function generateBill() {
        const btn = $('#generateBillBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Processing...';

        const items = [];
        $$('#productTableBody tr').forEach(row => {
            const productId = row.querySelector('.product-select').value;
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            if (productId && qty > 0) {
                items.push({ product_id: parseInt(productId), quantity: qty });
            }
        });

        try {
            const res = await fetch(`${API_BASE}/orders`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    customer_name: $('#customerName').value.trim(),
                    customer_email: $('#customerEmail').value.trim(),
                    items: items,
                }),
            });

            const json = await res.json();

            if (res.ok && json.success) {
                showToast(`Order #${json.data.id} created successfully!`, 'success');

                // Reset form
                $('#customerEmail').value = '';
                $('#customerName').value = '';
                $('#productTableBody').innerHTML = '';
                $('#amountPaid').value = '';
                currentOrderTotal = 0;
                recalculate();

                // Refresh products and low stock
                await loadProducts();
                await loadLowStock();
            } else {
                const errors = json.errors || {};
                const errorMsgs = Object.values(errors).flat();
                errorMsgs.forEach(msg => showToast(msg, 'error'));
            }
        } catch (e) {
            showToast('Network error. Please try again.', 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '⚡ Generate Bill';
        validateForm();
    }

    // ── Search Orders ──
    async function searchOrders() {
        const email = $('#searchEmail').value.trim();
        if (!email) {
            showToast('Please enter a customer email', 'error');
            return;
        }

        const container = $('#orderHistoryResults');
        container.innerHTML = '<div class="empty-state"><span class="spinner" style="margin:0 auto; border-color:var(--text-muted); border-top-color:var(--accent);"></span></div>';

        try {
            const res = await fetch(`${API_BASE}/customers/${encodeURIComponent(email)}/orders`);
            const json = await res.json();
            const orders = json.data || [];

            if (orders.length === 0) {
                container.innerHTML = '<div class="empty-state"><div class="icon">📭</div>No orders found for this email</div>';
                return;
            }

            container.innerHTML = orders.map(order => {
                const date = new Date(order.created_at).toLocaleDateString('en-IN', {
                    year: 'numeric', month: 'short', day: 'numeric'
                });
                const itemsList = (order.items || []).map(i =>
                    `${i.product?.name || 'Product'} × ${i.quantity}`
                ).join(', ');

                return `
                    <div class="order-card">
                        <div class="order-card-header">
                            <span class="order-id">Order #${order.id}</span>
                            <span class="order-date">${date}</span>
                        </div>
                        <div class="order-items-list">${itemsList}</div>
                        <div class="order-total">${fmt(order.grand_total)}</div>
                    </div>
                `;
            }).join('');
        } catch (e) {
            container.innerHTML = '<div class="empty-state"><div class="icon">❌</div>Failed to fetch orders</div>';
        }
    }

    // ── Event Listeners ──
    $('#addProductBtn').addEventListener('click', addProductRow);
    $('#generateBillBtn').addEventListener('click', generateBill);
    $('#searchOrdersBtn').addEventListener('click', searchOrders);
    $('#amountPaid').addEventListener('input', updateBalance);
    $('#customerEmail').addEventListener('input', validateForm);
    $('#customerName').addEventListener('input', validateForm);
    $('#searchEmail').addEventListener('keypress', (e) => { if (e.key === 'Enter') searchOrders(); });

    // ── Initialization ──
    (async () => {
        await loadProducts();
        await loadLowStock();
        addProductRow(); // Start with one product row
    })();
});
</script>
</body>
</html>
