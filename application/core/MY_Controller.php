<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        $this->load->model('User_model');
        $user = $this->User_model->get_by_id($this->session->userdata('id_user'));
        if (!$user || (int)$user->is_active !== 1) {
            $this->session->sess_destroy();
            redirect('auth/login');
            return;
        }

        $this->session->set_userdata([
            'id_user' => (int)$user->id,
            'nama' => $user->nama,
            'username' => $user->username,
            'role' => $user->role
        ]);
    }
}
