<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$id_menu     = isset($menu) ? $menu->id_menu     : '';
$id_kategori = isset($menu) ? $menu->id_kategori : '';
$nama_menu   = isset($menu) ? $menu->nama_menu   : '';
$harga       = isset($menu) ? $menu->harga       : '';
$stok        = isset($menu) ? $menu->stok        : '';
$foto        = isset($menu) ? $menu->foto        : '';
?>

<div class="flex items-center gap-2 mb-3">
    <a href="<?= base_url('menu') ?>" class="inline-flex items-center justify-center px-2 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <h4 class="mb-0 font-bold"><?= $title ?></h4>
</div>

<form action="<?= base_url('menu/simpan') ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_menu" value="<?= $id_menu ?>">

    <div class="row g-4">
        <!-- Form Panel -->
        <div class="col-md-8">
            <div class="ndc-card">
                <div class="px-4 py-2 border-b font-semibold">Informasi Produk</div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-600">Nama Menu / Produk</label>
                        <input type="text" name="nama_menu" class="ndc-input w-full px-3 py-2 rounded-md border"
                               value="<?= htmlspecialchars($nama_menu) ?>" required
                               placeholder="Contoh: Nasi Goreng Spesial">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="block text-sm font-semibold text-gray-600">Kategori</label>
                            <select name="id_kategori" class="ndc-input w-full px-3 py-2 rounded-md border" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach($kategori as $k): ?>
                                    <option value="<?= $k->id_kategori ?>" <?= ($k->id_kategori == $id_kategori) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($k->nama_kategori) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="block text-sm font-semibold text-gray-600">Stok Tersedia</label>
                            <input type="number" name="stok" class="ndc-input w-full px-3 py-2 rounded-md border"
                                   value="<?= $stok ?>" required min="0" placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-600">Harga Satuan (Rp)</label>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-2 bg-gray-100 rounded-l-md border">Rp</span>
                            <input type="text" id="harga_display" class="ndc-input flex-1 px-3 py-2 border rounded-r-md"
                                   onkeyup="formatInputRupiah(this)"
                                   value="<?= $harga ? number_format($harga, 0, ',', '.') : '' ?>"
                                   required placeholder="0">
                            <input type="hidden" name="harga" id="harga" value="<?= $harga ?>">
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 border-t text-right">
                    <a href="<?= base_url('menu') ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-200 text-gray-700 rounded-md mr-2">Batal</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-md">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                </div>
            </div>
        </div>

        <!-- Foto Panel -->
        <div class="col-md-4">
            <div class="ndc-card">
                <div class="px-3 py-2 border-b font-semibold">Foto Produk</div>
                <div class="p-4 text-center">
                    <input type="file" name="foto" id="fotoInput" class="hidden" accept="image/*" onchange="previewImage(this)">
                    <div class="border rounded p-2 mb-3" style="min-height: 180px; cursor: pointer; background: #f8f9fa;"
                         onclick="document.getElementById('fotoInput').click()">
                        <?php if($foto): ?>
                            <img src="<?= base_url('assets/images/menu/'.$foto) ?>" id="previewImg"
                                 class="rounded" style="max-height: 180px; width:auto; display:block; margin:0 auto;">
                        <?php else: ?>
                            <div id="uploadPlaceholder" class="py-4 text-gray-500">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                                <div class="small">Klik untuk pilih foto</div>
                                <div class="text-gray-400" style="font-size:11px;">Max 2MB</div>
                            </div>
                            <img id="previewImg" class="hidden rounded" style="max-height: 180px;">
                        <?php endif; ?>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-3 py-2 border rounded-md w-full text-sm"
                            onclick="document.getElementById('fotoInput').click()">
                        <i class="fas fa-image"></i> <?= $foto ? 'Ganti Foto' : 'Pilih Foto' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(input) {
    const preview     = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function formatInputRupiah(input) {
    let value  = input.value.replace(/[^\d]/g, '');
    let rupiah = parseInt(value || 0).toLocaleString('id-ID');
    input.value = rupiah;
    document.getElementById('harga').value = value;
}
</script>
