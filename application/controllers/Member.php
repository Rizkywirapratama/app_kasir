<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->load->model('Penjualan_model');
    }

    public function index() {
        $data['title'] = 'Manajemen Member';
        $data['member'] = $this->db->get('tbl_member')->result();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('member/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah() {
        $data = [
            'kode_member' => 'MBR' . time(),
            'nama_member' => $this->input->post('nama_member'),
            'telepon' => $this->input->post('telepon'),
            'diskon_persen' => $this->input->post('diskon_persen'),
            'total_poin' => 0
        ];
        $this->db->insert('tbl_member', $data);
        $this->session->set_flashdata('success', 'Member berhasil ditambahkan');
        redirect('member');
    }

    public function hapus($id) {
        $this->db->where('id_member', $id);
        $this->db->delete('tbl_member');
        $this->session->set_flashdata('success', 'Member berhasil dihapus');
        redirect('member');
    }
}
