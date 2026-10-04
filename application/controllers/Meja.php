<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meja extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->load->model('Meja_model');
    }

    public function index() {
        $data['title'] = 'Data Meja';
        $data['meja'] = $this->Meja_model->get_all();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('meja/index', $data);
        $this->load->view('templates/footer');
    }

    public function simpan() {
        $id = $this->input->post('id_meja');
        $data = [
            'nomor_meja' => $this->input->post('nomor_meja'),
            'status' => $this->input->post('status')
        ];

        if ($id) {
            $this->Meja_model->update($id, $data);
            $this->session->set_flashdata('success', 'Meja berhasil diupdate!');
        } else {
            $this->Meja_model->insert($data);
            $this->session->set_flashdata('success', 'Meja berhasil ditambahkan!');
        }
        redirect('meja');
    }

    public function ubah_status($id) {
        $meja = $this->Meja_model->get_by_id($id);
        $status_baru = ($meja->status == 'Tersedia') ? 'Terisi' : 'Tersedia';
        
        $this->Meja_model->update($id, ['status' => $status_baru]);
        $this->session->set_flashdata('success', 'Status meja berhasil diubah!');
        redirect('meja');
    }

    public function hapus($id) {
        $this->Meja_model->delete($id);
        $this->session->set_flashdata('success', 'Meja berhasil dihapus!');
        redirect('meja');
    }
}
