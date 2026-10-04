<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna_model extends CI_Model {
    public function get_all() {
        return $this->db->get('users')->result();
    }

    public function get_status_counts() {
        $query = $this->db->query(
            'SELECT
                COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) AS active,
                COALESCE(SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END), 0) AS inactive
             FROM users'
        );

        return $query->row();
    }

    public function get_by_id($id) {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function get_active_admin_count() {
        $this->db->where('role', 'admin');
        $this->db->where('is_active', 1);
        return $this->db->count_all_results('users');
    }

    public function insert($data) {
        return $this->db->insert('users', $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function set_active($id, $is_active) {
        $this->db->where('id', $id);
        return $this->db->update('users', ['is_active' => (int)$is_active]);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }

    public function check_username($username, $id = null) {
        $this->db->where('username', $username);
        if ($id) {
            $this->db->where('id !=', $id);
        }
        return $this->db->get('users')->num_rows() > 0;
    }
}
