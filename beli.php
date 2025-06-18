<?php
session_start();
include 'koneksi.php';

// Ambil dan validasi id_produk dari URL
$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_produk <= 0) {
    echo "<script>alert('Produk tidak valid!');</script>";
    echo "<script>location='index.php';</script>";
    exit;
}

// Cek apakah produk ada di database
$cek = $koneksi->query("SELECT * FROM produk WHERE id_produk = $id_produk");
if ($cek->num_rows == 0) {
    echo "<script>alert('Produk tidak ditemukan!');</script>";
    echo "<script>location='index.php';</script>";
    exit;
}

// Jika produk sudah ada di keranjang, tambah jumlahnya
if (isset($_SESSION['keranjang'][$id_produk])) {
    $_SESSION['keranjang'][$id_produk]++;
} else {
    // Jika belum ada, set jumlah 1
    $_SESSION['keranjang'][$id_produk] = 1;
}

echo "<script>alert('Produk telah masuk ke keranjang belanja');</script>";
echo "<script>location='keranjang.php';</script>";
