<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function cek_login($username) {
        $this->db->where('username', $username);
        $query = $this->db->get('users');
        return $query->row();
    }

    public function get_by_id($id) {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function get_all() {
        return $this->db->get('users')->result();
    }
}
