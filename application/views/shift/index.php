<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$id_shift    = $this->session->userdata('id_shift');
$total_buka  = 0;
$total_tutup = 0;
$total_modal = 0;
$total_akhir = 0;

if (!empty($shifts)) {
    foreach ($shifts as $s) {
        if ($s->status === 'buka') $total_buka++;
        else $total_tutup++;
        $total_modal += $s->saldo_awal;
        if ($s->saldo_akhir_aktual !== null) $total_akhir += $s->saldo_akhir_aktual;
    }
}
?>

<!-- ===== PAGE HEADER ===== -->
<div class="shi-header animate-in">
    <div class="shi-header-left">
        <div class="shi-header-icon">
            <i class="fas fa-clock-rotate-left"></i>
        </div>
        <div>
            <h1 class="shi-title">Riwayat Sesi Shift</h1>
            <p class="shi-subtitle">Pantau semua sesi buka &amp; tutup kasir secara real-time.</p>
        </div>
    </div>
    <div class="shi-header-right">
        <?php if ($id_shift): ?>
            <a href="<?= base_url('shift/tutup') ?>" class="shi-btn shi-btn-warning">
                <i class="fas fa-power-off"></i>
                <span>Tutup Shift Aktif</span>
            </a>
        <?php else: ?>
            <a href="<?= base_url('shift/buka') ?>" class="shi-btn shi-btn-primary">
                <i class="fas fa-door-open"></i>
                <span>Buka Shift Baru</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- ===== STAT CARDS ===== -->
<div class="shi-stats animate-in" style="animation-delay:0.07s">
    <div class="shi-stat">
        <div class="shi-stat-icon" style="background:rgba(99,102,241,.12);color:#6366f1;">
            <i class="fas fa-layer-group"></i>
        </div>
        <div>
            <div class="shi-stat-val"><?= count($shifts ?? []) ?></div>
            <div class="shi-stat-lbl">Total Sesi</div>
        </div>
        <div class="shi-stat-bar" style="background:linear-gradient(135deg,#6366f1,#4f46e5)"></div>
    </div>
    <div class="shi-stat">
        <div class="shi-stat-icon" style="background:rgba(16,185,129,.12);color:#10b981;">
            <i class="fas fa-circle-dot"></i>
        </div>
        <div>
            <div class="shi-stat-val"><?= $total_buka ?></div>
            <div class="shi-stat-lbl">Sedang Aktif</div>
        </div>
        <div class="shi-stat-bar" style="background:linear-gradient(135deg,#10b981,#059669)"></div>
    </div>
    <div class="shi-stat">
        <div class="shi-stat-icon" style="background:rgba(107,114,128,.12);color:#6b7280;">
            <i class="fas fa-lock"></i>
        </div>
        <div>
            <div class="shi-stat-val"><?= $total_tutup ?></div>
            <div class="shi-stat-lbl">Sudah Ditutup</div>
        </div>
        <div class="shi-stat-bar" style="background:linear-gradient(135deg,#6b7280,#4b5563)"></div>
    </div>
    <div class="shi-stat">
        <div class="shi-stat-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;">
            <?php if ($id_shift): ?>
                <i class="fas fa-toggle-on"></i>
            <?php else: ?>
                <i class="fas fa-toggle-off"></i>
            <?php endif; ?>
        </div>
        <div>
            <div class="shi-stat-val shi-status-<?= $id_shift ? 'on' : 'off' ?>"><?= $id_shift ? 'AKTIF' : 'TUTUP' ?></div>
            <div class="shi-stat-lbl">Status Shift Saya</div>
        </div>
        <div class="shi-stat-bar" style="background:<?= $id_shift ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#f59e0b,#d97706)' ?>"></div>
    </div>
</div>

<!-- ===== TABLE CARD ===== -->
<div class="shi-card animate-in" style="animation-delay:0.14s">
    <!-- Card Top -->
    <div class="shi-card-top">
        <div class="shi-card-title">
            <i class="fas fa-table-list"></i>
            Daftar Riwayat Shift
        </div>
        <div class="shi-card-actions">
            <div class="shi-search-wrap">
                <i class="fas fa-magnifying-glass shi-search-icon"></i>
                <input type="text" id="shiftSearch" class="shi-search" placeholder="Cari kasir atau tanggal…">
            </div>
            <div class="shi-filter-wrap">
                <select id="shiftFilter" class="shi-filter">
                    <option value="">Semua Status</option>
                    <option value="buka">Aktif</option>
                    <option value="tutup">Ditutup</option>
                </select>
            </div>
            <span class="shi-badge-count" id="recordCount"><?= count($shifts ?? []) ?> sesi</span>
        </div>
    </div>

    <!-- Table -->
    <div class="shi-table-wrap">
        <table class="shi-table" id="shiftTable">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Kasir</th>
                    <th>Waktu Buka</th>
                    <th>Waktu Tutup</th>
                    <th class="text-end">Modal Awal</th>
                    <th class="text-end">Saldo Akhir</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody id="shiftBody">
                <?php if (empty($shifts)): ?>
                <tr>
                    <td colspan="7" class="shi-empty-cell">
                        <div class="shi-empty">
                            <div class="shi-empty-icon">
                                <i class="fas fa-clock-rotate-left"></i>
                            </div>
                            <p class="shi-empty-title">Belum Ada Riwayat Shift</p>
                            <p class="shi-empty-sub">Mulai dengan membuka sesi shift pertama.</p>
                            <a href="<?= base_url('shift/buka') ?>" class="shi-btn shi-btn-primary" style="margin-top:1rem;font-size:.85rem;padding:.55rem 1.25rem">
                                <i class="fas fa-plus"></i> Buka Shift Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($shifts as $s): ?>
                    <tr class="shi-row<?= ($s->status === 'buka') ? ' shi-row-active' : '' ?>"
                        data-kasir="<?= strtolower(htmlspecialchars($s->nama_kasir ?? '')) ?>"
                        data-status="<?= $s->status ?>">
                        <td>
                            <span class="shi-num"><?= $no++ ?></span>
                        </td>
                        <td>
                            <div class="shi-kasir">
                                <div class="shi-avatar"><?= strtoupper(substr($s->nama_kasir ?? 'K', 0, 1)) ?></div>
                                <div>
                                    <div class="shi-kasir-name"><?= htmlspecialchars($s->nama_kasir ?? 'Kasir') ?></div>
                                    <?php if ($s->status === 'buka'): ?>
                                        <div class="shi-kasir-sub" style="color:#10b981;">
                                            <span class="pulse-dot-xs"></span> Sedang bertugas
                                        </div>
                                    <?php else: ?>
                                        <div class="shi-kasir-sub">Sesi selesai</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="shi-time">
                                <div class="shi-time-icon open"><i class="fas fa-play"></i></div>
                                <div>
                                    <div class="shi-time-date"><?= date('d M Y', strtotime($s->waktu_buka)) ?></div>
                                    <div class="shi-time-hour"><?= date('H:i', strtotime($s->waktu_buka)) ?> WIB</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($s->waktu_tutup): ?>
                                <div class="shi-time">
                                    <div class="shi-time-icon close"><i class="fas fa-stop"></i></div>
                                    <div>
                                        <div class="shi-time-date"><?= date('d M Y', strtotime($s->waktu_tutup)) ?></div>
                                        <div class="shi-time-hour"><?= date('H:i', strtotime($s->waktu_tutup)) ?> WIB</div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="shi-running">
                                    <span class="pulse-dot"></span>
                                    Sedang Berjalan
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <span class="shi-money">Rp <?= number_format($s->saldo_awal, 0, ',', '.') ?></span>
                        </td>
                        <td class="text-end">
                            <?php if ($s->saldo_akhir_aktual !== null): ?>
                                <?php
                                    $selisih = $s->saldo_akhir_aktual - ($s->saldo_awal + 0);
                                    $selisih_class = $selisih >= 0 ? 'positive' : 'negative';
                                ?>
                                <span class="shi-money shi-money-final">Rp <?= number_format($s->saldo_akhir_aktual, 0, ',', '.') ?></span>
                            <?php else: ?>
                                <span class="shi-money-dash">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($s->status === 'buka'): ?>
                                <span class="shi-badge shi-badge-open">
                                    <span class="pulse-dot-sm"></span> AKTIF
                                </span>
                            <?php else: ?>
                                <span class="shi-badge shi-badge-closed">
                                    <i class="fas fa-lock" style="font-size:.6rem"></i> SELESAI
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Empty search result placeholder -->
    <div id="emptySearch" class="shi-empty" style="display:none;padding:3rem 1rem;">
        <div class="shi-empty-icon"><i class="fas fa-magnifying-glass"></i></div>
        <p class="shi-empty-title">Tidak Ditemukan</p>
        <p class="shi-empty-sub">Tidak ada sesi shift yang cocok dengan filter ini.</p>
    </div>
</div>

<!-- ===== STYLES ===== -->
<style>
/* ===== SHI DARK THEME ===== */
:root {
    --shi-bg:       #f8fafc;
    --shi-card:     #ffffff;
    --shi-border:   #e2e8f0;
    --shi-text:     #475569;
    --shi-heading:  #0f172a;
    --shi-radius:   .875rem;
}
/* Header */
.shi-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem; }
.shi-header-left { display:flex; align-items:center; gap:1rem; }
.shi-header-icon { width:48px; height:48px; background:rgba(167,139,250,.15); color:#a78bfa; border-radius:var(--shi-radius); display:flex; align-items:center; justify-content:center; font-size:1.3rem; border:1px solid rgba(167,139,250,.2); }
.shi-title { font-size:1.35rem; font-weight:800; color:var(--shi-heading); margin:0; letter-spacing:-.3px; }
.shi-subtitle { font-size:.8375rem; color:#4b5563; margin:0; margin-top:.15rem; }
.shi-header-right { display:flex; gap:.625rem; flex-wrap:wrap; }
.shi-btn { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem; border-radius:.625rem; font-weight:700; font-size:.8375rem; text-decoration:none; border:0; cursor:pointer; transition:all .18s ease; }
.shi-btn-primary { background:linear-gradient(135deg,#a78bfa,#7c3aed); color:#fff; box-shadow:0 3px 12px rgba(167,139,250,.4); }
.shi-btn-primary:hover { background:linear-gradient(135deg,#b49bff,#8b5cf6); transform:translateY(-1px); box-shadow:0 5px 20px rgba(167,139,250,.5); color:#fff; }
.shi-btn-warning { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; box-shadow:0 3px 12px rgba(220,38,38,.28); }
.shi-btn-warning:hover { background:linear-gradient(135deg,#f87171,#ef4444); transform:translateY(-1px); color:#fff; }

/* Stats */
.shi-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
.shi-stat { background:var(--shi-card); border-radius:var(--shi-radius); padding:1.1rem 1.25rem; display:flex; align-items:center; gap:1rem; box-shadow:0 2px 12px rgba(15,23,42,.07); border:1px solid var(--shi-border); position:relative; overflow:hidden; transition:transform .2s,box-shadow .2s,border-color .2s; }
.shi-stat:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(15,23,42,.12); border-color:rgba(99,102,241,.2); }
.shi-stat-bar { position:absolute; bottom:0; left:0; right:0; height:2px; border-radius:0 0 var(--shi-radius) var(--shi-radius); }
.shi-stat-icon { width:44px; height:44px; border-radius:.75rem; display:flex; align-items:center; justify-content:center; font-size:1.15rem; flex-shrink:0; }
.shi-stat-val { font-size:1.55rem; font-weight:800; color:var(--shi-heading); line-height:1; }
.shi-stat-lbl { font-size:.76rem; color:#4b5563; font-weight:600; margin-top:.2rem; }
.shi-status-on  { color:#10b981 !important; font-size:.95rem !important; }
.shi-status-off { color:#f59e0b !important; font-size:.95rem !important; }

/* Table Card */
.shi-card { background:var(--shi-card); border-radius:var(--shi-radius); box-shadow:0 4px 18px rgba(15,23,42,.08); border:1px solid var(--shi-border); overflow:hidden; margin-bottom:1.5rem; }
.shi-card-top { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; padding:1.125rem 1.5rem; border-bottom:1px solid var(--shi-border); }
.shi-card-title { font-size:.9375rem; font-weight:700; color:var(--shi-heading); display:flex; align-items:center; gap:.5rem; }
.shi-card-title i { color:#a78bfa; }
.shi-card-actions { display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; }
.shi-search-wrap { position:relative; }
.shi-search-icon { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.8rem; pointer-events:none; }
.shi-search { padding:.45rem .875rem .45rem 2.15rem; border:1.5px solid var(--shi-border); border-radius:.625rem; font-size:.8375rem; font-weight:600; color:var(--shi-heading); background:rgba(255,255,255,.03); outline:none; transition:border-color .2s,box-shadow .2s; width:200px; }
.shi-search:focus { border-color:rgba(167,139,250,.5); box-shadow:0 0 0 3px rgba(167,139,250,.12); background:rgba(255,255,255,.05); }
.shi-filter { appearance:none; -webkit-appearance:none; -moz-appearance:none; background-image:none; padding:.45rem .875rem; border:1.5px solid var(--shi-border); border-radius:.625rem; font-size:.8375rem; font-weight:600; color:var(--shi-heading); background-color:#fff; outline:none; cursor:pointer; transition:border-color .2s; }
.shi-filter option { background:#ffffff; color:var(--shi-heading); }
.shi-filter:focus { border-color:rgba(167,139,250,.5); }
.shi-badge-count { font-size:.78rem; font-weight:700; color:#4b5563; background:rgba(255,255,255,.04); padding:.3rem .85rem; border-radius:50rem; white-space:nowrap; border:1px solid var(--shi-border); }

/* Table */
.shi-table-wrap { overflow-x:auto; }
.shi-table { width:100%; border-collapse:collapse; }
.shi-table thead tr th { font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; font-weight:800; background:rgba(255,255,255,.02); border-bottom:1px solid var(--shi-border); padding:.875rem 1rem; white-space:nowrap; }
.shi-table tbody td { padding:.875rem 1rem; border-bottom:1px solid #eef2f7; vertical-align:middle; color:var(--shi-text); font-size:.875rem; }
.shi-table tbody tr:last-child td { border-bottom:0; }
.shi-table tbody tr { transition:background .15s; }
.shi-table tbody tr:hover { background:rgba(99,102,241,.035); }
.shi-row-active { background:rgba(16,185,129,.04) !important; }

/* Cells */
.shi-num { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; background:rgba(255,255,255,.04); border-radius:50%; font-size:.78rem; font-weight:800; color:#4b5563; }
.shi-kasir { display:flex; align-items:center; gap:.7rem; }
.shi-avatar { width:36px; height:36px; background:rgba(167,139,250,.12); color:#a78bfa; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; flex-shrink:0; border:1.5px solid rgba(167,139,250,.2); }
.shi-kasir-name { font-size:.875rem; font-weight:700; color:var(--shi-heading); }
.shi-kasir-sub { font-size:.72rem; color:#94a3b8; font-weight:600; display:flex; align-items:center; gap:.3rem; margin-top:1px; }
.shi-time { display:flex; align-items:center; gap:.6rem; }
.shi-time-icon { width:28px; height:28px; border-radius:.5rem; display:flex; align-items:center; justify-content:center; font-size:.65rem; flex-shrink:0; }
.shi-time-icon.open  { background:rgba(16,185,129,.12); color:#10b981; }
.shi-time-icon.close { background:rgba(100,116,139,.1);  color:#4b5563; }
.shi-time-date { font-size:.82rem; font-weight:700; color:var(--shi-heading); line-height:1.3; }
.shi-time-hour { font-size:.73rem; color:#4b5563; font-weight:600; }
.shi-running { display:inline-flex; align-items:center; gap:.45rem; background:rgba(16,185,129,.08); color:#10b981; font-size:.78rem; font-weight:700; padding:.3rem .8rem; border-radius:50rem; border:1px solid rgba(16,185,129,.15); }
.shi-money { font-size:.85rem; font-weight:700; color:var(--shi-heading); font-family:SFMono-Regular,Menlo,Monaco,Consolas,monospace; }
.shi-money-final { color:#a78bfa; }
.shi-money-dash { color:#94a3b8; font-size:1.1rem; font-weight:700; }

/* Badges */
.shi-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .9rem; border-radius:50rem; font-size:.68rem; font-weight:800; letter-spacing:.07em; text-transform:uppercase; }
.shi-badge-open   { background:rgba(16,185,129,.1); color:#10b981; border:1px solid rgba(16,185,129,.2); }
.shi-badge-closed { background:rgba(100,116,139,.08); color:#4b5563; border:1px solid rgba(100,116,139,.12); }

/* Pulse */
.pulse-dot,.pulse-dot-sm,.pulse-dot-xs { border-radius:50%; display:inline-block; animation:pulseAnim 1.5s ease-in-out infinite; }
.pulse-dot    { width:7px;  height:7px;  background:#10b981; }
.pulse-dot-sm { width:6px;  height:6px;  background:#10b981; }
.pulse-dot-xs { width:5px;  height:5px;  background:#10b981; flex-shrink:0; }
@keyframes pulseAnim { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:.45;transform:scale(.75);} }

/* Empty */
.shi-empty-cell { padding:0 !important; }
.shi-empty { display:flex; flex-direction:column; align-items:center; padding:3.5rem 1rem; text-align:center; }
.shi-empty-icon { width:80px; height:80px; background:rgba(167,139,250,.07); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; color:#a78bfa; margin-bottom:1.25rem; }
.shi-empty-title { font-size:1rem; font-weight:800; color:var(--shi-heading); margin-bottom:.3rem; }
.shi-empty-sub   { font-size:.875rem; color:#4b5563; margin:0; }

@media (max-width:992px) { .shi-stats{grid-template-columns:repeat(2,1fr);} }
@media (max-width:576px)  {
    .shi-stats { gap:.65rem; }
    .shi-stat { gap:.65rem; padding:.8rem; }
    .shi-stat-icon { width:38px; height:38px; font-size:1rem; }
    .shi-stat-val { font-size:1.25rem; }
    .shi-header { flex-direction:column; align-items:flex-start; }
    .shi-header-right,.shi-header-right .shi-btn { width:100%; }
    .shi-card-top { flex-direction:column; align-items:flex-start; padding:.9rem 1rem; }
    .shi-card-actions { display:grid; grid-template-columns:minmax(0,1fr) auto; width:100%; }
    .shi-search-wrap { grid-column:1 / -1; width:100%; }
    .shi-search { width:100%; }
    .shi-filter-wrap,.shi-filter { width:100%; min-width:0; }
    .shi-table { min-width:900px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput  = document.getElementById('shiftSearch');
    const filterSelect = document.getElementById('shiftFilter');
    const tbody        = document.getElementById('shiftBody');
    const countBadge   = document.getElementById('recordCount');
    const emptyBox     = document.getElementById('emptySearch');

    function filterTable() {
        const q      = (searchInput?.value || '').toLowerCase().trim();
        const status = filterSelect?.value || '';
        const rows   = tbody ? Array.from(tbody.querySelectorAll('tr.shi-row, tr.shi-row-active')) : [];
        let visible  = 0;

        rows.forEach(row => {
            const kasir  = (row.dataset.kasir || '').toLowerCase();
            const rowSt  = row.dataset.status || '';
            const text   = row.textContent.toLowerCase();

            const matchQ = !q || kasir.includes(q) || text.includes(q);
            const matchS = !status || rowSt === status;

            if (matchQ && matchS) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if (countBadge) countBadge.textContent = visible + ' sesi';
        if (emptyBox) emptyBox.style.display = (visible === 0 && rows.length > 0) ? 'flex' : 'none';
    }

    searchInput?.addEventListener('input', filterTable);
    filterSelect?.addEventListener('change', filterTable);
});
</script>
