<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->load->model('Kategori_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Data Kategori';
        $data['kategori'] = $this->Kategori_model->get_all();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('kategori/index', $data);
        $this->load->view('templates/footer');
    }

    public function simpan() {
        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|min_length[3]|max_length[50]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('kategori');
        }

        $id = $this->input->post('id_kategori');
        $data = ['nama_kategori' => $this->input->post('nama_kategori')];

        if ($id) {
            $this->Kategori_model->update($id, $data);
            $this->session->set_flashdata('success', 'Kategori berhasil diupdate!');
        } else {
            $this->Kategori_model->insert($data);
            $this->session->set_flashdata('success', 'Kategori berhasil ditambahkan!');
        }
        redirect('kategori');
    }

    public function hapus($id) {
        $this->Kategori_model->delete($id);
        $this->session->set_flashdata('success', 'Kategori berhasil dihapus!');
        redirect('kategori');
    }
}
