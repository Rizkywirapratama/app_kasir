<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {
    public function get_laporan($tgl_mulai = null, $tgl_akhir = null) {
        // Validate date format
        if ($tgl_mulai && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_mulai)) return [];
        if ($tgl_akhir && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_akhir)) return [];

        $this->db->select('tbl_penjualan.*, tbl_meja.nomor_meja');
        $this->db->from('tbl_penjualan');
        $this->db->join('tbl_meja', 'tbl_meja.id_meja = tbl_penjualan.id_meja', 'left');
        
        if ($tgl_mulai && $tgl_akhir) {
            $this->db->where('DATE(tanggal) >=', $tgl_mulai);
            $this->db->where('DATE(tanggal) <=', $tgl_akhir);
        } else {
            // Default: hari ini
            $this->db->where('DATE(tanggal)', date('Y-m-d'));
        }
        
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }

    public function get_ringkasan($tgl_mulai = null, $tgl_akhir = null) {
        $this->db->select('COUNT(id_penjualan) as total_trx, SUM(total_harga) as total_pendapatan');
        $this->db->from('tbl_penjualan');
        
        if ($tgl_mulai && $tgl_akhir) {
            $this->db->where('DATE(tanggal) >=', $tgl_mulai);
            $this->db->where('DATE(tanggal) <=', $tgl_akhir);
        } else {
            $this->db->where('DATE(tanggal)', date('Y-m-d'));
        }
        
        return $this->db->get()->row();
    }
}
