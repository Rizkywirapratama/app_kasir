<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift_model extends CI_Model {

    // Cek apakah user memiliki shift yang sedang buka
    public function cek_shift_aktif($id_user) {
        $this->db->where('id_user', $id_user);
        $this->db->where('status', 'buka');
        return $this->db->get('tbl_shift')->row();
    }

    // Buka shift baru
    public function buka_shift($data) {
        if (!$this->db->insert('tbl_shift', $data)) {
            return FALSE;
        }
        $insert_id = $this->db->insert_id();
        return $insert_id ? $insert_id : FALSE;
    }

    // Tutup shift
    public function tutup_shift($id_shift, $data) {
        $this->db->where('id_shift', $id_shift);
        $this->db->where('status', 'buka');
        return $this->db->update('tbl_shift', $data) && $this->db->affected_rows() === 1;
    }

    // Hitung rekapitulasi penjualan selama shift
    public function get_rekap_penjualan($id_shift) {
        $this->db->select('
            SUM(CASE WHEN mode_pembayaran = "Cash" THEN bayar - kembalian ELSE 0 END) as total_cash,
            SUM(CASE WHEN mode_pembayaran = "QRIS" THEN total_harga ELSE 0 END) as total_qris,
            SUM(CASE WHEN mode_pembayaran LIKE "Debit%" OR mode_pembayaran LIKE "Kredit%" OR mode_pembayaran LIKE "Transfer%" THEN total_harga ELSE 0 END) as total_debit,
            SUM(total_harga) as total_semua
        ');
        $this->db->from('tbl_penjualan');
        $this->db->where('id_shift', $id_shift);
        $query = $this->db->get();
        return $query->row();
    }

    // Mendapatkan data satu shift
    public function get_shift($id_shift) {
        $this->db->select('tbl_shift.*, users.nama as nama_kasir');
        $this->db->from('tbl_shift');
        $this->db->join('users', 'users.id = tbl_shift.id_user', 'left');
        $this->db->where('tbl_shift.id_shift', $id_shift);
        return $this->db->get()->row();
    }

    // Mendapatkan seluruh riwayat sesi shift
    public function get_all_shift() {
        $this->db->select('tbl_shift.*, users.nama as nama_kasir');
        $this->db->from('tbl_shift');
        $this->db->join('users', 'users.id = tbl_shift.id_user', 'left');
        $this->db->order_by('tbl_shift.id_shift', 'DESC');
        return $this->db->get()->result();
    }
}
