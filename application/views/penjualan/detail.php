<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex gap-2 justify-center mb-4 no-print">
    <a href="<?= base_url('penjualan/buat') ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-200 text-gray-800 rounded-md">
        <i class="fas fa-arrow-left"></i> Transaksi Baru (POS)
    </a>
    <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-md">
        <i class="fas fa-file-invoice"></i> Cetak Invoice A4
    </button>
    <button onclick="cetakStrukThermal()" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md">
        <i class="fas fa-print"></i> Cetak Struk Thermal (58mm)
    </button>
</div>

<div class="ndc-card mx-auto" style="max-width: 720px;" id="invoiceArea">
    <div class="card-body p-4 p-md-5">
        <!-- Header Invoice -->
        <div class="flex items-start justify-between mb-4 pb-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <img src="<?= base_url('assets/images/ICON.png') ?>" alt="Logo" style="width:112px;height:auto;object-fit:contain;">
                <div>
                <p class="text-muted mb-0 small">Coffee, Drinks, and Bakery Eatery</p>
                </div>
            </div>
            <div class="text-end">
                <div class="text-muted small text-uppercase fw-semibold mb-1">INVOICE PENJUALAN</div>
                <div class="fw-bold fs-5 text-primary">#<?= $penjualan->invoice ?></div>
            </div>
        </div>

        <!-- Info Pesanan -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <h6 class="text-muted text-uppercase fw-bold small mb-2" style="font-size: 10px; letter-spacing: 0.5px;">Informasi Layanan</h6>
                <p class="mb-1 small text-dark">Layanan: 
                    <?php if($penjualan->{'tipe pesanan'} == 'Dine-in'): ?>
                        <strong class="text-success"><i class="fas fa-utensils me-1"></i>Dine-in</strong>
                    <?php else: ?>
                        <strong class="text-warning"><i class="fas fa-shopping-bag me-1"></i>Take-away</strong>
                    <?php endif; ?>
                </p>
                <?php if($penjualan->nomor_meja): ?>
                    <p class="mb-1 small text-dark">Nomor Meja: <strong class="text-primary">Meja <?= htmlspecialchars($penjualan->nomor_meja) ?></strong></p>
                <?php endif; ?>
                <p class="mb-0 small text-dark">Waktu: <strong class="text-muted"><?= date('d M Y, H:i', strtotime($penjualan->tanggal)) ?></strong></p>
            </div>
            <div class="col-6 text-end">
                <h6 class="text-muted text-uppercase fw-bold small mb-2" style="font-size: 10px; letter-spacing: 0.5px;">Kasir / Terminal</h6>
                <p class="mb-1 small fw-semibold text-dark"><?= htmlspecialchars($this->session->userdata('nama')) ?></p>
                <p class="mb-1 small text-muted">Terminal ID: POS-01</p>
                <p class="mb-0 small text-dark">Metode: <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 border border-indigo-200"><?= htmlspecialchars($penjualan->mode_pembayaran) ?></span>
                <?php if(!empty($penjualan->nomor_referensi)): ?>
                    <span class="d-block small text-muted mt-1 font-mono">Ref: <?= htmlspecialchars($penjualan->nomor_referensi) ?></span>
                <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Tabel Item -->
        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Deskripsi Menu</th>
                        <th class="text-center" style="width: 80px;">Qty</th>
                        <th class="text-end" style="width: 150px;">Harga Satuan</th>
                        <th class="text-end pe-3" style="width: 150px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal_items = 0;
                    foreach($detail as $d): 
                        $subtotal_items += ($d->harga * $d->qty);
                    ?>
                    <tr>
                        <td class="ps-3 fw-bold text-dark"><?= htmlspecialchars($d->nama_menu) ?></td>
                        <td class="text-center fw-semibold"><?= $d->qty ?>x</td>
                        <td class="text-end text-muted">Rp <?= number_format($d->harga, 0, ',', '.') ?></td>
                        <td class="text-end pe-3 fw-bold text-dark">Rp <?= number_format($d->subtotal, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Ringkasan Total -->
        <?php 
        $diskon = isset($penjualan->diskon) ? (int)$penjualan->diskon : 0;
        $ppn = round(max(0, $subtotal_items - $diskon) * 0.11);
        ?>
        <div class="ms-auto" style="max-width: 340px;">
            <table class="table table-sm table-borderless small">
                <tr>
                    <td class="text-muted">Subtotal Produk</td>
                    <td class="text-end fw-semibold text-dark">Rp <?= number_format($subtotal_items, 0, ',', '.') ?></td>
                </tr>
                <?php if($diskon > 0): ?>
                <tr>
                    <td class="text-danger fw-semibold"><i class="fas fa-tag me-1"></i>Diskon &amp; Promo</td>
                    <td class="text-end fw-bold text-danger">- Rp <?= number_format($diskon, 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-muted">PPN (11%)</td>
                    <td class="text-end text-muted">Rp <?= number_format($ppn, 0, ',', '.') ?></td>
                </tr>
                <tr class="border-top border-light-subtle">
                    <td class="fw-bold text-dark py-2">TOTAL TRANSAKSI</td>
                    <td class="text-end fw-bold text-primary py-2 h5 mb-0">Rp <?= number_format($penjualan->total_harga, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Jumlah Dibayar (<?= htmlspecialchars($penjualan->mode_pembayaran) ?>)</td>
                    <td class="text-end fw-semibold text-dark">Rp <?= number_format($penjualan->bayar, 0, ',', '.') ?></td>
                </tr>
                <tr class="border-top border-light-subtle">
                    <td class="text-muted py-2">Uang Kembalian</td>
                    <td class="text-end fw-bold text-success py-2">Rp <?= number_format($penjualan->kembalian, 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <hr class="border-light-subtle my-4">
        <p class="text-center text-muted small mb-0"><i class="fas fa-check-circle text-success me-1"></i> Terima kasih atas kunjungan Anda. Simpan struk ini sebagai bukti pembayaran digital Anda.</p>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #invoiceArea, #invoiceArea * { visibility: visible; }
    #invoiceArea { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
    .no-print { display: none !important; }
}
</style>

<script>
function formatRp(angka) {
    return (angka || 0).toLocaleString('id-ID');
}

function cetakStrukThermal() {
    const invoice  = '<?= $penjualan->invoice ?>';
    const total    = '<?= $penjualan->total_harga ?>';
    const diskon   = '<?= $penjualan->diskon ?? 0 ?>';
    const mode     = '<?= $penjualan->mode_pembayaran ?>';
    const ref      = '<?= addslashes($penjualan->nomor_referensi ?? "") ?>';
    const bayar    = '<?= $penjualan->bayar ?>';
    const kembali  = '<?= $penjualan->kembalian ?>';
    const type     = '<?= $penjualan->{'tipe pesanan'} ?>';
    const meja     = '<?= $penjualan->nomor_meja ? "Meja " . $penjualan->nomor_meja : "" ?>';
    const date     = '<?= date('d/m/Y H:i', strtotime($penjualan->tanggal)) ?>';
    const cashier  = '<?= $this->session->userdata('nama') ?>';
    const logoUrl  = '<?= base_url('assets/images/ICON.png') ?>';
    const items = [
        <?php foreach($detail as $d): ?>
        { name: '<?= addslashes($d->nama_menu) ?>', qty: <?= $d->qty ?>, price: <?= $d->harga ?> },
        <?php endforeach; ?>
    ];
    const w = window.open('', '_blank', 'width=300,height=600');
    w.document.write('<html><head><title>Struk Nol Derajat</title>');
    w.document.write('<style>body{font-family:"Courier New",monospace;width:58mm;padding:4mm;margin:0;font-size:11px;line-height:1.3;}.receipt-logo{display:block;width:34mm;height:auto;object-fit:contain;margin:0 auto 2mm;}');
    w.document.write('.c{text-align:center;}.r{text-align:right;}.b{font-weight:bold;}.d{border-top:1px dashed #000;margin:5px 0;}.row{display:flex;justify-content:space-between;}</style>');
    w.document.write('</head><body>');
    w.document.write('<div class="c"><img class="receipt-logo" src="' + logoUrl + '" alt="Logo"></div>');
    w.document.write('<div class="d"></div>');
    w.document.write('Inv : #' + invoice + '<br>Tgl : ' + date + '<br>Ksr : ' + cashier + '<br>Tipe: ' + type + (meja ? ' (' + meja + ')' : '') + '<br>');
    if (ref) w.document.write('Ref : ' + ref + '<br>');
    w.document.write('<div class="d"></div>');
    items.forEach(i => {
        const sub = i.price * i.qty;
        w.document.write('<div>' + i.name + '</div>');
        w.document.write('<div class="row"><span>  ' + i.qty + ' x ' + formatRp(i.price) + '</span><span>' + formatRp(sub) + '</span></div>');
    });
    w.document.write('<div class="d"></div>');
    if (parseInt(diskon) > 0) {
        w.document.write('<div class="row"><span>Diskon & Promo</span><span>- ' + formatRp(parseInt(diskon)) + '</span></div>');
    }
    w.document.write('<div class="row"><span>PPN (11%)</span><span>' + formatRp(<?= $ppn ?>) + '</span></div>');
    w.document.write('<div class="row b"><span>TOTAL</span><span>' + formatRp(parseInt(total)) + '</span></div>');
    w.document.write('<div class="row"><span>Bayar (' + mode + ')</span><span>' + formatRp(parseInt(bayar)) + '</span></div>');
    w.document.write('<div class="row"><span>Kembali</span><span>' + formatRp(parseInt(kembali)) + '</span></div>');
    w.document.write('<div class="d"></div>');
    w.document.write('<div class="c b">TERIMA KASIH</div>');
    w.document.write('<div class="c">Silakan Datang Kembali</div>');
    w.document.write('</body></html>');
    w.document.close();
    setTimeout(() => { w.print(); w.close(); }, 500);
}
</script>
