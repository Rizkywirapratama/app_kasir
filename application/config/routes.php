<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'dashboard';
$route['dashboard']                = 'dashboard/index';
$route['auth/login']               = 'auth/login';
$route['auth/proses_login']        = 'auth/proses_login';
$route['auth/logout']              = 'auth/logout';

// Menu
$route['menu']                     = 'menu/index';
$route['menu/tambah']              = 'menu/tambah';
$route['menu/simpan']              = 'menu/simpan';
$route['menu/edit/(:num)']         = 'menu/edit/$1';
$route['menu/update/(:num)']       = 'menu/update/$1';
$route['menu/hapus/(:num)']        = 'menu/hapus/$1';

// Kategori
$route['kategori']                 = 'kategori/index';
$route['kategori/simpan']          = 'kategori/simpan';
$route['kategori/hapus/(:num)']    = 'kategori/hapus/$1';

// Meja
$route['meja']                     = 'meja/index';
$route['meja/simpan']              = 'meja/simpan';
$route['meja/edit/(:num)']         = 'meja/edit/$1';
$route['meja/update/(:num)']       = 'meja/update/$1';
$route['meja/hapus/(:num)']        = 'meja/hapus/$1';
$route['meja/status/(:num)']       = 'meja/ubah_status/$1';

// Penjualan
$route['penjualan']                = 'penjualan/index';
$route['penjualan/buat']           = 'penjualan/buat';
$route['penjualan/simpan']         = 'penjualan/simpan';
$route['penjualan/detail/(:num)']  = 'penjualan/detail/$1';
$route['penjualan/cetak/(:num)']   = 'penjualan/cetak/$1';

// Laporan
$route['laporan']                  = 'laporan/index';
$route['laporan/export']           = 'laporan/export';

// Shift
$route['shift/buka']               = 'shift/buka';
$route['shift/proses_buka']        = 'shift/proses_buka';
$route['shift/tutup']              = 'shift/tutup';
$route['shift/proses_tutup']       = 'shift/proses_tutup';

// Penjualan Xendit
$route['penjualan/create_xendit_invoice'] = 'penjualan/create_xendit_invoice';

// Pengguna
$route['pengguna']                 = 'pengguna/index';
$route['pengguna/tambah']          = 'pengguna/tambah';
$route['pengguna/simpan']          = 'pengguna/simpan';
$route['pengguna/reset_password']  = 'pengguna/reset_password';
$route['pengguna/ubah_status']     = 'pengguna/ubah_status';
$route['pengguna/export']          = 'pengguna/export';
$route['pengguna/upload_qris']     = 'pengguna/upload_qris';
$route['pengguna/edit/(:num)']     = 'pengguna/edit/$1';
$route['pengguna/update/(:num)']   = 'pengguna/update/$1';
$route['pengguna/hapus/(:num)']    = 'pengguna/hapus/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
