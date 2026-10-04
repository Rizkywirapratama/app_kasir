<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="user-page-header flex items-center justify-between flex-wrap gap-3 mb-4">
    <div>
        <h4 class="mb-1 font-extrabold text-lg">Kelola Pengguna</h4>
        <p class="mb-0 text-sm text-gray-500">Kelola akses akun dan pembayaran QRIS merchant.</p>
    </div>
    <div class="flex items-center flex-wrap gap-2">
        <a href="<?= base_url('pengguna/export') ?>" class="user-secondary-action">
            <i class="fas fa-file-csv"></i><span>Ekspor CSV</span>
        </a>
        <button type="button" class="user-primary-action" onclick="openModal()">
            <i class="fas fa-user-plus"></i><span>Tambah Pengguna</span>
        </button>
    </div>
</div>

<section class="user-status-summary mb-4" aria-label="Ringkasan status pengguna">
    <article class="ndc-card user-status-summary-card">
        <span class="user-status-summary-icon is-active"><i class="fas fa-user-check" aria-hidden="true"></i></span>
        <div>
            <div class="user-status-summary-label">Pengguna Aktif</div>
            <div class="user-status-summary-count"><?= (int)$status_pengguna->active ?></div>
        </div>
    </article>
    <article class="ndc-card user-status-summary-card">
        <span class="user-status-summary-icon is-inactive"><i class="fas fa-user-slash" aria-hidden="true"></i></span>
        <div>
            <div class="user-status-summary-label">Pengguna Nonaktif</div>
            <div class="user-status-summary-count"><?= (int)$status_pengguna->inactive ?></div>
        </div>
    </article>
</section>

<section class="ndc-card user-qris-card mb-4" aria-labelledby="qrisSettingsTitle">
    <div class="user-qris-preview">
        <?php if (!empty($qris_image)): ?>
            <img src="<?= base_url('assets/images/payment/' . rawurlencode($qris_image)) ?>" alt="QRIS merchant aktif">
        <?php else: ?>
            <div class="user-qris-placeholder"><i class="fas fa-qrcode"></i><span>QRIS belum diunggah</span></div>
        <?php endif; ?>
    </div>
    <div class="user-qris-content">
        <div class="user-qris-eyebrow"><i class="fas fa-building-columns"></i> Pembayaran</div>
        <h5 id="qrisSettingsTitle" class="mb-1 font-extrabold">QRIS Merchant</h5>
        <p class="mb-3 text-sm text-gray-500">Gambar ini ditampilkan kasir saat memilih metode QRIS. Gunakan gambar PNG atau JPG yang jelas.</p>
        <form action="<?= base_url('pengguna/upload_qris') ?>" method="POST" enctype="multipart/form-data" class="user-qris-form">
            <label class="user-file-picker" for="qrisImageInput"><i class="fas fa-image"></i><span>Pilih gambar QR</span></label>
            <input type="file" name="qris_image" id="qrisImageInput" accept="image/png,image/jpeg" required>
            <span class="user-file-name" id="qrisFileName">PNG/JPG, maks. 3 MB</span>
            <button type="submit" class="user-primary-action"><i class="fas fa-cloud-arrow-up"></i><span>Simpan QRIS</span></button>
        </form>
    </div>
</section>

<div class="ndc-card">
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500" width="70">Avatar</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Nama Lengkap</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Username</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Role Akses</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Status</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 text-right" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($pengguna as $u): ?>
                    <?php $is_active = (int)$u->is_active === 1; ?>
                    <tr class="<?= $is_active ? '' : 'user-row-disabled' ?>">
                        <td class="px-3 py-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-sm"
                                 style="background:<?= ($u->role=='admin') ? '#4f46e5' : '#f59e0b' ?>;">
                                <?= strtoupper(substr($u->nama, 0, 1)) ?>
                            </div>
                        </td>
                        <td class="px-3 py-3">
                            <div class="font-semibold text-gray-900"><?= htmlspecialchars($u->nama) ?></div>
                            <small class="text-sm text-gray-500">User ID: #<?= $u->id ?></small>
                        </td>
                        <td class="px-3 py-3"><span class="inline-block bg-gray-50 px-2 py-1 rounded text-indigo-600 font-semibold">@<?= htmlspecialchars($u->username) ?></span></td>
                        <td class="px-3 py-3">
                            <?php if($u->role == 'admin'): ?>
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold"><i class="fas fa-shield-alt"></i>Administrator</span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-semibold"><i class="fas fa-cash-register"></i>Kasir / Staff</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-3">
                            <span class="user-status-badge <?= $is_active ? 'is-active' : 'is-inactive' ?>">
                                <span class="user-status-dot"></span><?= $is_active ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="px-3 py-3 text-right">
                            <div class="inline-flex items-center gap-2 justify-end">
                                <button type="button" class="user-icon-action edit-user-button" title="Edit pengguna"
                                        data-user-id="<?= (int)$u->id ?>"
                                        data-user-name="<?= htmlspecialchars($u->nama, ENT_QUOTES, 'UTF-8') ?>"
                                        data-user-username="<?= htmlspecialchars($u->username, ENT_QUOTES, 'UTF-8') ?>"
                                        data-user-role="<?= htmlspecialchars($u->role, ENT_QUOTES, 'UTF-8') ?>">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="user-icon-action reset-user-button" title="Reset password"
                                        data-user-id="<?= (int)$u->id ?>" data-user-name="<?= htmlspecialchars($u->nama, ENT_QUOTES, 'UTF-8') ?>">
                                    <i class="fas fa-key"></i>
                                </button>
                                <form action="<?= base_url('pengguna/ubah_status') ?>" method="POST" class="user-status-form">
                                    <input type="hidden" name="id" value="<?= (int)$u->id ?>">
                                    <button type="submit" class="user-icon-action <?= $is_active ? 'deactivate-user' : 'activate-user' ?>"
                                            title="<?= $is_active ? 'Nonaktifkan akun' : 'Aktifkan akun' ?>"
                                            <?= ((int)$u->id === (int)$this->session->userdata('id_user')) ? 'disabled' : '' ?>>
                                        <i class="fas <?= $is_active ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered max-w-lg">
        <div class="modal-content rounded-lg overflow-hidden shadow-lg">
            <div class="px-4 py-3 border-b bg-white/5 flex items-center justify-between">
                <h5 class="text-lg font-bold" id="modalTitle">Tambah Pengguna Baru</h5>
                <button type="button" data-bs-dismiss="modal" aria-label="Close" class="text-gray-500 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
            <form action="<?= base_url('pengguna/simpan') ?>" method="POST">
                <div class="px-4 py-4">
                    <input type="hidden" name="id" id="user_id">
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-600">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" class="ndc-input w-full px-3 py-2 rounded-md border" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-600">Username</label>
                        <input type="text" name="username" id="username" class="ndc-input w-full px-3 py-2 rounded-md border" required placeholder="Contoh: budis123">
                    </div>
                    <div class="mb-3" id="passwordField">
                        <label class="block text-sm font-semibold text-gray-600 flex items-center justify-between">
                            <span>Password Akses</span>
                            <span id="pw_help" class="text-indigo-600 text-sm" style="display:none;">(Reset melalui tombol kunci)</span>
                        </label>
                        <input type="password" name="password" id="password" class="ndc-input w-full px-3 py-2 rounded-md border" minlength="8" required placeholder="Minimal 8 karakter" autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-600">Role / Tingkat Akses</label>
                        <select name="role" id="role" class="ndc-input w-full px-3 py-2 rounded-md border" required>
                            <option value="kasir">Kasir / Staff Operasional</option>
                            <option value="admin">Administrator (Akses Penuh)</option>
                        </select>
                    </div>
                </div>
                <div class="px-4 py-3 border-t flex items-center justify-end gap-2">
                    <button type="button" data-bs-dismiss="modal" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-200 text-gray-700 rounded-md">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md">
                        <i class="fas fa-save"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b">
                <h5 class="mb-0 font-bold" id="resetPasswordTitle">Reset Password</h5>
                <p class="mb-0 mt-1 text-sm text-gray-500" id="resetPasswordName"></p>
            </div>
            <form action="<?= base_url('pengguna/reset_password') ?>" method="POST">
                <div class="px-4 py-4">
                    <input type="hidden" name="id" id="resetPasswordUserId">
                    <label for="newUserPassword" class="block text-sm font-semibold text-gray-600 mb-1">Password baru</label>
                    <input type="password" name="password" id="newUserPassword" class="ndc-input w-full px-3 py-2 rounded-md border" minlength="8" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                </div>
                <div class="px-4 py-3 border-t flex justify-end gap-2">
                    <button type="button" data-bs-dismiss="modal" class="user-secondary-action">Batal</button>
                    <button type="submit" class="user-primary-action"><i class="fas fa-key"></i><span>Simpan Password</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let userModal;
let resetPasswordModal;
document.addEventListener('DOMContentLoaded', function() {
    userModal = new bootstrap.Modal(document.getElementById('formModal'));
    resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    document.querySelectorAll('.edit-user-button').forEach(button => button.addEventListener('click', function() {
        editModal(this.dataset.userId, this.dataset.userName, this.dataset.userUsername, this.dataset.userRole);
    }));
    document.querySelectorAll('.reset-user-button').forEach(button => button.addEventListener('click', function() {
        document.getElementById('resetPasswordUserId').value = this.dataset.userId;
        document.getElementById('resetPasswordName').textContent = this.dataset.userName;
        document.getElementById('newUserPassword').value = '';
        resetPasswordModal.show();
    }));
    document.querySelectorAll('.user-status-form').forEach(form => form.addEventListener('submit', function(event) {
        event.preventDefault();
        const button = this.querySelector('button');
        const deactivate = button.classList.contains('deactivate-user');
        Swal.fire({
            title: deactivate ? 'Nonaktifkan akun?' : 'Aktifkan akun?',
            text: deactivate ? 'Pengguna tidak dapat login selama akun nonaktif.' : 'Pengguna dapat login kembali setelah akun aktif.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: deactivate ? 'Nonaktifkan' : 'Aktifkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: deactivate ? '#dc2626' : '#059669'
        }).then(result => { if (result.isConfirmed) this.submit(); });
    }));
    const qrisInput = document.getElementById('qrisImageInput');
    qrisInput?.addEventListener('change', function() {
        document.getElementById('qrisFileName').textContent = this.files[0]?.name || 'PNG/JPG, maks. 3 MB';
    });
});
function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Pengguna Baru';
    document.getElementById('user_id').value = '';
    document.getElementById('nama').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password').required = true;
    document.getElementById('passwordField').style.display = '';
    document.getElementById('pw_help').style.display = 'none';
    document.getElementById('role').value = 'kasir';
    userModal.show();
}
function editModal(id, nama, username, role) {
    document.getElementById('modalTitle').innerText = 'Edit Pengguna';
    document.getElementById('user_id').value = id;
    document.getElementById('nama').value = nama;
    document.getElementById('username').value = username;
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('passwordField').style.display = 'none';
    document.getElementById('pw_help').style.display = 'inline';
    document.getElementById('role').value = role;
    userModal.show();
}
</script>

<style>
.user-primary-action,.user-secondary-action,.user-file-picker { display:inline-flex; align-items:center; justify-content:center; gap:.45rem; min-height:36px; padding:.45rem .7rem; border:1px solid #dbe2ea; border-radius:.5rem; background:#fff; color:#475569; font:inherit; font-size:.75rem; font-weight:700; text-decoration:none; cursor:pointer; transition:all .16s ease; }
.user-primary-action { border-color:#4f46e5; background:#4f46e5; color:#fff; }
.user-primary-action:hover { border-color:#4338ca; background:#4338ca; color:#fff; }
.user-secondary-action:hover,.user-file-picker:hover { border-color:#a5b4fc; background:#eef2ff; color:#4338ca; }
.user-status-summary { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.75rem; margin-bottom:.75rem; }
.user-status-summary-card { display:flex; align-items:center; gap:.75rem; padding:1rem; margin-bottom:0; }
.user-status-summary-icon { display:grid; place-items:center; width:40px; height:40px; flex:0 0 40px; border-radius:.7rem; font-size:1rem; }
.user-status-summary-icon.is-active { background:#ecfdf5; color:#047857; }
.user-status-summary-icon.is-inactive { background:#f1f5f9; color:#64748b; }
.user-status-summary-label { color:#64748b; font-size:.72rem; font-weight:700; }
.user-status-summary-count { margin-top:.1rem; color:#172033; font-size:1.25rem; font-weight:900; line-height:1.2; }
.user-qris-card { display:grid; grid-template-columns:112px minmax(0,1fr); gap:1rem; align-items:center; padding:1rem; }
.user-qris-preview { display:flex; align-items:center; justify-content:center; width:112px; height:112px; overflow:hidden; border:1px solid #e2e8f0; border-radius:.6rem; background:#f8fafc; }
.user-qris-preview img { width:100%; height:100%; object-fit:contain; }
.user-qris-placeholder { display:grid; justify-items:center; gap:.4rem; color:#94a3b8; font-size:.65rem; text-align:center; }
.user-qris-placeholder i { color:#6366f1; font-size:1.7rem; }
.user-qris-eyebrow { margin-bottom:.25rem; color:#4f46e5; font-size:.68rem; font-weight:800; text-transform:uppercase; }
.user-qris-content h5 { color:#172033; font-size:.9rem; }
.user-qris-form { display:flex; align-items:center; flex-wrap:wrap; gap:.5rem; }
.user-qris-form input[type=file] { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; clip-path:inset(50%); }
.user-file-name { color:#64748b; font-size:.68rem; }
.user-status-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .5rem; border-radius:50rem; font-size:.68rem; font-weight:800; white-space:nowrap; }
.user-status-badge.is-active { background:#ecfdf5; color:#047857; }
.user-status-badge.is-inactive { background:#f1f5f9; color:#64748b; }
.user-status-dot { width:7px; height:7px; border-radius:50%; background:currentColor; }
.user-row-disabled { background:#fafafa; color:#94a3b8; }
.user-icon-action { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border:1px solid #e2e8f0; border-radius:.45rem; background:#fff; color:#4f46e5; cursor:pointer; transition:all .15s ease; }
.user-icon-action:hover:not(:disabled) { border-color:#c7d2fe; background:#eef2ff; }
.user-icon-action.deactivate-user { color:#dc2626; }
.user-icon-action.activate-user { color:#059669; }
.user-icon-action:disabled { opacity:.4; cursor:not-allowed; }
.user-status-form { margin:0; }
#formModal .modal-content,#resetPasswordModal .modal-content { background:#fff; border-color:#e2e8f0; color:#172033; }
#formModal .modal-header,#formModal .modal-footer,#resetPasswordModal .modal-header,#resetPasswordModal .modal-footer { background:#f8fafc; border-color:#e2e8f0 !important; }
#formModal .modal-header h5,#resetPasswordModal .modal-header h5 { color:#172033; }
#formModal label,#resetPasswordModal label { color:#475569 !important; }
#formModal .text-gray-500,#resetPasswordModal .text-gray-500 { color:#64748b !important; }
#formModal .ndc-input,#resetPasswordModal .ndc-input { border-color:#cbd5e1 !important; background:#fff !important; color:#172033 !important; box-shadow:none; }
#formModal .ndc-input::placeholder,#resetPasswordModal .ndc-input::placeholder { color:#94a3b8; opacity:1; }
#formModal .ndc-input:focus,#resetPasswordModal .ndc-input:focus { border-color:#6366f1 !important; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
#formModal select option { background:#fff; color:#172033; }
#formModal [data-bs-dismiss="modal"] { color:#64748b; }
@media(max-width:700px) {
    .user-status-summary { gap:.5rem; }
    .user-status-summary-card { gap:.55rem; padding:.75rem; }
    .user-status-summary-icon { width:34px; height:34px; flex-basis:34px; }
    .user-status-summary-label { font-size:.65rem; }
    .user-status-summary-count { font-size:1.1rem; }
    .user-qris-card { grid-template-columns:80px minmax(0,1fr); gap:.7rem; padding:.75rem; }
    .user-qris-preview { width:80px; height:80px; }
    .user-qris-form { align-items:flex-start; }
    .user-file-name { width:100%; }
    .user-page-header .user-primary-action,.user-page-header .user-secondary-action { font-size:.68rem; }
    .user-icon-action { width:30px; height:30px; }
}
</style>
