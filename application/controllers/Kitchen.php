<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kitchen extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->load->model('Penjualan_model');
    }

    public function index() {
        $data['title'] = 'Kitchen Display System';
        // Ambil transaksi hari ini yang belum selesai (Simulasi: Ambil 10 transaksi terakhir)
        $data['orders'] = $this->get_pending_orders();
        
        $this->load->view('templates/header', $data);
        $this->load->view('kitchen/index', $data);
        $this->load->view('templates/footer');
    }

    private function get_pending_orders() {
        // Ambil detail pesanan untuk transaksi hari ini
        $this->db->select('tbl_penjualan.*, tbl_meja.nomor_meja');
        $this->db->from('tbl_penjualan');
        $this->db->join('tbl_meja', 'tbl_meja.id_meja = tbl_penjualan.id_meja', 'left');
        $this->db->where('DATE(tanggal)', date('Y-m-d'));
        $this->db->order_by('tanggal', 'DESC');
        $this->db->limit(12);
        $orders = $this->db->get()->result();

        foreach ($orders as $order) {
            $order->items = $this->Penjualan_model->get_detail($order->id_penjualan);
        }
        return $orders;
    }

    public function get_updates() {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $orders = $this->get_pending_orders();
        echo json_encode($orders);
    }
}
