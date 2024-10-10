<?php
include "config/connect.php";

// Ambil nilai 'mod' dari URL jika ada, jika tidak, gunakan string kosong
$mod = isset($_GET['mod']) ? $_GET['mod'] : ''; 

// Cek nilai dari $mod dan lakukan include file sesuai dengan mod yang diberikan
if ($mod == 'home') {
    include "home.php";
    
} elseif ($mod == 'register') {
    include "register/register.php";

} elseif ($mod == 'reg-rumah') {
    include "register/register_rumah.php";

} elseif ($mod == 'reg-warung') {
    include "register/register_warung.php";

} elseif ($mod == 'reg-pengelola') {
    include "register/register_pengelola.php";

} elseif ($mod == 'verify') {
    include "pengelola/verify_user.php";

} elseif ($mod == 'verify-war') {
    include "pengelola/verify_warung.php";

} elseif ($mod == 'pengelola') {
    include "pengelola/pengelola.php";

} elseif ($mod == 'data-penarikan') {
    include "pengelola/data_penarikan.php";

} elseif ($mod == 'edit-sampah') {
    include "pengelola/manage_sampah.php";

} elseif ($mod == 'edit') {
    include "pengelola/edit.php";

} elseif ($mod == 'edit-user') {
    include "pengelola/edit_user.php";

} elseif ($mod == 'edit-war') {
    include "pengelola/edit_warung.php";

} elseif ($mod == 'history') {
    include "pengelola/history.php";

} elseif ($mod == 'warung') {
    include "warung/warung.php";

} elseif ($mod == 'pencairan') {
    include "warung/pencairan_saldo.php";

} elseif ($mod == 'admin') {
    include "admin/dashboard.php";

} elseif ($mod == 'admin-sampah') {
    include "admin/manage_sampah.php";

} elseif ($mod == 'admin-user') {
    include "admin/manage_user.php";

} elseif ($mod == 'jual') {
    include "users/sell_sampah.php";

} elseif ($mod == 'riwayat') {
    include "users/riwayat.php";

} elseif ($mod == 'profile') {
    include "users/profile.php";

} elseif ($mod == 'pembayaran') {
    include "users/pembayaran.php";

} elseif ($mod == 'users') {
    include "users/dashboard.php";

} elseif ($mod == 'unaut') {
    include "unauthorized.php";

} elseif ($mod == 'unaut2') {
    include "unauthorized2.php";

} elseif ($mod == 'search') {
    include "assets/components/search.php";

} else {
    // Jika mod tidak dikenali, Anda bisa memasukkan default behavior atau error page
    echo "Halaman tidak ditemukan.";
}
?>
