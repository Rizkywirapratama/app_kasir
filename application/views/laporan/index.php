<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Print Header (hanya tampil saat print) -->
<div class="hidden print:block" id="printHeader">
    <div class="text-center mb-4 pb-3 border-b-2">
        <h3 class="font-bold mb-1 text-lg">LAPORAN PENJUALAN</h3>
        <p class="mb-1 font-semibold uppercase">NOL DERAJAT COFFEE</p>
        <p class="mb-0">Periode: <?= date('d M Y', strtotime($tgl_mulai)) ?> s/d <?= date('d M Y', strtotime($tgl_akhir)) ?></p>
    </div>
</div>

<div class="flex items-center justify-between mb-3 no-print">
    <h4 class="mb-0 font-extrabold text-lg">Laporan Ringkasan Penjualan</h4>
    <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md hover:bg-gray-100">
        <i class="fas fa-print"></i>
        <span>Cetak Dokumen Laporan</span>
    </button>
</div>

<!-- Filter -->
<div class="ndc-card mb-4 no-print">
    <div class="p-4">
        <form action="<?= base_url('laporan') ?>" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-600">Dari Tanggal</label>
                <input type="date" name="tgl_mulai" class="w-full px-3 py-2 rounded-md border" value="<?= $tgl_mulai ?>">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Sampai Tanggal</label>
                <input type="date" name="tgl_akhir" class="w-full px-3 py-2 rounded-md border" value="<?= $tgl_akhir ?>">
            </div>
            <div>
                <button type="submit" class="w-full px-3 py-2 bg-indigo-600 text-white rounded-md inline-flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i> Filter Periode Penjualan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
    <div>
        <div class="flex items-center justify-between p-4 rounded-md bg-white/5">
            <div>
                <div class="text-xs font-semibold uppercase text-gray-400">Volume Transaksi</div>
                <h3 class="font-bold text-xl text-gray-900 mt-1"><?= number_format($ringkasan->total_trx) ?> Transaksi</h3>
            </div>
            <div class="w-10 h-10 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>
    <div>
        <div class="flex items-center justify-between p-4 rounded-md bg-white/5">
            <div>
                <div class="text-xs font-semibold uppercase text-gray-400">Total Pendapatan Bersih</div>
                <h3 class="font-bold text-xl text-gray-900 mt-1">Rp <?= number_format($ringkasan->total_pendapatan ?? 0, 0, ',', '.') ?></h3>
            </div>
            <div class="w-10 h-10 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="ndc-card">
    <div class="px-4 py-3 no-print">
        <span class="font-semibold text-gray-900 inline-flex items-center gap-2"><i class="fas fa-list-ul text-indigo-600"></i> Rincian Penjualan Terdata</span>
    </div>
    <div class="p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Waktu &amp; Tanggal</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">No. Invoice</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Tipe Pesanan</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Metode Bayar</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(empty($laporan)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-10 text-muted">
                            <i class="fas fa-folder-open fa-3x d-block mb-3 opacity-25"></i>
                            Tidak ada data transaksi pada periode ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($laporan as $l): ?>
                        <tr>
                            <td class="px-3 py-3 text-sm text-gray-500"><?= date('d M Y, H:i', strtotime($l->tanggal)) ?></td>
                            <td class="px-3 py-3"><span class="inline-block bg-gray-50 px-2 py-1 rounded text-indigo-600 font-semibold">#<?= htmlspecialchars($l->invoice) ?></span></td>
                            <td class="px-3 py-3">
                                        <?php if ($l->{'tipe pesanan'} == 'Dine-in'): ?>
                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700"><i class="fas fa-utensils"></i>Dine-in</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700"><i class="fas fa-shopping-bag"></i>Take-away</span>
                                        <?php endif; ?>
                                        <?php if($l->nomor_meja): ?>
                                            <span class="inline-block ml-2 px-3 py-1 rounded-full bg-gray-50 text-gray-700">Meja <?= htmlspecialchars($l->nomor_meja) ?></span>
                                        <?php endif; ?>
                            </td>
                            <td class="px-3 py-3">
                                <?php
                                $pm_colors = ['Cash'=>'text-emerald-700','QRIS'=>'text-indigo-700','Debit/Kredit'=>'text-gray-700'];
                                $pm_class  = $pm_colors[$l->mode_pembayaran] ?? 'text-gray-700';
                                ?>
                                <span class="inline-block px-3 py-1 rounded-full bg-gray-50 <?= $pm_class ?>"><?= htmlspecialchars($l->mode_pembayaran) ?></span>
                            </td>
                            <td class="px-3 py-3 text-right font-extrabold text-gray-900">Rp <?= number_format($l->total_harga, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    @page { margin: 2cm; size: A4; }
    body { background:#ffffff !important; font-size: 12pt; }
    .no-print { display: none !important; }
    #printHeader { display: block !important; }
    .card, .ndc-card { border: none !important; box-shadow: none !important; }
    .card-header { background: transparent !important; border-bottom: 2px solid #000 !important; }
    .table th { background: #f8f9fa !important; -webkit-print-color-adjust: exact; }
    .badge { border: 1px solid #ccc !important; background: transparent !important; color: #000 !important; }
}
</style>

