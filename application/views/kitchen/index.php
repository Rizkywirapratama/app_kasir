<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

:root {
    --bg-kds: #0f172a;
    --card-bg: #1e293b;
    --accent: #6366f1;
    --text-main: #f8fafc;
    --text-muted: #94a3b8;
}

body {
    background-color: var(--bg-kds);
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.kds-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 24px;
}

.order-card {
    background: var(--card-bg);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    padding: 20px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.4s ease-out;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.order-id { color: var(--accent); font-weight: 800; font-size: 1.1rem; }
.order-time { color: var(--text-muted); font-size: 0.8rem; font-weight: 600; }
.order-type { background: rgba(99, 102, 241, 0.1); color: var(--accent); padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }

.order-items { flex: 1; margin-bottom: 20px; }
.item-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.item-name { color: var(--text-main); font-weight: 600; font-size: 0.95rem; }
.item-qty { background: #334155; color: #fff; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: 800; font-size: 0.85rem; }

.btn-ready {
    width: 100%;
    padding: 12px;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-weight: 800;
    font-size: 0.9rem;
    cursor: pointer;
    transition: 0.3s;
}
.btn-ready:hover { filter: brightness(1.2); transform: translateY(-2px); }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="p-4 flex items-center justify-between">
    <h2 class="text-white font-extrabold m-0"><i class="fas fa-fire text-danger me-2"></i> KITCHEN DISPLAY</h2>
    <div id="connectionStatus" class="inline-flex items-center rounded-full bg-red-600 px-3 py-2 font-bold text-white">NODE.JS OFFLINE</div>
</div>

<div class="kds-container" id="orderContainer">
    <!-- Orders will be injected here -->
    <?php foreach($orders as $order): ?>
    <div class="order-card" id="order-<?= $order->invoice ?>">
        <div class="order-header">
            <div>
                <div class="order-id">#<?= substr($order->invoice, -4) ?></div>
                <div class="order-time"><?= date('H:i', strtotime($order->tanggal)) ?></div>
            </div>
            <div class="text-end">
                <div class="order-type"><?= $order->{'tipe pesanan'} ?></div>
                <div class="text-muted small fw-bold mt-1"><?= $order->nomor_meja ? 'Table '.$order->nomor_meja : 'Take-away' ?></div>
            </div>
        </div>
        <div class="order-items">
            <?php foreach($order->items as $item): ?>
            <div class="item-row">
                <div class="item-name"><?= $item->nama_menu ?></div>
                <div class="item-qty"><?= $item->qty ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="btn-ready" onclick="completeOrder('<?= $order->invoice ?>', this)">MARK AS READY</button>
    </div>
    <?php endforeach; ?>
</div>

<!-- Socket.io for Real-time -->
<script src="http://localhost:3000/socket.io/socket.io.js" onerror="console.log('Socket.io not found')"></script>
<script>
    const socket = typeof io !== 'undefined' ? io('http://localhost:3000') : null;
    
    if (socket) {
        document.getElementById('connectionStatus').innerText = 'NODE.JS LIVE';
        document.getElementById('connectionStatus').classList.replace('bg-danger', 'bg-success');

        socket.on('new_order', (data) => {
            console.log('New Order Received:', data);
            addOrderToDisplay(data);
        });
    }

    // Fallback Polling (Every 10 seconds) if Node.js is offline
    if (!socket) {
        setInterval(fetchUpdates, 10000);
    }

    async function fetchUpdates() {
        const res = await fetch('<?= base_url('kitchen/get_updates') ?>');
        const data = await res.json();
        renderOrders(data);
    }

    function renderOrders(orders) {
        const container = document.getElementById('orderContainer');
        // Logic to update existing orders or add new ones without clearing everything
    }

    function completeOrder(invoice, btn) {
        const card = document.getElementById('order-' + invoice);
        card.style.opacity = '0.5';
        card.style.pointerEvents = 'none';
        btn.innerText = 'COMPLETED';
        setTimeout(() => card.remove(), 1000);
    }
</script>
