<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex items-center justify-between mb-3">
    <h4 class="mb-0 font-bold">Kelola Tata Letak Meja</h4>
    <button class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md" onclick="openModal()">
        <i class="fas fa-plus-circle"></i> Tambah Meja Baru
    </button>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
    <?php if(empty($meja)): ?>
        <div class="col-12 text-center py-5 text-muted bg-white rounded-3 border">
            <i class="fas fa-border-all fa-3x d-block mb-3 opacity-25 text-secondary"></i>
            <span class="fw-semibold">Belum ada tata letak meja terdaftar.</span>
        </div>
    <?php else: ?>
        <?php foreach($meja as $m): ?>
        <?php 
        $is_available = ($m->status == 'Tersedia');
        $card_theme   = $is_available ? 'border-success' : 'border-danger';
        $badge_class  = $is_available ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
        $icon_color   = $is_available ? 'text-success' : 'text-danger';
        ?>
        <div>
            <div class="ndc-card text-center h-100">
                <div class="p-3">
                    <div class="bg-gray-50 rounded-full inline-flex items-center justify-center mb-2 w-12 h-12 mx-auto">
                        <i class="fas fa-chair fa-lg <?= $icon_color ?>"></i>
                    </div>
                    <div class="font-bold text-gray-900 mb-1">Meja <?= htmlspecialchars($m->nomor_meja) ?></div>
                    <div class="mb-3">
                        <span class="inline-block px-3 py-1 rounded-full text-sm <?= $is_available ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' ?>"><?= $m->status ?></span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <a href="<?= base_url('meja/status/'.$m->id_meja) ?>" class="inline-flex items-center justify-center p-1.5 border rounded bg-white/5" title="Ganti Status (Tersedia/Terisi)">
                            <i class="fas fa-sync text-gray-500"></i>
                        </a>
                        <button class="inline-flex items-center justify-center p-1.5 border rounded bg-white/5" onclick="editModal(<?= $m->id_meja ?>, '<?= addslashes($m->nomor_meja) ?>', '<?= $m->status ?>')" title="Edit Meja">
                            <i class="fas fa-edit text-indigo-500"></i>
                        </button>
                        <button type="button" class="inline-flex items-center justify-center p-1.5 border rounded bg-white/5" onclick="confirmDeleteMeja(<?= $m->id_meja ?>)" title="Hapus Meja">
                            <i class="fas fa-trash-alt text-red-500"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal (Bootstrap structure retained; Tailwind utilities applied) -->
<div class="modal fade" id="formModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white text-slate-900 rounded-lg overflow-hidden">
            <div class="modal-header px-4 py-3 border-b border-neutral-800">
                <h5 class="modal-title fw-bold text-lg" id="modalTitle">Tambah Meja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('meja/simpan') ?>" method="POST">
                <div class="modal-body px-4 py-4">
                    <input type="hidden" name="id_meja" id="id_meja">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small block text-sm text-slate-300">Nomor / Kode Meja</label>
                        <input type="text" name="nomor_meja" id="nomor_meja" class="ndc-input w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required placeholder="Cth: M-01 atau VIP-1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small block text-sm text-slate-300">Status Awal</label>
                        <select name="status" id="status" class="ndc-input w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Terisi">Terisi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer flex items-center justify-end gap-2 px-4 py-3 border-t border-neutral-800">
                    <button type="button" class="px-3 py-2 rounded-md bg-slate-700 text-slate-200 hover:bg-slate-600" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white">
                        <i class="fas fa-save mr-1"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let formModal;
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('formModal');
    document.body.appendChild(modalElement);
    formModal = new bootstrap.Modal(modalElement);
});
function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Meja Baru';
    document.getElementById('id_meja').value = '';
    document.getElementById('nomor_meja').value = '';
    document.getElementById('status').value = 'Tersedia';
    formModal.show();
}
function editModal(id, nomor, status) {
    document.getElementById('modalTitle').innerText = 'Edit Konfigurasi Meja';
    document.getElementById('id_meja').value = id;
    document.getElementById('nomor_meja').value = nomor;
    document.getElementById('status').value = status;
    formModal.show();
}
function confirmDeleteMeja(id) {
    Swal.fire({
        title: 'Hapus Meja?',
        text: "Tata letak meja ini akan dihapus secara permanen dari sistem POS.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('meja/hapus/') ?>" + id;
        }
    });
}
</script>
