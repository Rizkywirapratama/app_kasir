<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex items-center justify-between mb-3">
    <h4 class="mb-0 font-bold">Manajemen CRM & Member</h4>
    <button class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-user-plus"></i> Tambah Member Baru
    </button>
</div>

<div class="ndc-card">
    <div class="p-3 border-b">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="w-full md:w-1/2">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-search"></i></span>
                    <input type="text" id="memberSearch" class="pl-10 pr-3 py-2 w-full rounded-md bg-gray-50 border" placeholder="Cari nama member atau no telepon..." onkeyup="searchMemberTable()">
                </div>
            </div>
            <div class="md:auto">
                <span class="text-gray-500 text-sm">Total: <strong><?= count($member) ?></strong> member aktif</span>
            </div>
        </div>
    </div>
    <div class="p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="memberTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Nama Member</th>
                        <th class="px-4 py-2 text-left">Nomor Telepon</th>
                        <th class="px-4 py-2 text-center">Potongan Diskon (%)</th>
                        <th class="px-4 py-2 text-center">Loyalty Points</th>
                        <th class="px-4 py-2 text-right" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php foreach($member as $m): ?>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-bold text-gray-900"><?= htmlspecialchars($m->nama_member) ?></div>
                            <small class="text-gray-400 text-xs">ID: #<?= $m->id_member ?></small>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-600"><?= htmlspecialchars($m->telepon) ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-sm <?= $m->diskon_persen > 0 ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-50 text-gray-600 border border-gray-100' ?> font-bold">
                                <?= $m->diskon_persen ?>%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-50 text-yellow-600 border border-yellow-100 font-bold">
                                <i class="fas fa-star text-yellow-500"></i>
                                <span><?= number_format($m->total_poin) ?> Poin</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" class="inline-flex items-center px-3 py-1 border rounded bg-white/5" title="Hapus Member" onclick="confirmDeleteMember(<?= $m->id_member ?>)">
                                <i class="fas fa-trash-alt text-red-500"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Member (Tailwind utilities added; Bootstrap structure retained) -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white text-slate-900 rounded-lg overflow-hidden">
            <div class="modal-header px-4 py-3 border-b border-neutral-800">
                <h5 class="modal-title fw-bold text-lg">Tambah Member Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('member/tambah') ?>" method="post">
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small block text-sm text-slate-300">Nama Lengkap</label>
                        <input type="text" name="nama_member" class="ndc-input w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small block text-sm text-slate-300">Nomor Telepon</label>
                        <input type="text" name="telepon" class="ndc-input w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required placeholder="Contoh: 08123456xxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small block text-sm text-slate-300">Diskon Member (%)</label>
                        <input type="number" name="diskon_persen" class="ndc-input w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required min="0" max="100" value="10" placeholder="Contoh: 10">
                        <div class="form-text text-muted small text-slate-400">Member akan mendapatkan diskon otomatis di Kasir.</div>
                    </div>
                </div>
                <div class="modal-footer flex items-center justify-end gap-2 px-4 py-3 border-t border-neutral-800">
                    <button type="button" class="px-3 py-2 rounded-md bg-slate-700 text-slate-200 hover:bg-slate-600" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white">Simpan Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('modalTambah');
    document.body.appendChild(modalElement);
});

function searchMemberTable() {
    let input = document.getElementById("memberSearch").value.toLowerCase();
    let rows = document.querySelectorAll("#memberTable tbody tr");
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

function confirmDeleteMember(id) {
    Swal.fire({
        title: 'Hapus Member?',
        text: "Data member ini akan dihapus permanen. Poin loyalitas yang terkumpul juga akan terhapus.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('member/hapus/') ?>" + id;
        }
    });
}
</script>
