<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->load->model('Penjualan_model');
        $this->load->model('Menu_model');
        $this->load->model('Meja_model');
        $this->load->model('Shift_model'); // Tambahkan Shift_model
        $this->load->model('Pengaturan_model');
    }

    public function index() {
        $data['title'] = 'Data Penjualan';
        $data['penjualan'] = $this->Penjualan_model->get_all();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('penjualan/index', $data);
        $this->load->view('templates/footer');
    }

    public function buat() {
        // Cek shift aktif sebelum membuat transaksi
        $id_shift = $this->session->userdata('id_shift');
        if (!$id_shift) {
            $shift_aktif = $this->Shift_model->cek_shift_aktif($this->session->userdata('id_user'));
            if ($shift_aktif) {
                $this->session->set_userdata('id_shift', $shift_aktif->id_shift);
            } else {
                $this->session->set_flashdata('error', 'Anda harus Buka Buku (Shift) terlebih dahulu sebelum bertransaksi.');
                redirect('shift/buka');
            }
        }

        $data['title'] = 'Transaksi Baru';
        $data['menu'] = $this->Menu_model->get_all();
        $data['meja'] = $this->Meja_model->get_tersedia();
        $data['member'] = $this->Penjualan_model->get_all_member();
        $data['invoice'] = $this->Penjualan_model->generate_invoice();
        $data['qris_image'] = $this->Pengaturan_model->get_value('qris_image');
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('penjualan/buat', $data);
        $this->load->view('templates/footer');
    }

    public function simpan() {
        $is_ajax = $this->input->is_ajax_request();
        $fail = function($message, $redirect_to = 'penjualan/buat') use ($is_ajax) {
            if ($is_ajax) {
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode(['status' => 'error', 'message' => $message]));
                return;
            }

            $this->session->set_flashdata('error', $message);
            redirect($redirect_to);
        };

        $items = $this->input->post('items');
        if (!is_array($items) || empty($items)) {
            $fail('Pilih minimal 1 menu!');
            return;
        }

        $id_shift = (int)$this->session->userdata('id_shift');
        $shift_aktif = $this->Shift_model->cek_shift_aktif($this->session->userdata('id_user'));
        if (!$shift_aktif || (int)$shift_aktif->id_shift !== $id_shift) {
            $this->session->unset_userdata('id_shift');
            $fail('Shift tidak aktif. Buka shift terlebih dahulu.', 'shift/buka');
            return;
        }

        $requested_items = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                $fail('Detail menu tidak valid.');
                return;
            }

            $id_menu_input = $item['id_menu'] ?? NULL;
            $qty_input = $item['qty'] ?? NULL;
            if (!is_scalar($id_menu_input) || !is_scalar($qty_input)) {
                $fail('Jumlah atau menu tidak valid.');
                return;
            }
            $id_menu = filter_var($id_menu_input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $qty = filter_var($qty_input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($id_menu === FALSE || $qty === FALSE) {
                $fail('Jumlah atau menu tidak valid.');
                return;
            }

            $requested_items[$id_menu] = ($requested_items[$id_menu] ?? 0) + $qty;
        }

        $data_detail = [];
        $subtotal = 0;
        foreach ($requested_items as $id_menu => $qty) {
            $menu = $this->Menu_model->get_by_id($id_menu);
            if (!$menu || (int)$menu->stok < $qty) {
                $fail('Menu tidak tersedia atau stok tidak mencukupi. Silakan periksa keranjang.');
                return;
            }

            $harga = (int)$menu->harga;
            if ($harga < 0) {
                $fail('Harga menu tidak valid.');
                return;
            }

            $item_subtotal = $harga * $qty;
            $subtotal += $item_subtotal;
            $data_detail[] = [
                'id_menu' => (int)$id_menu,
                'qty' => $qty,
                'subtotal' => $item_subtotal
            ];
        }

        $member_id_input = $this->input->post('id_member');
        $member = NULL;
        if ($member_id_input !== NULL && $member_id_input !== '') {
            if (!is_scalar($member_id_input)) {
                $fail('Data member tidak valid.');
                return;
            }
            $member_id = filter_var($member_id_input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($member_id !== FALSE) {
                foreach ($this->Penjualan_model->get_all_member() as $candidate) {
                    if ((int)$candidate->id_member === (int)$member_id) {
                        $member = $candidate;
                        break;
                    }
                }
            }
            if (!$member) {
                $fail('Data member tidak ditemukan.');
                return;
            }
        }

        $member_discount_pct = $member ? max(0, min(100, (float)$member->diskon_persen)) : 0;
        $promo_input = $this->input->post('kode_promo');
        if ($promo_input !== NULL && !is_string($promo_input)) {
            $fail('Kode promo tidak valid.');
            return;
        }
        $promo_code = strtoupper(trim((string)$promo_input));
        $promo_codes = [
            'HEMAT10' => 10,
            'DISKON15' => 15,
            'MEMBER20' => 20,
            'SPESIAL25' => 25,
            'GRATIS5' => 5,
            'OPENING50' => 50
        ];
        if ($promo_code !== '' && !isset($promo_codes[$promo_code])) {
            $fail('Kode promo tidak valid.');
            return;
        }

        $additional_pct_input = $this->input->post('diskon_tambahan_persen');
        if ($additional_pct_input === NULL || $additional_pct_input === '') {
            $additional_pct = 0;
        } elseif (!is_scalar($additional_pct_input) || !is_numeric($additional_pct_input) || (float)$additional_pct_input < 0 || (float)$additional_pct_input > 100) {
            $fail('Persentase diskon tambahan harus antara 0 dan 100.');
            return;
        } else {
            $additional_pct = (float)$additional_pct_input;
        }

        $discount_member = (int)round($subtotal * $member_discount_pct / 100);
        $after_member = $subtotal - $discount_member;
        $discount_promo = (int)round($after_member * ($promo_codes[$promo_code] ?? 0) / 100);
        $after_promo = $after_member - $discount_promo;
        $discount_additional = (int)round($after_promo * $additional_pct / 100);
        $discount_total = $discount_member + $discount_promo + $discount_additional;
        $after_discount = max(0, $subtotal - $discount_total);
        $tax = (int)round($after_discount * 0.11);
        $total_harga = $after_discount + $tax;

        $tipe_pesanan = $this->input->post('tipe_pesanan');
        if (!in_array($tipe_pesanan, ['Take-away', 'Dine-in'], TRUE)) {
            $fail('Tipe pesanan tidak valid.');
            return;
        }

        $id_meja = 0;
        if ($tipe_pesanan === 'Dine-in') {
            $id_meja_input = $this->input->post('id_meja');
            $id_meja = is_scalar($id_meja_input) ? filter_var($id_meja_input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : FALSE;
            $meja = $id_meja === FALSE ? NULL : $this->Meja_model->get_by_id($id_meja);
            if (!$meja || $meja->status !== 'Tersedia') {
                $fail('Meja tidak tersedia. Silakan pilih meja lain.');
                return;
            }
        }

        $mode_pembayaran = $this->input->post('mode_pembayaran');
        if (!is_string($mode_pembayaran)) {
            $fail('Metode pembayaran tidak valid.');
            return;
        }
        $mode_pembayaran = trim($mode_pembayaran);
        $is_card = preg_match('/^(Debit|Kredit)(\/Kredit)?( (BCA|Mandiri|BNI|BRI|Lainnya))?$/i', $mode_pembayaran);
        $is_transfer = preg_match('/^Transfer (BCA|Mandiri|BNI|BRI)$/i', $mode_pembayaran);
        if (!in_array($mode_pembayaran, ['Cash', 'QRIS'], TRUE) && !$is_card && !$is_transfer) {
            $fail('Metode pembayaran tidak didukung.');
            return;
        }

        $reference_input = $this->input->post('nomor_referensi');
        if ($reference_input !== NULL && !is_string($reference_input)) {
            $fail('Nomor referensi pembayaran tidak valid.');
            return;
        }
        $nomor_referensi = trim((string)$reference_input);
        if (($is_card || $is_transfer) && $nomor_referensi === '') {
            $fail('Nomor referensi pembayaran wajib diisi.');
            return;
        }
        if ($mode_pembayaran === 'QRIS' && $this->input->post('qris_confirmed') !== '1') {
            $fail('Pembayaran QRIS harus dikonfirmasi terlebih dahulu.');
            return;
        }

        if ($mode_pembayaran === 'Cash') {
            $bayar_input = $this->input->post('bayar');
            $bayar = is_scalar($bayar_input) ? filter_var($bayar_input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) : FALSE;
            if ($bayar === FALSE || $bayar < $total_harga) {
                $fail('Nominal tunai kurang dari total tagihan.');
                return;
            }
            $kembalian = $bayar - $total_harga;
        } else {
            $bayar = $total_harga;
            $kembalian = 0;
        }

        $invoice_input = $this->input->post('invoice');
        $invoice = is_scalar($invoice_input) ? trim((string)$invoice_input) : '';
        if ($invoice === '') {
            $invoice = $this->Penjualan_model->generate_invoice();
        }

        $data_penjualan = [
            'invoice' => $invoice,
            'nama_pelanggan' => $member ? $member->nama_member : 'Umum',
            'id_member' => $member ? (int)$member->id_member : NULL,
            'tipe pesanan' => $tipe_pesanan,
            'id_meja' => $id_meja,
            'mode_pembayaran' => $mode_pembayaran,
            'total_harga' => $total_harga,
            'diskon' => $discount_total,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'poin_didapat' => $member ? (int)floor($total_harga / 10000) : 0,
            'nomor_referensi' => $nomor_referensi,
            'id_shift' => $id_shift
        ];

        $insert_id = $this->Penjualan_model->simpan_transaksi($data_penjualan, $data_detail);

        if ($insert_id) {
            // Trigger Real-time notification to Node.js Bridge
            $this->notify_node('new_order', [
                'invoice' => $invoice,
                'total' => $total_harga,
                'tanggal' => date('Y-m-d H:i:s'),
                'tipe pesanan' => $tipe_pesanan,
                'nomor_meja' => $id_meja,
                'id_shift' => $this->session->userdata('id_shift'),
                'items' => $data_detail
            ]);
        }

        if ($is_ajax) {
            if ($insert_id) {
                $this->session->set_flashdata('success', 'Transaksi berhasil disimpan!');
                echo json_encode(['status' => 'success', 'id' => $insert_id]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi']);
            }
            return;
        }

        if ($insert_id) {
            $this->session->set_flashdata('success', 'Transaksi berhasil disimpan!');
            redirect('penjualan/detail/' . $insert_id);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan transaksi!');
            redirect('penjualan/buat');
        }
    }

    private function notify_node($event, $data) {
        $url = 'http://localhost:3000/notify';
        $payload = json_encode(['event' => $event, 'data' => $data]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Timeout cepat agar tidak membebani PHP
        curl_exec($ch);
        curl_close($ch);
    }

    public function detail($id) {
        $data['title'] = 'Detail Transaksi';
        $data['penjualan'] = $this->Penjualan_model->get_by_id($id);
        if (!$data['penjualan']) redirect('penjualan');
        $data['detail'] = $this->Penjualan_model->get_detail($id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('penjualan/detail', $data);
        $this->load->view('templates/footer');
    }

    public function create_xendit_invoice() {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        
        $this->config->load('xendit');
        $secret_key = $this->config->item('xendit_secret_key');
        
        $invoice_id = $this->input->post('invoice');
        $total = (int)$this->input->post('total_harga');
        
        // Payload untuk Xendit
        $payload = [
            'external_id' => $invoice_id . '-' . time(),
            'amount' => $total,
            'description' => 'Pembayaran Pesanan ' . $invoice_id,
            'invoice_duration' => 86400,
            'customer' => [
                'given_names' => 'Pelanggan',
                'surname' => 'Kasir',
            ],
            'success_redirect_url' => base_url('penjualan'),
            'failure_redirect_url' => base_url('penjualan/buat'),
            'currency' => 'IDR',
            'items' => []
        ];

        // Tambahkan item details
        $id_menus = $this->input->post('id_menu');
        $qtys = $this->input->post('qty');
        $hargas = $this->input->post('harga');
        
        for ($i = 0; $i < count($id_menus); $i++) {
            $payload['items'][] = [
                'name' => 'Menu #' . $id_menus[$i],
                'quantity' => (int)$qtys[$i],
                'price' => (int)$hargas[$i]
            ];
        }

        $url = "https://api.xendit.co/v2/invoices";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($secret_key . ':')
        ]);
        
        $result = curl_exec($ch);
        if ($result === false) {
            echo json_encode(['status' => 'error', 'message' => 'cURL Error: ' . curl_error($ch)], JSON_UNESCAPED_UNICODE);
            curl_close($ch);
            return;
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code >= 200 && $http_code < 300) {
            $res = json_decode($result);
            echo json_encode(['status' => 'success', 'invoice_url' => $res->invoice_url], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuat Invoice Xendit: ' . $result], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * AJAX: Kembalikan 30 transaksi terakhir untuk Order List Panel di POS.
     */
    public function get_recent_orders() {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode([]); return;
        }

        // Ambil 30 transaksi terbaru (hari ini + kemarin)
        $rows = $this->Penjualan_model->get_recent_for_panel(30);

        $result = [];
        foreach ($rows as $r) {
            // Ambil nama menu per transaksi
            $detail = $this->Penjualan_model->get_detail($r->id_penjualan);
            $items_preview = '';
            if (!empty($detail)) {
                $names = array_map(function($detail_row) {
                    return $detail_row->nama_menu;
                }, (array)$detail);
                $items_preview = implode(', ', array_slice($names, 0, 3));
                if (count($names) > 3) $items_preview .= ' +' . (count($names) - 3) . ' lainnya';
            }

            $is_today = (!empty($r->tanggal) && date('Y-m-d', strtotime($r->tanggal)) === date('Y-m-d'));

            $result[] = [
                'id_penjualan'  => $r->id_penjualan,
                'invoice'       => $r->invoice,
                'nama_pelanggan'=> $r->nama_pelanggan,
                'tipe_pesanan'  => $r->{'tipe pesanan'} ?? ($r->tipe_pesanan ?? 'Take-away'),
                'nomor_meja'    => $r->nomor_meja ?? null,
                'total_harga'   => $r->total_harga,
                'diskon'        => $r->diskon ?? 0,
                'bayar'         => $r->bayar ?? 0,
                'kembalian'     => $r->kembalian ?? 0,
                'mode_pembayaran'=> $r->mode_pembayaran,
                'waktu_buat'    => $r->tanggal ?? $r->created_at ?? null,
                'is_today'      => $is_today,
                'items_preview' => $items_preview,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * AJAX: Ambil data detail pesanan untuk cetak struk modal di POS
     */
    public function get_order_receipt($id) {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); return;
        }
        $penjualan = $this->Penjualan_model->get_by_id($id);
        if (!$penjualan) {
            echo json_encode(['status' => 'error', 'message' => 'Pesanan tidak ditemukan']); return;
        }
        $detail = $this->Penjualan_model->get_detail($id);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'order'  => [
                'id_penjualan'   => $penjualan->id_penjualan,
                'invoice'        => $penjualan->invoice,
                'nama_pelanggan' => $penjualan->nama_pelanggan,
                'tipe_pesanan'   => $penjualan->{'tipe pesanan'} ?? ($penjualan->tipe_pesanan ?? 'Take-away'),
                'nomor_meja'     => $penjualan->nomor_meja ?? null,
                'total_harga'    => $penjualan->total_harga,
                'diskon'         => $penjualan->diskon,
                'bayar'          => $penjualan->bayar,
                'kembalian'      => $penjualan->kembalian,
                'mode_pembayaran'=> $penjualan->mode_pembayaran,
                'tanggal'        => $penjualan->tanggal,
                'kasir'          => $this->session->userdata('nama')
            ],
            'items'  => $detail
        ]);
    }
}
