<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/process', 'Auth::process');
$routes->get('/auth/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('/install', 'Install::index');

$routes->group('anggota', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Anggota::index');
    $routes->get('create', 'Anggota::create');
    $routes->post('store', 'Anggota::store');
    $routes->get('edit/(:num)', 'Anggota::edit/$1');
    $routes->post('update/(:num)', 'Anggota::update/$1');
    $routes->get('delete/(:num)', 'Anggota::delete/$1');
});

$routes->group('kelompok', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Kelompok::index');
    $routes->post('store', 'Kelompok::store');
    $routes->post('update/(:num)', 'Kelompok::update/$1');
    $routes->get('delete/(:num)', 'Kelompok::delete/$1');
    
    $routes->get('bulk', 'Kelompok::bulk_index');
    $routes->post('bulk_process', 'Kelompok::bulk_process');
});

$routes->group('jenis-simpanan', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'JenisSimpanan::index');
    $routes->get('create', 'JenisSimpanan::create');
    $routes->post('store', 'JenisSimpanan::store');
    $routes->get('edit/(:num)', 'JenisSimpanan::edit/$1');
    $routes->post('update/(:num)', 'JenisSimpanan::update/$1');
    $routes->get('delete/(:num)', 'JenisSimpanan::delete/$1');
});

$routes->group('simpanan', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Simpanan::index');
    $routes->get('create', 'Simpanan::create');
    $routes->post('store', 'Simpanan::store');
    $routes->get('edit/(:num)', 'Simpanan::edit/$1');
    $routes->post('update/(:num)', 'Simpanan::update/$1');
    $routes->get('delete/(:num)', 'Simpanan::delete/$1');
    $routes->get('print/(:num)', 'Simpanan::print/$1');
    $routes->get('api/get_saldo', 'Simpanan::getSaldo');
});

$routes->group('pinjaman', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Pinjaman::index');
    $routes->get('create', 'Pinjaman::create');
    $routes->post('store', 'Pinjaman::store');
    $routes->get('approve/(:num)', 'Pinjaman::approve/$1');
    $routes->get('reject/(:num)', 'Pinjaman::reject/$1');
});

$routes->group('angsuran', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Angsuran::index');
    $routes->get('create', 'Angsuran::create');
    $routes->post('store', 'Angsuran::store');
    $routes->get('edit/(:num)', 'Angsuran::edit/$1');
    $routes->post('update/(:num)', 'Angsuran::update/$1');
    $routes->get('delete/(:num)', 'Angsuran::delete/$1');
    $routes->get('pelunasan/(:num)', 'Angsuran::pelunasan/$1');
    $routes->post('prosespelunasan/(:num)', 'Angsuran::prosespelunasan/$1');
    $routes->get('api/pinjaman/(:num)', 'Angsuran::getPinjamanByAnggota/$1');
    $routes->get('print/(:num)', 'Angsuran::print/$1');
});

$routes->group('pengaturan', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Pengaturan::index');
    $routes->post('update', 'Pengaturan::update');
});

$routes->group('kas', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'KasKoperasi::index');
    $routes->get('create', 'KasKoperasi::create');
    $routes->post('store', 'KasKoperasi::store');
    $routes->get('edit/(:num)', 'KasKoperasi::edit/$1');
    $routes->post('update/(:num)', 'KasKoperasi::update/$1');
    $routes->get('delete/(:num)', 'KasKoperasi::delete/$1');
});

$routes->group('laporan', ['filter' => 'auth'], static function ($routes) {
    $routes->get('kas', 'Laporan::kas');
    $routes->get('shu', 'Laporan::shu');
    $routes->get('neraca', 'Laporan::neraca');
    $routes->get('anggota', 'Laporan::anggota');
});

$routes->group('profil', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Profil::index');
    $routes->post('update-password', 'Profil::update_password');
});

$routes->group('role', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Role::index');
    $routes->get('create', 'Role::create');
    $routes->post('store', 'Role::store');
    $routes->get('edit/(:num)', 'Role::edit/$1');
    $routes->post('update/(:num)', 'Role::update/$1');
    $routes->get('delete/(:num)', 'Role::delete/$1');
});

$routes->group('user', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'User::index');
    $routes->get('create', 'User::create');
    $routes->post('store', 'User::store');
    $routes->get('edit/(:num)', 'User::edit/$1');
    $routes->post('update/(:num)', 'User::update/$1');
    $routes->get('delete/(:num)', 'User::delete/$1');
});

$routes->group('massal', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'InputMassal::index');
    $routes->get('form', 'InputMassal::form');
    $routes->post('store', 'InputMassal::store');
    $routes->get('riwayat', 'InputMassal::riwayat');
});

$routes->group('informasi', ['filter' => 'auth'], static function ($routes) {
    $routes->get('fitur', 'Informasi::fitur');
    $routes->get('panduan', 'Informasi::panduan');
    $routes->get('support', 'Informasi::support');
});

$routes->group('backup', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Backup::index');
    $routes->get('download', 'Backup::download');
    $routes->post('restore', 'Backup::restore');
});

// Routes end here.
