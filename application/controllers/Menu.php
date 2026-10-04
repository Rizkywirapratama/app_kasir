<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->load->model('Menu_model');
        $this->load->model('Kategori_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Data Menu';
        $data['menu'] = $this->Menu_model->get_all();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('menu/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah() {
        $data['title'] = 'Tambah Menu';
        $data['kategori'] = $this->Kategori_model->get_all();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('menu/form', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $data['title'] = 'Edit Menu';
        $data['kategori'] = $this->Kategori_model->get_all();
        $data['menu'] = $this->Menu_model->get_by_id($id);
        
        if (!$data['menu']) redirect('menu');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('menu/form', $data);
        $this->load->view('templates/footer');
    }

    public function simpan() {
        $this->form_validation->set_rules('nama_menu', 'Nama Menu', 'required|min_length[3]');
        $this->form_validation->set_rules('harga', 'Harga', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('stok', 'Stok', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('id_kategori', 'Kategori', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('menu/tambah');
        }

        $id = $this->input->post('id_menu');
        $data = [
            'id_kategori' => $this->input->post('id_kategori'),
            'nama_menu' => $this->input->post('nama_menu'),
            'harga' => $this->input->post('harga'),
            'stok' => $this->input->post('stok')
        ];

        // Konfigurasi upload foto
        $config['upload_path']   = './assets/images/menu/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size']      = 2048; // 2MB
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        // Buat folder jika belum ada
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        if ($this->upload->do_upload('foto')) {
            $upload_data = $this->upload->data();
            $data['foto'] = $upload_data['file_name'];

            // Hapus foto lama jika sedang edit
            if ($id) {
                $old_menu = $this->Menu_model->get_by_id($id);
                if ($old_menu->foto && file_exists('./assets/images/menu/' . $old_menu->foto)) {
                    unlink('./assets/images/menu/' . $old_menu->foto);
                }
            }
        } elseif (!$id && empty($_FILES['foto']['name'])) {
            // Default foto saat tambah
            $data['foto'] = 'default.png';
        }

        if ($id) {
            $this->Menu_model->update($id, $data);
            $this->session->set_flashdata('success', 'Menu berhasil diupdate!');
        } else {
            $this->Menu_model->insert($data);
            $this->session->set_flashdata('success', 'Menu berhasil ditambahkan!');
        }
        redirect('menu');
    }

    public function hapus($id) {
        $old_menu = $this->Menu_model->get_by_id($id);
        if ($old_menu->foto && $old_menu->foto != 'default.png' && file_exists('./assets/images/menu/' . $old_menu->foto)) {
            unlink('./assets/images/menu/' . $old_menu->foto);
        }
        
        $this->Menu_model->delete($id);
        $this->session->set_flashdata('success', 'Menu berhasil dihapus!');
        redirect('menu');
    }
}
