<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Dashboard_model');
        $this->cek_login();
    }

    private function cek_login() {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $data['title']              = 'Dashboard';
        $data['total_menu']         = $this->Dashboard_model->total_menu();
        $data['total_penjualan']    = $this->Dashboard_model->total_penjualan_hari_ini();
        $data['total_pendapatan']   = $this->Dashboard_model->total_pendapatan_hari_ini();
        $data['total_meja']         = $this->Dashboard_model->total_meja();
        $data['meja_tersedia']      = $this->Dashboard_model->meja_tersedia();
        $data['total_user']         = $this->Dashboard_model->total_user();
        $data['penjualan_terakhir'] = $this->Dashboard_model->penjualan_terakhir(5);
        $data['menu_terlaris']      = $this->Dashboard_model->menu_terlaris(5);
        $data['chart_mingguan']     = $this->Dashboard_model->pendapatan_7_hari();
        $data['shift_aktif']        = $this->Dashboard_model->shift_aktif_data();
        $data['low_stock']          = $this->Dashboard_model->low_stock_items(5);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer', $data);
    }
}
