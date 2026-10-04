<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Shift_model');
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index() {
        $this->login();
    }

    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }

        $this->load->view('auth/login');
    }

    public function proses_login() {
        $is_ajax = $this->input->is_ajax_request();
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if (!is_string($username) || !is_string($password) || $username === '' || $password === '') {
            if ($is_ajax) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Masukkan username dan password.']));
                return;
            }
            $this->session->set_flashdata('error', 'Masukkan username dan password.');
            redirect('auth/login');
            return;
        }

        $user = $this->User_model->cek_login(trim($username));
        if (!$user || (int)$user->is_active !== 1 || !password_verify($password, $user->password)) {
            if ($is_ajax) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Username atau password salah!']));
                return;
            }
            $this->session->set_flashdata('error', 'Username atau password salah!');
            redirect('auth/login');
            return;
        }

        $this->complete_login($user, $is_ajax);
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    private function complete_login($user, $is_ajax = FALSE) {
        $this->session->sess_regenerate(TRUE);
        $session_data = [
            'logged_in' => TRUE,
            'id_user' => (int)$user->id,
            'nama' => $user->nama,
            'username' => $user->username,
            'role' => $user->role
        ];
        $this->session->set_userdata($session_data);

        $shift_aktif = $this->Shift_model->cek_shift_aktif($user->id);
        if ($shift_aktif) {
            $this->session->set_userdata('id_shift', $shift_aktif->id_shift);
        }

        if ($is_ajax) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'redirect' => site_url('dashboard')]));
            return;
        }

        redirect('dashboard');
    }
}
