<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengaturan_model extends CI_Model {
    public function get_value($key, $default = '') {
        $row = $this->db->get_where('app_settings', ['setting_key' => $key])->row();
        return $row ? $row->setting_value : $default;
    }

    public function set_value($key, $value) {
        return $this->db->replace('app_settings', [
            'setting_key' => $key,
            'setting_value' => $value
        ]);
    }
}
