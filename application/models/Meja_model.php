<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meja_model extends CI_Model {
    public function get_all() {
        return $this->db->get('tbl_meja')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('tbl_meja', ['id_meja' => $id])->row();
    }

    public function get_tersedia() {
        return $this->db->get_where('tbl_meja', ['status' => 'Tersedia'])->result();
    }

    public function insert($data) {
        return $this->db->insert('tbl_meja', $data);
    }

    public function update($id, $data) {
        $this->db->where('id_meja', $id);
        return $this->db->update('tbl_meja', $data);
    }

    public function delete($id) {
        $this->db->where('id_meja', $id);
        return $this->db->delete('tbl_meja');
    }
}
