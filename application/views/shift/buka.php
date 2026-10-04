<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ===== BUKA SHIFT PAGE ===== -->
<div class="buka-wrap animate-in">
    <div class="buka-container">

        <!-- Left: Info Panel -->
        <div class="buka-info-panel">
            <div class="buka-brand-badge">
                <i class="fas fa-mug-hot"></i>
                <span>NDC POS Kasir</span>
            </div>
            <h2 class="buka-info-title">Mulai Sesi Shift Baru</h2>
            <p class="buka-info-desc">Masukkan modal awal laci kasir untuk memulai sesi penjualan dan mencatat semua transaksi selama shift.</p>

            <div class="buka-steps">
                <div class="buka-step">
                    <div class="buka-step-num">1</div>
                    <div>
                        <div class="buka-step-title">Hitung Uang Laci</div>
                        <div class="buka-step-desc">Hitung fisik seluruh pecahan uang di laci kasir.</div>
                    </div>
                </div>
                <div class="buka-step">
                    <div class="buka-step-num">2</div>
                    <div>
                        <div class="buka-step-title">Input Modal Awal</div>
                        <div class="buka-step-desc">Masukkan total uang yang tersedia sebagai modal awal.</div>
                    </div>
                </div>
                <div class="buka-step">
                    <div class="buka-step-num">3</div>
                    <div>
                        <div class="buka-step-title">Mulai Transaksi</div>
                        <div class="buka-step-desc">Sistem siap mencatat penjualan di sesi ini.</div>
                    </div>
                </div>
            </div>

            <div class="buka-tips">
                <i class="fas fa-lightbulb buka-tips-icon"></i>
                <div>
                    <div class="buka-tips-title">Tips Penting</div>
                    <ul class="buka-tips-list">
                        <li>Pastikan mesin kasir dalam kondisi siap pakai.</li>
                        <li>Laporkan selisih ke supervisor saat tutup shift.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right: Form Card -->
        <div class="buka-form-panel">

            <!-- Card Banner -->
            <div class="buka-banner">
                <div class="buka-banner-dots"></div>
                <div class="buka-banner-inner">
                    <div class="buka-banner-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h3 class="buka-banner-title">Buka Sesi Shift</h3>
                    <p class="buka-banner-sub">Silakan isi form di bawah untuk memulai.</p>
                </div>
            </div>

            <!-- Kasir Info Bar -->
            <div class="buka-kasir-bar">
                <div class="buka-kasir-left">
                    <div class="buka-kasir-avatar">
                        <?= strtoupper(substr($this->session->userdata('nama') ?? 'K', 0, 1)) ?>
                    </div>
                    <div>
                        <div class="buka-kasir-label">Kasir Bertugas</div>
                        <div class="buka-kasir-name"><?= htmlspecialchars($this->session->userdata('nama') ?? 'Kasir') ?></div>
                    </div>
                </div>
                <div class="buka-kasir-time">
                    <i class="fas fa-clock"></i>
                    <span><?= date('d M Y, H:i') ?> WIB</span>
                </div>
            </div>

            <!-- Form Body -->
            <div class="buka-form-body">

                <?php if ($this->session->flashdata('error')): ?>
                <div class="buka-alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <span><?= $this->session->flashdata('error') ?></span>
                </div>
                <?php endif; ?>

                <form action="<?= base_url('shift/proses_buka') ?>" method="POST" id="formBukaShift">

                    <!-- Nominal Input -->
                    <div class="buka-field">
                        <label class="buka-label">
                            <i class="fas fa-wallet"></i>
                            Modal Awal Laci (Tunai)
                        </label>
                        <div class="buka-input-wrap">
                            <span class="buka-prefix">Rp</span>
                            <input type="text"
                                   class="buka-input"
                                   id="saldo_awal"
                                   name="saldo_awal"
                                   value="0"
                                   required
                                   autocomplete="off"
                                   placeholder="0">
                        </div>
                        <p class="buka-hint">
                            <i class="fas fa-circle-info"></i>
                            Uang kembalian tunai yang tersedia di laci kasir saat ini.
                        </p>
                    </div>

                    <!-- Quick Preset Amounts -->
                    <div class="buka-field">
                        <label class="buka-label">
                            <i class="fas fa-bolt-lightning" style="color:#f59e0b"></i>
                            Nominal Cepat
                        </label>
                        <div class="buka-presets">
                            <button type="button" class="buka-preset buka-preset-clear" onclick="setPreset(0)">
                                <i class="fas fa-times"></i> Reset
                            </button>
                            <button type="button" class="buka-preset" onclick="addPreset(50000)">+50 Rb</button>
                            <button type="button" class="buka-preset" onclick="addPreset(100000)">+100 Rb</button>
                            <button type="button" class="buka-preset" onclick="addPreset(200000)">+200 Rb</button>
                            <button type="button" class="buka-preset" onclick="addPreset(500000)">+500 Rb</button>
                            <button type="button" class="buka-preset" onclick="addPreset(1000000)">+1 Jt</button>
                        </div>
                    </div>

                    <div class="buka-preview" id="saldoPreview">
                        <div class="buka-preview-lbl">Modal yang akan dicatat</div>
                        <div class="buka-preview-val" id="previewVal">Rp 0</div>
                    </div>

                    <button type="submit" class="buka-submit" id="btnSubmitShift">
                        <i class="fas fa-door-open" id="submitIcon"></i>
                        <span id="submitText">Buka Shift Sekarang</span>
                        <i class="fas fa-arrow-right buka-submit-arrow"></i>
                    </button>

                    <a href="<?= base_url('dashboard') ?>" class="buka-cancel">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===== STYLES ===== -->
<style>
:root { --dk-card:#ffffff; --dk-border:#e2e8f0; --dk-text:#64748b; --dk-head:#0f172a; --dk-radius:.875rem; }
.buka-wrap { padding:.5rem 0 2rem; }
.buka-container { display:grid; grid-template-columns:1fr 1.15fr; gap:1.75rem; max-width:960px; margin:0 auto; align-items:start; }
.buka-brand-badge { display:inline-flex; align-items:center; gap:.5rem; background:rgba(167,139,250,.1); color:#a78bfa; padding:.35rem .875rem; border-radius:50rem; font-size:.78rem; font-weight:800; letter-spacing:.06em; text-transform:uppercase; margin-bottom:.75rem; border:1px solid rgba(167,139,250,.15); }
.buka-info-title { font-size:1.5rem; font-weight:800; color:var(--dk-head); letter-spacing:-.3px; line-height:1.25; margin-bottom:.5rem; }
.buka-info-sub { font-size:.875rem; color:#4b5563; line-height:1.6; margin-bottom:1.25rem; }
.buka-feature-list { display:flex; flex-direction:column; gap:.6rem; margin-bottom:1.5rem; }
.buka-feature { display:flex; align-items:center; gap:.625rem; font-size:.8375rem; font-weight:600; color:#64748b; }
.buka-feature i { width:22px; text-align:center; color:#a78bfa; font-size:.9rem; }
.buka-card { background:var(--dk-card); border-radius:var(--dk-radius); border:1px solid var(--dk-border); box-shadow:0 4px 24px rgba(15,23,42,.1); padding:2rem; }
.buka-card-top { display:flex; align-items:center; gap:.75rem; margin-bottom:1.5rem; padding-bottom:1.25rem; border-bottom:1px solid var(--dk-border); }
.buka-card-icon { width:44px; height:44px; background:rgba(167,139,250,.12); color:#a78bfa; border-radius:.75rem; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; border:1px solid rgba(167,139,250,.2); }
.buka-card-title { font-size:1rem; font-weight:800; color:var(--dk-head); margin:0; }
.buka-card-sub { font-size:.78rem; color:#4b5563; margin:0; }
.buka-label { font-size:.8rem; font-weight:700; color:#64748b; margin-bottom:.35rem; text-transform:uppercase; letter-spacing:.05em; }
.buka-display-amount { font-size:2.25rem; font-weight:800; color:var(--dk-head); letter-spacing:-.5px; line-height:1; }
.buka-display-sub { font-size:.78rem; color:#4b5563; margin-top:.2rem; }
.buka-amount-display { background:rgba(255,255,255,.02); border:1.5px solid var(--dk-border); border-radius:.75rem; padding:1rem 1.25rem; margin-bottom:1.25rem; transition:border-color .2s; }
.buka-amount-display:focus-within { border-color:rgba(167,139,250,.4); box-shadow:0 0 0 3px rgba(167,139,250,.08); }
.buka-amount-input { width:100%; background:transparent; border:0; outline:none; font-size:1.75rem; font-weight:800; color:var(--dk-head); font-family:inherit; padding:.1rem 0; }
.buka-amount-input::placeholder { color:#2d3748; }
.buka-presets { display:grid; grid-template-columns:repeat(3,1fr); gap:.5rem; margin-bottom:1.25rem; }
.buka-preset { padding:.5rem; border:1.5px solid var(--dk-border); border-radius:.625rem; background:rgba(255,255,255,.02); color:#64748b; font-size:.78rem; font-weight:700; cursor:pointer; transition:all .15s; text-align:center; }
.buka-preset:hover { background:rgba(167,139,250,.1); border-color:rgba(167,139,250,.3); color:#a78bfa; }
.buka-note-area { width:100%; min-height:70px; border:1.5px solid var(--dk-border); border-radius:.75rem; padding:.75rem .875rem; font-size:.875rem; font-family:inherit; color:var(--dk-head); background:rgba(255,255,255,.02); outline:none; resize:vertical; transition:border-color .2s; }
.buka-note-area:focus { border-color:rgba(167,139,250,.4); box-shadow:0 0 0 3px rgba(167,139,250,.08); }
.buka-note-area::placeholder { color:#2d3748; }
.buka-submit-btn { width:100%; padding:.85rem; background:linear-gradient(135deg,#a78bfa,#7c3aed); border:0; border-radius:.75rem; color:#fff; font-size:1rem; font-weight:800; cursor:pointer; transition:all .18s; box-shadow:0 4px 16px rgba(167,139,250,.4); display:flex; align-items:center; justify-content:center; gap:.5rem; }
.buka-submit-btn:hover { background:linear-gradient(135deg,#b49bff,#8b5cf6); transform:translateY(-1px); box-shadow:0 6px 24px rgba(167,139,250,.5); }
.buka-info-card { background:rgba(167,139,250,.06); border:1px solid rgba(167,139,250,.12); border-radius:.75rem; padding:1rem 1.125rem; display:flex; gap:.75rem; align-items:flex-start; margin-top:1.25rem; }
.buka-info-card i { color:#a78bfa; margin-top:.1rem; font-size:.9rem; }
.buka-info-card p { font-size:.8125rem; color:#4b5563; margin:0; line-height:1.55; }
:root { --buka-border:#e2e8f0; --buka-ink:#172033; }
.buka-wrap { width:100%; padding:.75rem 0 2rem; }
.buka-container { display:grid; grid-template-columns:minmax(0,.92fr) minmax(0,1.08fr); gap:2rem; max-width:1160px; margin:0 auto; align-items:center; }
.buka-info-panel { min-width:0; padding:.5rem 0; }
.buka-brand-badge { display:inline-flex; align-items:center; gap:.45rem; padding:.35rem .75rem; margin-bottom:.8rem; border:1px solid #dbeafe; border-radius:50rem; background:#eff6ff; color:#2563eb; font-size:.72rem; font-weight:800; letter-spacing:.04em; text-transform:uppercase; }
.buka-info-title { margin:0 0 .55rem; color:var(--buka-ink); font-size:1.65rem; line-height:1.2; font-weight:800; }
.buka-info-desc { max-width:42rem; margin:0 0 1.15rem; color:#64748b; font-size:.88rem; line-height:1.65; }
.buka-steps { display:grid; gap:.55rem; margin-bottom:1rem; }
.buka-step { display:grid; grid-template-columns:32px minmax(0,1fr); align-items:start; gap:.65rem; padding:.65rem .7rem; border:1px solid var(--buka-border); border-radius:.6rem; background:rgba(255,255,255,.78); }
.buka-step-num { display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:.5rem; background:#e0e7ff; color:#4338ca; font-size:.75rem; font-weight:900; }
.buka-step-title { margin:.05rem 0 .15rem; color:#334155; font-size:.78rem; font-weight:800; }
.buka-step-desc { color:#64748b; font-size:.73rem; line-height:1.45; }
.buka-tips { display:flex; align-items:flex-start; gap:.65rem; padding:.75rem .8rem; border:1px solid #bae6fd; border-radius:.6rem; background:#f0f9ff; }
.buka-tips-icon { margin-top:.15rem; color:#0284c7; }
.buka-tips-title { margin-bottom:.2rem; color:#0c4a6e; font-size:.75rem; font-weight:800; }
.buka-tips-list { margin:0; padding-left:1rem; color:#475569; font-size:.7rem; line-height:1.55; }
.buka-form-panel { min-width:0; overflow:hidden; border:1px solid var(--buka-border); border-radius:.85rem; background:#fff; box-shadow:0 12px 34px rgba(15,23,42,.1); }
.buka-banner { position:relative; overflow:hidden; padding:1.1rem 1.25rem; background:linear-gradient(120deg,#4338ca,#2563eb); color:#fff; }
.buka-banner-dots { position:absolute; inset:0; opacity:.12; background-image:radial-gradient(#fff 1px,transparent 1px); background-size:14px 14px; }
.buka-banner-inner { position:relative; display:grid; grid-template-columns:42px minmax(0,1fr); column-gap:.7rem; align-items:center; }
.buka-banner-icon { display:flex; grid-row:span 2; align-items:center; justify-content:center; width:40px; height:40px; border:1px solid rgba(255,255,255,.25); border-radius:.65rem; background:rgba(255,255,255,.14); font-size:1rem; }
.buka-banner-title { margin:0; font-size:1rem; line-height:1.3; font-weight:800; }
.buka-banner-sub { margin:.15rem 0 0; color:rgba(255,255,255,.82); font-size:.75rem; }
.buka-kasir-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.65rem; padding:.75rem 1.15rem; border-bottom:1px solid #edf0f5; background:#fbfcfe; }
.buka-kasir-left { display:flex; align-items:center; gap:.55rem; }
.buka-kasir-avatar { display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; background:#e0e7ff; color:#4338ca; font-size:.78rem; font-weight:900; }
.buka-kasir-label { color:#94a3b8; font-size:.64rem; font-weight:700; text-transform:uppercase; }
.buka-kasir-name { color:#334155; font-size:.75rem; font-weight:800; }
.buka-kasir-time { display:flex; align-items:center; gap:.35rem; color:#64748b; font-size:.7rem; font-weight:600; }
.buka-form-body { padding:1rem 1.15rem 1.1rem; }
.buka-alert-error { display:flex; align-items:flex-start; gap:.5rem; margin-bottom:.8rem; padding:.65rem .75rem; border:1px solid #fecaca; border-radius:.55rem; background:#fff1f2; color:#b91c1c; font-size:.75rem; line-height:1.45; }
.buka-field { margin-bottom:.85rem; }
.buka-label { display:flex; align-items:center; gap:.4rem; margin-bottom:.4rem; color:#475569; font-size:.72rem; font-weight:800; text-transform:uppercase; }
.buka-label i { color:#4f46e5; }
.buka-input-wrap { display:flex; align-items:center; min-height:54px; overflow:hidden; border:1px solid #cbd5e1; border-radius:.6rem; background:#fff; transition:border-color .18s,box-shadow .18s; }
.buka-input-wrap:focus-within { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.buka-prefix { padding:0 .8rem; color:#64748b; font-size:.9rem; font-weight:800; }
.buka-input { width:100%; min-width:0; min-height:52px; padding:.55rem .8rem .55rem 0; border:0; outline:0; background:transparent; color:#172033; font:inherit; font-size:1.35rem; font-weight:800; font-variant-numeric:tabular-nums; }
.buka-input::placeholder { color:#cbd5e1; }
.buka-hint { display:flex; align-items:flex-start; gap:.35rem; margin:.4rem 0 0; color:#64748b; font-size:.68rem; line-height:1.45; }
.buka-hint i { margin-top:.1rem; color:#0891b2; }
.buka-presets { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.4rem; margin:0; }
.buka-preset { min-height:36px; padding:.4rem .5rem; border:1px solid #dbe2ea; border-radius:.5rem; background:#f8fafc; color:#475569; font:inherit; font-size:.7rem; font-weight:800; cursor:pointer; transition:all .16s ease; }
.buka-preset:hover { border-color:#818cf8; background:#eef2ff; color:#4338ca; }
.buka-preset-clear { color:#dc2626; }
.buka-preset-clear:hover { border-color:#fca5a5; background:#fff1f2; color:#b91c1c; }
.buka-preview { display:flex; align-items:center; justify-content:space-between; gap:.75rem; margin:.2rem 0 .8rem; padding:.65rem .75rem; border:1px solid #c7d2fe; border-radius:.55rem; background:#eef2ff; }
.buka-preview-lbl { color:#475569; font-size:.7rem; font-weight:700; }
.buka-preview-val { color:#3730a3; font-size:.9rem; font-weight:900; font-variant-numeric:tabular-nums; text-align:right; }
.buka-submit { display:flex; align-items:center; justify-content:center; gap:.5rem; width:100%; min-height:44px; padding:.65rem .8rem; border:0; border-radius:.55rem; background:linear-gradient(120deg,#4f46e5,#2563eb); color:#fff; font:inherit; font-size:.8rem; font-weight:800; cursor:pointer; box-shadow:0 4px 12px rgba(79,70,229,.2); transition:transform .16s ease,box-shadow .16s ease; }
.buka-submit:hover { transform:translateY(-1px); box-shadow:0 7px 16px rgba(79,70,229,.25); }
.buka-submit-arrow { margin-left:auto; }
.buka-cancel { display:flex; align-items:center; justify-content:center; gap:.35rem; margin-top:.65rem; color:#64748b; font-size:.72rem; font-weight:700; text-decoration:none; }
.buka-cancel:hover { color:#334155; }
.input-flash { animation:bukaFlash .32s ease; }
@keyframes bukaFlash { 50% { background:#eef2ff; } }
@media(max-width:900px) { .buka-container { grid-template-columns:1fr; gap:1rem; max-width:640px; } .buka-info-panel { padding:0; } }
@media(max-width:520px) { .buka-wrap { padding-top:.25rem; } .buka-info-title { font-size:1.35rem; } .buka-container { gap:.75rem; } .buka-banner,.buka-form-body { padding-left:.85rem; padding-right:.85rem; } .buka-kasir-bar { padding:.65rem .85rem; } .buka-presets { gap:.3rem; } .buka-preset { padding:.35rem .25rem; font-size:.66rem; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input    = document.getElementById('saldo_awal');
    const preview  = document.getElementById('previewVal');

    const fmt  = n => new Intl.NumberFormat('id-ID').format(n);
    const raw  = () => parseInt(input.value.replace(/\D/g,'')) || 0;

    function updatePreview() {
        const n = raw();
        preview.textContent = 'Rp ' + fmt(n);
    }

    window.setPreset = amount => {
        input.value = fmt(amount);
        updatePreview();
        input.classList.add('input-flash');
        setTimeout(() => input.classList.remove('input-flash'), 320);
    };

    window.addPreset = amount => {
        input.value = fmt(raw() + amount);
        updatePreview();
        input.classList.add('input-flash');
        setTimeout(() => input.classList.remove('input-flash'), 320);
    };

    input.addEventListener('input', function() {
        const n = raw();
        this.value = fmt(n);
        updatePreview();
    });
    input.addEventListener('focus', function() {
        if (this.value === '0') this.value = '';
    });
    input.addEventListener('blur', function() {
        if (!this.value) { this.value = '0'; updatePreview(); }
    });

    document.getElementById('formBukaShift').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitShift');
        document.getElementById('submitIcon').className = 'fas fa-spinner fa-spin';
        document.getElementById('submitText').textContent = 'Memproses…';
        btn.disabled = true;
    });

    updatePreview();
});
</script>
