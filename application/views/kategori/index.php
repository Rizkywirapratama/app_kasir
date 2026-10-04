<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex items-center justify-between mb-3">
    <h4 class="mb-0 font-bold">Manajemen Kategori Menu</h4>
    <button class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md" onclick="openModal()">
        <i class="fas fa-plus-circle"></i> Tambah Kategori Baru
    </button>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
    <?php if(empty($kategori)): ?>
        <div class="col-span-4 text-center py-8 text-gray-400 bg-white/3 rounded-lg border">
            <i class="fas fa-tags fa-3x mb-3 opacity-25"></i>
            <div class="font-semibold">Belum ada data kategori menu terdaftar.</div>
        </div>
    <?php else: ?>
        <?php foreach($kategori as $k): ?>
        <div>
            <div class="ndc-card h-100">
                <div class="p-3 flex items-center gap-3">
                    <div class="bg-indigo-50 text-indigo-600 rounded-md flex items-center justify-center w-10 h-10 flex-shrink-0">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold truncate"><?= htmlspecialchars($k->nama_kategori) ?></div>
                        <small class="text-gray-400 text-xs">ID: #<?= $k->id_kategori ?></small>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="inline-flex items-center justify-center p-1.5 border rounded bg-white/5" onclick="editModal(<?= $k->id_kategori ?>, '<?= addslashes($k->nama_kategori) ?>')" title="Edit Kategori">
                            <i class="fas fa-edit text-indigo-500"></i>
                        </button>
                        <button type="button" class="inline-flex items-center justify-center p-1.5 border rounded bg-white/5" onclick="confirmDeleteKategori(<?= $k->id_kategori ?>)" title="Hapus Kategori">
                            <i class="fas fa-trash-alt text-red-500"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Tambah/Edit Kategori (Bootstrap structure kept for JS; Tailwind utilities added) -->
<div class="modal fade" id="formModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white text-slate-900 rounded-lg overflow-hidden">
            <div class="modal-header flex items-center justify-between px-4 py-3 border-b border-neutral-800">
                <h5 class="modal-title fw-bold text-lg" id="modalTitle">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('kategori/simpan') ?>" method="POST">
                <div class="modal-body px-4 py-4">
                    <input type="hidden" name="id_kategori" id="id_kategori">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small block text-sm text-slate-300">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori"
                               class="ndc-input w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required placeholder="Contoh: Coffee Hot / Blended Drinks">
                    </div>
                </div>
                <div class="modal-footer flex items-center justify-end gap-2 px-4 py-3 border-t border-neutral-800">
                    <button type="button" class="px-3 py-2 rounded-md bg-slate-700 text-slate-200 hover:bg-slate-600" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white">
                        <i class="fas fa-save mr-1"></i> Simpan Kategori
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
    document.getElementById('modalTitle').innerText = 'Tambah Kategori Baru';
    document.getElementById('id_kategori').value = '';
    document.getElementById('nama_kategori').value = '';
    formModal.show();
}
function editModal(id, nama) {
    document.getElementById('modalTitle').innerText = 'Edit Kategori';
    document.getElementById('id_kategori').value = id;
    document.getElementById('nama_kategori').value = nama;
    formModal.show();
}
function confirmDeleteKategori(id) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: "Kategori menu ini akan dihapus secara permanen dari sistem.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('kategori/hapus/') ?>" + id;
        }
    });
}
</script>
