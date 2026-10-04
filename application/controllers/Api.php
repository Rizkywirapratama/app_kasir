<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Penjualan_model');
        $this->load->model('Dashboard_model');
        header('Content-Type: application/json');
    }

    public function menu() {
        $this->db->select('m.*, k.nama_kategori');
        $this->db->from('tbl_menu m');
        $this->db->join('tbl_kategori k', 'k.id_kategori = m.id_kategori', 'left');
        $this->db->where('m.stok >', 0);
        $query = $this->db->get();
        echo json_encode($query->result());
    }

    public function members() {
        $query = $this->db->get('tbl_member');
        echo json_encode($query->result());
    }

    public function tables() {
        $this->db->where('status', 'Tersedia');
        $query = $this->db->get('tbl_meja');
        echo json_encode($query->result());
    }

    public function save_transaction() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            return;
        }

        // Prepare data for Penjualan_model
        $invoice = $this->Penjualan_model->generate_invoice();
        $data_penjualan = [
            'invoice' => $invoice,
            'nama_pelanggan' => $data['nama_pelanggan'] ?? 'Pelanggan Umum',
            'id_member' => $data['id_member'] ?? 0,
            'tipe pesanan' => $data['tipe_pesanan'] ?? 'Take-away',
            'id_meja' => $data['id_meja'] ?? 0,
            'mode_pembayaran' => $data['mode_pembayaran'] ?? 'Cash',
            'total_harga' => $data['total_harga'],
            'diskon' => $data['diskon'] ?? 0,
            'bayar' => $data['bayar'],
            'kembalian' => $data['kembalian'],
            'poin_didapat' => $data['poin_didapat'] ?? 0,
            'nomor_referensi' => '',
            'id_shift' => $this->session->userdata('id_shift')
        ];

        $data_detail = [];
        foreach ($data['items'] as $item) {
            $data_detail[] = [
                'id_menu' => $item['id_menu'],
                'qty' => $item['qty'],
                'harga_satuan' => $item['harga'],
                'subtotal' => $item['harga'] * $item['qty']
            ];
        }

        $insert_id = $this->Penjualan_model->simpan_transaksi($data_penjualan, $data_detail);

        if ($insert_id) {
            echo json_encode(['status' => 'success', 'invoice' => $invoice, 'id' => $insert_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi']);
        }
    }
}
