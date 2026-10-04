<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect('auth/login');
        $this->load->model('Shift_model');
    }

    public function index() {
        $data['title'] = 'Riwayat Sesi Shift';
        $data['shifts'] = $this->Shift_model->get_all_shift();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('shift/index', $data);
        $this->load->view('templates/footer');
    }

    public function buka() {
        // Cek jika sudah ada shift aktif
        $shift_aktif = $this->Shift_model->cek_shift_aktif($this->session->userdata('id_user'));
        if ($shift_aktif) {
            $this->session->set_flashdata('info', 'Anda sudah membuka shift. Silakan tutup shift terlebih dahulu jika ingin membuka shift baru.');
            redirect('dashboard');
        }

        $data['title'] = 'Buka Buku (Shift)';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('shift/buka', $data);
        $this->load->view('templates/footer');
    }

    public function proses_buka() {
        $shift_aktif = $this->Shift_model->cek_shift_aktif($this->session->userdata('id_user'));
        if ($shift_aktif) {
            $this->session->set_userdata('id_shift', $shift_aktif->id_shift);
            $this->session->set_flashdata('info', 'Shift Anda sudah aktif.');
            redirect('dashboard');
            return;
        }

        $saldo_input = $this->input->post('saldo_awal');
        if (!is_string($saldo_input) || !preg_match('/^[0-9.]+$/', $saldo_input)) {
            $this->session->set_flashdata('error', 'Nominal modal awal tidak valid.');
            redirect('shift/buka');
            return;
        }
        $saldo_raw = str_replace('.', '', $saldo_input);
        $saldo_awal = filter_var($saldo_raw === '' ? '0' : $saldo_raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($saldo_awal === FALSE) {
            $this->session->set_flashdata('error', 'Nominal modal awal tidak valid.');
            redirect('shift/buka');
            return;
        }
        
        // Buat data shift
        $data = [
            'id_user' => $this->session->userdata('id_user'),
            'waktu_buka' => date('Y-m-d H:i:s'),
            'saldo_awal' => $saldo_awal,
            'status' => 'buka'
        ];

        $id_shift = $this->Shift_model->buka_shift($data);
        
        if ($id_shift) {
            $this->session->set_userdata('id_shift', $id_shift);
            $this->session->set_flashdata('success', 'Buka shift berhasil! Silakan mulai transaksi.');
            redirect('penjualan/buat');
        } else {
            $this->session->set_flashdata('error', 'Gagal membuka shift!');
            redirect('shift/buka');
        }
    }

    public function tutup() {
        $id_shift = $this->session->userdata('id_shift');
        if (!$id_shift) {
            // Coba ambil dari DB jika session hilang tapi ada yang buka
            $shift_aktif = $this->Shift_model->cek_shift_aktif($this->session->userdata('id_user'));
            if($shift_aktif) {
                $id_shift = $shift_aktif->id_shift;
                $this->session->set_userdata('id_shift', $id_shift);
            } else {
                $this->session->set_flashdata('error', 'Tidak ada shift yang sedang aktif.');
                redirect('dashboard');
            }
        }

        $data['title'] = 'Tutup Buku (Shift)';
        $data['shift'] = $this->Shift_model->get_shift($id_shift);
        $data['rekap'] = $this->Shift_model->get_rekap_penjualan($id_shift);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('shift/tutup', $data);
        $this->load->view('templates/footer');
    }

    public function proses_tutup() {
        $id_shift = $this->session->userdata('id_shift');
        $shift_aktif = $this->Shift_model->cek_shift_aktif($this->session->userdata('id_user'));
        if (!$id_shift || !$shift_aktif || (int)$shift_aktif->id_shift !== (int)$id_shift) {
            $this->session->unset_userdata('id_shift');
            $this->session->set_flashdata('error', 'Shift aktif tidak ditemukan.');
            redirect('dashboard');
            return;
        }

        $saldo_input = $this->input->post('saldo_akhir_aktual');
        if (!is_string($saldo_input) || !preg_match('/^[0-9.]+$/', $saldo_input)) {
            $this->session->set_flashdata('error', 'Masukkan jumlah uang fisik di laci.');
            redirect('shift/tutup');
            return;
        }
        $saldo_raw = str_replace('.', '', $saldo_input);
        $saldo_akhir_aktual = filter_var($saldo_raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($saldo_akhir_aktual === FALSE) {
            $this->session->set_flashdata('error', 'Nominal uang fisik tidak valid.');
            redirect('shift/tutup');
            return;
        }

        $data = [
            'waktu_tutup' => date('Y-m-d H:i:s'),
            'saldo_akhir_aktual' => $saldo_akhir_aktual,
            'status' => 'tutup'
        ];

        if (!$this->Shift_model->tutup_shift($id_shift, $data)) {
            $this->session->set_flashdata('error', 'Gagal menyimpan penutupan shift.');
            redirect('shift/tutup');
            return;
        }

        $this->session->unset_userdata('id_shift');
        $this->session->set_flashdata('success', 'Shift berhasil ditutup.');
        redirect('dashboard');
    }
}
