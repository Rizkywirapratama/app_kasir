<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Low Stock Warning -->
<?php if (!empty($low_stock)): ?>
<div class="animate-in mb-4">
    <div class="flex items-center justify-between bg-yellow-600/10 border border-yellow-600/20 rounded-2xl p-4 flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-yellow-600/15 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-triangle-exclamation text-yellow-500 text-lg"></i>
            </div>
            <div>
                <div class="font-semibold text-slate-100 text-sm">Peringatan Stok Rendah!</div>
                <div class="text-sm text-slate-400">Ada <strong><?= count($low_stock) ?></strong> produk dengan stok hampir habis (&lt; 5). Segera lakukan restock.</div>
            </div>
        </div>
        <button class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-md" type="button" data-bs-toggle="collapse" data-bs-target="#lowStockDetails">
            Detail <i class="fas fa-chevron-down ml-2" style="font-size: 0.7rem;"></i>
        </button>
    </div>
    <div class="collapse mt-2" id="lowStockDetails">
        <div class="ndc-card">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50"><tr>
                            <th class="px-4 py-2 text-left">Nama Produk</th>
                            <th class="px-4 py-2 text-left">Kategori</th>
                            <th class="px-4 py-2 text-center">Sisa Stok</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr></thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php foreach($low_stock as $ls): ?>
                            <tr>
                                <td class="px-4 font-bold text-slate-100"><?= htmlspecialchars($ls->nama_menu) ?></td>
                                <td class="px-4"><?= htmlspecialchars($ls->nama_kategori ?? '-') ?></td>
                                <td class="px-4 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-sm" style="background: rgba(239,68,68,0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); font-weight:700;"><?= $ls->stok ?> item</span>
                                </td>
                                <td class="px-4 text-right">
                                    <a href="<?= base_url('menu') ?>" class="inline-flex items-center px-3 py-1.5 border rounded-md text-indigo-600">Restock</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-4 animate-in">
    <!-- Total Produk -->
    <div>
        <div class="ndc-card h-full">
            <div class="card-body flex items-center justify-between gap-3">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total Produk</div>
                    <div class="text-2xl font-extrabold text-slate-100" id="cnt-menu">0</div>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: rgba(99,102,241,0.12);">
                    <i class="fas fa-mug-hot text-indigo-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Transaksi Hari Ini -->
    <div>
        <div class="ndc-card h-full">
            <div class="card-body flex items-center justify-between gap-3">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Transaksi Hari Ini</div>
                    <div class="text-2xl font-extrabold text-slate-100" id="cnt-jual">0</div>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: rgba(16,185,129,0.12);">
                    <i class="fas fa-receipt text-emerald-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Pendapatan -->
    <div>
        <div class="ndc-card h-full">
            <div class="card-body flex items-center justify-between gap-3">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Pendapatan Hari Ini</div>
                    <div class="text-lg font-extrabold text-slate-100 whitespace-nowrap" id="cnt-pendapatan">Rp 0</div>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: rgba(6,182,212,0.12);">
                    <i class="fas fa-wallet text-cyan-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Meja Tersedia -->
    <div>
        <div class="ndc-card h-full">
            <div class="card-body flex items-center justify-between gap-3">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Meja Tersedia</div>
                    <div class="text-2xl font-extrabold text-slate-100"><?= $meja_tersedia ?><span class="text-base text-slate-400 font-medium"> / <?= $total_meja ?></span></div>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: rgba(245,158,11,0.12);">
                    <i class="fas fa-chair text-amber-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Shift Monitor + Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4 animate-in" style="animation-delay: 0.06s;">
    <!-- Live Shift -->
    <div>
        <div class="ndc-card h-100">
            <div class="p-3 flex items-center justify-between">
                <span class="card-title">
                    <i class="fas fa-user-clock me-2" style="color: #6366f1;"></i>Live Shift Monitor
                </span>
                <?php if($shift_aktif): ?>
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold text-emerald-700" style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.2);">
                        <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite;"></span>
                        SHIFT AKTIF
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-slate-600" style="background: rgba(100,116,139,0.1); border: 1px solid rgba(100,116,139,0.15);">
                        SHIFT TUTUP
                    </span>
                <?php endif; ?>
            </div>
            <div class="p-4">
                <?php if($shift_aktif): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <div class="bg-white/2 border border-white/5 rounded-lg p-4">
                            <div style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; margin-bottom: 0.3rem;">Kasir Bertugas</div>
                            <div style="font-weight: 800; color:#f1f5f9; font-size: 0.9375rem;"><?= htmlspecialchars($shift_aktif->nama_kasir) ?></div>
                            <div style="font-size: 0.75rem; color: #6366f1; margin-top: 0.25rem; font-weight: 600;">
                                <i class="fas fa-clock me-1"></i>Mulai <?= date('H:i', strtotime($shift_aktif->waktu_buka)) ?> WIB
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4">
                            <div style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6366f1; margin-bottom: 0.3rem;">Saldo Penjualan</div>
                            <div class="fw-bold h6 mb-0" id="liveShiftTotal" style="color: #4f46e5; font-weight: 800;">Rp <?= number_format($shift_aktif->total_penjualan, 0, ',', '.') ?></div>
                            <input type="hidden" id="rawShiftTotal" value="<?= $shift_aktif->total_penjualan ?>">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="bg-white/2 border border-white/5 rounded-lg p-3 flex items-center gap-3">
                            <i class="fas fa-circle-info" style="color: #06b6d4;"></i>
                            <span style="font-size: 0.8375rem; color:#64748b;">Tercatat <strong id="liveShiftCount" class="text-dark"><?= $shift_aktif->jumlah_transaksi ?></strong> transaksi pada shift ini.</span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <div style="width: 64px; height: 64px; background:#eef2ff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <i class="fas fa-lock" style="font-size: 1.5rem; color: #cbd5e1;"></i>
                    </div>
                    <p style="font-weight: 700; color:#f1f5f9; margin-bottom: 0.375rem;">Shift Kasir Belum Dibuka</p>
                    <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 1.25rem;">Buka shift kasir terlebih dahulu untuk mulai mencatat transaksi.</p>
                    <a href="<?= base_url('shift/buka') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-md">
                        <i class="fas fa-door-open"></i> Buka Shift Sekarang
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div>
        <div class="ndc-card h-100">
            <div class="p-3 flex items-center justify-between">
                <span class="font-semibold"><i class="fas fa-bolt-lightning mr-2" style="color: #f59e0b;"></i>Aksi Cepat</span>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 gap-2">
                    <?php
                    $quick_actions = [
                        ['url' => 'penjualan/buat', 'icon' => 'fa-cash-register', 'label' => 'Kasir (POS)', 'color' => '#6366f1', 'bg' => 'rgba(99,102,241,0.08)', 'border' => 'rgba(99,102,241,0.15)'],
                        ['url' => 'menu',           'icon' => 'fa-mug-hot',       'label' => 'Data Menu',   'color' => '#10b981', 'bg' => 'rgba(16,185,129,0.08)',  'border' => 'rgba(16,185,129,0.15)'],
                        ['url' => 'meja',           'icon' => 'fa-chair',         'label' => 'Kelola Meja', 'color' => '#06b6d4', 'bg' => 'rgba(6,182,212,0.08)',   'border' => 'rgba(6,182,212,0.15)'],
                        ['url' => 'laporan',        'icon' => 'fa-chart-line',    'label' => 'Laporan',     'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.08)',  'border' => 'rgba(245,158,11,0.15)'],
                    ];
                    foreach ($quick_actions as $qa):
                    ?>
                    <div>
                        <a href="<?= base_url($qa['url']) ?>" class="flex items-center gap-3 p-3 rounded-lg" style="border:1px solid <?= $qa['border'] ?>; background: <?= $qa['bg'] ?>;">
                            <div class="w-10 h-10 rounded-md flex items-center justify-center" style="color: <?= $qa['color'] ?>;">
                                <i class="fas <?= $qa['icon'] ?>"></i>
                            </div>
                            <span class="font-semibold" style="color: <?= $qa['color'] ?>;"><?= $qa['label'] ?></span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart + Menu Terlaris -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4 animate-in" style="animation-delay: 0.1s;">
    <!-- Revenue Chart -->
    <div class="lg:col-span-2">
        <div class="ndc-card h-100">
            <div class="p-3 flex items-center justify-between">
                <span class="font-semibold"><i class="fas fa-chart-line mr-2 text-primary"></i>Tren Pendapatan (7 Hari Terakhir)</span>
                <a href="<?= base_url('laporan') ?>" class="text-sm font-semibold text-gray-600 hover:underline">Detail <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
            </div>
            <div class="p-4">
                <canvas id="chartPendapatan" style="height: 260px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Best Sellers -->
    <div>
        <div class="ndc-card h-100">
            <div class="p-3 flex items-center justify-between">
                <span class="font-semibold"><i class="fas fa-fire mr-2 text-danger"></i>Menu Terlaris</span>
                <a href="<?= base_url('menu') ?>" class="text-sm font-semibold text-indigo-600">Lihat Semua</a>
            </div>
            <div class="p-0">
                <?php if (empty($menu_terlaris)): ?>
                <div class="text-center py-5" style="color: #94a3b8;">
                    <i class="fas fa-box-open fa-2x mb-2" style="opacity: 0.3;"></i>
                    <p class="small mb-0">Belum ada data penjualan</p>
                </div>
                <?php else: ?>
                <div>
                    <?php foreach ($menu_terlaris as $i => $m): ?>
                    <div class="flex items-center justify-between px-4 py-3 border-b hover:bg-gray-50">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold" style="background: <?= ['rgba(99,102,241,0.12)','rgba(245,158,11,0.12)','rgba(16,185,129,0.12)','rgba(239,68,68,0.1)','rgba(6,182,212,0.1)'][$i] ?? 'rgba(99,102,241,0.12)' ?>; color: <?= ['#6366f1','#f59e0b','#10b981','#ef4444','#06b6d4'][$i] ?? '#6366f1' ?>;"><?= $i + 1 ?></span>
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($m->nama_menu) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($m->nama_kategori ?? '-') ?></div>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="font-extrabold text-gray-900">Rp <?= number_format($m->total_penjualan, 0, ',', '.') ?></div>
                            <div class="text-xs inline-block bg-black/5 text-gray-500 px-2 py-0.5 rounded-full mt-1 font-medium"><?= $m->total_qty ?> terjual</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="ndc-card animate-in" style="animation-delay: 0.14s;">
    <div class="card-header">
        <span class="card-title">
            <i class="fas fa-receipt me-2 text-success"></i>Transaksi Terakhir
        </span>
        <a href="<?= base_url('penjualan') ?>" class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-xs font-semibold text-slate-600" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
            Riwayat Lengkap <i class="fas fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
        </a>
    </div>
    <div class="p-0">
        <?php if (empty($penjualan_terakhir)): ?>
        <div class="text-center py-5 text-gray-500">
            <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.2;"></i>
            <p class="mb-0 font-semibold">Belum ada data transaksi hari ini</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="recentTable">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-2 text-left">No. Invoice</th>
                    <th class="px-4 py-2 text-left">Tipe Pesanan</th>
                    <th class="px-4 py-2 text-left">Nomor Meja</th>
                    <th class="px-4 py-2 text-left">Metode Bayar</th>
                    <th class="px-4 py-2 text-left">Total</th>
                    <th class="px-4 py-2 text-left">Waktu</th>
                </tr></thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php foreach ($penjualan_terakhir as $p): ?>
                    <tr>
                        <td class="px-4 py-3">
                            <code class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-semibold text-sm">#<?= htmlspecialchars($p->invoice) ?></code>
                        </td>
                        <td>
                            <?php if ($p->{'tipe pesanan'} == 'Dine-in'): ?>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-emerald-700" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">
                                    <i class="fas fa-utensils me-1"></i>Dine-in
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-amber-700" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
                                    <i class="fas fa-shopping-bag me-1"></i>Take-away
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 600; color:#64748b;"><?= $p->nomor_meja ? 'Meja ' . htmlspecialchars($p->nomor_meja) : '—' ?></td>
                        <td>
                            <?php
                            $pm_map = [
                                'Cash'         => ['bg' => 'rgba(16,185,129,0.1)',   'color' => '#059669', 'border' => 'rgba(16,185,129,0.2)'],
                                'QRIS'         => ['bg' => 'rgba(99,102,241,0.1)',   'color' => '#4f46e5', 'border' => 'rgba(99,102,241,0.2)'],
                                'Debit/Kredit' => ['bg' => 'rgba(100,116,139,0.1)',  'color' => '#475569', 'border' => 'rgba(100,116,139,0.2)'],
                            ];
                            $pm = $pm_map[$p->mode_pembayaran] ?? $pm_map['Debit/Kredit'];
                            ?>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold" style="background: <?= $pm['bg'] ?>; color: <?= $pm['color'] ?>; border: 1px solid <?= $pm['border'] ?>;">
                                <?= htmlspecialchars($p->mode_pembayaran) ?>
                            </span>
                        </td>
                        <td style="font-weight: 800; color:#f1f5f9; font-family: 'SF Mono', monospace; font-size: 0.875rem;">
                            Rp <?= number_format($p->total_harga, 0, ',', '.') ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= date('d M Y, H:i', strtotime($p->tanggal)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js + Socket.io -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="http://localhost:3000/socket.io/socket.io.js" onerror="console.log('Node.js server not running — realtime disabled.')"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const socket = typeof io !== 'undefined' ? io('http://localhost:3000') : null;

    if (socket) {
        socket.on('new_order', (data) => {
            prependTransaction(data);
            bumpStats(data);
            if (typeof showToast === 'function') {
                showToast('success', '🧾 Transaksi Baru: #' + data.invoice);
            }
        });
    }

    /* ── Animate Counters ── */
    function animateCount(id, target, isRp) {
        const el = document.getElementById(id);
        if (!el) return;
        const dur = 1100, start = performance.now();
        (function step(now) {
            const p    = Math.min((now - start) / dur, 1);
            const ease = p < 0.5 ? 2*p*p : -1 + (4 - 2*p)*p;
            const val  = Math.round(ease * target);
            el.textContent = isRp ? 'Rp ' + val.toLocaleString('id-ID') : val;
            if (p < 1) requestAnimationFrame(step);
        })(start);
    }

    animateCount('cnt-menu',       <?= (int)$total_menu ?>,         false);
    animateCount('cnt-jual',       <?= (int)$total_penjualan ?>,    false);
    animateCount('cnt-pendapatan', <?= (float)$total_pendapatan ?>, true);

    /* ── Revenue Chart ── */
    const chartEl = document.getElementById('chartPendapatan');
    if (chartEl) {
        const ctx    = chartEl.getContext('2d');
        const labels = <?= json_encode(array_column($chart_mingguan, 'tanggal')) ?>;
        const values = <?= json_encode(array_column($chart_mingguan, 'total')) ?>;

        const grad = ctx.createLinearGradient(0, 0, 0, 260);
        grad.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        grad.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: values,
                    borderColor: '#6366f1',
                    borderWidth: 2.5,
                    backgroundColor: grad,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2.5,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#94a3b8',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: c => ' Rp ' + c.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { weight: '600', size: 11 } },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#94a3b8',
                            font: { weight: '600', size: 11 },
                            callback: v => v === 0 ? '0' : 'Rp ' + (v/1000) + 'k'
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.8)', borderDash: [4, 4] },
                        border: { display: false }
                    }
                }
            }
        });
    }

    /* ── Live stat bumps ── */
    function bumpStats(data) {
        const rawEl    = document.getElementById('rawShiftTotal');
        const totalEl  = document.getElementById('liveShiftTotal');
        const countEl  = document.getElementById('liveShiftCount');
        const pendEl   = document.getElementById('cnt-pendapatan');

        if (rawEl && totalEl && data.total) {
            const newTotal = (parseInt(rawEl.value) || 0) + parseInt(data.total);
            rawEl.value   = newTotal;
            totalEl.textContent = 'Rp ' + newTotal.toLocaleString('id-ID');
        }
        if (countEl) countEl.textContent = (parseInt(countEl.textContent) || 0) + 1;
        if (pendEl) {
            const cur = parseInt(pendEl.textContent.replace(/\D/g,'')) || 0;
            pendEl.textContent = 'Rp ' + (cur + parseInt(data.total)).toLocaleString('id-ID');
        }
    }

    function prependTransaction(data) {
        const tbody = document.querySelector('#recentTable tbody');
        if (!tbody) return;
        const row = document.createElement('tr');
        row.style.animation = 'fadeInUp 0.3s ease both';
        row.innerHTML = `
            <td class="px-4 py-3"><code class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-semibold text-sm">#${data.invoice}</code></td>
            <td class="px-4 py-3"><span class="inline-block px-3 py-1 rounded-full text-sm" style="background:rgba(16,185,129,.1);color:#059669;border:1px solid rgba(16,185,129,.2);">${data.tipe_pesanan || 'Dine-in'}</span></td>
            <td class="px-4 py-3" style="font-weight:600;color:#64748b;">${data.nomor_meja ? 'Meja '+data.nomor_meja : '—'}</td>
            <td class="px-4 py-3"><span class="inline-block px-3 py-1 rounded-full text-sm" style="background:rgba(99,102,241,.1);color:#4f46e5;border:1px solid rgba(99,102,241,.2);">${data.mode_pembayaran || 'Cash'}</span></td>
            <td class="px-4 py-3" style="font-weight:800;color:#f1f5f9;">Rp ${parseInt(data.total||0).toLocaleString('id-ID')}</td>
            <td class="px-4 py-3 text-sm text-gray-500">Baru saja</td>
        `;
        tbody.insertAdjacentElement('afterbegin', row);
    }
});
</script>

<style>
.quick-action-btn {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:.6rem; padding:1.25rem .75rem; border-radius:.875rem; border:1.5px solid;
    text-decoration:none; transition:all .2s cubic-bezier(.34,1.56,.64,1);
}
.quick-action-btn:hover { transform:translateY(-3px); box-shadow:0 6px 20px rgba(0,0,0,.4); filter:brightness(1.08); }
.quick-action-icon { font-size:1.5rem; transition:transform .2s; }
.quick-action-btn:hover .quick-action-icon { transform:scale(1.1); }
.quick-action-label { font-size:.8125rem; font-weight:700; letter-spacing:.01em; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:.5;transform:scale(.75);} }
</style>
