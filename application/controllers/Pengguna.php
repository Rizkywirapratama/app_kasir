<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini!');
            redirect('dashboard');
        }
        $this->load->model('Pengguna_model');
        $this->load->model('Pengaturan_model');
        $this->load->model('Shift_model');
    }

    public function index() {
        $data['title'] = 'Data Pengguna';
        $data['pengguna'] = $this->Pengguna_model->get_all();
        $data['status_pengguna'] = $this->Pengguna_model->get_status_counts();
        $data['qris_image'] = $this->Pengaturan_model->get_value('qris_image');
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pengguna/index', $data);
        $this->load->view('templates/footer');
    }

    public function simpan() {
        $id_input = $this->input->post('id');
        $id = ($id_input === NULL || $id_input === '') ? NULL : (is_scalar($id_input) ? filter_var($id_input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : FALSE);
        $nama_input = $this->input->post('nama');
        $username_input = $this->input->post('username');
        $password_input = $this->input->post('password');
        $role = $this->input->post('role');
        if (!is_string($nama_input) || !is_string($username_input) || !is_string($password_input)) {
            $this->session->set_flashdata('error', 'Data pengguna tidak valid.');
            redirect('pengguna');
            return;
        }
        $nama = trim($nama_input);
        $username = trim($username_input);
        $password = $password_input;

        if ($id === FALSE || $nama === '' || strlen($nama) > 100 || !preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username) || !in_array($role, ['admin', 'kasir'], TRUE)) {
            $this->session->set_flashdata('error', 'Data pengguna tidak valid.');
            redirect('pengguna');
            return;
        }
        $existing_user = $id ? $this->Pengguna_model->get_by_id($id) : NULL;
        if ($id && !$existing_user) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
            redirect('pengguna');
            return;
        }
        if (!$id && strlen($password) < 8) {
            $this->session->set_flashdata('error', 'Password pengguna baru minimal 8 karakter.');
            redirect('pengguna');
            return;
        }
        if ($id && $password !== '' && strlen($password) < 8) {
            $this->session->set_flashdata('error', 'Password baru minimal 8 karakter.');
            redirect('pengguna');
            return;
        }
        if ($existing_user && $existing_user->role === 'admin' && $existing_user->is_active && $role !== 'admin' && $this->Pengguna_model->get_active_admin_count() <= 1) {
            $this->session->set_flashdata('error', 'Tidak dapat mengubah role admin aktif terakhir.');
            redirect('pengguna');
            return;
        }

        // Cek username duplikat
        if ($this->Pengguna_model->check_username($username, $id)) {
            $this->session->set_flashdata('error', 'Username sudah digunakan!');
            redirect('pengguna');
        }

        $data = [
            'nama' => $nama,
            'username' => $username,
            'role' => $role
        ];

        if ($id) {
            // Edit
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
            $saved = $this->Pengguna_model->update($id, $data);
            $this->session->set_flashdata('success', 'Pengguna berhasil diupdate!');
        } else {
            // Tambah
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            $saved = $this->Pengguna_model->insert($data);
            $this->session->set_flashdata('success', 'Pengguna berhasil ditambahkan!');
        }
        if (!$saved) $this->session->set_flashdata('error', 'Gagal menyimpan data pengguna.');
        redirect('pengguna');
    }

    public function reset_password() {
        $id_input = $this->input->post('id');
        $id = is_scalar($id_input) ? filter_var($id_input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : FALSE;
        $password = $this->input->post('password');
        if ($id === FALSE || !is_string($password) || strlen($password) < 8) {
            $this->session->set_flashdata('error', 'Password baru wajib diisi dan minimal 8 karakter.');
            redirect('pengguna');
            return;
        }

        if (!$this->Pengguna_model->get_by_id($id)) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
            redirect('pengguna');
            return;
        }

        $saved = $this->Pengguna_model->update($id, ['password' => password_hash($password, PASSWORD_BCRYPT)]);
        $this->session->set_flashdata($saved ? 'success' : 'error', $saved ? 'Password berhasil direset.' : 'Gagal mereset password.');
        redirect('pengguna');
    }

    public function ubah_status() {
        $id_input = $this->input->post('id');
        $id = is_scalar($id_input) ? filter_var($id_input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : FALSE;
        $user = $id === FALSE ? NULL : $this->Pengguna_model->get_by_id($id);
        if (!$user) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
            redirect('pengguna');
            return;
        }

        $aktif = (int)$user->is_active === 1;
        if ($aktif && (int)$id === (int)$this->session->userdata('id_user')) {
            $this->session->set_flashdata('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
            redirect('pengguna');
            return;
        }
        if ($aktif && $this->Shift_model->cek_shift_aktif($id)) {
            $this->session->set_flashdata('error', 'Tutup shift aktif pengguna terlebih dahulu sebelum menonaktifkan akun.');
            redirect('pengguna');
            return;
        }
        if ($aktif && $user->role === 'admin' && $this->Pengguna_model->get_active_admin_count() <= 1) {
            $this->session->set_flashdata('error', 'Admin aktif terakhir tidak dapat dinonaktifkan.');
            redirect('pengguna');
            return;
        }

        $new_status = $aktif ? 0 : 1;
        $saved = $this->Pengguna_model->set_active($id, $new_status);
        $this->session->set_flashdata($saved ? 'success' : 'error', $saved ? 'Status akun berhasil diubah.' : 'Gagal mengubah status akun.');
        redirect('pengguna');
    }

    public function export() {
        $rows = $this->Pengguna_model->get_all();
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, ['ID', 'Nama', 'Username', 'Role', 'Status']);
        $safe_cell = function($value) {
            $value = (string)$value;
            return preg_match('/^[\x00-\x20]*[=+\-@]/', $value) ? "'" . $value : $value;
        };
        foreach ($rows as $row) {
            fputcsv($stream, array_map($safe_cell, [$row->id, $row->nama, $row->username, $row->role, (int)$row->is_active ? 'Aktif' : 'Nonaktif']));
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        $this->output
            ->set_content_type('text/csv', 'utf-8')
            ->set_header('Content-Disposition: attachment; filename="pengguna-' . date('Ymd') . '.csv"')
            ->set_output($csv);
    }

    public function upload_qris() {
        $upload_path = FCPATH . 'assets/images/payment/';
        if (!is_dir($upload_path) && !mkdir($upload_path, 0755, TRUE)) {
            $this->session->set_flashdata('error', 'Folder penyimpanan QRIS tidak dapat dibuat.');
            redirect('pengguna');
            return;
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'png|jpg|jpeg';
        $config['max_size'] = 3072;
        $config['max_width'] = 3000;
        $config['max_height'] = 3000;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('qris_image')) {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors('', '')));
            redirect('pengguna');
            return;
        }

        $upload = $this->upload->data();
        $previous = $this->Pengaturan_model->get_value('qris_image');
        if (!$this->Pengaturan_model->set_value('qris_image', $upload['file_name'])) {
            @unlink($upload_path . $upload['file_name']);
            $this->session->set_flashdata('error', 'QRIS gagal disimpan.');
            redirect('pengguna');
            return;
        }

        if ($previous && is_file($upload_path . basename($previous))) {
            unlink($upload_path . basename($previous));
        }
        $this->session->set_flashdata('success', 'QRIS merchant berhasil diperbarui.');
        redirect('pengguna');
    }

    public function hapus($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $user = $id === FALSE ? NULL : $this->Pengguna_model->get_by_id($id);
        if (!$user) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
        } elseif ((int)$id === (int)$this->session->userdata('id_user')) {
            $this->session->set_flashdata('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        } elseif ($this->Shift_model->cek_shift_aktif($id)) {
            $this->session->set_flashdata('error', 'Tutup shift aktif pengguna terlebih dahulu sebelum menonaktifkan akun.');
        } elseif ($user->role === 'admin' && (int)$user->is_active === 1 && $this->Pengguna_model->get_active_admin_count() <= 1) {
            $this->session->set_flashdata('error', 'Admin aktif terakhir tidak dapat dinonaktifkan.');
        } elseif ($this->Pengguna_model->set_active($id, 0)) {
            $this->session->set_flashdata('success', 'Pengguna berhasil dinonaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menonaktifkan pengguna.');
        }
        redirect('pengguna');
    }
}
