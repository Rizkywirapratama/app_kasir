<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model {
    public function get_all() {
        $this->db->select('tbl_menu.*, tbl_kategori.nama_kategori');
        $this->db->from('tbl_menu');
        $this->db->join('tbl_kategori', 'tbl_menu.id_kategori = tbl_kategori.id_kategori', 'left');
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('tbl_menu', ['id_menu' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert('tbl_menu', $data);
    }

    public function update($id, $data) {
        $this->db->where('id_menu', $id);
        return $this->db->update('tbl_menu', $data);
    }

    public function delete($id) {
        $this->db->where('id_menu', $id);
        return $this->db->delete('tbl_menu');
    }
}
