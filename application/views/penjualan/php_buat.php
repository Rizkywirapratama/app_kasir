<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
    :root {
        --primary: #2563eb;
        --primary-light: #eff6ff;
        --success: #10b981;
        --success-light: #ecfdf5;
        --danger: #ef4444;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .pos-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        height: calc(100vh - 120px);
    }

    /* Left Side: Product Selection */
    .product-section {
        background:#ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        gap: 20px;
        overflow: hidden;
    }

    .search-bar {
        position: relative;
    }
    .search-bar i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
    }
    .search-bar input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: var(--bg-light);
        font-weight: 600;
        transition: all 0.2s;
    }
    .search-bar input:focus {
        border-color: var(--primary);
        background:#ffffff;
        box-shadow: 0 0 0 4px var(--primary-light);
        outline: none;
    }

    .category-filter {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .category-btn {
        padding: 8px 16px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background:#ffffff;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--muted);
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s;
    }
    .category-btn:hover { background: var(--bg-light); }
    .category-btn.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 16px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .product-card {
        background:#ffffff;
        border-radius: 16px;
        padding: 12px;
        border: 1px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }
    .product-card img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        background: var(--bg-light);
    }
    .product-card .name {
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--dark);
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-card .price {
        font-weight: 900;
        color: var(--primary);
        font-size: 1rem;
    }
    .product-card .stock-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--muted);
        border: 1px solid var(--border-color);
    }

    /* Right Side: Order Summary */
    .order-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
        height: 100%;
    }

    .cart-card {
        background:#ffffff;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        flex: 1;
        box-shadow: var(--shadow);
    }

    .cart-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cart-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    .cart-item {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--bg-light);
    }
    .cart-item-info { flex: 1; }
    .cart-item-name { font-weight: 700; font-size: 0.85rem; margin-bottom: 2px; }
    .cart-item-price { font-size: 0.75rem; color: var(--muted); font-weight: 600; }
    .cart-item-qty {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-light);
        padding: 4px 8px;
        border-radius: 8px;
    }
    .cart-item-qty button {
        background:#ffffff;
        border: 1px solid var(--border-color);
        width: 20px;
        height: 20px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
    }
    .cart-item-qty span { font-weight: 800; font-size: 0.8rem; min-width: 15px; text-align: center; }

    .cart-footer {
        background:#ffffff;
        padding: 24px;
        border-top: 1px solid var(--border-color);
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .summary-total {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 2px solid var(--bg-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .total-label { font-weight: 900; font-size: 1rem; color: var(--dark); }
    .total-amount { font-weight: 900; font-size: 1.5rem; color: var(--primary); letter-spacing: -1px; }

    .payment-method-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin: 16px 0;
    }
    .pm-item {
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        padding: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 800;
        font-size: 0.65rem;
        color: var(--muted);
    }
    .pm-item.active {
        border-color: var(--primary);
        background: var(--primary-light);
        color: var(--primary);
    }
    .pm-item i { display: block; font-size: 1.1rem; margin-bottom: 4px; }

    .btn-checkout {
        width: 100%;
        padding: 16px;
        border-radius: 16px;
        border: none;
        background: var(--primary);
        color: #fff;
        font-weight: 900;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        transition: all 0.2s;
    }
    .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3); }
    .btn-checkout:disabled { background: #e2e8f0; box-shadow: none; cursor: not-allowed; transform: none; }

    .product-section,
    .product-card,
    .cart-card,
    .cart-header,
    .cart-footer,
    .search-bar input,
    .category-btn,
    .cart-item-qty {
        background: #ffffff !important;
        color: #334155 !important;
        border-color: #e2e8f0 !important;
    }
    .product-card .name,
    .cart-header h5,
    .total-label { color: #0f172a !important; }
</style>

<div class="pos-layout">
    <!-- Left: Menu Selection -->
    <div class="product-section">
        <div class="flex items-center justify-between">
                <h5 class="font-extrabold m-0"><i class="fas fa-th-large text-primary me-2"></i> Menu Selection</h5>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchMenu" placeholder="Search menu..." onkeyup="filterMenu()">
            </div>
        </div>

            <div class="category-filter no-scrollbar">
            <div class="category-btn active" onclick="filterCategory('All', this)">All Items</div>
            <?php 
            $cats = array_unique(array_column($menu, 'nama_kategori'));
            foreach($cats as $cat): ?>
                <div class="category-btn" onclick="filterCategory('<?= $cat ?>', this)"><?= $cat ?></div>
            <?php endforeach; ?>
        </div>

        <div class="product-grid" id="menuContainer">
            <?php foreach($menu as $m): ?>
            <div class="product-card item-menu" data-id="<?= $m->id_menu ?>" data-nama="<?= $m->nama_menu ?>" data-harga="<?= $m->harga ?>" data-stok="<?= $m->stok ?>" data-category="<?= $m->nama_kategori ?>" onclick="addToCart(this)">
                <span class="stock-badge">Stok: <?= $m->stok ?></span>
                <img src="<?= $m->foto ? base_url('assets/images/menu/'.$m->foto) : 'https://placehold.co/400x300/f8fafc/6366f1?text='.$m->nama_menu ?>" alt="<?= $m->nama_menu ?>">
                <div class="name"><?= $m->nama_menu ?></div>
                <div class="price">Rp <?= number_format($m->harga, 0, ',', '.') ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right: Cart & Checkout -->
    <div class="order-section">
        <div class="cart-card">
            <div class="cart-header">
                <h5>Current Order</h5>
                <button class="inline-flex items-center px-2 py-1 text-red-600 font-bold rounded-md hover:bg-red-50" onclick="clearCart()">Clear</button>
            </div>

            <div class="cart-items" id="cartContainer">
                <div class="text-center py-5 text-gray-400" id="emptyCart">
                    <i class="fas fa-shopping-basket fa-3x mb-3" style="opacity: 0.1;"></i>
                    <p class="font-bold">Your cart is empty</p>
                </div>
            </div>

            <div class="cart-footer">
                <div class="summary-item">
                    <span class="text-muted">Subtotal</span>
                    <span id="displaySubtotal">Rp 0</span>
                </div>
                <div class="summary-item">
                    <span class="text-muted">Discount</span>
                    <span id="displayDiskon" class="text-danger">-Rp 0</span>
                </div>
                <div class="summary-total">
                    <span class="total-label">TOTAL</span>
                    <span class="total-amount" id="displayTotal">Rp 0</span>
                </div>

                <div class="payment-method-grid">
                    <div class="pm-item active" data-method="Cash" onclick="setPayment('Cash', this)">
                        <i class="fas fa-money-bill-wave"></i> Cash
                    </div>
                    <div class="pm-item" data-method="QRIS" onclick="setPayment('QRIS', this)">
                        <i class="fas fa-qrcode"></i> QRIS
                    </div>
                    <div class="pm-item" data-method="Debit/Kredit" onclick="setPayment('Debit/Kredit', this)">
                        <i class="fas fa-credit-card"></i> Debit
                    </div>
                </div>

                <form id="formCheckout" action="<?= base_url('penjualan/simpan') ?>" method="POST">
                    <input type="hidden" name="invoice" value="<?= $invoice ?>">
                    <input type="hidden" name="mode_pembayaran" id="inputMode" value="Cash">
                    <input type="hidden" name="total_harga" id="inputTotal" value="0">
                    <input type="hidden" name="diskon" id="inputDiskon" value="0">
                    <input type="hidden" name="bayar" id="inputBayar" value="0">
                    <input type="hidden" name="kembalian" id="inputKembalian" value="0">
                    <input type="hidden" name="tipe_pesanan" value="Take-away">
                    <input type="hidden" name="id_meja" value="0">
                    <input type="hidden" name="nama_pelanggan" value="Umum">
                    
                    <button type="button" id="btnCheckout" class="btn-checkout" onclick="prosesCheckout()" disabled>
                        PLACE ORDER (F8)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let currentMethod = 'Cash';

function filterMenu() {
    let q = document.getElementById('searchMenu').value.toLowerCase();
    document.querySelectorAll('.item-menu').forEach(el => {
        let name = el.dataset.nama.toLowerCase();
        el.style.display = name.includes(q) ? 'flex' : 'none';
    });
}

function filterCategory(cat, btn) {
    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.item-menu').forEach(el => {
        if(cat === 'All' || el.dataset.category === cat) el.style.display = 'flex';
        else el.style.display = 'none';
    });
}

function addToCart(el) {
    let id = el.dataset.id;
    let nama = el.dataset.nama;
    let harga = parseInt(el.dataset.harga);
    let stok = parseInt(el.dataset.stok);

    let exist = cart.find(i => i.id == id);
    if (exist) {
        if (exist.qty >= stok) {
            Swal.fire({ icon:'error', title:'Out of stock', toast:true, position:'top-end', showConfirmButton:false, timer:2000 });
            return;
        }
        exist.qty++;
    } else {
        cart.push({ id, nama, harga, qty: 1 });
    }
    renderCart();
}

function updateQty(id, delta) {
    let item = cart.find(i => i.id == id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) cart = cart.filter(i => i.id != id);
    renderCart();
}

function clearCart() { cart = []; renderCart(); }

function renderCart() {
    let container = document.getElementById('cartContainer');
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted" id="emptyCart">
                <i class="fas fa-shopping-basket fa-3x mb-3" style="opacity: 0.1;"></i>
                <p class="fw-bold">Your cart is empty</p>
            </div>`;
        updateSummary(0);
        document.getElementById('btnCheckout').disabled = true;
        return;
    }

    container.innerHTML = cart.map(i => `
        <div class="cart-item animate__animated animate__fadeInRight">
            <div class="cart-item-info">
                <div class="cart-item-name">${i.nama}</div>
                <div class="cart-item-price">Rp ${i.harga.toLocaleString('id-ID')}</div>
            </div>
            <div class="cart-item-qty">
                <button onclick="updateQty('${i.id}', -1)"><i class="fas fa-minus"></i></button>
                <span>${i.qty}</span>
                <button onclick="updateQty('${i.id}', 1)"><i class="fas fa-plus"></i></button>
            </div>
        </div>
    `).join('');

    let subtotal = cart.reduce((acc, i) => acc + (i.harga * i.qty), 0);
    updateSummary(subtotal);
    document.getElementById('btnCheckout').disabled = false;
}

function updateSummary(subtotal) {
    let diskon = 0;
    let total = subtotal - diskon;
    document.getElementById('displaySubtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('displayTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('inputTotal').value = total;
}

function setPayment(method, el) {
    currentMethod = method;
    document.querySelectorAll('.pm-item').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('inputMode').value = method;
}

async function prosesCheckout() {
    const total = parseInt(document.getElementById('inputTotal').value);
    if (currentMethod === 'Cash') {
        const { value: bayar } = await Swal.fire({
            title: 'Cash Payment',
            input: 'number',
            inputLabel: 'Total: Rp ' + total.toLocaleString('id-ID'),
            inputPlaceholder: 'Amount received...',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            preConfirm: (val) => {
                if (!val || parseInt(val) < total) Swal.showValidationMessage('Insufficient amount!');
                return val;
            }
        });
        if (bayar) {
            document.getElementById('inputBayar').value = bayar;
            document.getElementById('inputKembalian').value = bayar - total;
            submitForm();
        }
    } else {
        Swal.fire({
            title: 'Confirm Payment',
            text: `Process via ${currentMethod}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb'
        }).then((res) => {
            if (res.isConfirmed) {
                document.getElementById('inputBayar').value = total;
                document.getElementById('inputKembalian').value = 0;
                submitForm();
            }
        });
    }
}

function submitForm() {
    let form = document.getElementById('formCheckout');
    cart.forEach((i, index) => {
        form.insertAdjacentHTML('beforeend', `
            <input type="hidden" name="items[${index}][id_menu]" value="${i.id}">
            <input type="hidden" name="items[${index}][qty]" value="${i.qty}">
            <input type="hidden" name="items[${index}][subtotal]" value="${i.harga * i.qty}">
        `);
    });
    Swal.fire({ title: 'Processing...', didOpen: () => Swal.showLoading() });
    form.submit();
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'F8' && !document.getElementById('btnCheckout').disabled) {
        e.preventDefault();
        prosesCheckout();
    }
});
</script>
