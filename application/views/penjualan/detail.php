<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$subtotal_items = 0;
$invoice_items = [];
foreach ($detail as $item) {
    $subtotal_items += ($item->harga * $item->qty);
    $invoice_items[] = [
        'name' => $item->nama_menu,
        'qty' => (int)$item->qty,
        'price' => (int)$item->harga,
        'subtotal' => (int)$item->subtotal
    ];
}
$diskon = isset($penjualan->diskon) ? (int)$penjualan->diskon : 0;
$ppn = round(max(0, $subtotal_items - $diskon) * 0.11);
$date_timestamp = strtotime($penjualan->tanggal);
$bulan_indonesia = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tanggal_invoice = date('d', $date_timestamp) . ' '
    . $bulan_indonesia[(int)date('n', $date_timestamp)] . ' '
    . date('Y, H:i', $date_timestamp);
$tipe_pesanan = $penjualan->{'tipe pesanan'};
?>

<div class="invoice-actions no-print">
    <a href="<?= base_url('penjualan/buat') ?>" class="invoice-action">
        <i class="fas fa-arrow-left" aria-hidden="true"></i> Transaksi baru
    </a>
    <button type="button" class="invoice-action invoice-action-primary" onclick="window.print()">
        <i class="fas fa-file-invoice" aria-hidden="true"></i> Cetak Invoice A4
    </button>
    <button type="button" class="invoice-action" onclick="cetakStrukThermal()">
        <i class="fas fa-print" aria-hidden="true"></i> Cetak struk thermal
    </button>
</div>

<article class="invoice-document" id="invoiceArea" aria-label="Invoice penjualan">
    <header class="invoice-document-heading">
        <h1>INVOICE</h1>
        <img src="<?= base_url('assets/images/ICON.png') ?>" alt="Nol Derajat Coffee" class="invoice-document-logo">
    </header>

    <section class="invoice-document-info">
        <div class="invoice-code">
            <span>Kode Invoice</span>
            <strong>#<?= htmlspecialchars($penjualan->invoice) ?></strong>
        </div>
        <dl class="invoice-party-details">
            <div>
                <dt>Nama Pelanggan</dt>
                <dd><?= htmlspecialchars($penjualan->nama_pelanggan ?: 'Umum') ?></dd>
            </div>
            <div>
                <dt>Layanan</dt>
                <dd><?= htmlspecialchars($tipe_pesanan) ?><?= !empty($penjualan->nomor_meja) ? ' - Meja ' . htmlspecialchars($penjualan->nomor_meja) : '' ?></dd>
            </div>
            <div>
                <dt>Kasir</dt>
                <dd><?= htmlspecialchars($this->session->userdata('nama')) ?></dd>
            </div>
            <div>
                <dt>Terminal</dt>
                <dd>POS-01</dd>
            </div>
            <div>
                <dt>Tanggal Pemesanan</dt>
                <dd><?= $tanggal_invoice ?> WIB</dd>
            </div>
            <div>
                <dt>Tanggal Pembayaran</dt>
                <dd><?= $tanggal_invoice ?> WIB</dd>
            </div>
            <div>
                <dt>Metode Pembayaran</dt>
                <dd><?= htmlspecialchars($penjualan->mode_pembayaran) ?></dd>
            </div>
            <?php if (!empty($penjualan->nomor_referensi)): ?>
            <div>
                <dt>No. Referensi</dt>
                <dd><?= htmlspecialchars($penjualan->nomor_referensi) ?></dd>
            </div>
            <?php endif; ?>
        </dl>
    </section>

    <section class="invoice-section">
        <h2>DATA PESANAN</h2>
        <div class="invoice-table-wrap">
            <table class="invoice-table invoice-order-table">
                <thead>
                    <tr>
                        <th class="invoice-number-cell">NO.</th>
                        <th>NAMA MENU</th>
                        <th class="invoice-qty-cell">QTY</th>
                        <th class="invoice-amount-cell">HARGA SATUAN</th>
                        <th class="invoice-amount-cell">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoice_items as $index => $item): ?>
                    <tr>
                        <td class="invoice-number-cell"><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td class="invoice-qty-cell"><?= $item['qty'] ?></td>
                        <td class="invoice-amount-cell">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td class="invoice-amount-cell">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="invoice-section invoice-payment-section">
        <h2>RINCIAN PEMBAYARAN</h2>
        <div class="invoice-table-wrap">
            <table class="invoice-table invoice-payment-table">
                <thead>
                    <tr>
                        <th>URAIAN</th>
                        <th class="invoice-amount-cell">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Subtotal Produk</td>
                        <td class="invoice-amount-cell">Rp <?= number_format($subtotal_items, 0, ',', '.') ?></td>
                    </tr>
                    <?php if ($diskon > 0): ?>
                    <tr>
                        <td>Diskon &amp; Promo</td>
                        <td class="invoice-amount-cell">- Rp <?= number_format($diskon, 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td>PPN (11%)</td>
                        <td class="invoice-amount-cell">Rp <?= number_format($ppn, 0, ',', '.') ?></td>
                    </tr>
                    <tr class="invoice-grand-total">
                        <td>TOTAL PEMBAYARAN</td>
                        <td class="invoice-amount-cell">Rp <?= number_format($penjualan->total_harga, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Jumlah Dibayar (<?= htmlspecialchars($penjualan->mode_pembayaran) ?>)</td>
                        <td class="invoice-amount-cell">Rp <?= number_format($penjualan->bayar, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Uang Kembalian</td>
                        <td class="invoice-amount-cell">Rp <?= number_format($penjualan->kembalian, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="invoice-document-footer">
        <div class="invoice-signature">
            <span>Hormat kami,</span>
            <span class="invoice-signature-space"></span>
            <span>Kasir: <?= htmlspecialchars($this->session->userdata('nama')) ?></span>
        </div>
        <p>Terima kasih atas kunjungan Anda.<br>Simpan invoice ini sebagai bukti pembayaran.</p>
    </footer>
</article>

<style>
.invoice-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.6rem;
    margin: 0 auto 1rem;
}
.invoice-action {
    display: inline-flex;
    min-height: 40px;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.55rem 0.85rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.5rem;
    background: #fff;
    color: #334155;
    font: inherit;
    font-size: 0.84rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}
.invoice-action:hover { background: #f1f5f9; color: #0f172a; }
.invoice-action-primary { border-color: #263b63; background: #263b63; color: #fff; }
.invoice-action-primary:hover { background: #1e2f50; color: #fff; }
.invoice-document {
    width: min(100%, 900px);
    margin: 0 auto 2rem;
    padding: clamp(1rem, 4vw, 2.5rem);
    border: 1px solid #1f2937;
    background: #fff;
    color: #111;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    line-height: 1.35;
}
.invoice-document-heading {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 0.4rem;
    color: #111;
    font-size: 13px;
}
.invoice-document-heading h1,
.invoice-document-heading strong {
    margin: 0;
    color: #111;
    font-size: 13px;
    font-weight: 700;
}
.invoice-document-logo {
    display: block;
    width: 152px;
    max-height: 46px;
    object-fit: contain;
    object-position: right center;
}
.invoice-document-info {
    display: grid;
    grid-template-columns: minmax(150px, 0.8fr) minmax(0, 1.8fr);
    gap: 1rem;
    padding: 0.6rem 0 0.9rem;
}
.invoice-code span { display: block; }
.invoice-code strong { display: block; font-weight: 700; }
.invoice-party-details { margin: 1.4rem 0 0; }
.invoice-party-details > div {
    display: grid;
    grid-template-columns: minmax(130px, 0.8fr) minmax(0, 1.5fr);
    gap: 0.5rem;
    margin: 0.12rem 0;
}
.invoice-party-details dt,
.invoice-party-details dd { margin: 0; font-weight: 400; }
.invoice-section { margin-top: 0.45rem; }
.invoice-section h2 {
    margin: 0 0 0.15rem;
    color: #111;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    font-weight: 400;
}
.invoice-table-wrap { width: 100%; overflow-x: auto; }
.invoice-table {
    width: 100%;
    border-collapse: collapse;
    color: #111;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
}
.invoice-table th,
.invoice-table td {
    padding: 0.3rem 0.4rem;
    border: 1px solid #333;
    color: #111;
    font-weight: 400;
    text-align: left;
    vertical-align: top;
}
.invoice-table th { font-weight: 700; }
.invoice-table .invoice-number-cell,
.invoice-table .invoice-qty-cell { text-align: center; white-space: nowrap; }
.invoice-table .invoice-amount-cell { text-align: right; white-space: nowrap; }
.invoice-order-table th:first-child,
.invoice-order-table td:first-child { width: 42px; }
.invoice-order-table th:nth-child(3),
.invoice-order-table td:nth-child(3) { width: 60px; }
.invoice-grand-total td { font-weight: 700; }
.invoice-payment-section { margin-top: 0.75rem; }
.invoice-document-footer {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.7rem 0.3rem 0;
}
.invoice-document-footer > p {
    margin: 0;
    color: #333;
    font-size: 10px;
    text-align: right;
}
.invoice-signature { display: flex; flex-direction: column; gap: 0.1rem; }
.invoice-signature-space { height: 2.5rem; }
.invoice-signature strong { font-size: 10px; }
.invoice-signature-logo { display: block; width: 96px; max-height: 30px; object-fit: contain; object-position: left center; }
@media (max-width: 640px) {
    .invoice-document { padding: 0.85rem; font-size: 10px; }
    .invoice-document-heading,
    .invoice-document-heading h1,
    .invoice-document-heading strong { font-size: 11px; }
    .invoice-document-logo { width: 124px; max-height: 38px; }
    .invoice-document-info { grid-template-columns: minmax(88px, 0.7fr) minmax(0, 1.8fr); gap: 0.5rem; }
    .invoice-party-details { margin-top: 1rem; }
    .invoice-party-details > div { grid-template-columns: minmax(88px, 0.8fr) minmax(0, 1.4fr); gap: 0.25rem; }
    .invoice-table { min-width: 560px; }
    .invoice-payment-table { min-width: 340px; }
    .invoice-document-footer { align-items: flex-start; flex-direction: column; }
    .invoice-document-footer > p { text-align: left; }
}
@page { size: A4; margin: 12mm; }
@media print {
    html, body { width: auto !important; min-height: 0 !important; margin: 0 !important; background: #fff !important; }
    body * { visibility: hidden !important; }
    #invoiceArea, #invoiceArea * { visibility: visible !important; }
    .layout-overlay, #layout-menu, #layout-navbar, .content-footer, .no-print { display: none !important; }
    .layout-wrapper, .layout-container, .layout-page, .content-wrapper, .container-xxl {
        display: block !important;
        width: auto !important;
        min-height: 0 !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        background: #fff !important;
    }
    #invoiceArea {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 8mm !important;
        border: 1px solid #111 !important;
        box-shadow: none !important;
        break-inside: auto;
    }
    .invoice-table-wrap { overflow: visible !important; }
    .invoice-section, .invoice-document-info, .invoice-document-footer { break-inside: avoid; }
    .invoice-table thead { display: table-header-group; }
    .invoice-table tr { break-inside: avoid; }
}
.content-wrapper.invoice-document-scroll {
    min-height: auto;
    overflow: visible !important;
}
.layout-page.invoice-document-scroll { min-height: 100vh; }
</style>

<script>
document.querySelector('.content-wrapper')?.classList.add('invoice-document-scroll');
document.querySelector('.layout-page')?.classList.add('invoice-document-scroll');

function formatRp(angka) {
    return (angka || 0).toLocaleString('id-ID');
}

function escapeReceiptText(value) {
    return String(value).replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[character]));
}

function cetakStrukThermal() {
    const receipt = <?= json_encode([
        'invoice' => (string)$penjualan->invoice,
        'total' => (int)$penjualan->total_harga,
        'diskon' => $diskon,
        'mode' => (string)$penjualan->mode_pembayaran,
        'referensi' => (string)($penjualan->nomor_referensi ?? ''),
        'bayar' => (int)$penjualan->bayar,
        'kembalian' => (int)$penjualan->kembalian,
        'tipe' => (string)$tipe_pesanan,
        'meja' => !empty($penjualan->nomor_meja) ? 'Meja ' . $penjualan->nomor_meja : '',
        'tanggal' => date('d/m/Y H:i', $date_timestamp),
        'kasir' => (string)$this->session->userdata('nama'),
        'logo' => base_url('assets/images/ICON.png'),
        'items' => $invoice_items,
        'ppn' => (int)$ppn
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    const w = window.open('', '_blank', 'width=300,height=600');
    if (!w) {
        window.showToast?.('error', 'Izinkan pop-up browser untuk mencetak struk.');
        return;
    }

    w.document.write('<html><head><title>Struk Nol Derajat</title>');
    w.document.write('<style>body{font-family:"Courier New",monospace;width:58mm;padding:4mm;margin:0;font-size:11px;line-height:1.3;}.receipt-logo{display:block;width:34mm;height:auto;object-fit:contain;margin:0 auto 2mm;}');
    w.document.write('.c{text-align:center;}.b{font-weight:bold;}.d{border-top:1px dashed #000;margin:5px 0;}.row{display:flex;justify-content:space-between;gap:4px;}</style>');
    w.document.write('</head><body>');
    w.document.write('<div class="c"><img class="receipt-logo" src="' + escapeReceiptText(receipt.logo) + '" alt="Logo"></div>');
    w.document.write('<div class="d"></div>');
    w.document.write('Inv : #' + escapeReceiptText(receipt.invoice) + '<br>Tgl : ' + escapeReceiptText(receipt.tanggal) + '<br>Ksr : ' + escapeReceiptText(receipt.kasir) + '<br>Tipe: ' + escapeReceiptText(receipt.tipe) + (receipt.meja ? ' (' + escapeReceiptText(receipt.meja) + ')' : '') + '<br>');
    if (receipt.referensi) w.document.write('Ref : ' + escapeReceiptText(receipt.referensi) + '<br>');
    w.document.write('<div class="d"></div>');
    receipt.items.forEach(item => {
        w.document.write('<div>' + escapeReceiptText(item.name) + '</div>');
        w.document.write('<div class="row"><span>' + item.qty + ' x ' + formatRp(item.price) + '</span><span>' + formatRp(item.subtotal) + '</span></div>');
    });
    w.document.write('<div class="d"></div>');
    if (receipt.diskon > 0) {
        w.document.write('<div class="row"><span>Diskon & Promo</span><span>- ' + formatRp(receipt.diskon) + '</span></div>');
    }
    w.document.write('<div class="row"><span>PPN (11%)</span><span>' + formatRp(receipt.ppn) + '</span></div>');
    w.document.write('<div class="row b"><span>TOTAL</span><span>' + formatRp(receipt.total) + '</span></div>');
    w.document.write('<div class="row"><span>Bayar (' + escapeReceiptText(receipt.mode) + ')</span><span>' + formatRp(receipt.bayar) + '</span></div>');
    w.document.write('<div class="row"><span>Kembali</span><span>' + formatRp(receipt.kembalian) + '</span></div>');
    w.document.write('<div class="d"></div>');
    w.document.write('<div class="c b">TERIMA KASIH</div>');
    w.document.write('<div class="c">Silakan Datang Kembali</div>');
    w.document.write('</body></html>');
    w.document.close();
    setTimeout(() => { w.print(); w.close(); }, 500);
}
</script>
