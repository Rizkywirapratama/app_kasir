<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan_model extends CI_Model {
    public function get_all() {
        $this->db->select('tbl_penjualan.*, tbl_meja.nomor_meja');
        $this->db->from('tbl_penjualan');
        $this->db->join('tbl_meja', 'tbl_meja.id_meja = tbl_penjualan.id_meja', 'left');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        $this->db->select('tbl_penjualan.*, tbl_meja.nomor_meja');
        $this->db->from('tbl_penjualan');
        $this->db->join('tbl_meja', 'tbl_meja.id_meja = tbl_penjualan.id_meja', 'left');
        $this->db->where('id_penjualan', $id);
        return $this->db->get()->row();
    }

    public function get_detail($id_penjualan) {
        $this->db->select('tbl_detail_penjualan.*, tbl_menu.nama_menu, tbl_menu.harga');
        $this->db->from('tbl_detail_penjualan');
        $this->db->join('tbl_menu', 'tbl_menu.id_menu = tbl_detail_penjualan.id_menu');
        $this->db->where('id_penjualan', $id_penjualan);
        return $this->db->get()->result();
    }

    public function generate_invoice() {
        $date = date('Ymd');
        $this->db->select('RIGHT(invoice,4) as last_num');
        $this->db->where('DATE(tanggal)', date('Y-m-d'));
        $this->db->order_by('id_penjualan', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tbl_penjualan');

        if ($query->num_rows() > 0) {
            $last_num = intval($query->row()->last_num);
            $new_num = sprintf("%04d", $last_num + 1);
        } else {
            $new_num = "0001";
        }
        return "INV-" . $date . "-" . $new_num;
    }

    public function get_all_member() {
        return $this->db->get('tbl_member')->result();
    }

    public function simpan_transaksi($data_penjualan, $data_detail) {
        $this->db->trans_begin();

        // Menggunakan Raw SQL untuk memastikan kolom berspasi `tipe pesanan` tertangani dengan benar
        $sql = "INSERT INTO tbl_penjualan (invoice, nama_pelanggan, id_member, `tipe pesanan`, id_meja, mode_pembayaran, total_harga, diskon, bayar, kembalian, poin_didapat, nomor_referensi, id_shift) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $inserted = $this->db->query($sql, [
            $data_penjualan['invoice'],
            $data_penjualan['nama_pelanggan'],
            $data_penjualan['id_member'],
            $data_penjualan['tipe pesanan'],
            $data_penjualan['id_meja'],
            $data_penjualan['mode_pembayaran'],
            $data_penjualan['total_harga'],
            $data_penjualan['diskon'],
            $data_penjualan['bayar'],
            $data_penjualan['kembalian'],
            $data_penjualan['poin_didapat'],
            $data_penjualan['nomor_referensi'],
            $data_penjualan['id_shift']
        ]);
        if (!$inserted) {
            $this->db->trans_rollback();
            return FALSE;
        }
        $insert_id = $this->db->insert_id();
        if (!$insert_id) {
            $this->db->trans_rollback();
            return FALSE;
        }

        // Update poin member jika transaksi menggunakan member
        if (!empty($data_penjualan['id_member']) && $data_penjualan['poin_didapat'] > 0) {
            $this->db->set('total_poin', 'total_poin+' . (int)$data_penjualan['poin_didapat'], FALSE);
            $this->db->where('id_member', $data_penjualan['id_member']);
            if (!$this->db->update('tbl_member') || $this->db->affected_rows() !== 1) {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        // Update status meja jika dine-in
        if ($data_penjualan['tipe pesanan'] == 'Dine-in' && !empty($data_penjualan['id_meja'])) {
            $this->db->where('id_meja', $data_penjualan['id_meja']);
            $this->db->where('status', 'Tersedia');
            if (!$this->db->update('tbl_meja', ['status' => 'Terisi']) || $this->db->affected_rows() !== 1) {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        // Insert tabel detail_penjualan dan kurangi stok
        foreach ($data_detail as $detail) {
            $detail['id_penjualan'] = $insert_id;
            if (!$this->db->insert('tbl_detail_penjualan', $detail)) {
                $this->db->trans_rollback();
                return FALSE;
            }

            // Guard stock again inside the transaction to prevent concurrent overselling.
            $this->db->set('stok', 'stok-' . (int)$detail['qty'], FALSE);
            $this->db->where('id_menu', $detail['id_menu']);
            $this->db->where('stok >=', (int)$detail['qty']);
            if (!$this->db->update('tbl_menu') || $this->db->affected_rows() !== 1) {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        if (!$this->db->trans_status()) {
            $this->db->trans_rollback();
            return FALSE;
        }

        return $this->db->trans_commit() ? $insert_id : FALSE;
    }

    public function get_recent_for_panel($limit = 30) {
        $this->db->select('tbl_penjualan.*, tbl_meja.nomor_meja');
        $this->db->from('tbl_penjualan');
        $this->db->join('tbl_meja', 'tbl_meja.id_meja = tbl_penjualan.id_meja', 'left');
        $this->db->order_by('tbl_penjualan.id_penjualan', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
}
