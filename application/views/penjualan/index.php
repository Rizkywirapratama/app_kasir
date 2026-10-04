<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex items-center justify-between mb-3">
    <h4 class="mb-0 font-bold">Riwayat Transaksi Penjualan</h4>
    <a href="<?= base_url('penjualan/buat') ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md">
        <i class="fas fa-cart-plus"></i> Transaksi Baru (POS)
    </a>
</div>

<div class="ndc-card">
    <div class="p-3 border-b">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="w-full md:w-1/2">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-search"></i></span>
                    <input type="text" id="salesSearch" class="pl-10 pr-3 py-2 w-full rounded-md bg-gray-50 border" placeholder="Cari nomor invoice atau meja..." onkeyup="searchSalesTable()">
                </div>
            </div>
            <div>
                <span class="text-gray-500 text-sm">Total: <strong><?= count($penjualan) ?></strong> transaksi tercatat</span>
            </div>
        </div>
    </div>
    <div class="p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="salesTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Waktu Transaksi</th>
                        <th class="px-4 py-2 text-left">No. Invoice</th>
                        <th class="px-4 py-2 text-left">Tipe Pesanan</th>
                        <th class="px-4 py-2 text-left">Nomor Meja</th>
                        <th class="px-4 py-2 text-left">Metode Bayar</th>
                        <th class="px-4 py-2 text-left">Total Transaksi</th>
                        <th class="px-4 py-2 text-right" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if(empty($penjualan)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400">
                            <i class="fas fa-history fa-3x d-block mb-3 opacity-25"></i>
                            Belum ada data transaksi yang tersimpan.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($penjualan as $p): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900"><?= date('H:i', strtotime($p->tanggal)) ?></div>
                                <small class="text-gray-500"><?= date('d M Y', strtotime($p->tanggal)) ?></small>
                            </td>
                            <td class="px-4 py-3">
                                <code class="bg-indigo-50 px-2 py-1 rounded text-indigo-600 font-bold">#<?= htmlspecialchars($p->invoice) ?></code>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($p->{'tipe pesanan'} == 'Dine-in'): ?>
                                    <span class="inline-block px-3 py-1 rounded-full bg-green-50 text-green-600 border border-green-100"><i class="fas fa-utensils mr-1"></i>Dine-in</span>
                                <?php else: ?>
                                    <span class="inline-block px-3 py-1 rounded-full bg-yellow-50 text-yellow-600 border border-yellow-100"><i class="fas fa-shopping-bag mr-1"></i>Take-away</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-600">
                                <?= $p->nomor_meja ? 'Meja '.htmlspecialchars($p->nomor_meja) : '-' ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                $mode = $p->mode_pembayaran;
                                if ($mode === 'Cash') {
                                    $badge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    $icon  = 'fa-money-bill-wave';
                                } elseif ($mode === 'QRIS') {
                                    $badge = 'bg-violet-50 text-violet-700 border-violet-200';
                                    $icon  = 'fa-qrcode';
                                } elseif (stripos($mode, 'Transfer') !== false) {
                                    $badge = 'bg-sky-50 text-sky-700 border-sky-200';
                                    $icon  = 'fa-building-columns';
                                } else {
                                    $badge = 'bg-blue-50 text-blue-700 border-blue-200';
                                    $icon  = 'fa-credit-card';
                                }
                                ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold <?= $badge ?> border">
                                    <i class="fas <?= $icon ?> text-xs"></i> <?= htmlspecialchars($p->mode_pembayaran) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900">Rp <?= number_format($p->total_harga, 0, ',', '.') ?></div>
                                <?php if(!empty($p->diskon) && $p->diskon > 0): ?>
                                    <small class="inline-flex items-center gap-1 text-xs text-rose-600 font-semibold"><i class="fas fa-tag"></i> Hemat Rp <?= number_format($p->diskon, 0, ',', '.') ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= base_url('penjualan/detail/'.$p->id_penjualan) ?>" class="inline-flex items-center px-3 py-1 border rounded bg-white/5 text-indigo-600 font-semibold" title="Lihat Detail Invoice">
                                    <i class="fas fa-receipt mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function searchSalesTable() {
    let input = document.getElementById("salesSearch").value.toLowerCase();
    let rows = document.querySelectorAll("#salesTable tbody tr");
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}
</script>

