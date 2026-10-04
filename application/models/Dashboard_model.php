<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    public function total_menu() {
        return $this->db->count_all('tbl_menu');
    }

    public function total_penjualan_hari_ini() {
        $this->db->where('DATE(tanggal)', date('Y-m-d'));
        return $this->db->count_all_results('tbl_penjualan');
    }

    public function total_pendapatan_hari_ini() {
        $this->db->select_sum('total_harga');
        $this->db->where('DATE(tanggal)', date('Y-m-d'));
        $query = $this->db->get('tbl_penjualan');
        $row = $query->row();
        return $row->total_harga ? $row->total_harga : 0;
    }

    public function total_meja() {
        return $this->db->count_all('tbl_meja');
    }

    public function meja_tersedia() {
        $this->db->where('status', 'Tersedia');
        return $this->db->count_all_results('tbl_meja');
    }

    public function total_user() {
        return $this->db->count_all('users');
    }

    public function penjualan_terakhir($limit = 5) {
        $this->db->select('p.*, m.nomor_meja');
        $this->db->from('tbl_penjualan p');
        $this->db->join('tbl_meja m', 'm.id_meja = p.id_meja', 'left');
        $this->db->order_by('p.tanggal', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function menu_terlaris($limit = 5) {
        $this->db->select('m.nama_menu, k.nama_kategori, SUM(d.qty) as total_qty, SUM(d.subtotal) as total_penjualan');
        $this->db->from('tbl_detail_penjualan d');
        $this->db->join('tbl_menu m', 'm.id_menu = d.id_menu', 'left');
        $this->db->join('tbl_kategori k', 'k.id_kategori = m.id_kategori', 'left');
        $this->db->group_by('d.id_menu');
        $this->db->order_by('total_qty', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function pendapatan_7_hari() {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = date('Y-m-d', strtotime("-$i days"));
            $this->db->select_sum('total_harga');
            $this->db->where('DATE(tanggal)', $tanggal);
            $query = $this->db->get('tbl_penjualan');
            $row = $query->row();
            $data[] = [
                'tanggal' => date('d M', strtotime($tanggal)),
                'total'   => $row->total_harga ? (float)$row->total_harga : 0,
            ];
        }
        return $data;
    }
    public function shift_aktif_data() {
        $this->db->select('s.*, u.nama as nama_kasir');
        $this->db->from('tbl_shift s');
        $this->db->join('users u', 'u.id = s.id_user');
        $this->db->where('s.status', 'Buka');
        $this->db->order_by('s.waktu_buka', 'DESC');
        $this->db->limit(1);
        $shift = $this->db->get()->row();

        if ($shift) {
            // Hitung total penjualan di shift ini
            $this->db->select_sum('total_harga');
            $this->db->where('id_shift', $shift->id_shift);
            $res = $this->db->get('tbl_penjualan')->row();
            $shift->total_penjualan = $res->total_harga ? $res->total_harga : 0;
            
            $this->db->where('id_shift', $shift->id_shift);
            $shift->jumlah_transaksi = $this->db->count_all_results('tbl_penjualan');
        }
        return $shift;
    }

    public function low_stock_items($threshold = 5) {
        $this->db->where('stok <', $threshold);
        $this->db->order_by('stok', 'ASC');
        return $this->db->get('tbl_menu')->result();
    }
}

