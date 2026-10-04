<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$saldo_awal         = $shift->saldo_awal;
$total_cash         = $rekap->total_cash  ?? 0;
$total_qris         = $rekap->total_qris  ?? 0;
$total_debit        = $rekap->total_debit ?? 0;
$total_txn          = $rekap->total_transaksi ?? 0;
$seharusnya         = $saldo_awal + $total_cash;
$total_non_tunai    = $total_qris + $total_debit;
$grand_total        = $total_cash + $total_non_tunai;

// Duration
$buka_time  = new DateTime($shift->waktu_buka);
$now_time   = new DateTime();
$duration   = $buka_time->diff($now_time);
$dur_str    = '';
if ($duration->h > 0) $dur_str = $duration->h . ' jam ' . $duration->i . ' mnt';
else                  $dur_str = $duration->i . ' menit';
?>

<!-- ===== TUTUP SHIFT ===== -->
<div class="tutup-wrap animate-in">

    <!-- Header -->
    <div class="tutup-header">
        <div class="tutup-header-left">
            <div class="tutup-icon-wrap">
                <i class="fas fa-power-off"></i>
            </div>
            <div>
                <h1 class="tutup-main-title">Tutup Sesi Shift</h1>
                <div class="tutup-meta">
                    <span><i class="fas fa-clock"></i> Dibuka: <strong><?= date('d M Y, H:i', strtotime($shift->waktu_buka)) ?> WIB</strong></span>
                    <span class="tutup-meta-dot">·</span>
                    <span><i class="fas fa-user"></i> <?= htmlspecialchars($shift->nama_kasir ?? 'Kasir') ?></span>
                    <span class="tutup-meta-dot">·</span>
                    <span><i class="fas fa-hourglass-half"></i> Durasi: <strong><?= $dur_str ?></strong></span>
                </div>
            </div>
        </div>
        <button type="button" onclick="window.print()" class="tutup-print-btn">
            <i class="fas fa-print"></i>
            Cetak Laporan
        </button>
    </div>

    <!-- Grand Stats Row -->
    <div class="tutup-kpi-row">
        <div class="tutup-kpi">
            <div class="tutup-kpi-icon" style="background:rgba(99,102,241,.1);color:#6366f1;">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <div class="tutup-kpi-val">Rp <?= number_format($saldo_awal, 0, ',', '.') ?></div>
                <div class="tutup-kpi-lbl">Modal Awal Laci</div>
            </div>
        </div>
        <div class="tutup-kpi">
            <div class="tutup-kpi-icon" style="background:rgba(16,185,129,.1);color:#10b981;">
                <i class="fas fa-money-bills"></i>
            </div>
            <div>
                <div class="tutup-kpi-val" id="kpi_cash">Rp <?= number_format($total_cash, 0, ',', '.') ?></div>
                <div class="tutup-kpi-lbl">Total Penjualan Tunai</div>
            </div>
        </div>
        <div class="tutup-kpi">
            <div class="tutup-kpi-icon" style="background:rgba(6,182,212,.1);color:#06b6d4;">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <div class="tutup-kpi-val" id="kpi_nontunai">Rp <?= number_format($total_non_tunai, 0, ',', '.') ?></div>
                <div class="tutup-kpi-lbl">Total Non-Tunai</div>
            </div>
        </div>
        <div class="tutup-kpi tutup-kpi-highlight">
            <div class="tutup-kpi-icon" style="background:rgba(99,102,241,.15);color:#6366f1;">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="tutup-kpi-val" id="kpi_seharusnya">Rp <?= number_format($seharusnya, 0, ',', '.') ?></div>
                <div class="tutup-kpi-lbl">Seharusnya di Laci</div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="tutup-grid">

        <!-- Left Column: Rekap + Calculator -->
        <div class="tutup-left">

            <!-- Rekap Pembayaran -->
            <div class="tutup-card">
                <div class="tutup-card-header">
                    <span class="tutup-card-title">
                        <i class="fas fa-chart-pie"></i> Rekap Pembayaran
                    </span>
                    <span class="tutup-txn-badge">
                        <i class="fas fa-receipt"></i>
                        <?= number_format($total_txn, 0) ?> transaksi
                    </span>
                </div>
                <div class="tutup-card-body">
                    <!-- Tunai -->
                    <div class="tutup-pay-section">
                        <div class="tutup-pay-label">
                            <i class="fas fa-money-bill-wave" style="color:#10b981"></i> Pembayaran Tunai
                        </div>
                        <div class="tutup-pay-row">
                            <span>Modal Awal Laci</span>
                            <span class="fw-600">Rp <?= number_format($saldo_awal, 0, ',', '.') ?></span>
                        </div>
                        <div class="tutup-pay-row tutup-pay-pos">
                            <span>+ Penjualan Tunai</span>
                            <span id="disp_cash">+ Rp <?= number_format($total_cash, 0, ',', '.') ?></span>
                        </div>
                        <div class="tutup-pay-row tutup-pay-total">
                            <span>= Seharusnya di Laci</span>
                            <span id="disp_seharusnya">Rp <?= number_format($seharusnya, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="tutup-divider"></div>

                    <!-- Non-Tunai -->
                    <div class="tutup-pay-section">
                        <div class="tutup-pay-label">
                            <i class="fas fa-qrcode" style="color:#06b6d4"></i> Pembayaran Non-Tunai
                        </div>
                        <div class="tutup-pay-row">
                            <span>QRIS</span>
                            <span id="disp_qris">Rp <?= number_format($total_qris, 0, ',', '.') ?></span>
                        </div>
                        <div class="tutup-pay-row">
                            <span>Debit / Kartu Kredit</span>
                            <span id="disp_debit">Rp <?= number_format($total_debit, 0, ',', '.') ?></span>
                        </div>
                        <div class="tutup-pay-row tutup-pay-total tutup-pay-info">
                            <span>= Total Non-Tunai</span>
                            <span id="disp_nontunai">Rp <?= number_format($total_non_tunai, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Konfirmasi Form -->
        <div class="tutup-right">
            <div class="tutup-card tutup-sticky-card">
                <div class="tutup-card-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border-radius:1rem 1rem 0 0;">
                    <span class="tutup-card-title" style="color:#fff;">
                        <i class="fas fa-circle-check"></i> Konfirmasi Penutupan Shift
                    </span>
                </div>
                <div class="tutup-card-body">
                    <form action="<?= base_url('shift/proses_tutup') ?>" method="POST" id="formTutupShift">

                        <!-- Total Fisik Input -->
                        <div class="tutup-field">
                            <label class="tutup-field-label">
                                <i class="fas fa-money-bills"></i>
                                Total Uang Fisik di Laci
                            </label>
                            <div class="tutup-amount-wrap">
                                <span class="tutup-amount-prefix">Rp</span>
                                <input type="text"
                                       class="tutup-amount-input"
                                       id="saldo_akhir_aktual"
                                       name="saldo_akhir_aktual"
                                       required autocomplete="off"
                                       placeholder="0">
                            </div>
                        </div>

                        <!-- Selisih Box -->
                        <div class="tutup-selisih-box" id="selisihBox" style="display:none;">
                            <div class="tutup-selisih-header" id="selisihHeader">Status Selisih</div>
                            <div class="tutup-selisih-amount" id="selisihAmount">Rp 0</div>
                            <div class="tutup-selisih-note" id="selisihNote"></div>
                        </div>

                        <!-- Seharusnya reference -->
                        <div class="tutup-ref-box">
                            <div class="tutup-ref-row">
                                <span>Seharusnya di laci:</span>
                                <strong id="ref_seharusnya">Rp <?= number_format($seharusnya, 0, ',', '.') ?></strong>
                            </div>
                        </div>

                        <!-- Warning -->
                        <div class="tutup-warning">
                            <i class="fas fa-triangle-exclamation"></i>
                            <span>Tindakan ini <strong>tidak dapat dibatalkan</strong>. Pastikan semua data sudah benar sebelum menutup shift.</span>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="tutup-submit" id="btnSubmitTutup">
                            <i class="fas fa-power-off" id="tutupIcon"></i>
                            <span id="tutupText">Akhiri &amp; Tutup Shift</span>
                        </button>

                        <a href="<?= base_url('dashboard') ?>" class="tutup-cancel-link">
                            <i class="fas fa-arrow-left"></i> Batal, kembali ke Dashboard
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== STYLES ===== -->
<style>
:root {
    --tutup-card: #ffffff;
    --tutup-border: #e2e8f0;
    --tutup-text: #64748b;
    --tutup-heading: #172033;
    --tutup-red: #e33b42;
    --tutup-radius: .75rem;
}
.tutup-wrap { width: 100%; padding: .25rem 0 2rem; color: var(--tutup-text); }
.tutup-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem; }
.tutup-header-left { display:flex; align-items:center; gap:.85rem; min-width:0; }
.tutup-icon-wrap { width:48px; height:48px; background:linear-gradient(145deg,#f0444b,#d92f38); border-radius:.8rem; display:flex; align-items:center; justify-content:center; font-size:1.1rem; color:#fff; flex-shrink:0; box-shadow:0 5px 14px rgba(220,47,56,.22); }
.tutup-main-title { font-size:1.2rem; line-height:1.25; font-weight:800; color:var(--tutup-heading); margin:0 0 .3rem; }
.tutup-meta { display:flex; align-items:center; flex-wrap:wrap; gap:.35rem .55rem; font-size:.76rem; line-height:1.4; }
.tutup-meta span { display:inline-flex; align-items:center; gap:.3rem; }
.tutup-meta i { color:#64748b; }
.tutup-meta strong { color:#334155; font-weight:700; }
.tutup-meta-dot { color:#cbd5e1; }
.tutup-print-btn { display:inline-flex; align-items:center; justify-content:center; gap:.45rem; min-height:38px; padding:.5rem .8rem; border:1px solid var(--tutup-border); border-radius:.55rem; background:#fff; color:#475569; font:inherit; font-size:.78rem; font-weight:700; cursor:pointer; transition:all .18s ease; }
.tutup-print-btn:hover { border-color:#94a3b8; background:#f8fafc; color:#172033; }
.tutup-kpi-row { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.75rem; margin-bottom:1rem; }
.tutup-kpi { display:flex; align-items:center; gap:.7rem; min-width:0; padding:.8rem .85rem; border:1px solid var(--tutup-border); border-radius:var(--tutup-radius); background:var(--tutup-card); box-shadow:0 2px 8px rgba(15,23,42,.04); }
.tutup-kpi-icon { display:flex; align-items:center; justify-content:center; width:40px; height:40px; flex:0 0 40px; border-radius:.65rem; font-size:1rem; }
.tutup-kpi-val { color:var(--tutup-heading); font-size:1rem; line-height:1.25; font-weight:800; font-variant-numeric:tabular-nums; white-space:nowrap; }
.tutup-kpi-lbl { margin-top:.18rem; color:#64748b; font-size:.7rem; line-height:1.3; font-weight:600; }
.tutup-kpi-highlight { border-color:#fecaca; background:linear-gradient(135deg,#fff7f7,#ffffff 75%); }
.tutup-kpi-highlight .tutup-kpi-val { color:#c72f39; }
.tutup-grid { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(300px,.9fr); gap:1rem; align-items:stretch; }
.tutup-left { display:grid; grid-template-columns:1fr; align-items:stretch; gap:.75rem; min-width:0; }
.tutup-right { min-width:0; }
.tutup-left .tutup-card { height:100%; }
.tutup-card { overflow:hidden; margin-bottom:1rem; border:1px solid var(--tutup-border); border-radius:var(--tutup-radius); background:var(--tutup-card); box-shadow:0 4px 16px rgba(15,23,42,.07); }
.tutup-sticky-card { position:sticky; top:1rem; height:100%; }
.tutup-card-header { display:flex; align-items:center; justify-content:space-between; gap:.75rem; min-height:48px; padding:.7rem .9rem; border-bottom:1px solid var(--tutup-border); background:#f8fafc; }
.tutup-card-title { display:inline-flex; align-items:center; gap:.45rem; margin:0; color:var(--tutup-heading); font-size:.84rem; line-height:1.35; font-weight:800; }
.tutup-card-title i { color:#64748b; }
.tutup-txn-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .5rem; border:1px solid #dbeafe; border-radius:50rem; background:#eff6ff; color:#2563eb; font-size:.68rem; font-weight:700; white-space:nowrap; }
.tutup-card-body { padding:.9rem; }
.tutup-pay-section { display:grid; gap:.15rem; }
.tutup-pay-label { display:flex; align-items:center; gap:.4rem; margin-bottom:.35rem; color:#334155; font-size:.76rem; font-weight:800; }
.tutup-pay-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.3rem 0; color:#64748b; font-size:.76rem; line-height:1.4; }
.tutup-pay-row span:last-child { color:#334155; font-weight:700; font-variant-numeric:tabular-nums; text-align:right; white-space:nowrap; }
.tutup-pay-row.tutup-pay-pos span:last-child { color:#059669; }
.tutup-pay-row.tutup-pay-total { margin-top:.2rem; padding:.55rem .65rem; border:1px solid #dbeafe; border-radius:.5rem; background:#eff6ff; color:#334155; font-weight:700; }
.tutup-pay-row.tutup-pay-total span:last-child { color:#1d4ed8; font-weight:800; }
.tutup-pay-row.tutup-pay-info { border-color:#bae6fd; background:#f0f9ff; }
.tutup-pay-row.tutup-pay-info span:last-child { color:#0369a1; }
.tutup-divider { height:1px; margin:.75rem 0; background:linear-gradient(90deg,transparent,#e2e8f0 12%,#e2e8f0 88%,transparent); }
.tutup-amount-input:focus { outline:0; border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.1); }
.tutup-field { margin-bottom:.8rem; }
.tutup-field-label { display:flex; align-items:center; gap:.4rem; margin-bottom:.45rem; color:#475569; font-size:.75rem; font-weight:700; }
.tutup-field-label i { color:#64748b; }
.tutup-amount-wrap { display:flex; align-items:center; overflow:hidden; border:1px solid #dbe2ea; border-radius:.65rem; background:#fff; }
.tutup-amount-prefix { padding:0 .7rem; color:#64748b; font-size:.85rem; font-weight:700; }
.tutup-amount-input { width:100%; min-width:0; min-height:54px; padding:.65rem .75rem .65rem 0; border:0; border-radius:0; background:transparent; color:#172033; font:inherit; font-size:1.45rem; line-height:1.2; font-weight:800; font-variant-numeric:tabular-nums; }
.tutup-amount-input:focus { outline:0; box-shadow:none; }
.tutup-amount-input::placeholder { color:#cbd5e1; }
.tutup-amount-wrap:focus-within { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.1); }
.tutup-selisih-box { margin:.75rem 0; padding:.7rem .8rem; border:1px solid #fed7aa; border-radius:.6rem; background:#fff7ed; }
.tutup-selisih-header { color:#9a3412; font-size:.7rem; font-weight:800; text-transform:uppercase; }
.tutup-selisih-amount { margin:.15rem 0; color:#c2410c; font-size:1.15rem; font-weight:900; font-variant-numeric:tabular-nums; }
.tutup-selisih-note { color:#7c2d12; font-size:.72rem; line-height:1.45; }
.tutup-ref-box { margin:.75rem 0; padding:.65rem .75rem; border:1px solid #dbeafe; border-radius:.55rem; background:#eff6ff; }
.tutup-ref-row { display:flex; align-items:center; justify-content:space-between; gap:.7rem; color:#475569; font-size:.73rem; }
.tutup-ref-row strong { color:#1d4ed8; font-size:.8rem; font-variant-numeric:tabular-nums; text-align:right; }
.tutup-warning { display:flex; align-items:flex-start; gap:.5rem; margin:.8rem 0; padding:.65rem .7rem; border:1px solid #fecaca; border-radius:.55rem; background:#fff5f5; color:#7f1d1d; font-size:.7rem; line-height:1.5; }
.tutup-warning i { margin-top:.12rem; color:#dc2626; }
.tutup-submit { display:flex; align-items:center; justify-content:center; gap:.5rem; width:100%; min-height:44px; padding:.65rem .8rem; border:0; border-radius:.55rem; background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; font:inherit; font-size:.8rem; font-weight:800; cursor:pointer; box-shadow:0 4px 10px rgba(220,47,56,.2); transition:transform .16s ease,box-shadow .16s ease; }
.tutup-submit:hover { transform:translateY(-1px); box-shadow:0 6px 14px rgba(220,47,56,.25); }
.tutup-cancel-link { display:flex; align-items:center; justify-content:center; gap:.35rem; margin-top:.65rem; color:#64748b; font-size:.72rem; font-weight:700; text-decoration:none; }
.tutup-cancel-link:hover { color:#334155; }
@media(max-width:1100px) {
    .tutup-kpi-row { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .tutup-grid { grid-template-columns:1fr; gap:.75rem; }
}
@media(max-width:820px) {
    .tutup-grid { grid-template-columns:1fr; }
    .tutup-sticky-card { position:static; }
}
@media(max-width:520px) {
    .tutup-header { align-items:flex-start; }
    .tutup-header-left { gap:.65rem; }
    .tutup-icon-wrap { width:42px; height:42px; border-radius:.7rem; font-size:1rem; }
    .tutup-main-title { font-size:1.05rem; }
    .tutup-meta { font-size:.68rem; }
    .tutup-print-btn { width:100%; }
    .tutup-kpi-row { gap:.5rem; }
    .tutup-kpi { align-items:flex-start; gap:.5rem; padding:.65rem; }
    .tutup-kpi-icon { width:34px; height:34px; flex-basis:34px; font-size:.85rem; }
    .tutup-kpi-val { font-size:.82rem; white-space:normal; overflow-wrap:anywhere; }
    .tutup-kpi-lbl { font-size:.63rem; }
    .tutup-card-body { padding:.75rem; }
}
@media print {
    .tutup-print-btn,.tutup-submit,.tutup-cancel-link { display:none !important; }
    .tutup-wrap { padding:0; }
    .tutup-card { box-shadow:none; break-inside:avoid; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputAktual  = document.getElementById('saldo_akhir_aktual');
    const selisihBox   = document.getElementById('selisihBox');
    const selisihHead  = document.getElementById('selisihHeader');
    const selisihAmt   = document.getElementById('selisihAmount');
    const selisihNote  = document.getElementById('selisihNote');
    const data = {
        saldo_awal:  <?= (float)$saldo_awal  ?>,
        total_cash:  <?= (float)$total_cash  ?>,
        total_qris:  <?= (float)$total_qris  ?>,
        total_debit: <?= (float)$total_debit ?>,
    };

    const fmt = n => new Intl.NumberFormat('id-ID').format(Math.abs(n));
    const fmtRp = n => 'Rp ' + fmt(n);

    function compute() {
        const seharusnya = data.saldo_awal + data.total_cash;
        const nonTunai   = data.total_qris + data.total_debit;

        document.getElementById('disp_cash').textContent       = '+ Rp ' + fmt(data.total_cash);
        document.getElementById('disp_seharusnya').textContent = fmtRp(seharusnya);
        document.getElementById('disp_qris').textContent       = fmtRp(data.total_qris);
        document.getElementById('disp_debit').textContent      = fmtRp(data.total_debit);
        document.getElementById('disp_nontunai').textContent   = fmtRp(nonTunai);
        document.getElementById('ref_seharusnya').textContent  = fmtRp(seharusnya);

        // KPI bar
        document.getElementById('kpi_cash').textContent     = fmtRp(data.total_cash);
        document.getElementById('kpi_nontunai').textContent = fmtRp(nonTunai);
        document.getElementById('kpi_seharusnya').textContent = fmtRp(seharusnya);

        updateSelisih(seharusnya);
    }

    function updateSelisih(seharusnya) {
        const aktual  = parseInt(inputAktual.value.replace(/\D/g,'')) || 0;
        const selisih = aktual - seharusnya;

        selisihBox.style.display = 'block';
        selisihBox.className     = 'tutup-selisih-box';

        if (selisih > 0) {
            selisihBox.classList.add('surplus');
            selisihHead.textContent = 'Selisih Lebih (Surplus)';
            selisihAmt.textContent  = '+ Rp ' + fmt(selisih);
            selisihNote.textContent = 'Uang lebih dari seharusnya.';
        } else if (selisih < 0) {
            selisihBox.classList.add('shortage');
            selisihHead.textContent = 'Selisih Kurang (Shortage)';
            selisihAmt.textContent  = '− Rp ' + fmt(Math.abs(selisih));
            selisihNote.textContent = 'Uang kurang dari seharusnya.';
        } else {
            selisihBox.classList.add('balance');
            selisihHead.textContent = 'Saldo Seimbang (Balance)';
            selisihAmt.textContent  = 'Rp 0';
            selisihNote.textContent = 'Jumlah pas, tidak ada selisih.';
        }

        if (aktual === 0) selisihBox.style.display = 'none';
    }

    // Manual input
    inputAktual.addEventListener('input', function() {
        const raw = parseInt(this.value.replace(/\D/g,'')) || 0;
        this.value = new Intl.NumberFormat('id-ID').format(raw);
        compute();
    });

    // Submit
    document.getElementById('formTutupShift')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitTutup');
        document.getElementById('tutupIcon').className = 'fas fa-spinner fa-spin';
        document.getElementById('tutupText').textContent = 'Memproses…';
        btn.disabled = true;
    });

    compute();
});
</script>
