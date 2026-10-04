<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex items-center justify-between mb-3">
    <h4 class="mb-0 font-extrabold text-lg">Manajemen Menu & Produk</h4>
    <a href="<?= base_url('menu/tambah') ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        <i class="fas fa-plus-circle"></i>
        <span>Tambah Menu Baru</span>
    </a>
</div>

<div class="ndc-card">
    <div class="px-3 py-3 border-b bg-white/5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="md:w-1/3 w-full">
                <div class="flex items-center bg-gray-50 rounded-md overflow-hidden">
                    <span class="px-3 text-gray-400"><i class="fas fa-search"></i></span>
                    <input type="text" id="tableSearch" class="w-full px-3 py-2 bg-transparent outline-none" placeholder="Cari menu..." onkeyup="searchTable()">
                </div>
            </div>
            <div class="text-sm text-gray-500 md:text-right">
                Total: <strong><?= count($menu) ?></strong> produk terdaftar
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="menuTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500" style="width:80px;">Foto</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Nama Menu / SKU</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Kategori</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Harga Jual</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500">Stok Laci</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 text-right" style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($menu as $m): ?>
                    <tr>
                        <td class="px-3 py-3">
                            <img src="<?= base_url('assets/images/menu/' . ($m->foto ? $m->foto : 'default.png')) ?>"
                                 class="w-12 h-12 object-cover rounded-md border" onerror="this.src='https://placehold.co/50x50?text=None'">
                        </td>
                        <td class="px-3 py-3">
                            <div class="font-semibold text-gray-900"><?= htmlspecialchars($m->nama_menu) ?></div>
                            <div class="text-sm text-gray-500">ID: #<?= $m->id_menu ?></div>
                        </td>
                        <td class="px-3 py-3">
                            <span class="inline-block bg-gray-50 px-3 py-1 rounded-full text-sm text-gray-700"><?= htmlspecialchars($m->nama_kategori) ?></span>
                        </td>
                        <td class="px-3 py-3 font-extrabold text-indigo-600">Rp <?= number_format($m->harga, 0, ',', '.') ?></td>
                        <td class="px-3 py-3">
                            <?php if($m->stok <= 0): ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm bg-red-50 text-red-600">Habis</span>
                            <?php elseif($m->stok < 5): ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm bg-amber-50 text-amber-700">Hampir Habis (<?= $m->stok ?>)</span>
                            <?php else: ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm bg-emerald-50 text-emerald-700">Tersedia (<?= $m->stok ?>)</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-3 text-right">
                            <a href="<?= base_url('menu/edit/'.$m->id_menu) ?>" class="inline-flex items-center p-2 bg-white/5 border rounded-md mr-1" title="Edit Menu">
                                <i class="fas fa-edit text-indigo-600"></i>
                            </a>
                            <button type="button" class="inline-flex items-center p-2 bg-white/5 border rounded-md" title="Hapus Menu" onclick="confirmDelete(<?= $m->id_menu ?>)">
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

<script>
function searchTable() {
    let input = document.getElementById("tableSearch").value.toLowerCase();
    let rows = document.querySelectorAll("#menuTable tbody tr");
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Produk?',
        text: "Data menu ini akan dihapus secara permanen dari sistem database.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('menu/hapus/') ?>" + id;
        }
    });
}
</script>

<style>
/* Responsive table stack for small screens (menu list) */
@media (max-width: 640px) {
    #menuTable thead { display: none; }
    #menuTable tbody tr { display: block; border-bottom: 1px solid rgba(0,0,0,0.06); padding: .6rem 0; }
    #menuTable td { display: block; padding: .25rem 0; }
    #menuTable td.text-right { text-align: left !important; }
    .w-12 { width: 56px !important; height: 56px !important; }
}
</style>

