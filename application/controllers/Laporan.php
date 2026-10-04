<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('error', 'Fitur laporan penjualan hanya dapat diakses admin.');
            redirect('dashboard');
            return;
        }
        $this->load->model('Laporan_model');
    }

    public function index() {
        $data['title'] = 'Laporan Penjualan';
        
        $tgl_mulai = $this->input->get('tgl_mulai') ?? date('Y-m-d');
        $tgl_akhir = $this->input->get('tgl_akhir') ?? date('Y-m-d');

        $data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_akhir'] = $tgl_akhir;

        $data['laporan'] = $this->Laporan_model->get_laporan($tgl_mulai, $tgl_akhir);
        $data['ringkasan'] = $this->Laporan_model->get_ringkasan($tgl_mulai, $tgl_akhir);
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('laporan/index', $data);
        $this->load->view('templates/footer');
    }

    public function export() {
        $tgl_mulai = $this->input->get('tgl_mulai') ?? date('Y-m-d');
        $tgl_akhir = $this->input->get('tgl_akhir') ?? date('Y-m-d');

        $laporan = $this->Laporan_model->get_laporan($tgl_mulai, $tgl_akhir);

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_penjualan_' . $tgl_mulai . '_to_' . $tgl_akhir . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV header
        fputcsv($output, ['Tanggal', 'No. Invoice', 'Menu', 'Jumlah', 'Harga', 'Total', 'Kasir']);

        // CSV data
        foreach ($laporan as $row) {
            fputcsv($output, [
                $row->tanggal,
                $row->no_invoice,
                $row->nama_menu,
                $row->jumlah,
                $row->harga,
                $row->total,
                $row->nama_kasir
            ]);
        }

        fclose($output);
    }
}
