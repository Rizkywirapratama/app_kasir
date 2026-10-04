<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ===== POS SHELL LAYOUT ===== -->
<div class="pos-shell" id="posShell">

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- PANEL KIRI: KATALOG MENU PRODUK                         -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <div class="pos-products-panel md:pr-4">
        <!-- Search & Barcode Bar -->
        <div class="pos-search-bar">
            <div class="pos-search-inner flex items-center gap-2">
                <i class="fas fa-search pos-search-ico"></i>
                <input type="text" id="searchMenu" class="pos-search-input"
                       placeholder="Cari menu atau ketik SKU..."
                       onkeyup="filterMenu()" autocomplete="off">
                <button type="button" class="pos-search-clear-btn" id="btnClearSearch" onclick="clearSearch()" style="display:none;">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <button type="button" class="pos-scan-btn inline-flex items-center gap-2" onclick="triggerBarcodeScan()" title="Scan Barcode / SKU">
                <i class="fas fa-barcode"></i>
                <span class="hidden md:inline">Scan</span>
            </button>
        </div>

        <!-- Kategori Pills -->
        <div class="pos-category-container">
            <button type="button" class="pos-category-btn active" data-kategori="All" onclick="filterCategory('All', this)">
                <i class="fas fa-grid-2"></i> Semua Menu
            </button>
            <?php
            $cats = [];
            foreach ($menu as $menu_item) {
                $cats[$menu_item->id_kategori] = $menu_item->nama_kategori;
            }
            foreach($cats as $category_id => $category_name): ?>
                <button type="button" class="pos-category-btn" data-kategori="<?= (int)$category_id ?>" onclick="filterCategory(this.dataset.kategori, this)">
                    <?= htmlspecialchars($category_name) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Grid Produk (Modern CSS Auto-Fit Grid) -->
        <div class="pos-products-grid" id="menuList">
            <?php foreach($menu as $m): ?>
            <?php $is_out = ($m->stok <= 0); ?>
              <div class="pos-product-card menu-item <?= $is_out ? 'out-of-stock' : '' ?> shadow-sm hover:shadow-md"
                 data-id="<?= $m->id_menu ?>"
                 data-nama="<?= strtolower($m->nama_menu) ?>"
                 data-kategori="<?= (int)$m->id_kategori ?>"
                 data-harga="<?= $m->harga ?>"
                 data-stok="<?= $m->stok ?>"
                 onclick="addToCart(<?= $m->id_menu ?>, '<?= addslashes($m->nama_menu) ?>', <?= $m->harga ?>, <?= $m->stok ?>)">
                <?php if($is_out): ?>
                    <span class="badge-stock inline-flex items-center gap-1 px-2 py-1 text-xs font-bold bg-red-600 text-white"><i class="fas fa-ban"></i> Habis</span>
                <?php else: ?>
                    <span class="badge-stock inline-flex items-center gap-1 px-2 py-1 text-xs font-bold bg-emerald-600 text-white">Stok: <?= $m->stok ?></span>
                <?php endif; ?>
                <div class="img-container">
                    <img src="<?= $m->foto && file_exists('./assets/images/menu/'.$m->foto) ? base_url('assets/images/menu/'.$m->foto) : 'https://placehold.co/160x120?text='.urlencode($m->nama_menu) ?>"
                         alt="<?= htmlspecialchars($m->nama_menu) ?>"
                         loading="lazy"
                         onerror="this.src='https://placehold.co/160x120?text=Food'">
                </div>
                <div class="card-body p-2">
                    <div class="pos-product-title" title="<?= htmlspecialchars($m->nama_menu) ?>"><?= htmlspecialchars($m->nama_menu) ?></div>
                    <div class="pos-product-price text-sm">Rp <?= number_format($m->harga, 0, ',', '.') ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- PANEL TENGAH: KERANJANG KASIR (TIDY & ERGONOMIC)        -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <div class="pos-cart-panel">
        <div class="pos-cart-card is-empty md:pl-4">

            <!-- ── Cart Header ── -->
            <div class="cart-header">
                <div class="cart-header-top flex items-center justify-between">
                    <div class="cart-title-group flex items-center gap-3">
                        <div class="cart-icon-wrap">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <div class="cart-title">Keranjang Kasir</div>
                            <div class="cart-invoice" id="cartInvoiceLabel"><?= $invoice ?></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="cart-clear-btn" id="btnClearCart" onclick="clearCartConfirm()" title="Kosongkan Keranjang (Esc)">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </div>
                </div>

                <!-- Order Type & Table Selection -->
                <div class="cart-row-2col flex items-center gap-2">
                    <div class="cart-type-toggle">
                        <input type="radio" class="btn-check" name="tipe_pesanan_opt" id="opt_takeaway" value="Take-away" checked onchange="toggleOrderType('Take-away')">
                        <label class="cart-type-lbl" for="opt_takeaway">
                            <i class="fas fa-bag-shopping"></i> Take-away
                        </label>
                        <input type="radio" class="btn-check" name="tipe_pesanan_opt" id="opt_dinein" value="Dine-in" onchange="toggleOrderType('Dine-in')">
                        <label class="cart-type-lbl" for="opt_dinein">
                            <i class="fas fa-utensils"></i> Dine-in
                        </label>
                    </div>
                    <select id="selectMeja" class="cart-select" disabled onchange="updateMeja(this.value)">
                        <option value="0">Pilih Meja</option>
                        <?php foreach($meja as $mj): ?>
                            <option value="<?= $mj->id_meja ?>">Meja <?= htmlspecialchars($mj->nomor_meja) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Customer / Member Selector -->
                <div class="cart-member-row flex items-center gap-2">
                    <div class="cart-member-icon" title="Member / Pelanggan"><i class="fas fa-user-tag"></i></div>
                    <select id="selectMember" class="cart-select flex-1" onchange="applyMember(this.value)">
                        <option value="">Pelanggan Umum (Tanpa Member)</option>
                        <?php foreach($member as $mb): ?>
                            <option value="<?= $mb->id_member ?>"
                                    data-nama="<?= htmlspecialchars($mb->nama_member) ?>"
                                    data-diskon="<?= $mb->diskon_persen ?>">
                                <?= htmlspecialchars($mb->nama_member) ?> (Diskon <?= $mb->diskon_persen ?>%)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="cart-add-member-btn inline-flex items-center justify-center" data-bs-toggle="modal" data-bs-target="#modalTambahMember" title="Daftar Member Baru">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
            </div>

            <!-- ── Cart Items List (Scrollable) ── -->
            <div class="cart-items-wrap" id="cartList">
                <div class="cart-empty" id="emptyCart">
                    <div class="cart-empty-icon"><i class="fas fa-receipt"></i></div>
                    <p class="cart-empty-title">Keranjang masih kosong</p>
                    <small class="cart-empty-sub">Pilih menu di sisi kiri untuk menambahkan pesanan</small>
                    <div class="cart-empty-actions">
                        <button type="button" class="cart-empty-action" onclick="focusMenuCatalog()">
                            <i class="fas fa-utensils"></i> Pilih Menu
                        </button>
                        <button type="button" class="cart-empty-action cart-empty-action-secondary" onclick="triggerBarcodeScan()">
                            <i class="fas fa-barcode"></i> Scan SKU
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── Cart Summary ── -->
            <div class="cart-summary">
                <div class="checkout-feature-tabs" role="tablist" aria-label="Fitur transaksi">
                    <button type="button" class="checkout-feature-tab" id="tabDiskon" role="tab"
                            aria-selected="false" aria-controls="discountFeaturePanel"
                            onclick="switchCheckoutFeature('discount', this)">
                        <i class="fas fa-tag" aria-hidden="true"></i>
                        <span>Diskon</span>
                        <span class="checkout-tab-indicator" id="discountTabIndicator" style="display:none;" aria-label="Diskon aktif"></span>
                    </button>
                    <button type="button" class="checkout-feature-tab active" id="tabPembayaran" role="tab"
                            aria-selected="true" aria-controls="paymentFeaturePanel"
                            onclick="switchCheckoutFeature('payment', this)">
                        <i class="fas fa-wallet" aria-hidden="true"></i>
                        <span>Pembayaran</span>
                    </button>
                </div>
                <div class="cart-summary-row">
                    <span>Subtotal</span>
                    <span id="displaySubtotal" class="fw-bold">Rp 0</span>
                </div>

                <!-- ══ ENHANCED DISCOUNT SECTION ══ -->
                <section class="cart-discount-section" id="discountFeaturePanel" role="tabpanel"
                         aria-labelledby="tabDiskon" aria-hidden="true" style="display:none;">
                    <div class="cart-discount-section-header">
                        <div class="cart-section-heading cart-discount-heading" id="cartDiscountHeading">
                            <i class="fas fa-tag" aria-hidden="true"></i>
                            <span>Diskon &amp; Promo</span>
                        </div>
                        <!-- Active discount badge -->
                        <div id="discountActiveBadge" class="discount-active-badge" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="discountBadgeText">Hemat!</span>
                        </div>
                    </div>

                    <!-- Member Discount Row -->
                    <div class="cart-summary-row text-danger" id="memberDiscountRow" style="display:none;">
                        <span class="disc-row-label">
                            <span class="disc-pill disc-pill-member"><i class="fas fa-id-card"></i> Member</span>
                            <span id="memberDiscountPercent">0</span>% OFF
                        </span>
                        <span id="displayDiskonMember" class="disc-amount">- Rp 0</span>
                    </div>


                    <!-- Promo / Kupon Kode -->
                    <div class="promo-code-row">
                        <div class="promo-input-wrap" id="promoInputWrap">
                            <i class="fas fa-ticket-alt promo-icon"></i>
                            <input type="text" id="inputPromoCode" class="promo-input"
                                   placeholder="Kode Promo / Kupon..."
                                   oninput="this.value=this.value.toUpperCase()"
                                   onkeydown="if(event.key==='Enter'){applyPromoCode();event.preventDefault();}">
                            <button type="button" class="promo-apply-btn" id="btnApplyPromo" onclick="applyPromoCode()">
                                Pakai
                            </button>
                        </div>
                        <!-- Quick Promo Suggestion Chips -->
                        <div class="promo-chips-scroll" id="promoChipsScroll">
                            <button type="button" class="promo-suggest-chip" onclick="applyPromoDirect('HEMAT10')" title="Diskon 10%"><i class="fas fa-bolt"></i> HEMAT10</button>
                            <button type="button" class="promo-suggest-chip" onclick="applyPromoDirect('DISKON15')" title="Diskon 15%"><i class="fas fa-bolt"></i> DISKON15</button>
                            <button type="button" class="promo-suggest-chip" onclick="applyPromoDirect('SPESIAL25')" title="Diskon 25%"><i class="fas fa-bolt"></i> SPESIAL25</button>
                            <button type="button" class="promo-suggest-chip" onclick="applyPromoDirect('OPENING50')" title="Diskon 50%"><i class="fas fa-bolt"></i> OPENING50</button>
                        </div>
                        <div class="promo-badge-row" id="promoActiveBadgeRow" style="display:none;">
                            <span class="promo-active-badge">
                                <i class="fas fa-gift"></i>
                                <span id="promoCodeApplied">PROMO10</span> — <span id="promoDiscText">10% OFF</span>
                            </span>
                            <button type="button" class="promo-remove-btn" onclick="removePromoCode()" title="Hapus kode promo">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Additional Discount (Manual %) -->
                    <div class="cart-discount-row">
                        <label for="inputDiskonTambahan" class="disc-add-label">
                            <i class="fas fa-percent" style="color:#6366f1;font-size:.7rem;"></i>
                            Diskon Tambahan
                        </label>
                        <div class="cart-discount-control">
                            <button type="button" class="discount-step-btn" onclick="adjustAdditionalDiscount(-5)" aria-label="Kurangi diskon">
                                <i class="fas fa-minus" aria-hidden="true"></i>
                            </button>
                            <input type="number" id="inputDiskonTambahan" name="diskon_tambahan_persen" class="cart-disc-input"
                                   value="0" min="0" max="100" step="1" inputmode="numeric" oninput="hitungTotalAkhir()" aria-label="Diskon tambahan dalam persen">
                            <span class="cart-disc-pct">%</span>
                            <button type="button" class="discount-step-btn" onclick="adjustAdditionalDiscount(5)" aria-label="Tambah diskon">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Quick Discount Presets -->
                    <div class="disc-preset-chips">
                        <span class="disc-preset-label">Preset:</span>
                        <button type="button" class="disc-preset-chip" onclick="setAdditionalDiscount(0)">0%</button>
                        <button type="button" class="disc-preset-chip" onclick="setAdditionalDiscount(5)">5%</button>
                        <button type="button" class="disc-preset-chip" onclick="setAdditionalDiscount(10)">10%</button>
                        <button type="button" class="disc-preset-chip" onclick="setAdditionalDiscount(15)">15%</button>
                        <button type="button" class="disc-preset-chip" onclick="setAdditionalDiscount(20)">20%</button>
                        <button type="button" class="disc-preset-chip" onclick="setAdditionalDiscount(50)">50%</button>
                    </div>

                    <!-- Promo Discount row (hidden until promo applied) -->
                    <div class="cart-summary-row text-danger" id="promoDiscountRow" style="display:none;">
                        <span class="disc-row-label">
                            <span class="disc-pill disc-pill-promo"><i class="fas fa-gift"></i> Promo</span>
                            <span id="promoDiscPct">0</span>% OFF
                        </span>
                        <span id="displayDiskonPromo" class="disc-amount">- Rp 0</span>
                    </div>

                    <!-- Additional discount row (hidden when 0) -->
                    <div class="cart-summary-row text-danger" id="additionalDiscountRow" style="display:none;">
                        <span class="disc-row-label">
                            <span class="disc-pill disc-pill-extra"><i class="fas fa-scissors"></i> Ekstra</span>
                            <span id="additionalDiscPct">0</span>% OFF
                        </span>
                        <span id="displayDiskonTambahan" class="disc-amount">- Rp 0</span>
                    </div>

                    <!-- Total Discount Summary -->
                    <div class="disc-total-row" id="discTotalRow" style="display:none;">
                        <span><i class="fas fa-arrow-trend-down"></i> Total Hemat</span>
                        <span id="displayDiskon">- Rp 0</span>
                    </div>
                </section>

                <div class="cart-summary-row">
                    <span>PPN (11%)</span>
                    <span id="displayTax">Rp 0</span>
                </div>
                <div class="cart-summary-total">
                    <div>
                        <span class="total-label">Grand Total</span>
                        <div class="total-items-count" id="totalItemsCount">0 item</div>
                    </div>
                    <span id="displayTotal" class="total-amount">Rp 0</span>
                </div>
            </div>

            <!-- ── Cart Actions / Checkout Form ── -->
            <div class="cart-actions">
                <form id="formSimpan" action="<?= base_url('penjualan/simpan') ?>" method="POST">
                    <input type="hidden" name="invoice" value="<?= $invoice ?>">
                    <input type="hidden" name="total_harga" id="inputTotal" value="0">
                    <input type="hidden" name="diskon" id="inputDiskonVal" value="0">
                    <input type="hidden" name="kode_promo" id="inputKodePromo" value="">
                    <input type="hidden" name="qris_confirmed" id="inputQRISConfirmed" value="0">
                    <input type="hidden" name="bayar" id="inputBayar" value="0">
                    <input type="hidden" name="kembalian" id="inputKembalian" value="0">
                    <input type="hidden" name="tipe_pesanan" id="inputTipePesanan" value="Take-away">
                    <input type="hidden" name="id_meja" id="inputIdMeja" value="0">
                    <input type="hidden" name="id_member" id="inputIdMember" value="">
                    <input type="hidden" name="nama_pelanggan" id="inputNamaPelanggan" value="Umum">
                    <input type="hidden" name="poin_didapat" id="inputPoinDidapat" value="0">
                    <input type="hidden" name="mode_pembayaran" id="inputModePembayaran" value="Cash">
                    <input type="hidden" name="nomor_referensi" id="inputNomorReferensi" value="">

                    <div class="payment-feature-panel" id="paymentFeaturePanel" role="tabpanel"
                        aria-labelledby="tabPembayaran" aria-hidden="false">
                    <!-- ══ ENHANCED PAYMENT SECTION ══ -->
                    <div class="cart-section-heading cart-payment-heading">
                        <i class="fas fa-wallet" aria-hidden="true"></i>
                        <span>Pembayaran</span>
                        <!-- Payment status dot -->
                        <span class="pay-status-dot" id="payStatusDot"></span>
                    </div>

                    <!-- Payment Mode Selector (4 methods) -->
                    <div class="pay-method-header">
                        <span class="pay-method-label">Pilih Metode</span>
                    </div>
                    <div class="pay-method-group" role="group" aria-label="Metode pembayaran">
                        <button type="button" class="pay-method-btn active" data-payment-mode="Cash"
                                aria-pressed="true" onclick="togglePaymentMode('Cash', this)" id="pmCash">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Cash</span>
                        </button>
                        <button type="button" class="pay-method-btn" data-payment-mode="QRIS"
                                aria-pressed="false" onclick="togglePaymentMode('QRIS', this)" id="pmQRIS">
                            <i class="fas fa-qrcode"></i>
                            <span>QRIS</span>
                        </button>
                        <button type="button" class="pay-method-btn" data-payment-mode="Debit/Kredit"
                                aria-pressed="false" onclick="togglePaymentMode('Debit/Kredit', this)" id="pmKartu">
                            <i class="fas fa-credit-card"></i>
                            <span>Kartu</span>
                        </button>
                        <button type="button" class="pay-method-btn" data-payment-mode="Transfer"
                                aria-pressed="false" onclick="togglePaymentMode('Transfer', this)" id="pmTransfer">
                            <i class="fas fa-building-columns"></i>
                            <span>Transfer</span>
                        </button>
                    </div>

                    <!-- ── CASH AREA ── -->
                    <div id="cashArea" class="pay-area">
                        <div class="cash-header">
                            <label class="cash-label" for="inputBayarFormatted">Nominal Pembayaran</label>
                        </div>
                        <div class="cash-input-wrap">
                            <span class="cash-prefix">Rp</span>
                            <input type="text" id="inputBayarFormatted" class="cash-input"
                                   placeholder="Masukkan nominal" inputmode="numeric" autocomplete="off"
                                   oninput="formatAndCalculateCash()">
                        </div>
                        <div class="quick-cash-section">
                            <span class="cash-label">Nominal Cepat</span>
                            <div class="quick-cash-list" id="quickCashList"></div>
                        </div>
                        <div class="kembalian-row">
                            <span class="kembalian-label">Kembalian:</span>
                            <span class="kembalian-val" id="displayKembalian">Rp 0</span>
                        </div>
                    </div>

                    <!-- ── QRIS AREA ── -->
                    <div id="qrisArea" class="pay-area" style="display:none;">
                        <div class="qris-container">
                            <div class="qris-card">
                                <div class="qris-header">
                                    <span class="qris-brand"><i class="fas fa-qrcode"></i> QRIS</span>
                                    <span class="qris-subtitle">Konfirmasi manual setelah dana masuk</span>
                                </div>
                                <div class="qris-qr-wrap" id="qrisQrWrap">
                                    <?php if (!empty($qris_image)): ?>
                                        <img class="qris-merchant-image" src="<?= base_url('assets/images/payment/' . rawurlencode($qris_image)) ?>" alt="QRIS merchant untuk pembayaran">
                                    <?php else: ?>
                                        <div class="qris-merchant-placeholder">
                                            <i class="fas fa-qrcode" aria-hidden="true"></i>
                                            <strong>QRIS Merchant</strong>
                                            <span>Admin belum mengunggah QR pembayaran</span>
                                        </div>
                                    <?php endif; ?>
                                    <svg class="qris-qr-svg" aria-hidden="true" style="display:none" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Corner markers -->
                                        <rect x="5" y="5" width="25" height="25" rx="3" fill="none" stroke="#1e1b4b" stroke-width="4"/>
                                        <rect x="10" y="10" width="15" height="15" rx="1" fill="#1e1b4b"/>
                                        <rect x="70" y="5" width="25" height="25" rx="3" fill="none" stroke="#1e1b4b" stroke-width="4"/>
                                        <rect x="75" y="10" width="15" height="15" rx="1" fill="#1e1b4b"/>
                                        <rect x="5" y="70" width="25" height="25" rx="3" fill="none" stroke="#1e1b4b" stroke-width="4"/>
                                        <rect x="10" y="75" width="15" height="15" rx="1" fill="#1e1b4b"/>
                                        <!-- Data modules (decorative) -->
                                        <rect x="36" y="5" width="5" height="5" fill="#1e1b4b"/><rect x="44" y="5" width="5" height="5" fill="#1e1b4b"/><rect x="52" y="5" width="5" height="5" fill="#1e1b4b"/><rect x="60" y="5" width="5" height="5" fill="#1e1b4b"/>
                                        <rect x="36" y="13" width="5" height="5" fill="#1e1b4b"/><rect x="52" y="13" width="5" height="5" fill="#1e1b4b"/>
                                        <rect x="5" y="36" width="5" height="5" fill="#1e1b4b"/><rect x="13" y="36" width="5" height="5" fill="#1e1b4b"/><rect x="36" y="36" width="5" height="5" fill="#1e1b4b"/><rect x="44" y="36" width="5" height="5" fill="#1e1b4b"/><rect x="60" y="36" width="5" height="5" fill="#1e1b4b"/><rect x="68" y="36" width="5" height="5" fill="#1e1b4b"/><rect x="84" y="36" width="5" height="5" fill="#1e1b4b"/><rect x="92" y="36" width="5" height="5" fill="#1e1b4b"/>
                                        <rect x="5" y="44" width="5" height="5" fill="#1e1b4b"/><rect x="21" y="44" width="5" height="5" fill="#1e1b4b"/><rect x="44" y="44" width="5" height="5" fill="#1e1b4b"/><rect x="52" y="44" width="5" height="5" fill="#1e1b4b"/><rect x="76" y="44" width="5" height="5" fill="#1e1b4b"/><rect x="92" y="44" width="5" height="5" fill="#1e1b4b"/>
                                        <rect x="5" y="52" width="5" height="5" fill="#1e1b4b"/><rect x="13" y="52" width="5" height="5" fill="#1e1b4b"/><rect x="36" y="52" width="5" height="5" fill="#1e1b4b"/><rect x="60" y="52" width="5" height="5" fill="#1e1b4b"/><rect x="68" y="52" width="5" height="5" fill="#1e1b4b"/><rect x="84" y="52" width="5" height="5" fill="#1e1b4b"/>
                                        <rect x="5" y="60" width="5" height="5" fill="#1e1b4b"/><rect x="21" y="60" width="5" height="5" fill="#1e1b4b"/><rect x="44" y="60" width="5" height="5" fill="#1e1b4b"/><rect x="52" y="60" width="5" height="5" fill="#1e1b4b"/><rect x="60" y="60" width="5" height="5" fill="#1e1b4b"/><rect x="76" y="60" width="5" height="5" fill="#1e1b4b"/><rect x="92" y="60" width="5" height="5" fill="#1e1b4b"/>
                                        <rect x="36" y="76" width="5" height="5" fill="#1e1b4b"/><rect x="44" y="76" width="5" height="5" fill="#1e1b4b"/><rect x="60" y="76" width="5" height="5" fill="#1e1b4b"/><rect x="76" y="76" width="5" height="5" fill="#1e1b4b"/><rect x="92" y="76" width="5" height="5" fill="#1e1b4b"/>
                                        <rect x="36" y="84" width="5" height="5" fill="#1e1b4b"/><rect x="52" y="84" width="5" height="5" fill="#1e1b4b"/><rect x="68" y="84" width="5" height="5" fill="#1e1b4b"/><rect x="84" y="84" width="5" height="5" fill="#1e1b4b"/>
                                        <rect x="44" y="92" width="5" height="5" fill="#1e1b4b"/><rect x="60" y="92" width="5" height="5" fill="#1e1b4b"/><rect x="76" y="92" width="5" height="5" fill="#1e1b4b"/>
                                        <!-- Center logo spot -->
                                        <rect x="42" y="42" width="16" height="16" rx="2" fill="#4f46e5"/>
                                        <text x="50" y="53" text-anchor="middle" font-size="9" fill="white" font-family="Arial" font-weight="bold">Q</text>
                                    </svg>
                                </div>
                                <div class="qris-amount-lbl">Total Pembayaran</div>
                                <div class="qris-amount" id="qrisAmount">Rp 0</div>
                                <div class="qris-note">
                                    <i class="fas fa-circle-info"></i>
                                    Pastikan dana masuk sebelum konfirmasi
                                </div>
                            </div>
                            <!-- QRIS Confirm button -->
                            <button type="button" class="qris-confirm-btn" onclick="konfirmasiQRIS()">
                                <i class="fas fa-check-double"></i> Konfirmasi Sudah Dibayar
                            </button>
                            <input type="hidden" id="inputBayarQRIS" value="0">
                            <input type="hidden" id="inputKembalianQRIS" value="0">
                        </div>
                    </div>

                    <!-- ── KARTU AREA ── -->
                    <div id="kartuArea" class="pay-area" style="display:none;">
                        <div class="card-pay-container">
                            <div class="card-type-tabs">
                                <button type="button" class="card-type-btn active" onclick="selectCardType('Debit', this)">
                                    <i class="fas fa-credit-card"></i> Debit
                                </button>
                                <button type="button" class="card-type-btn" onclick="selectCardType('Kredit', this)">
                                    <i class="fas fa-credit-card"></i> Kredit
                                </button>
                            </div>
                            <div class="card-info-wrap">
                                <label class="cash-label">Nama Bank / Penerbit</label>
                                <div class="bank-chips">
                                    <button type="button" class="bank-chip active" onclick="selectBank('BCA', this)">BCA</button>
                                    <button type="button" class="bank-chip" onclick="selectBank('Mandiri', this)">Mandiri</button>
                                    <button type="button" class="bank-chip" onclick="selectBank('BNI', this)">BNI</button>
                                    <button type="button" class="bank-chip" onclick="selectBank('BRI', this)">BRI</button>
                                    <button type="button" class="bank-chip" onclick="selectBank('Lainnya', this)">Lainnya</button>
                                </div>
                                <label class="cash-label mt-2">No. Referensi / Approval</label>
                                <div class="ref-input-wrap">
                                    <i class="fas fa-hashtag ref-icon"></i>
                                    <input type="text" id="inputCardRef" class="ref-input"
                                           placeholder="Kode approval 6 digit..."
                                           oninput="document.getElementById('inputNomorReferensi').value=this.value">
                                </div>
                                <div class="card-amount-row">
                                    <span class="cash-label">Total Tagihan</span>
                                    <span class="card-total-amt" id="cardTotalAmt">Rp 0</span>
                                </div>
                            </div>
                            <input type="hidden" id="inputBayarKartu" value="0">
                            <input type="hidden" id="inputKembalianKartu" value="0">
                        </div>
                    </div>

                    <!-- ── TRANSFER AREA ── -->
                    <div id="transferArea" class="pay-area" style="display:none;">
                        <div class="transfer-container">
                            <div class="transfer-bank-select">
                                <label class="cash-label">Bank Tujuan</label>
                                <div class="bank-chips">
                                    <button type="button" class="bank-chip active" onclick="selectTransferBank('BCA', this)">BCA</button>
                                    <button type="button" class="bank-chip" onclick="selectTransferBank('Mandiri', this)">Mandiri</button>
                                    <button type="button" class="bank-chip" onclick="selectTransferBank('BNI', this)">BNI</button>
                                    <button type="button" class="bank-chip" onclick="selectTransferBank('BRI', this)">BRI</button>
                                </div>
                            </div>
                            <div class="transfer-rekening-box">
                                <div class="rek-row">
                                    <span class="rek-label">Atas Nama</span>
                                    <span class="rek-val">NOL DERAJAT COFFEE</span>
                                </div>
                                <div class="rek-row">
                                    <span class="rek-label">No. Rekening</span>
                                    <div style="display:flex;align-items:center;gap:.4rem;">
                                        <span class="rek-val" id="transferRekening">123-456-7890</span>
                                        <button type="button" class="rek-copy-btn" onclick="copyTransferRekening()" title="Salin No. Rekening">
                                            <i class="fas fa-copy"></i> Salin
                                        </button>
                                    </div>
                                </div>
                                <div class="rek-row">
                                    <span class="rek-label">Jumlah Transfer</span>
                                    <span class="rek-val rek-total" id="transferAmountDisplay">Rp 0</span>
                                </div>
                            </div>
                            <label class="cash-label">Bukti Transfer (No. Referensi)</label>
                            <div class="ref-input-wrap">
                                <i class="fas fa-hashtag ref-icon"></i>
                                <input type="text" id="inputTransferRef" class="ref-input"
                                       placeholder="No. transaksi dari bank..."
                                       oninput="document.getElementById('inputNomorReferensi').value=this.value">
                            </div>
                            <input type="hidden" id="inputBayarTransfer" value="0">
                            <input type="hidden" id="inputKembalianTransfer" value="0">
                        </div>
                    </div>

                    </div>
                    <!-- Checkout Button -->
                    <button type="button" class="btn-checkout" id="btnProses" onclick="simpanTransaksi()" disabled>
                        <i class="fas fa-print"></i>
                        <span>Simpan &amp; Cetak Struk</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

</div><!-- /pos-shell -->

<!-- ===== MODAL CEPAT: CETAK STRUK DIRECT POS ===== -->
<div class="modal fade" id="modalReceiptQuick" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content rounded-xl overflow-hidden border-0 shadow-lg bg-white text-slate-900" style="max-width:100%;">
            <div class="modal-header py-2 px-3 bg-slate-100">
                <h6 class="modal-title fw-bold mb-0 text-slate-900"><i class="fas fa-receipt mr-2 text-amber-500"></i>Struk Pembayaran</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 bg-transparent">
                <!-- Thermal Preview Paper -->
                <div class="receipt-paper" id="receiptPaperArea">
                    <div class="text-center mb-2">
                        <img class="receipt-brand-logo" src="<?= base_url('assets/images/ICON.png') ?>" alt="Logo">
                    </div>
                    <div class="receipt-divider"></div>
                    <div class="flex justify-between receipt-line">
                        <span>Invoice:</span>
                        <strong id="recInvoice">INV-xxxx</strong>
                    </div>
                    <div class="flex justify-between receipt-line">
                        <span>Waktu:</span>
                        <span id="recWaktu">00/00/0000</span>
                    </div>
                    <div class="flex justify-between receipt-line">
                        <span>Kasir:</span>
                        <span id="recKasir">Kasir</span>
                    </div>
                    <div class="flex justify-between receipt-line">
                        <span>Pelanggan:</span>
                        <strong id="recPelanggan">Umum</strong>
                    </div>
                    <div class="flex justify-between receipt-line">
                        <span>Layanan:</span>
                        <strong id="recLayanan">Take-away</strong>
                    </div>
                    <div class="receipt-divider"></div>
                    <!-- Items Table -->
                    <div id="recItemsList"></div>
                    <div class="receipt-divider"></div>
                    <div class="flex justify-between receipt-line">
                        <span>Subtotal</span>
                        <span id="recSubtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between receipt-line text-red-400" id="recDiskonRow">
                        <span>Diskon</span>
                        <span id="recDiskon">- Rp 0</span>
                    </div>
                    <div class="flex justify-between receipt-line">
                        <span>PPN (11%)</span>
                        <span id="recPpn">Rp 0</span>
                    </div>
                    <div class="flex justify-between receipt-line font-bold text-lg mt-1 pt-1 border-t border-dashed border-black">
                        <span>TOTAL</span>
                        <span id="recTotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between receipt-line mt-1">
                        <span>Bayar (<span id="recMode">Cash</span>)</span>
                        <span id="recBayar">Rp 0</span>
                    </div>
                    <div class="flex justify-between receipt-line">
                        <span>Kembalian</span>
                        <span id="recKembalian">Rp 0</span>
                    </div>
                    <div class="receipt-divider"></div>
                    <div class="text-center mt-2">
                        <small class="text-slate-400 block" style="font-size:10px;">Terima kasih atas kunjungan Anda!</small>
                        <small class="text-slate-400 block" style="font-size:9px;">Instagram: @nolderajat.coffee</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 px-3 bg-transparent flex items-center justify-between">
                <button type="button" class="px-3 py-1 border rounded text-slate-300 border-slate-600" data-bs-dismiss="modal">Tutup</button>
                <div class="flex gap-1">
                    <a href="#" id="btnRecFullInvoice" target="_blank" class="px-3 py-1 border rounded text-slate-300 border-slate-600" title="Buka Detail Lengkap">
                        <i class="fas fa-file-invoice"></i> A4
                    </a>
                    <button type="button" class="px-3 py-1 bg-indigo-600 text-white rounded" onclick="printReceiptThermalNow()">
                        <i class="fas fa-print mr-1"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL TAMBAH MEMBER ===== -->
<div class="modal fade" id="modalTambahMember" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xl overflow-hidden bg-white text-slate-900" style="border-radius:1rem;overflow:hidden;border:0;">
            <div class="modal-header bg-gradient-to-r from-indigo-600 to-indigo-500 text-white py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus mr-2"></i>Tambah Member Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('member/tambah') ?>" method="post" id="formQuickMember">
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-slate-600">Nama Lengkap</label>
                        <input type="text" name="nama_member" class="w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required placeholder="Contoh: Andi Wijaya">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-slate-600">No. Telepon</label>
                        <input type="text" name="telepon" class="w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required placeholder="Contoh: 08571234xxxx">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-slate-600">Diskon Member (%)</label>
                        <input type="number" name="diskon_persen" class="w-full px-3 py-2 rounded-md bg-white border border-slate-200 text-slate-900" required min="0" max="100" value="10">
                    </div>
                </div>
                <div class="modal-footer flex items-center justify-end gap-2 px-4 py-3 border-t border-slate-200">
                    <button type="button" class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white">Simpan Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('modalTambahMember');
    document.body.appendChild(modalElement);
});
</script>

<!-- ===== STYLES ===== -->
<style>
.pos-shell {
    display: flex;
    height: calc(100vh - 142px);
    min-height: 560px;
    gap: 0;
    position: relative;
    overflow: hidden;
}

.pos-products-panel {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    height: 100%;
    padding-right: .75rem;
}

/* Search Bar */
.pos-search-bar {
    display: flex;
    gap: .5rem;
    margin-bottom: .65rem;
    flex-shrink: 0;
}
.pos-search-inner {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}
.pos-search-ico {
    position: absolute;
    left: .875rem;
    color: #94a3b8;
    font-size: .875rem;
    pointer-events: none;
}
.pos-search-input {
    width: 100%;
    padding: .55rem 2rem .55rem 2.5rem;
    border:1.5px solid rgba(255,255,255,0.07);
    border-radius: .75rem;
    font-size: .85rem;
    font-weight: 600;
    background:#ffffff;
    color:#0f172a;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    font-family: inherit;
}
.pos-search-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}
.pos-search-clear-btn {
    position: absolute;
    right: .625rem;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: .25rem;
    font-size: .75rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pos-search-clear-btn:hover { color: #ef4444; }

.pos-scan-btn {
    padding: 0 .875rem;
    background:#ffffff;
    border:1.5px solid rgba(255,255,255,0.07);
    border-radius: .75rem;
    font-size: .85rem;
    font-weight: 700;
    color: #6366f1;
    cursor: pointer;
    transition: all .18s;
    display: flex;
    align-items: center;
    gap: .35rem;
}
.pos-scan-btn:hover {
    background: #6366f1;
    color: #fff;
    border-color: #6366f1;
    box-shadow: 0 3px 10px rgba(99,102,241,.25);
}

/* Category Pills */
.pos-category-container {
    display: flex;
    gap: .4rem;
    overflow-x: auto;
    padding-bottom: .5rem;
    margin-bottom: .4rem;
    flex-shrink: 0;
    scrollbar-width: none;
}
.pos-category-container::-webkit-scrollbar { display: none; }
.pos-category-btn {
    white-space: nowrap;
    padding: .35rem .8rem;
    border-radius: 50rem;
    border:1.5px solid rgba(255,255,255,0.07);
    background:#ffffff;
    color:#64748b;
    font-size: .78rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s;
    font-family: inherit;
    display: flex;
    align-items: center;
    gap: .35rem;
}
.pos-category-btn:hover {
    border-color: #6366f1;
    color: #6366f1;
}
.pos-category-btn.active {
    background: #6366f1;
    border-color: #6366f1;
    color: #fff;
    box-shadow: 0 3px 10px rgba(99,102,241,.25);
}

/* Product Grid (Auto-Fit CSS Grid with fixed auto-rows) */
.pos-products-grid {
    display: grid !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    grid-auto-rows: max-content !important;
    gap: .65rem !important;
    overflow-y: auto !important;
    padding-right: 4px;
    align-content: start !important;
    flex: 1;
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 transparent;
}
.pos-products-grid::-webkit-scrollbar { width: 4px; }
.pos-products-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.pos-product-card {
    border:1.5px solid rgba(255,255,255,0.07);
    border-radius: .875rem;
    background:#ffffff;
    overflow: hidden;
    cursor: pointer;
    display: flex !important;
    flex-direction: column !important;
    height: auto !important;
    min-height: 185px !important;
    position: relative;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    user-select: none;
}
#menuList > .pos-product-card[hidden] {
    display: none !important;
}
.pos-product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(15,23,42,.08);
    border-color: #6366f1;
}
.pos-product-card:active {
    transform: scale(0.98);
}
.pos-product-card.out-of-stock {
    opacity: .55;
    filter: grayscale(80%);
    cursor: not-allowed;
}
.badge-stock {
    position: absolute;
    top: .45rem;
    right: .45rem;
    font-size: .62rem;
    font-weight: 800;
    padding: .2rem .45rem;
    border-radius: .4rem;
    z-index: 2;
    box-shadow: 0 2px 4px rgba(0,0,0,.15);
}
.pos-product-card .img-container {
    width: 100%;
    height: 105px !important;
    min-height: 105px !important;
    overflow: hidden;
    background-color:rgba(255,255,255,0.03);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0 !important;
}
.pos-product-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .3s ease;
}
.pos-product-card:hover img { transform: scale(1.08); }
.pos-product-card .card-body {
    padding: .5rem .6rem .65rem !important;
    display: flex !important;
    flex-direction: column !important;
    flex-grow: 1 !important;
    margin-bottom: 0;
}
.pos-product-title {
    font-size: .78rem;
    font-weight: 700;
    color:#f1f5f9;
    margin-bottom: 3px;
    line-height: 1.25;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.pos-product-price {
    font-size: .85rem;
    font-weight: 800;
    color: #4f46e5;
    margin-top: auto !important;
    padding-top: 3px;
    line-height: 1.2;
    font-family: SFMono-Regular,Menlo,monospace;
}

/* ════════════ CART PANEL (MIDDLE) ════════════ */
.pos-cart-panel {
    width: 335px;
    flex-shrink: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.pos-cart-card {
    background:#ffffff;
    border-radius: 1.125rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 20px rgba(15,23,42,.08);
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow: hidden;
}

/* Cart Header */
.cart-header {
    padding: .55rem .85rem .45rem;
    border-bottom:1px solid rgba(255,255,255,0.07);
    flex-shrink: 0;
    background:#ffffff;
}
.cart-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .45rem;
}
.cart-title-group { display: flex; align-items: center; gap: .45rem; }
.cart-icon-wrap {
    width: 28px; height: 28px;
    background: linear-gradient(135deg,#6366f1,#4f46e5);
    border-radius: .5rem;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .8rem;
    box-shadow: 0 3px 8px rgba(99,102,241,.28);
    flex-shrink: 0;
}
.cart-title { font-size: .88rem; font-weight: 800; color:#f1f5f9; line-height: 1.1; }
.cart-invoice { font-size: .65rem; font-weight: 700; color: #94a3b8; font-family: SFMono-Regular,Menlo,monospace; }

/* Quick toggle for side orders */
.btn-side-orders-toggle {
    background:rgba(255,255,255,0.03);
    border:1.5px solid rgba(255,255,255,0.07);
    border-radius: .5rem;
    padding: .2rem .45rem;
    font-size: .72rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: .3rem;
    transition: all .15s;
    font-family: inherit;
}
.btn-side-orders-toggle:hover {
    background: #eef2ff;
    border-color: #6366f1;
    color: #6366f1;
}
.btn-side-orders-toggle .badge {
    font-size: .62rem;
    padding: .15rem .35rem;
}

.cart-clear-btn {
    width: 26px; height: 26px;
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.2);
    border-radius: .45rem;
    color: #ef4444;
    font-size: .72rem;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
}
.cart-clear-btn:hover { background: #ef4444; color: #fff; }

/* 2-col row: Order Type & Table */
.cart-row-2col { display: flex; gap: .4rem; margin-bottom: .4rem; }
.cart-type-toggle {
    flex: 1.2;
    display: flex;
    background:rgba(255,255,255,0.03);
    border:1.5px solid rgba(255,255,255,0.07);
    border-radius: .5rem;
    overflow: hidden;
    padding: 2px;
    gap: 2px;
}
.cart-type-lbl {
    flex: 1;
    text-align: center;
    padding: .2rem .3rem;
    font-size: .72rem;
    font-weight: 700;
    color:#64748b;
    cursor: pointer;
    border-radius: .35rem;
    transition: all .15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .25rem;
    user-select: none;
}
.btn-check:checked + .cart-type-lbl {
    background: linear-gradient(135deg,#6366f1,#4f46e5);
    color: #fff;
    box-shadow: 0 2px 6px rgba(99,102,241,.25);
}
.cart-select {
    padding: .2rem .45rem;
    border:1.5px solid rgba(255,255,255,0.07);
    border-radius: .5rem;
    font-size: .74rem;
    font-weight: 700;
    color:#f1f5f9;
    background:rgba(255,255,255,0.03);
    outline: none;
    cursor: pointer;
    transition: border-color .2s;
    font-family: inherit;
    min-width: 0;
}
.cart-select:focus { border-color: #6366f1; }
.cart-select:disabled { color: #94a3b8; cursor: not-allowed; background:#f8fafc; }
.flex-1 { flex: 1; }

/* Member row */
.cart-member-row {
    display: flex;
    align-items: center;
    gap: .35rem;
}
.cart-member-icon {
    width: 24px; height: 24px;
    background: rgba(16,185,129,.1);
    border: 1px solid rgba(16,185,129,.2);
    border-radius: .4rem;
    display: flex; align-items: center; justify-content: center;
    color: #10b981; font-size: .7rem; flex-shrink: 0;
}
.cart-add-member-btn {
    width: 24px; height: 24px;
    background: rgba(99,102,241,.1);
    border: 1px solid rgba(99,102,241,.2);
    border-radius: .4rem;
    color: #6366f1; font-size: .7rem; flex-shrink: 0;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
}
.cart-add-member-btn:hover { background: #6366f1; color: #fff; }

/* ── Cart Items List ── */
.cart-items-wrap {
    flex: 1 1 0px;
    min-height: 60px;
    overflow-y: auto;
    padding: .35rem .85rem;
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 transparent;
}
.cart-items-wrap::-webkit-scrollbar { width: 4px; }
.cart-items-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

/* Empty state */
.cart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.25rem 1rem;
    text-align: center;
    height: 100%;
}
.cart-empty-icon {
    width: 44px; height: 44px;
    background: rgba(99,102,241,.07);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem; color: #a5b4fc; margin-bottom: .5rem;
}
.cart-empty-title { font-size: .82rem; font-weight: 700; color: #475569; margin-bottom: .15rem; }
.cart-empty-sub { font-size: .72rem; color: #94a3b8; }
.cart-empty-action {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    margin-top: .8rem;
    padding: .4rem .75rem;
    border: 1px solid rgba(99,102,241,.2);
    border-radius: .5rem;
    background: #eef2ff;
    color: #4f46e5;
    font-size: .72rem;
    font-weight: 800;
    cursor: pointer;
    transition: background .15s, color .15s, transform .15s;
}
.cart-empty-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    flex-wrap: wrap;
    margin-top: .8rem;
}
.cart-empty-actions .cart-empty-action {
    margin-top: 0;
}
.cart-empty-action-secondary {
    background: #ffffff;
    color: #64748b;
    border-color: #cbd5e1;
}
.cart-empty-action-secondary:hover {
    background: #f1f5f9;
    color: #334155;
}
.cart-empty-action:hover {
    background: #6366f1;
    color: #fff;
    transform: translateY(-1px);
}
.pos-cart-card.is-empty .cart-discount-section,
.pos-cart-card.is-empty .cart-summary-row:nth-child(3),
.pos-cart-card.is-empty .cart-actions {
    display: none;
}
.pos-cart-card.is-empty .cart-summary {
    padding-top: .65rem;
}

/* Cart item row */
.pos-cart-item {
    display: grid;
    grid-template-columns: 1fr auto auto auto;
    align-items: center;
    gap: .4rem;
    padding: .4rem 0;
    border-bottom: 1px dashed rgba(255,255,255,0.07);
}
.pos-cart-item:last-child { border-bottom: 0; }
.cart-item-name { font-size: .8rem; font-weight: 700; color:#f1f5f9; line-height: 1.25; }
.cart-item-price { font-size: .68rem; color: #94a3b8; font-weight: 600; }
.cart-item-qty {
    display: flex;
    align-items: center;
    gap: .15rem;
}
.qty-btn {
    width: 22px; height: 22px;
    background:#f8fafc;
    border:1px solid rgba(255,255,255,0.07);
    border-radius: .3rem;
    font-size: .8rem;
    font-weight: 700;
    color: #4f46e5;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
    flex-shrink: 0;
    line-height: 1;
}
.qty-btn:hover { background: #6366f1; color: #fff; border-color: #6366f1; }
.qty-input {
    width: 30px;
    text-align: center;
    border:1px solid rgba(255,255,255,0.07);
    border-radius: .3rem;
    font-size: .75rem;
    font-weight: 800;
    color:#f1f5f9;
    background:rgba(255,255,255,0.03);
    padding: .1rem 0;
    outline: none;
    font-family: inherit;
}
.qty-input:focus { border-color: #6366f1; background:#ffffff; }
.cart-item-total {
    font-size: .78rem;
    font-weight: 800;
    color: #4338ca;
    text-align: right;
    white-space: nowrap;
    font-family: SFMono-Regular,Menlo,monospace;
    min-width: 60px;
}
.cart-item-del {
    width: 18px; height: 18px;
    background: none; border: none;
    color: #cbd5e1; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem;
    transition: color .15s;
    flex-shrink: 0;
    padding: 0;
}
.cart-item-del:hover { color: #ef4444; }

/* ── Cart Summary ── */
.cart-summary {
    padding: .65rem .85rem .75rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.cart-summary-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    justify-content: space-between;
    align-items: center;
    gap: .65rem;
    min-height: 30px;
    font-size: .8rem;
    font-weight: 600;
    color: #475569;
    padding: .2rem 0;
}
.cart-summary-row.text-danger { color: #ef4444 !important; }
.cart-summary-row span:last-child { font-family: SFMono-Regular,Menlo,monospace; font-size: .76rem; text-align: right; white-space: nowrap; }
.checkout-feature-tabs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 4px;
    padding: 4px;
    margin-bottom: .55rem;
    border: 1px solid #e2e8f0;
    border-radius: .65rem;
    background: #f1f5f9;
}
.checkout-feature-tab {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    min-width: 0;
    min-height: 34px;
    padding: .35rem .5rem;
    border: 0;
    border-radius: .45rem;
    background: transparent;
    color: #64748b;
    font: inherit;
    font-size: .76rem;
    font-weight: 800;
    cursor: pointer;
    transition: color .18s, background .18s, box-shadow .18s;
}
.checkout-feature-tab.active {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff;
    box-shadow: 0 2px 6px rgba(79,70,229,.22);
}
.checkout-feature-tab:not(.active):hover { background: #e2e8f0; color: #334155; }
.checkout-tab-indicator {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 2px rgba(16,185,129,.16);
}
.cart-discount-section {
    margin: 0;
    padding: .65rem;
    max-height: min(44vh, 340px);
    overflow-y: auto;
    overscroll-behavior: contain;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    border: 1px solid #fecdd3;
    border-radius: .55rem;
    background: linear-gradient(145deg, #fff1f2, #ffffff 72%);
}
.cart-discount-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .3rem;
}
.discount-active-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    padding: .15rem .55rem;
    background: linear-gradient(135deg,#10b981,#059669);
    color: #fff;
    font-size: .65rem;
    font-weight: 800;
    border-radius: 50rem;
    animation: badgePulse 2s ease-in-out infinite;
}
@keyframes badgePulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(16,185,129,.4); }
    50% { box-shadow: 0 0 0 5px rgba(16,185,129,.0); }
}
.cart-section-heading {
    display: flex;
    align-items: center;
    gap: .45rem;
    margin-bottom: .35rem;
    color: #334155;
    font-size: .8rem;
    font-weight: 800;
}
.cart-section-heading i { width: 16px; text-align: center; }
.cart-discount-heading i { color: #e11d48; }
.cart-payment-heading i { color: #4f46e5; }
.pay-status-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #cbd5e1;
    margin-left: auto;
    flex-shrink: 0;
    transition: background .3s;
}
.pay-status-dot.ready { background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.25); }

/* Discount pills */
.disc-row-label {
    display: flex;
    align-items: center;
    gap: .35rem;
    font-size: .77rem;
}
.disc-pill {
    display: inline-flex;
    align-items: center;
    gap: .2rem;
    padding: .1rem .4rem;
    border-radius: 50rem;
    font-size: .62rem;
    font-weight: 800;
}
.disc-pill-member { background: #dbeafe; color: #1d4ed8; }
.disc-pill-promo { background: #fce7f3; color: #be185d; }
.disc-pill-extra { background: #ede9fe; color: #6d28d9; }
.disc-amount { font-family: SFMono-Regular,Menlo,monospace; font-size: .76rem; color: #ef4444; font-weight: 700; }
.disc-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .3rem .5rem;
    background: linear-gradient(135deg,rgba(239,68,68,.06),rgba(239,68,68,.03));
    border-radius: .4rem;
    margin-top: .3rem;
    font-size: .77rem;
    font-weight: 800;
    color: #dc2626;
}
.disc-total-row span:last-child { font-family: SFMono-Regular,Menlo,monospace; }

/* Promo code row */
.promo-code-row { margin: .35rem 0; }
.promo-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: .3rem;
}
.promo-icon {
    position: absolute;
    left: .65rem;
    color: #a855f7;
    font-size: .75rem;
    pointer-events: none;
    z-index: 1;
}
.promo-input {
    flex: 1;
    padding: .38rem 4.5rem .38rem 2rem;
    border: 1.5px dashed #c4b5fd;
    border-radius: .5rem;
    background: #faf5ff;
    font-size: .78rem;
    font-weight: 700;
    color: #7c3aed;
    outline: none;
    letter-spacing: .05em;
    transition: all .2s;
    font-family: SFMono-Regular,Menlo,monospace;
}
.promo-input:focus { border-color: #7c3aed; background: #ede9fe; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
.promo-apply-btn {
    position: absolute;
    right: .3rem;
    padding: .22rem .7rem;
    background: linear-gradient(135deg,#7c3aed,#6d28d9);
    color: #fff;
    border: none;
    border-radius: .35rem;
    font-size: .7rem;
    font-weight: 800;
    cursor: pointer;
    transition: all .15s;
    font-family: inherit;
}
.promo-apply-btn:hover { background: #5b21b6; box-shadow: 0 2px 6px rgba(109,40,217,.3); }
.promo-badge-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .4rem;
    padding: .3rem .5rem;
    background: linear-gradient(135deg,#fdf4ff,#fce7f3);
    border: 1px solid #f0abfc;
    border-radius: .45rem;
    animation: promoSlideIn .3s ease;
}
@keyframes promoSlideIn {
    from { opacity:0; transform: translateY(-6px); }
    to { opacity:1; transform: translateY(0); }
}
.promo-active-badge {
    display: flex;
    align-items: center;
    gap: .35rem;
    font-size: .72rem;
    font-weight: 800;
    color: #be185d;
}
.promo-active-badge i { color: #a855f7; }
.promo-remove-btn {
    background: none;
    border: none;
    color: #f43f5e;
    cursor: pointer;
    font-size: .78rem;
    padding: .2rem;
    border-radius: .3rem;
    transition: all .15s;
}
.promo-remove-btn:hover { background: #fee2e2; }
.promo-chips-scroll {
    display: flex;
    flex-wrap: wrap;
    gap: .25rem;
    margin-top: .25rem;
    margin-bottom: .3rem;
}
.promo-suggest-chip {
    display: inline-flex;
    align-items: center;
    gap: .2rem;
    padding: .15rem .45rem;
    font-size: .63rem;
    font-weight: 800;
    background: #fdf4ff;
    border: 1px dashed #d8b4fe;
    color: #9333ea;
    border-radius: 50rem;
    cursor: pointer;
    transition: all .15s;
    user-select: none;
}
.promo-suggest-chip i { font-size: .55rem; color: #a855f7; }
.promo-suggest-chip:hover {
    background: #9333ea;
    color: #fff;
    border-color: #9333ea;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(147,51,234,.25);
}
.promo-suggest-chip:hover i { color: #fff; }

.cart-discount-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    min-height: 38px;
    color: #475569;
    font-size: .8rem;
    font-weight: 700;
}
.disc-add-label {
    display: flex;
    align-items: center;
    gap: .3rem;
    margin: 0;
    font-size: .77rem;
}
.cart-discount-control { display: flex; align-items: center; gap: .25rem; }
.discount-step-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border: 1px solid #cbd5e1;
    border-radius: .45rem;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    font-size: .75rem;
    transition: all .15s;
}
.discount-step-btn:hover, .discount-step-btn:focus-visible { border-color: #4f46e5; background: #eef2ff; color: #4338ca; }
.cart-disc-input {
    width: 44px;
    min-height: 34px;
    text-align: center;
    border: 1px solid #cbd5e1;
    border-radius: .4rem;
    background: #ffffff;
    font-size: .8rem;
    font-weight: 800;
    color: #334155;
    padding: .15rem .25rem;
    outline: none;
    font-family: inherit;
}
.cart-disc-input:focus { border-color: #6366f1; }
.cart-disc-pct { font-size: .8rem; font-weight: 700; color:#64748b; }
.disc-preset-chips {
    display: flex;
    align-items: center;
    gap: .25rem;
    margin-top: .25rem;
    margin-bottom: .35rem;
}
.disc-preset-label {
    font-size: .62rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #94a3b8;
    margin-right: .1rem;
}
.disc-preset-chip {
    padding: .12rem .38rem;
    font-size: .65rem;
    font-weight: 700;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    border-radius: .35rem;
    cursor: pointer;
    transition: all .15s;
    user-select: none;
}
.disc-preset-chip:hover {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
    transform: translateY(-1px);
}

.cart-summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    padding: .65rem .75rem;
    margin-top: .45rem;
    border: 1px solid #c7d2fe;
    border-radius: .6rem;
    background: #eef2ff;
}
.total-label { display: block; font-size: .78rem; font-weight: 800; color:#3730a3; text-transform: uppercase; letter-spacing: .04em; }
.total-items-count { font-size: .7rem; font-weight: 600; color: #64748b; }
.total-amount { color: #3730a3; font-size: 1.08rem; font-weight: 900; font-family: SFMono-Regular,Menlo,monospace; text-align: right; white-space: nowrap; }

/* ── Cart Payment & Checkout ── */
.cart-actions {
    padding: .7rem .85rem .75rem;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
    background:#ffffff;
}
.payment-feature-panel {
    padding: .6rem;
    max-height: min(44vh, 340px);
    overflow-y: auto;
    overscroll-behavior: contain;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    border: 1px solid #c7d2fe;
    border-radius: .55rem;
    background: linear-gradient(145deg, #eef2ff, #ffffff 72%);
}
.cart-payment-heading {
    padding-bottom: .5rem;
    margin-bottom: .55rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: .9rem;
}
.pay-method-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: .25rem;
}
.pay-method-label {
    font-size: .65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
}
.pay-method-group {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin-bottom: .65rem;
    gap: .35rem;
}
.pay-method-btn {
    text-align: center;
    min-width: 0;
    min-height: 48px;
    padding: .45rem .25rem;
    border: 1.5px solid #e2e8f0;
    font-size: .72rem;
    font-weight: 800;
    color: #475569;
    background: #f8fafc;
    cursor: pointer;
    border-radius: .6rem;
    transition: all .2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .2rem;
    user-select: none;
    font-family: inherit;
}
.pay-method-btn i { font-size: .95rem; }
.pay-method-btn:hover {
    border-color: #6366f1;
    color: #4f46e5;
    background: #eef2ff;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(99,102,241,.12);
}
.pay-method-btn.active {
    background: linear-gradient(135deg,#4f46e5,#6366f1);
    color: #fff;
    border-color: #4f46e5;
    box-shadow: 0 4px 12px rgba(79,70,229,.3);
    transform: translateY(-1px);
}
.pay-method-btn.active i { color: #fff; }
.pay-method-btn:focus-visible { outline: 2px solid #4f46e5; outline-offset: 2px; }

/* Pay areas */
.pay-area { animation: payAreaIn .2s ease; }
@keyframes payAreaIn {
    from { opacity:0; transform: translateY(6px); }
    to { opacity:1; transform: translateY(0); }
}

/* QRIS styles */
.qris-container { padding: .35rem 0; }
.qris-card {
    border: 1px solid #e0e7ff;
    border-radius: .75rem;
    background: linear-gradient(135deg,#f5f3ff,#ede9fe);
    padding: .75rem .65rem .6rem;
    text-align: center;
    margin-bottom: .6rem;
    position: relative;
    overflow: hidden;
}
.qris-card::before {
    content: '';
    position: absolute;
    top: -20px; right: -20px;
    width: 80px; height: 80px;
    background: rgba(99,102,241,.08);
    border-radius: 50%;
}
.qris-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .5rem;
}
.qris-brand { font-size: .72rem; font-weight: 900; color: #4338ca; letter-spacing: .05em; }
.qris-subtitle { font-size: .6rem; color: #6d28d9; font-weight: 600; }
.qris-qr-wrap {
    width: 180px; height: 180px;
    margin: 0 auto .45rem;
    background: #fff;
    border-radius: .5rem;
    padding: 6px;
    box-shadow: 0 2px 8px rgba(79,70,229,.15);
}
.qris-qr-svg { width: 100%; height: 100%; }
.qris-merchant-image { display:block; width:100%; height:100%; object-fit:contain; }
.qris-merchant-placeholder {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .2rem;
    color: #4338ca;
    line-height: 1.2;
}
.qris-merchant-placeholder i { font-size: 2rem; }
.qris-merchant-placeholder strong { font-size: .62rem; }
.qris-merchant-placeholder span { max-width: 78px; font-size: .48rem; color: #64748b; }
.qris-amount-lbl { font-size: .62rem; font-weight: 700; color: #6d28d9; text-transform: uppercase; letter-spacing: .04em; }
.qris-amount { font-size: 1rem; font-weight: 900; color: #3730a3; font-family: SFMono-Regular,Menlo,monospace; margin-bottom: .35rem; }
.qris-note { font-size: .62rem; color: #7c3aed; display: flex; align-items: center; justify-content: center; gap: .3rem; }
.qris-confirm-btn {
    width: 100%;
    padding: .55rem;
    background: linear-gradient(135deg,#059669,#10b981);
    color: #fff;
    border: none;
    border-radius: .55rem;
    font-weight: 800;
    font-size: .78rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    transition: all .2s;
    box-shadow: 0 3px 10px rgba(5,150,105,.25);
    font-family: inherit;
}
.qris-confirm-btn:hover { background: linear-gradient(135deg,#047857,#059669); box-shadow: 0 5px 15px rgba(5,150,105,.35); transform: translateY(-1px); }

/* Card styles */
.card-pay-container { padding: .3rem 0; }
.card-type-tabs {
    display: flex;
    gap: .35rem;
    margin-bottom: .5rem;
}
.card-type-btn {
    flex: 1;
    padding: .3rem;
    border: 1.5px solid #e2e8f0;
    border-radius: .45rem;
    background: #f8fafc;
    color: #64748b;
    font-size: .72rem;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .3rem;
    transition: all .15s;
    font-family: inherit;
}
.card-type-btn.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }
.card-info-wrap { padding: .15rem 0; }
.card-info-wrap .cash-label { margin-bottom: .25rem; display: block; }
.bank-chips {
    display: flex;
    flex-wrap: wrap;
    gap: .3rem;
    margin-bottom: .5rem;
}
.bank-chip {
    padding: .2rem .6rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 50rem;
    background: #f8fafc;
    color: #475569;
    font-size: .68rem;
    font-weight: 800;
    cursor: pointer;
    transition: all .15s;
    font-family: inherit;
}
.bank-chip.active { background: #1e40af; color: #fff; border-color: #1e40af; }
.bank-chip:hover:not(.active) { border-color: #3b82f6; color: #2563eb; }
.card-amount-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .35rem .5rem;
    background: #f1f5f9;
    border-radius: .4rem;
    margin-top: .4rem;
}
.card-total-amt { font-size: .88rem; font-weight: 900; color: #1e40af; font-family: SFMono-Regular,Menlo,monospace; }
.mt-2 { margin-top: .5rem !important; }

/* Transfer styles */
.transfer-container { padding: .3rem 0; }
.transfer-bank-select { margin-bottom: .5rem; }
.transfer-bank-select .cash-label { display: block; margin-bottom: .25rem; }
.transfer-rekening-box {
    background: linear-gradient(135deg,#eff6ff,#dbeafe);
    border: 1px solid #bfdbfe;
    border-radius: .6rem;
    padding: .55rem .65rem;
    margin-bottom: .5rem;
}
.rek-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: .2rem;
    font-size: .72rem;
}
.rek-label { color: #6b7280; font-weight: 600; }
.rek-val { font-weight: 800; color: #1e3a8a; font-family: SFMono-Regular,Menlo,monospace; }
.rek-total { font-size: .88rem; color: #1d4ed8; }
.rek-copy-btn {
    background: rgba(30,58,138,.08);
    border: 1px solid rgba(30,58,138,.2);
    color: #1e40af;
    padding: .12rem .4rem;
    border-radius: .35rem;
    font-size: .65rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    transition: all .15s;
}
.rek-copy-btn:hover {
    background: #1e40af;
    color: #ffffff;
    border-color: #1e40af;
}
.transfer-container .cash-label { display: block; margin-bottom: .2rem; }

/* Ref input */
.ref-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: .35rem;
}
.ref-icon {
    position: absolute;
    left: .65rem;
    color: #94a3b8;
    font-size: .72rem;
    pointer-events: none;
    z-index: 1;
}
.ref-input {
    width: 100%;
    padding: .4rem .6rem .4rem 2rem;
    border: 1.5px solid #e2e8f0;
    border-radius: .5rem;
    background: #fff;
    font-size: .78rem;
    font-weight: 700;
    color: #334155;
    outline: none;
    font-family: SFMono-Regular,Menlo,monospace;
    transition: all .2s;
}
.ref-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }

/* Cash inputs */
.cash-header { display: block; margin-bottom: .35rem; }
.cash-label { display: block; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #475569; }
.quick-cash-section { margin: .6rem 0; }
.quick-cash-list { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .4rem; margin-top: .35rem; }
.quick-cash-btn {
    min-width: 0;
    min-height: 42px;
    padding: .4rem .5rem;
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    border-radius: .5rem;
    font-size: .75rem;
    font-weight: 800;
    color: #4f46e5;
    cursor: pointer;
    transition: all .15s;
    font-family: inherit;
}
.quick-cash-btn:hover, .quick-cash-btn:focus-visible { background: #4f46e5; color: #fff; border-color: #4f46e5; }

.cash-input-wrap { position: relative; margin-bottom: .35rem; }
.cash-prefix {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    display: flex; align-items: center;
    padding: 0 .6rem;
    font-weight: 800; color:#64748b; font-size: .8rem;
    border-right: 1.5px solid #e2e8f0;
    pointer-events: none; z-index: 1;
}
.cash-input {
    width: 100%;
    padding: .55rem .65rem .55rem 2.6rem;
    border:1.5px solid rgba(255,255,255,0.07);
    min-height: 46px;
    border-radius: .5rem;
    font-size: .88rem;
    font-weight: 800;
    color: #4338ca;
    background:rgba(255,255,255,0.03);
    outline: none;
    text-align: right;
    transition: all .2s;
    font-family: SFMono-Regular,Menlo,monospace;
}
.cash-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); background:#ffffff; }

.kembalian-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background:rgba(255,255,255,0.03);
    border: 1px solid #f1f5f9;
    border-radius: .5rem;
    min-height: 40px;
    padding: .4rem .6rem;
    margin-bottom: .6rem;
}
.kembalian-label { font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #475569; }
.kembalian-val { font-size: .9rem; font-weight: 800; color: #059669; font-family: SFMono-Regular,Menlo,monospace; }

/* Checkout Button */
.btn-checkout {
    width: 100%;
    min-height: 50px;
    margin-top: .5rem;
    padding: .75rem;
    background: linear-gradient(135deg,#6366f1,#4f46e5);
    color: #fff;
    border: none;
    border-radius: .7rem;
    font-weight: 800;
    font-size: .82rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    letter-spacing: .02em;
    box-shadow: 0 4px 12px rgba(99,102,241,.35);
    transition: all .2s;
    font-family: inherit;
    position: relative;
}
.btn-checkout:hover:not(:disabled) {
    box-shadow: 0 6px 18px rgba(99,102,241,.45);
    transform: translateY(-1px);
}
.btn-checkout:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }

/* ════════════ RUANG SISI: SELURUH PESANAN PELANGGAN ════════════ */
.pos-orders-panel {
    position: relative;
    display: flex;
    align-items: stretch;
    flex-shrink: 0;
}

/* Toggle Edge Tab (Visible only when side panel is closed) */
.orders-toggle-tab {
    width: 38px;
    height: 96px;
    background:#ffffff;
    border:1.5px solid rgba(255,255,255,0.07);
    border-left: 0;
    border-radius: 0 .875rem .875rem 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .25rem;
    cursor: pointer;
    color: #4f46e5;
    font-size: .85rem;
    box-shadow: 3px 0 14px rgba(15,23,42,.08);
    transition: all .2s;
    flex-shrink: 0;
    margin-top: 1.5rem;
    align-self: flex-start;
    position: relative;
    user-select: none;
    font-family: inherit;
}
.orders-toggle-tab:hover {
    background: #6366f1;
    color: #fff;
    border-color: #6366f1;
}
.orders-toggle-tab:hover .orders-tab-txt { color: #fff; }
.orders-tab-txt {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .05em;
    color: #475569;
}
.orders-tab-badge {
    position: absolute;
    top: -7px;
    right: -7px;
    min-width: 20px;
    height: 20px;
    padding: 0 4px;
    background: #ef4444;
    border-radius: 50rem;
    color: #fff;
    font-size: .62rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(239,68,68,.4);
}

/* Hide toggle tab when side space is OPEN */
.pos-orders-panel.is-open .orders-toggle-tab {
    display: none !important;
}

/* Side Space Container */
.orders-panel-inner {
    width: 0;
    overflow: hidden;
    transition: width .25s cubic-bezier(.4,0,.2,1);
    background:#ffffff;
    border-radius: 1.125rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 20px rgba(15,23,42,.08);
    display: flex;
    flex-direction: column;
    margin-left: 0;
}
.pos-orders-panel.is-open .orders-panel-inner {
    width: 315px;
    margin-left: .5rem;
}

/* Header */
.orders-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .65rem .85rem;
    border-bottom:1px solid rgba(255,255,255,0.07);
    flex-shrink: 0;
    min-width: 315px;
}
.orders-panel-title {
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .85rem;
    font-weight: 800;
    color:#f1f5f9;
}
.orders-count-badge {
    font-size: .68rem;
    font-weight: 800;
    color: #4f46e5;
    background: rgba(99,102,241,.1);
    padding: .15rem .5rem;
    border-radius: 50rem;
}
.orders-close-btn {
    width: 24px; height: 24px;
    background:#f8fafc;
    border: none;
    border-radius: .35rem;
    color:#64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .72rem;
    transition: all .15s;
}
.orders-close-btn:hover { background: #ef4444; color: #fff; }

/* Live search inside orders */
.orders-search-box {
    padding: .45rem .75rem 0;
    position: relative;
    min-width: 315px;
}
.orders-search-box i {
    position: absolute;
    left: 1.35rem;
    top: 50%;
    transform: translateY(-20%);
    font-size: .7rem;
    color: #94a3b8;
}
.orders-search-box input {
    width: 100%;
    padding: .3rem .55rem .3rem 1.85rem;
    border:1.5px solid rgba(255,255,255,0.07);
    border-radius: .5rem;
    font-size: .74rem;
    font-weight: 600;
    outline: none;
    background:rgba(255,255,255,0.03);
    font-family: inherit;
    transition: border-color .15s;
}
.orders-search-box input:focus {
    border-color: #6366f1;
    background:#ffffff;
}

/* Filter tabs */
.orders-filter-tabs {
    display: flex;
    gap: .25rem;
    padding: .35rem .75rem;
    border-bottom:1px solid rgba(255,255,255,0.07);
    flex-shrink: 0;
    min-width: 315px;
    flex-wrap: wrap;
}
.orders-tab {
    padding: .18rem .5rem;
    border:1.5px solid rgba(255,255,255,0.07);
    border-radius: .4rem;
    font-size: .68rem;
    font-weight: 700;
    color:#64748b;
    background:rgba(255,255,255,0.03);
    cursor: pointer;
    transition: all .15s;
    display: flex;
    align-items: center;
    gap: .25rem;
    font-family: inherit;
}
.orders-tab:hover { border-color: #6366f1; color: #6366f1; }
.orders-tab.active { background: #6366f1; border-color: #6366f1; color: #fff; }
.tab-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.tab-dot-green { background: #10b981; }

/* Orders List */
.orders-list {
    flex: 1;
    overflow-y: auto;
    padding: .45rem .7rem;
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 transparent;
    min-width: 315px;
}
.orders-list::-webkit-scrollbar { width: 4px; }
.orders-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

/* Order Card */
.order-card {
    background:#ffffff;
    border: 1.5px solid #eef2f6;
    border-radius: .75rem;
    padding: .55rem .7rem;
    margin-bottom: .45rem;
    cursor: pointer;
    transition: all .18s ease;
    box-shadow: 0 2px 6px rgba(15,23,42,.03);
}
.order-card:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 12px rgba(99,102,241,.12);
    transform: translateY(-1px);
}
.order-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: .2rem;
}
.order-invoice {
    font-size: .7rem;
    font-weight: 800;
    color: #4f46e5;
    font-family: SFMono-Regular,Menlo,monospace;
}
.order-status-indicator {
    display: flex;
    align-items: center;
    gap: .3rem;
    font-size: .65rem;
    font-weight: 700;
}
.order-status-dot {
    width: 7px; height: 7px; border-radius: 50%;
    display: inline-block; flex-shrink: 0;
}
.order-status-dot.active { background: #10b981; box-shadow: 0 0 0 2px rgba(16,185,129,.25); }
.order-status-dot.done { background: #94a3b8; }

.order-customer {
    font-size: .78rem;
    font-weight: 700;
    color:#f1f5f9;
    margin-bottom: .15rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.order-meta {
    font-size: .66rem;
    color:#64748b;
    font-weight: 600;
    display: flex;
    gap: .35rem;
    flex-wrap: wrap;
    margin-bottom: .25rem;
}
.order-meta-chip {
    display: inline-flex; align-items: center; gap: .2rem;
    background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);
    border-radius: .35rem; padding: .1rem .3rem;
    font-size: .64rem; font-weight: 700; color: #475569;
}
.order-items-preview {
    font-size: .68rem;
    color:#64748b;
    margin-bottom: .35rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.order-card-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: .3rem;
    border-top: 1px dashed #f1f5f9;
}
.order-total {
    font-size: .8rem;
    font-weight: 900;
    color:#f1f5f9;
    font-family: SFMono-Regular,Menlo,monospace;
}
.order-actions-group {
    display: flex;
    gap: .25rem;
}
.order-btn-action {
    padding: .18rem .4rem;
    border:1px solid rgba(255,255,255,0.07);
    border-radius: .35rem;
    background:rgba(255,255,255,0.03);
    color: #475569;
    font-size: .66rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: .2rem;
    text-decoration: none;
    font-family: inherit;
}
.order-btn-action:hover {
    background: #6366f1;
    color: #fff;
    border-color: #6366f1;
}

/* Loading & Empty */
.orders-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: 2.5rem 1rem;
    color: #94a3b8;
    font-size: .8rem;
    font-weight: 600;
}
.orders-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2.5rem 1rem;
    text-align: center;
}
.orders-empty-icon {
    width: 44px; height: 44px;
    background: rgba(99,102,241,.07);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem; color: #a5b4fc; margin-bottom: .5rem;
}

/* Panel Footer */
.orders-panel-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .5rem .75rem;
    border-top:1px solid rgba(255,255,255,0.07);
    flex-shrink: 0;
    min-width: 315px;
    background: #fafbfc;
}
.orders-refresh-btn {
    font-size: .72rem; font-weight: 700; color: #475569;
    background: none; border: none; cursor: pointer;
    display: flex; align-items: center; gap: .3rem;
    transition: color .15s; font-family: inherit;
}
.orders-refresh-btn:hover { color: #6366f1; }
.orders-see-all-btn {
    font-size: .72rem; font-weight: 700; color: #4f46e5;
    text-decoration: none;
    display: flex; align-items: center; gap: .3rem;
    transition: opacity .15s;
}
.orders-see-all-btn:hover { opacity: .8; text-decoration: underline; }

/* ── Receipt Thermal Paper Preview ── */
.receipt-paper {
    background:#ffffff;
    border-radius: .5rem;
    padding: 1.25rem 1rem;
    font-family: 'Courier New', Courier, monospace;
    font-size: 12px;
    color: #000;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.receipt-divider {
    border-top: 1px dashed #999;
    margin: .5rem 0;
}

/* Make receipt preview light inside modal for readability */
#modalReceiptQuick .receipt-paper {
    background: #fff !important;
    color: #000 !important;
    box-shadow: none !important;
}
.receipt-line {
    font-size: 11px;
    margin-bottom: 2px;
}
.receipt-brand-logo { display:block; width:34mm; height:auto; object-fit:contain; margin:0 auto 2mm; }
.receipt-item-row {
    margin-bottom: 4px;
}
.receipt-item-top {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
}
.receipt-item-sub {
    font-size: 10px;
    color: #555;
}

/* Light interface overrides for the POS surface. */
.pos-products-panel,
.pos-product-card,
.pos-cart-card,
.cart-header,
.cart-items-wrap,
.cart-summary,
.cart-actions,
.orders-panel-inner,
.orders-panel-header,
.orders-list,
.orders-search-box,
.orders-filter-tabs,
.orders-panel-footer,
.pos-search-input,
.pos-scan-btn,
.pos-category-btn,
.cart-type-toggle,
.cart-select,
.cash-input {
    background: #ffffff !important;
    color: #334155 !important;
    border-color: #e2e8f0 !important;
}
.cart-title,
.pos-product-title,
.orders-panel-title,
.pay-method-label,
.cash-label,
.total-label {
    color: #0f172a !important;
}
.cart-invoice,
.pos-product-card .text-muted,
.orders-empty,
.orders-loading,
.cart-empty-sub {
    color: #64748b !important;
}
.cart-title,
.cart-item-name,
.cart-item-total,
.total-label,
.order-customer,
.order-total,
.orders-panel-title,
.pos-product-title {
    color: #0f172a !important;
}
.cart-invoice,
.cart-item-price,
.total-items-count,
.pay-method-label,
.cash-label,
.kembalian-label,
.orders-loading,
.order-meta,
.order-items-preview,
.orders-empty,
.cart-empty-sub {
    color: #475569 !important;
}
.cart-select,
.qty-input,
.cart-disc-input,
.cash-input,
.orders-search-box input,
.orders-tab,
.order-btn-action {
    color: #334155 !important;
    background: #ffffff !important;
}
.pay-method-btn.active {
    background: linear-gradient(135deg,#4f46e5,#6366f1) !important;
    color: #ffffff !important;
    border-color: #4f46e5 !important;
}
#posShell .cart-summary {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
}
#posShell .cart-summary-row {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: .65rem;
}
#posShell .cart-summary-total {
    flex-direction: row !important;
    align-items: center !important;
}
#formSimpan .pay-method-btn {
    display: flex !important;
    flex-direction: column !important;
    min-height: 48px;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: .6rem !important;
    background: #f8fafc !important;
    color: #475569 !important;
}
#formSimpan .pay-method-btn:hover {
    border-color: #6366f1 !important;
    background: #eef2ff !important;
    color: #4f46e5 !important;
}
#formSimpan .pay-method-btn.active {
    border-color: #4f46e5 !important;
    background: linear-gradient(135deg,#4f46e5,#6366f1) !important;
    color: #ffffff !important;
}
#formSimpan .quick-cash-btn {
    display: flex !important;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    border: 1px solid #c7d2fe !important;
    background: #eef2ff !important;
    color: #4338ca !important;
    border-radius: .5rem;
}
#formSimpan .quick-cash-btn:hover,
#formSimpan .quick-cash-btn:focus-visible {
    border-color: #4f46e5 !important;
    background: #4f46e5 !important;
    color: #ffffff !important;
}
/* New pay area elements - ensure correct light mode */
.promo-input { background: #faf5ff !important; color: #7c3aed !important; border-color: #c4b5fd !important; }
.promo-input:focus { background: #ede9fe !important; }
.ref-input { background: #ffffff !important; color: #334155 !important; border-color: #e2e8f0 !important; }
.qris-card { background: linear-gradient(135deg,#f5f3ff,#ede9fe) !important; }
.qris-qr-wrap { background: #ffffff !important; }
.transfer-rekening-box { background: linear-gradient(135deg,#eff6ff,#dbeafe) !important; border-color: #bfdbfe !important; }
.card-type-btn { background: #f8fafc !important; color: #64748b !important; border-color: #e2e8f0 !important; }
.card-type-btn.active { background: #4f46e5 !important; color: #fff !important; border-color: #4f46e5 !important; }
.bank-chip { background: #f8fafc !important; color: #475569 !important; border-color: #cbd5e1 !important; }
.bank-chip.active { background: #1e40af !important; color: #fff !important; border-color: #1e40af !important; }
.promo-suggest-chip { background: #fdf4ff !important; color: #9333ea !important; border-color: #d8b4fe !important; }
.promo-suggest-chip:hover { background: #9333ea !important; color: #fff !important; }
.disc-preset-chip { background: #f8fafc !important; color: #475569 !important; border-color: #cbd5e1 !important; }
.disc-preset-chip:hover { background: #4f46e5 !important; color: #fff !important; }
.rek-copy-btn { background: #dbeafe !important; color: #1e40af !important; border-color: #bfdbfe !important; }
.rek-copy-btn:hover { background: #1e40af !important; color: #fff !important; }
/* ════════════ RESPONSIVE ════════════ */
@media (max-width: 1199px) {
    .pos-shell {
        flex-direction: row;
        height: calc(100vh - 142px);
        min-height: 560px;
        overflow: hidden;
    }
    .pos-products-panel {
        height: 100%;
        padding-right: .75rem;
    }
    .pos-cart-panel {
        width: min(360px, 42vw);
        height: 100%;
        min-height: 0;
        margin-top: 0;
    }
    .pos-orders-panel {
        position: fixed;
        top: 80px;
        right: 0;
        bottom: 16px;
        z-index: 1100;
    }
    .pos-orders-panel.is-open .orders-panel-inner {
        width: min(360px, calc(100vw - 24px));
        margin-left: 0;
        box-shadow: -8px 0 28px rgba(15, 23, 42, .16);
    }
    .pos-orders-panel.is-open .orders-panel-header,
    .pos-orders-panel.is-open .orders-search-box,
    .pos-orders-panel.is-open .orders-filter-tabs,
    .pos-orders-panel.is-open .orders-list,
    .pos-orders-panel.is-open .orders-panel-footer {
        min-width: 0;
    }
}

@media (min-width: 1200px) and (max-width: 1280px) {
    .pos-cart-panel { width: 315px; }
    .pos-orders-panel.is-open .orders-panel-inner { width: 290px; }
    .orders-panel-header, .orders-search-box, .orders-filter-tabs, .orders-list, .orders-panel-footer { min-width: 290px; }
}

@media (max-width: 767px) {
    .pos-shell {
        flex-direction: column;
        height: auto;
        overflow: visible;
    }
    .pos-products-panel {
        order: 1;
        display: flex !important;
        padding-right: 0;
        height: 420px;
        min-height: 420px;
    }
    .pos-products-grid {
        min-height: 300px;
        flex: 1 1 auto;
    }
    .pos-cart-panel { width: 100%; height: auto; min-height: 540px; margin-top: 1rem; }
    .pos-orders-panel {
        position: fixed;
        right: 0;
        top: 80px;
        bottom: 20px;
        z-index: 1050;
    }
    .pos-orders-panel.is-open .orders-panel-inner {
        box-shadow: -6px 0 25px rgba(0,0,0,.2);
    }
}

/* Mobile-specific tweaks for better layout on small screens */
@media (max-width: 767px) {
    .pos-shell { flex-direction: column; height: auto; min-height: 0; }
    .pos-products-panel { order: 1; padding-right: 0; height: 420px; min-height: 420px; }
    .pos-products-grid { grid-template-columns: repeat(2, 1fr); gap: .5rem; }
    .pos-product-card { min-height: 150px !important; }
    .pos-product-card .img-container { height: 90px !important; min-height: 90px !important; }
    .pos-cart-panel { width: 100%; order: 2; min-height: 420px; margin-top: .75rem; }
    .pos-orders-panel { position: fixed; right: 0; top: 70px; bottom: 0; z-index: 1100; width: 100%; }
    .orders-toggle-tab { display: none !important; }
    .cart-summary-total { flex-direction: row; align-items: center; }
    .btn-checkout { font-size: .9rem; padding: .6rem; }
    .cash-input { font-size: 1rem; padding: .5rem .6rem .5rem 2.6rem; }
    .pos-products-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
}
</style>

<!-- ===== SCRIPTS ===== -->
<script>
/* ═══ APPLICATION STATE ═══ */
let cart = [];
let memberDiscount = 0;
let transactionAudioContext = null;

function unlockTransactionAudio() {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (!AudioContextClass) return;

    try {
        transactionAudioContext = transactionAudioContext || new AudioContextClass();
        transactionAudioContext.resume().catch(error => console.warn('Audio transaksi tidak dapat diaktifkan:', error));
    } catch (error) {
        console.warn('Audio transaksi tidak tersedia:', error);
    }
}

function playTransactionSound(success) {
    if (!transactionAudioContext) return;

    const notes = success ? [660, 880] : [440, 300];
    const startAt = transactionAudioContext.currentTime;
    notes.forEach((frequency, index) => {
        const oscillator = transactionAudioContext.createOscillator();
        const gain = transactionAudioContext.createGain();
        const noteStart = startAt + index * 0.16;

        oscillator.type = 'sine';
        oscillator.frequency.value = frequency;
        gain.gain.setValueAtTime(0.0001, noteStart);
        gain.gain.exponentialRampToValueAtTime(0.16, noteStart + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, noteStart + 0.14);
        oscillator.connect(gain);
        gain.connect(transactionAudioContext.destination);
        oscillator.start(noteStart);
        oscillator.stop(noteStart + 0.15);
    });
}

/* ═══ INIT ON LOAD ═══ */
document.addEventListener('DOMContentLoaded', () => {
    const checkoutForm = document.getElementById('formSimpan');
    const featureTabs = document.querySelector('.checkout-feature-tabs');
    const discountPanel = document.getElementById('discountFeaturePanel');
    const paymentPanel = document.getElementById('paymentFeaturePanel');
    if (checkoutForm && featureTabs && discountPanel && paymentPanel) {
        checkoutForm.insertBefore(featureTabs, paymentPanel);
        checkoutForm.insertBefore(discountPanel, paymentPanel);
    }

    const posShell = document.getElementById('posShell');
    if (posShell) posShell.scrollIntoView({ block: 'start', behavior: 'auto' });
    window.scrollTo(0, 0);
});

/* ═══ PRODUCT SEARCH & CATEGORY FILTER ═══ */
let activeMenuCategory = 'All';

function applyMenuFilters() {
    const searchInput = document.getElementById('searchMenu');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

    document.querySelectorAll('#menuList .menu-item').forEach(item => {
        const categoryMatches = activeMenuCategory === 'All'
            || item.dataset.kategori === String(activeMenuCategory);
        const searchMatches = item.dataset.nama.includes(query)
            || item.dataset.id === query;
        item.hidden = !(categoryMatches && searchMatches);
    });
}

function filterMenu() {
    const searchInput = document.getElementById('searchMenu');
    const q = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const clearBtn = document.getElementById('btnClearSearch');
    if (clearBtn) clearBtn.style.display = q ? 'flex' : 'none';
    applyMenuFilters();
}

function clearSearch() {
    const inp = document.getElementById('searchMenu');
    inp.value = '';
    filterMenu();
    inp.focus();
}

function focusMenuCatalog() {
    const catalog = document.querySelector('.pos-products-panel');
    const search = document.getElementById('searchMenu');
    if (catalog) catalog.scrollIntoView({ behavior: 'smooth', block: 'start' });
    if (search) setTimeout(() => search.focus(), 250);
}

function filterCategory(cat, btn) {
    activeMenuCategory = cat;
    document.querySelectorAll('.pos-category-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    applyMenuFilters();
}

/* ═══ CART MANAGEMENT ═══ */
function addToCart(id, nama, harga, stok) {
    if (stok <= 0) {
        Swal.fire({ icon: 'error', title: 'Stok Habis', text: `Menu "${nama}" sedang tidak tersedia.`, timer: 1800, showConfirmButton: false });
        return;
    }

    let exist = cart.find(i => i.id_menu == id);
    if (exist && exist.qty + 1 > stok) {
        Swal.fire({ icon: 'warning', title: 'Stok Terbatas', text: `Stok "${nama}" tersisa ${stok} item.`, timer: 1800, showConfirmButton: false });
        return;
    }

    if (exist) {
        exist.qty++;
    } else {
        cart.push({ id_menu: id, nama, harga, qty: 1, max_stok: stok });
    }

    // Gentle POS Audio Feedback
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(ctx.destination);
        o.frequency.value = 880; g.gain.value = 0.04;
        o.start(); setTimeout(() => o.stop(), 70);
    } catch(e) {}

    renderCart();
}

function updateQty(id, delta) {
    let item = cart.find(i => i.id_menu == id);
    if (!item) return;
    const nq = item.qty + delta;
    if (nq <= 0) {
        cart = cart.filter(i => i.id_menu != id);
    } else if (nq > item.max_stok) {
        Swal.fire({ icon: 'warning', title: 'Stok Tidak Cukup', text: `Maksimal stok tersedia ${item.max_stok}.`, timer: 1800, showConfirmButton: false });
        return;
    } else {
        item.qty = nq;
    }
    renderCart();
}

function handleQtyInput(id, el) {
    const val = parseInt(el.value) || 1;
    const item = cart.find(i => i.id_menu == id);
    if (!item) return;
    if (val <= 0) {
        cart = cart.filter(i => i.id_menu != id);
    } else if (val > item.max_stok) {
        Swal.fire({ icon: 'warning', title: 'Stok Tidak Cukup', text: `Maksimal stok tersedia ${item.max_stok}.`, timer: 1800, showConfirmButton: false });
        el.value = item.qty;
        return;
    } else {
        item.qty = val;
    }
    renderCart();
}

function clearCartConfirm() {
    if (!cart.length) return;
    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: 'Semua item pesanan yang dipilih akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (r.isConfirmed) {
            cart = [];
            renderCart();
        }
    });
}

function renderCart() {
    const list = document.getElementById('cartList');
    const totalCountEl = document.getElementById('totalItemsCount');
    const cartCard = document.querySelector('.pos-cart-card');

    if (cart.length === 0) {
        if (cartCard) cartCard.classList.add('is-empty');
        list.innerHTML = `
          <div class="cart-empty" id="emptyCart">
            <div class="cart-empty-icon"><i class="fas fa-receipt"></i></div>
            <p class="cart-empty-title">Keranjang masih kosong</p>
            <small class="cart-empty-sub">Pilih menu di sisi kiri untuk menambahkan pesanan</small>
                        <div class="cart-empty-actions">
                            <button type="button" class="cart-empty-action" onclick="focusMenuCatalog()">
                                <i class="fas fa-utensils"></i> Pilih Menu
                            </button>
                            <button type="button" class="cart-empty-action cart-empty-action-secondary" onclick="triggerBarcodeScan()">
                                <i class="fas fa-barcode"></i> Scan SKU
                            </button>
                        </div>
          </div>`;
        if (totalCountEl) totalCountEl.textContent = '0 item';
        resetSummary();
        return;
    }

    if (cartCard) cartCard.classList.remove('is-empty');
    const totalQty = cart.reduce((sum, i) => sum + i.qty, 0);
    if (totalCountEl) totalCountEl.textContent = `${totalQty} item`;

    list.innerHTML = cart.map(i => `
      <div class="pos-cart-item animate-in">
        <div>
          <div class="cart-item-name">${escapeHtml(i.nama)}</div>
          <div class="cart-item-price">Rp ${i.harga.toLocaleString('id-ID')} / pcs</div>
        </div>
        <div class="cart-item-qty">
          <button type="button" class="qty-btn" onclick="updateQty(${i.id_menu}, -1)" title="Kurangi">−</button>
          <input type="number" class="qty-input" value="${i.qty}" min="1" max="${i.max_stok}"
                 onchange="handleQtyInput(${i.id_menu}, this)">
          <button type="button" class="qty-btn" onclick="updateQty(${i.id_menu}, 1)" title="Tambah">+</button>
        </div>
        <div class="cart-item-total">Rp ${(i.harga * i.qty).toLocaleString('id-ID')}</div>
        <button type="button" class="cart-item-del" onclick="updateQty(${i.id_menu}, -${i.qty})" title="Hapus Item">
          <i class="fas fa-xmark"></i>
        </button>
      </div>
    `).join('');

    hitungTotalAkhir();
    document.getElementById('btnProses').disabled = false;
}

function resetSummary() {
    ['displaySubtotal','displayDiskon','displayTax','displayTotal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = (id === 'displayDiskon' ? '- ' : '') + 'Rp 0';
    });
    document.getElementById('inputTotal').value = 0;
    document.getElementById('inputDiskonVal').value = 0;
    const addInput = document.getElementById('inputDiskonTambahan');
    if (addInput) addInput.value = 0;

    // Reset discount rows & badge
    const memberRow = document.getElementById('memberDiscountRow');
    if (memberRow) memberRow.style.display = 'none';
    const promoRow = document.getElementById('promoDiscountRow');
    if (promoRow) promoRow.style.display = 'none';
    const addRow = document.getElementById('additionalDiscountRow');
    if (addRow) addRow.style.display = 'none';
    const discTotalRow = document.getElementById('discTotalRow');
    if (discTotalRow) discTotalRow.style.display = 'none';
    const badge = document.getElementById('discountActiveBadge');
    if (badge) badge.style.display = 'none';

    // Reset promo state silently without re-running hitungTotalAkhir
    removePromoCode(true);

    document.getElementById('inputBayarFormatted').value = '';
    document.getElementById('inputBayar').value = 0;
    const dispEl = document.getElementById('displayKembalian');
    if (dispEl) {
        dispEl.textContent = 'Rp 0';
        dispEl.className = 'kembalian-val text-muted';
    }
    document.getElementById('inputKembalian').value = 0;
    document.getElementById('quickCashList').innerHTML = '';
    document.getElementById('btnProses').disabled = true;

    // Reset non-cash displays
    const qrisAmt = document.getElementById('qrisAmount');
    if (qrisAmt) qrisAmt.textContent = 'Rp 0';
    const cardAmt = document.getElementById('cardTotalAmt');
    if (cardAmt) cardAmt.textContent = 'Rp 0';
    const trfAmt = document.getElementById('transferAmountDisplay');
    if (trfAmt) trfAmt.textContent = 'Rp 0';

    const dot = document.getElementById('payStatusDot');
    if (dot) dot.classList.remove('ready');
}

/* ═══ PROMO CODE STATE ═══ */
let promoDiscount = 0; // percentage
let promoCode = '';

/* Built-in promo codes */
const PROMO_CODES = {
    'HEMAT10': { pct: 10, label: '10% OFF' },
    'DISKON15': { pct: 15, label: '15% OFF' },
    'MEMBER20': { pct: 20, label: '20% OFF' },
    'SPESIAL25': { pct: 25, label: '25% OFF' },
    'GRATIS5': { pct: 5, label: '5% OFF' },
    'OPENING50': { pct: 50, label: '50% OFF' },
};

function applyPromoDirect(code) {
    const input = document.getElementById('inputPromoCode');
    if (input) input.value = code;
    applyPromoCode();
}

function applyPromoCode() {
    const code = document.getElementById('inputPromoCode').value.trim().toUpperCase();
    if (!code) {
        Swal.fire({ icon: 'warning', title: 'Kode kosong!', text: 'Masukkan kode promo terlebih dahulu.', timer: 1800, showConfirmButton: false });
        return;
    }
    if (PROMO_CODES[code]) {
        const promo = PROMO_CODES[code];
        promoDiscount = promo.pct;
        promoCode = code;
        document.getElementById('inputKodePromo').value = code;
        document.getElementById('promoCodeApplied').textContent = code;
        document.getElementById('promoDiscText').textContent = promo.label;
        document.getElementById('promoInputWrap').style.display = 'none';
        const chipsScroll = document.getElementById('promoChipsScroll');
        if (chipsScroll) chipsScroll.style.display = 'none';
        document.getElementById('promoActiveBadgeRow').style.display = 'flex';
        document.getElementById('promoDiscPct').textContent = promo.pct;
        hitungTotalAkhir();
        Swal.fire({ icon: 'success', title: 'Promo Aktif!', html: `Kode <strong>${code}</strong> berhasil digunakan. Diskon <strong>${promo.label}</strong>!`, timer: 2000, showConfirmButton: false });
    } else {
        Swal.fire({ icon: 'error', title: 'Kode Tidak Valid', text: `Kode promo "${code}" tidak ditemukan atau sudah kadaluarsa.`, timer: 2000, showConfirmButton: false });
    }
}

function removePromoCode(silent = false) {
    promoDiscount = 0;
    promoCode = '';
    const promoInputValue = document.getElementById('inputKodePromo');
    if (promoInputValue) promoInputValue.value = '';
    const input = document.getElementById('inputPromoCode');
    if (input) input.value = '';
    const wrap = document.getElementById('promoInputWrap');
    if (wrap) wrap.style.display = 'flex';
    const chipsScroll = document.getElementById('promoChipsScroll');
    if (chipsScroll) chipsScroll.style.display = 'flex';
    const badgeRow = document.getElementById('promoActiveBadgeRow');
    if (badgeRow) badgeRow.style.display = 'none';
    const discRow = document.getElementById('promoDiscountRow');
    if (discRow) discRow.style.display = 'none';
    if (!silent) hitungTotalAkhir();
}

function setAdditionalDiscount(pct) {
    const input = document.getElementById('inputDiskonTambahan');
    if (input) {
        input.value = Math.min(100, Math.max(0, pct));
        hitungTotalAkhir();
    }
}

function copyTransferRekening() {
    const rekEl = document.getElementById('transferRekening');
    const rek = rekEl ? rekEl.textContent.trim() : '';
    if (!rek || rek === '---') return;
    const cleanNumber = rek.replace(/[^0-9]/g, '');
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(cleanNumber).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Tersalin!',
                text: `No. Rekening ${rek} disalin ke clipboard.`,
                timer: 1400,
                showConfirmButton: false
            });
        }).catch(() => fallbackCopy(cleanNumber, rek));
    } else {
        fallbackCopy(cleanNumber, rek);
    }
}

function fallbackCopy(val, label) {
    const dummy = document.createElement('textarea');
    dummy.value = val;
    document.body.appendChild(dummy);
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);
    Swal.fire({
        icon: 'success',
        title: 'Tersalin!',
        text: `No. Rekening ${label} disalin ke clipboard.`,
        timer: 1400,
        showConfirmButton: false
    });
}

/* ═══ PAYMENT HELPERS ═══ */
let selectedBank = 'BCA';
let selectedTransferBank = 'BCA';
let selectedCardType = 'Debit';
let qrisConfirmed = false;
const TRANSFER_REKENING = { BCA: '123-456-7890', Mandiri: '900-123-4567', BNI: '234-567-8901', BRI: '345-678-9012' };

function selectBank(bank, el) {
    selectedBank = bank;
    document.querySelectorAll('#kartuArea .bank-chip').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    updatePaymentMode();
}
function selectTransferBank(bank, el) {
    selectedTransferBank = bank;
    document.querySelectorAll('#transferArea .bank-chip').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('transferRekening').textContent = TRANSFER_REKENING[bank] || '---';
    updatePaymentMode();
}
function selectCardType(type, el) {
    selectedCardType = type;
    document.querySelectorAll('.card-type-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    updatePaymentMode();
}
function updatePaymentMode() {
    const mode = document.getElementById('inputModePembayaran').value;
    const gt = parseInt(document.getElementById('inputTotal').value) || 0;
    if (mode === 'QRIS') {
        document.getElementById('inputBayarQRIS').value = gt;
        document.getElementById('inputKembalianQRIS').value = 0;
        document.getElementById('qrisAmount').textContent = 'Rp ' + gt.toLocaleString('id-ID');
    } else if (mode === 'Debit/Kredit') {
        const fullMode = selectedCardType + ' ' + selectedBank;
        document.getElementById('inputModePembayaran').value = fullMode;
        document.getElementById('inputBayarKartu').value = gt;
        document.getElementById('inputKembalianKartu').value = 0;
        document.getElementById('cardTotalAmt').textContent = 'Rp ' + gt.toLocaleString('id-ID');
    } else if (mode === 'Transfer') {
        const fullMode = 'Transfer ' + selectedTransferBank;
        document.getElementById('inputModePembayaran').value = fullMode;
        document.getElementById('inputBayarTransfer').value = gt;
        document.getElementById('inputKembalianTransfer').value = 0;
        document.getElementById('transferAmountDisplay').textContent = 'Rp ' + gt.toLocaleString('id-ID');
    }
}

function konfirmasiQRIS() {
    const gt = parseInt(document.getElementById('inputTotal').value) || 0;
    Swal.fire({
        title: 'Konfirmasi Pembayaran QRIS',
        html: `Nominal: <strong class="text-primary fs-5">Rp ${gt.toLocaleString('id-ID')}</strong><br><small class="text-muted">Pastikan notifikasi pembayaran QRIS sudah diterima.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check"></i> Sudah Dibayar',
        cancelButtonText: 'Belum'
    }).then(r => {
        if (r.isConfirmed) {
            qrisConfirmed = true;
            document.getElementById('inputQRISConfirmed').value = '1';
            document.getElementById('payStatusDot').classList.add('ready');
            Swal.fire({ icon: 'success', title: 'QRIS Dikonfirmasi!', text: 'Silakan lanjutkan simpan transaksi.', timer: 1500, showConfirmButton: false });
        }
    });
}

/* ═══ CALCULATION & QUICK CASH ═══ */
function hitungTotalAkhir() {
    const sub = cart.reduce((acc, i) => acc + i.harga * i.qty, 0);

    // Member discount
    const dMbr = Math.round(sub * memberDiscount / 100);

    // Promo discount (applied on sub - member disc)
    const afterMember = sub - dMbr;
    const dPromo = Math.round(afterMember * promoDiscount / 100);

    // Additional manual discount
    const additionalDiscountInput = document.getElementById('inputDiskonTambahan');
    const enteredAdditionalPct = parseFloat(additionalDiscountInput.value) || 0;
    const dAddPct = Math.min(100, Math.max(0, enteredAdditionalPct));
    if (additionalDiscountInput.value !== '' && enteredAdditionalPct !== dAddPct) {
        additionalDiscountInput.value = dAddPct;
    }
    const dAdd = Math.round((afterMember - dPromo) * dAddPct / 100);

    const totalDiskon = dMbr + dPromo + dAdd;
    const sthDiskon = Math.max(0, sub - totalDiskon);
    const tax = Math.round(sthDiskon * 0.11);
    const grand = sthDiskon + tax;

    document.getElementById('displaySubtotal').textContent = 'Rp ' + sub.toLocaleString('id-ID');
    document.getElementById('displayTax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
    document.getElementById('displayTotal').textContent = 'Rp ' + grand.toLocaleString('id-ID');

    document.getElementById('inputTotal').value = grand;
    document.getElementById('inputDiskonVal').value = totalDiskon;

    // ── Update discount rows visibility ──
    const hasMember = dMbr > 0;
    const hasPromo = dPromo > 0;
    const hasAdditional = dAdd > 0;
    const hasAnyDiscount = hasMember || hasPromo || hasAdditional;

    // Member row
    const memberRow = document.getElementById('memberDiscountRow');
    if (memberRow) {
        memberRow.style.display = hasMember ? 'grid' : 'none';
        const mEl = document.getElementById('displayDiskonMember');
        if (mEl) mEl.textContent = '- Rp ' + dMbr.toLocaleString('id-ID');
        const mpEl = document.getElementById('memberDiscountPercent');
        if (mpEl) mpEl.textContent = memberDiscount;
    }

    // Promo row
    const promoRow = document.getElementById('promoDiscountRow');
    if (promoRow) {
        promoRow.style.display = hasPromo ? 'grid' : 'none';
        const pEl = document.getElementById('displayDiskonPromo');
        if (pEl) pEl.textContent = '- Rp ' + dPromo.toLocaleString('id-ID');
    }

    // Additional row
    const addRow = document.getElementById('additionalDiscountRow');
    if (addRow) {
        addRow.style.display = hasAdditional ? 'grid' : 'none';
        const aEl = document.getElementById('displayDiskonTambahan');
        if (aEl) aEl.textContent = '- Rp ' + dAdd.toLocaleString('id-ID');
        const apEl = document.getElementById('additionalDiscPct');
        if (apEl) apEl.textContent = dAddPct;
    }

    // Total discount row
    const discTotalRow = document.getElementById('discTotalRow');
    if (discTotalRow) {
        discTotalRow.style.display = hasAnyDiscount ? 'flex' : 'none';
        // Find the displayDiskon span inside discTotalRow
        const dtEl = discTotalRow.querySelector('span:last-child');
        if (dtEl) dtEl.textContent = '- Rp ' + totalDiskon.toLocaleString('id-ID');
    }

    // Active discount badge
    const badge = document.getElementById('discountActiveBadge');
    if (badge) {
        badge.style.display = hasAnyDiscount ? 'inline-flex' : 'none';
        const badgeTxt = document.getElementById('discountBadgeText');
        if (badgeTxt) badgeTxt.textContent = 'Hemat Rp ' + totalDiskon.toLocaleString('id-ID');
    }
    const discountTabIndicator = document.getElementById('discountTabIndicator');
    if (discountTabIndicator) discountTabIndicator.style.display = hasAnyDiscount ? 'inline-block' : 'none';

    // Poin member: 1 poin per Rp 10.000
    const idPembeli = document.getElementById('inputIdMember').value;
    document.getElementById('inputPoinDidapat').value = idPembeli ? Math.floor(grand / 10000) : 0;

    // Refresh quick cash buttons
    generateQuickCash(grand);

    // Sync non-cash payment areas
    const mode = document.getElementById('inputModePembayaran').value;
    if (mode === 'Cash') {
        formatAndCalculateCash();
    } else {
        if (mode === 'QRIS' && qrisConfirmed) {
            qrisConfirmed = false;
            document.getElementById('inputQRISConfirmed').value = '0';
            document.getElementById('payStatusDot').classList.remove('ready');
        }
        // update QRIS / card / transfer amounts
        document.getElementById('qrisAmount').textContent = 'Rp ' + grand.toLocaleString('id-ID');
        document.getElementById('cardTotalAmt').textContent = 'Rp ' + grand.toLocaleString('id-ID');
        document.getElementById('transferAmountDisplay').textContent = 'Rp ' + grand.toLocaleString('id-ID');
        // update hidden bayar fields for non-cash
        ['inputBayarQRIS','inputBayarKartu','inputBayarTransfer'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = grand;
        });
    }
}

function adjustAdditionalDiscount(amount) {
    const input = document.getElementById('inputDiskonTambahan');
    const current = parseInt(input.value, 10) || 0;
    input.value = Math.min(100, Math.max(0, current + amount));
    hitungTotalAkhir();
}

function generateQuickCash(total) {
    const wrap = document.getElementById('quickCashList');
    if (!total || total <= 0) { wrap.innerHTML = ''; return; }

    const options = [
        { label: 'Uang Pas', val: total },
        { label: '10k', val: Math.ceil(total / 10000) * 10000 },
        { label: '20k', val: Math.ceil(total / 20000) * 20000 },
        { label: '50k', val: Math.ceil(total / 50000) * 50000 },
        { label: '100k', val: Math.ceil(total / 100000) * 100000 }
    ];

    // Filter duplikat
    const seen = new Set();
    const unique = options.filter(o => {
        if (o.val < total || seen.has(o.val)) return false;
        seen.add(o.val);
        return true;
    });

    wrap.innerHTML = unique.map(o => `
        <button type="button" class="quick-cash-btn" onclick="applyQuickCash(${o.val})" aria-label="Bayar ${o.label} Rp ${o.val}">${o.label}</button>
    `).join('');
}

function applyQuickCash(val) {
    document.getElementById('inputBayar').value = val;
    document.getElementById('inputBayarFormatted').value = val.toLocaleString('id-ID');
    formatAndCalculateCash();
}

function formatAndCalculateCash() {
    const raw = document.getElementById('inputBayarFormatted').value.replace(/[^0-9]/g, '');
    const bayar = parseInt(raw) || 0;
    document.getElementById('inputBayar').value = bayar;
    if (raw) document.getElementById('inputBayarFormatted').value = bayar.toLocaleString('id-ID');

    const total = parseInt(document.getElementById('inputTotal').value) || 0;
    const kembalian = bayar - total;

    const dispEl = document.getElementById('displayKembalian');
    const dot = document.getElementById('payStatusDot');

    if (bayar === 0) {
        dispEl.textContent = 'Rp 0';
        dispEl.className = 'kembalian-val text-muted';
        document.getElementById('inputKembalian').value = 0;
        if (dot) dot.classList.remove('ready');
    } else if (kembalian < 0) {
        dispEl.textContent = 'Kurang Rp ' + Math.abs(kembalian).toLocaleString('id-ID');
        dispEl.className = 'kembalian-val text-danger fw-bold';
        document.getElementById('inputKembalian').value = 0;
        if (dot) dot.classList.remove('ready');
    } else {
        dispEl.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
        dispEl.className = 'kembalian-val text-success fw-bold';
        document.getElementById('inputKembalian').value = kembalian;
        if (dot) dot.classList.add('ready');
    }
}

/* ═══ ORDER TYPE / MEJA / MEMBER ═══ */
function toggleOrderType(type) {
    document.getElementById('inputTipePesanan').value = type;
    const sel = document.getElementById('selectMeja');
    sel.disabled = (type !== 'Dine-in');
    if (type !== 'Dine-in') {
        sel.value = '0';
        document.getElementById('inputIdMeja').value = '0';
    }
}

function updateMeja(id) {
    document.getElementById('inputIdMeja').value = id;
}

function applyMember(id) {
    const sel = document.getElementById('selectMember');
    const opt = sel.options[sel.selectedIndex];
    memberDiscount = id ? (parseFloat(opt.dataset.diskon) || 0) : 0;
    document.getElementById('inputIdMember').value = id || '';
    document.getElementById('inputNamaPelanggan').value = id ? opt.dataset.nama : 'Umum';
    document.getElementById('memberDiscountPercent').textContent = memberDiscount;
    hitungTotalAkhir();
}

function switchCheckoutFeature(feature, button) {
    const showDiscount = feature === 'discount';
    const discountPanel = document.getElementById('discountFeaturePanel');
    const paymentPanel = document.getElementById('paymentFeaturePanel');

    discountPanel.style.display = showDiscount ? '' : 'none';
    paymentPanel.style.display = showDiscount ? 'none' : '';
    discountPanel.setAttribute('aria-hidden', showDiscount ? 'false' : 'true');
    paymentPanel.setAttribute('aria-hidden', showDiscount ? 'true' : 'false');

    document.querySelectorAll('.checkout-feature-tab').forEach(tab => {
        const isActive = tab === button;
        tab.classList.toggle('active', isActive);
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
}

function togglePaymentMode(mode, button) {
    qrisConfirmed = false;
    document.getElementById('inputQRISConfirmed').value = '0';
    document.getElementById('inputModePembayaran').value = mode;

    document.querySelectorAll('.pay-method-btn').forEach(function(methodButton) {
        const isActive = methodButton === button;
        methodButton.classList.toggle('active', isActive);
        methodButton.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    // Show/hide pay areas
    document.getElementById('cashArea').style.display = (mode === 'Cash') ? '' : 'none';
    document.getElementById('qrisArea').style.display = (mode === 'QRIS') ? '' : 'none';
    document.getElementById('kartuArea').style.display = (mode === 'Debit/Kredit') ? '' : 'none';
    document.getElementById('transferArea').style.display = (mode === 'Transfer') ? '' : 'none';

    // Update payment status dot
    const dot = document.getElementById('payStatusDot');
    if (dot) dot.classList.remove('ready');

    const gt = parseInt(document.getElementById('inputTotal').value) || 0;

    if (mode === 'Cash') {
        formatAndCalculateCash();
    } else if (mode === 'QRIS') {
        document.getElementById('qrisAmount').textContent = 'Rp ' + gt.toLocaleString('id-ID');
        document.getElementById('inputBayarQRIS').value = gt;
        document.getElementById('inputKembalianQRIS').value = 0;
        document.getElementById('inputBayar').value = gt;
        document.getElementById('inputKembalian').value = 0;
    } else if (mode === 'Debit/Kredit') {
        const fullMode = selectedCardType + ' ' + selectedBank;
        document.getElementById('inputModePembayaran').value = fullMode;
        document.getElementById('cardTotalAmt').textContent = 'Rp ' + gt.toLocaleString('id-ID');
        document.getElementById('inputBayarKartu').value = gt;
        document.getElementById('inputKembalianKartu').value = 0;
        document.getElementById('inputBayar').value = gt;
        document.getElementById('inputKembalian').value = 0;
        if (dot) dot.classList.add('ready');
    } else if (mode === 'Transfer') {
        const fullMode = 'Transfer ' + selectedTransferBank;
        document.getElementById('inputModePembayaran').value = fullMode;
        document.getElementById('transferAmountDisplay').textContent = 'Rp ' + gt.toLocaleString('id-ID');
        document.getElementById('inputBayarTransfer').value = gt;
        document.getElementById('inputKembalianTransfer').value = 0;
        document.getElementById('inputBayar').value = gt;
        document.getElementById('inputKembalian').value = 0;
        document.getElementById('transferRekening').textContent = TRANSFER_REKENING[selectedTransferBank] || '---';
        if (dot) dot.classList.add('ready');
    }
}

/* ═══ BARCODE / SKU SCANNER ═══ */
function triggerBarcodeScan() {
    Swal.fire({
        title: 'Scan Barcode / SKU',
        text: 'Arahkan barcode scanner atau masukkan ID Menu',
        icon: 'info',
        input: 'text',
        inputPlaceholder: 'ID Menu atau Barcode...',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        confirmButtonText: 'Tambahkan ke Keranjang',
        cancelButtonText: 'Batal',
        preConfirm: val => {
            if (!val) Swal.showValidationMessage('ID tidak boleh kosong!');
            return val;
        }
    }).then(r => {
        if (!r.isConfirmed) return;
        const prod = document.querySelector(`.menu-item[data-id="${r.value}"]`);
        if (prod) {
            prod.click();
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Produk ditambahkan ke keranjang.', timer: 1200, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'Tidak Ditemukan', text: `Menu dengan ID "${r.value}" tidak ditemukan.` });
        }
    });
}

/* ═══ SIMPAN TRANSAKSI ═══ */
function simpanTransaksi() {
    unlockTransactionAudio();
    const total = parseInt(document.getElementById('inputTotal').value) || 0;
    const mode  = document.getElementById('inputModePembayaran').value;
    const tipe  = document.getElementById('inputTipePesanan').value;
    const meja  = document.getElementById('inputIdMeja').value;

    // Sync bayar fields for non-cash
    let bayar = 0;
    if (mode === 'Cash') {
        bayar = parseInt(document.getElementById('inputBayar').value) || 0;
    } else {
        // For non-cash, bayar = total
        bayar = total;
        document.getElementById('inputBayar').value = total;
        document.getElementById('inputKembalian').value = 0;
    }

    // Sync nomor_referensi for Kartu / Transfer
    if (mode.startsWith('Debit') || mode.startsWith('Kredit')) {
        const ref = document.getElementById('inputCardRef')?.value || '';
        document.getElementById('inputNomorReferensi').value = ref;
        // Also update mode with card type + bank
        const modeText = selectedCardType + ' ' + selectedBank;
        document.getElementById('inputModePembayaran').value = modeText;
    } else if (mode === 'Transfer' || mode.startsWith('Transfer')) {
        const ref = document.getElementById('inputTransferRef')?.value || '';
        document.getElementById('inputNomorReferensi').value = ref;
        const modeText = 'Transfer ' + selectedTransferBank;
        document.getElementById('inputModePembayaran').value = modeText;
    }

    if (!cart.length) {
        Swal.fire('Keranjang Kosong', 'Silakan pilih menu terlebih dahulu!', 'warning');
        return;
    }
    if (tipe === 'Dine-in' && (!meja || meja === '0')) {
        Swal.fire('Pilih Meja!', 'Nomor meja wajib dipilih untuk pesanan Dine-in.', 'warning');
        return;
    }
    if ((mode === 'Cash') && bayar < total) {
        Swal.fire('Nominal Kurang!', 'Nominal pembayaran kurang dari total tagihan.', 'warning');
        return;
    }
    if (mode === 'QRIS' && !qrisConfirmed) {
        Swal.fire('Konfirmasi QRIS', 'Pastikan pembayaran sudah masuk, lalu konfirmasi terlebih dahulu.', 'warning');
        return;
    }
    if ((mode.startsWith('Debit') || mode.startsWith('Kredit')) && !document.getElementById('inputCardRef').value.trim()) {
        Swal.fire('Referensi Kartu', 'Masukkan nomor referensi atau kode approval dari mesin EDC.', 'warning');
        return;
    }
    if (mode.startsWith('Transfer') && !document.getElementById('inputTransferRef').value.trim()) {
        Swal.fire('Referensi Transfer', 'Masukkan nomor transaksi dari bukti transfer.', 'warning');
        return;
    }

    // Build summary HTML
    const diskon = parseInt(document.getElementById('inputDiskonVal').value) || 0;
    const modeDisplay = document.getElementById('inputModePembayaran').value;
    let summaryHtml = `
        <div style="text-align:left;margin:0 .5rem;">
            <table style="width:100%;font-size:.85rem;border-collapse:collapse;">
                <tr><td style="color:#64748b;padding:.2rem 0;">Total Tagihan</td><td style="text-align:right;font-weight:900;color:#4f46e5;font-size:1.05rem;">Rp ${total.toLocaleString('id-ID')}</td></tr>
                ${diskon > 0 ? `<tr><td style="color:#ef4444;padding:.2rem 0;">Diskon</td><td style="text-align:right;color:#ef4444;font-weight:700;">- Rp ${diskon.toLocaleString('id-ID')}</td></tr>` : ''}
                <tr><td style="color:#64748b;padding:.2rem 0;">Metode</td><td style="text-align:right;font-weight:700;">${modeDisplay}</td></tr>
                <tr><td style="color:#64748b;padding:.2rem 0;">Layanan</td><td style="text-align:right;font-weight:700;">${tipe}${meja && meja !== '0' ? ' (Meja '+meja+')' : ''}</td></tr>
            </table>
        </div>`;

    Swal.fire({
        title: '<i class="fas fa-receipt" style="color:#6366f1;"></i> Konfirmasi Transaksi',
        html: summaryHtml,
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-print"></i> Simpan &amp; Cetak Struk',
        cancelButtonText: '<i class="fas fa-arrow-left"></i> Kembali',
        customClass: { confirmButton: 'px-4', cancelButton: 'px-4' }
    }).then(async r => {
        if (!r.isConfirmed) return;

        Swal.fire({
            title: 'Memproses...',
            html: '<div style="display:flex;align-items:center;justify-content:center;gap:.75rem;"><i class="fas fa-spinner fa-spin" style="font-size:1.5rem;color:#6366f1;"></i><span>Menyimpan transaksi...</span></div>',
            allowOutsideClick: false,
            showConfirmButton: false
        });

        try {
            const form = document.getElementById('formSimpan');
            form.querySelectorAll('.cart-hidden-input').forEach(e => e.remove());
            cart.forEach((i, idx) => {
                form.insertAdjacentHTML('beforeend', `<input type="hidden" class="cart-hidden-input" name="items[${idx}][id_menu]" value="${i.id_menu}">`);
                form.insertAdjacentHTML('beforeend', `<input type="hidden" class="cart-hidden-input" name="items[${idx}][qty]" value="${i.qty}">`);
                form.insertAdjacentHTML('beforeend', `<input type="hidden" class="cart-hidden-input" name="items[${idx}][subtotal]" value="${i.harga * i.qty}">`);
            });

            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();
            if (!response.ok || result.status !== 'success' || !Number.isInteger(Number(result.id)) || Number(result.id) < 1) {
                throw new Error(result.message || 'Gagal menyimpan transaksi.');
            }

            playTransactionSound(true);
            await new Promise(resolve => setTimeout(resolve, 400));
            window.location.href = `<?= base_url('penjualan/detail') ?>/${encodeURIComponent(result.id)}`;
        } catch (error) {
            playTransactionSound(false);
            Swal.fire({
                icon: 'error',
                title: 'Transaksi Gagal',
                text: error.message || 'Transaksi tidak dapat disimpan. Silakan coba lagi.'
            });
        }
    });
}

/* ═══ RECEIPT MODAL & PRINTING ═══ */
function openReceiptModal(idPenjualan, evt) {
    if (evt) evt.stopPropagation();

    // Fetch order details via AJAX
    fetch(`<?= base_url('penjualan/get_order_receipt') ?>/${idPenjualan}`)
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') {
                Swal.fire('Error', res.message || 'Gagal memuat struk', 'error');
                return;
            }
            populateReceiptModal(res.order, res.items);
            const modal = new bootstrap.Modal(document.getElementById('modalReceiptQuick'));
            modal.show();
        })
        .catch(() => {
            Swal.fire('Error', 'Gagal memuat struk pesanan.', 'error');
        });
}

function populateReceiptModal(order, items) {
    document.getElementById('recInvoice').textContent = order.invoice;
    document.getElementById('recWaktu').textContent = order.tanggal || '';
    document.getElementById('recKasir').textContent = order.kasir || 'Kasir';
    document.getElementById('recPelanggan').textContent = order.nama_pelanggan || 'Umum';
    document.getElementById('recLayanan').textContent = `${order.tipe_pesanan} ${order.nomor_meja ? '(Meja ' + order.nomor_meja + ')' : ''}`;

    // Items list
    const itemsWrap = document.getElementById('recItemsList');
    let subtotal = 0;
    itemsWrap.innerHTML = (items || []).map(i => {
        const lineTotal = parseInt(i.harga) * parseInt(i.qty);
        subtotal += lineTotal;
        return `
          <div class="receipt-item-row">
            <div class="receipt-item-top">
              <span>${escapeHtml(i.nama_menu)}</span>
              <span>Rp ${lineTotal.toLocaleString('id-ID')}</span>
            </div>
            <div class="receipt-item-sub">${i.qty}x @ Rp ${parseInt(i.harga).toLocaleString('id-ID')}</div>
          </div>
        `;
    }).join('');

    const diskon = parseInt(order.diskon) || 0;
    const ppn = Math.round(Math.max(0, subtotal - diskon) * 0.11);
    const total = parseInt(order.total_harga) || 0;
    const bayar = parseInt(order.bayar) || 0;
    const kembalian = parseInt(order.kembalian) || 0;

    document.getElementById('recSubtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    const diskonRow = document.getElementById('recDiskonRow');
    if (diskon > 0) {
        diskonRow.style.display = 'flex';
        document.getElementById('recDiskon').textContent = '- Rp ' + diskon.toLocaleString('id-ID');
    } else {
        diskonRow.style.display = 'none';
    }

    document.getElementById('recPpn').textContent = 'Rp ' + ppn.toLocaleString('id-ID');
    document.getElementById('recTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('recMode').textContent = order.mode_pembayaran || 'Cash';
    document.getElementById('recBayar').textContent = 'Rp ' + bayar.toLocaleString('id-ID');
    document.getElementById('recKembalian').textContent = 'Rp ' + kembalian.toLocaleString('id-ID');

    // Link ke detail full
    document.getElementById('btnRecFullInvoice').href = `<?= base_url('penjualan/detail') ?>/${order.id_penjualan}`;
}

function printReceiptThermalNow() {
    const printContent = document.getElementById('receiptPaperArea').innerHTML;
    const printWin = window.open('', '_blank', 'width=400,height=600');
    printWin.document.write(`
      <html>
        <head>
          <title>Struk Thermal</title>
          <style>
            @page { margin: 0; size: 58mm auto; }
            body {
              font-family: 'Courier New', Courier, monospace;
              width: 58mm;
              margin: 0;
              padding: 6px;
              font-size: 11px;
              color: #000;
            }
            .receipt-divider { border-top: 1px dashed #000; margin: 4px 0; }
            .receipt-line { display: flex; justify-content: space-between; margin-bottom: 2px; }
            .receipt-brand-logo { display:block; width:34mm; height:auto; object-fit:contain; margin:0 auto 2mm; }
            .receipt-item-row { margin-bottom: 3px; }
            .receipt-item-top { display: flex; justify-content: space-between; font-weight: bold; }
            .receipt-item-sub { font-size: 9px; color: #333; }
            .text-center { text-align: center; }
            .fw-bold { font-weight: bold; }
            .mt-1 { margin-top: 4px; }
            .mt-2 { margin-top: 8px; }
          </style>
        </head>
        <body>${printContent}</body>
      </html>
    `);
    printWin.document.close();
    printWin.focus();
    setTimeout(() => {
        printWin.print();
        printWin.close();
    }, 250);
}

/* ═══ UTILS ═══ */
function escapeHtml(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
