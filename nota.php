<?php
session_start();
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1" />
 <title>Nota Pembelian</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

 <style>
  body {
   background-color: #f8f9fa;
   font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  section.konten {
   padding: 40px 0;
  }
  h2 {
   margin-bottom: 30px;
   font-weight: 600;
   color: #333;
   border-bottom: 2px solid #0d6efd;
   padding-bottom: 8px;
  }
  .detail-header h3 {
   margin-bottom: 15px;
   font-weight: 600;
   color: #0d6efd;
  }
  .table thead {
   background-color: #0d6efd;
   color: white;
  }
  .table td, .table th {
   vertical-align: middle;
  }
  .alert-info {
   background-color: #e7f1ff;
   border-color: #b6d4fe;
   color: #084298;
   font-weight: 600;
   font-size: 1.1rem;
   padding: 20px;
  }
 </style>
</head>
<body>

<?php include 'menu.php'; ?>

<section class="konten">
 <div class="container">
  <h2>Detail Pembelian</h2>

  <?php
    // --- START PERBAIKAN ---
    // Pastikan id pembelian ada dan tidak kosong untuk mencegah error
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo "<script>alert('ID Pembelian tidak ditemukan.');</script>";
        echo "<script>location='riwayat.php';</script>";
        exit();
    }

    // Amankan nilai $_GET['id'] menggunakan real_escape_string
    // Ini PENTING untuk mencegah SQL Injection dan error query
    $id_pembelian_aman = $koneksi->real_escape_string($_GET['id']);

  $ambil = $koneksi->query("SELECT * FROM pembelian JOIN pelanggan
   ON pembelian.id_pelanggan=pelanggan.id_pelanggan
   WHERE pembelian.id_pembelian='$id_pembelian_aman'"); // Gunakan variabel yang sudah di-escape

  $detail = $ambil->fetch_assoc();

    // Cek apakah data pembelian ditemukan
    if (empty($detail)) {
        echo "<script>alert('Nota tidak ditemukan atau ID pembelian tidak valid.');</script>";
        echo "<script>location='riwayat.php';</script>";
        exit();
    }
    // --- END PERBAIKAN ---

  $idpelangganyangbeli = $detail['id_pelanggan'];
  $idpelangganyanglogin = $_SESSION['pelanggan']['id_pelanggan'];

  if ($idpelangganyangbeli !== $idpelangganyanglogin) {
   echo "<script>alert('Jangan jail! Anda tidak berhak melihat nota ini.');</script>";
   echo "<script>location='riwayat.php';</script>";
   exit();
  }

  // Ambil status pembelian dari data detail
  // Asumsi kolom 'status_pembelian' ada di tabel 'pembelian'
  $status_pembelian = strtolower($detail['status_pembelian'] ?? 'pending');
  ?>

  <div class="row detail-header mb-4">
   <div class="col-md-4">
    <h3>Pembelian</h3>
    <p><strong>No. Pembelian:</strong> <?= htmlspecialchars($detail['id_pembelian']) ?></p>
    <p><strong>Tanggal:</strong> <?= htmlspecialchars($detail['tanggal_pembelian']) ?></p>
    <p><strong>Total:</strong> Rp <?= number_format($detail['total_pembelian']) ?></p>
   </div>
   <div class="col-md-4">
    <h3>Pelanggan</h3>
    <p><strong><?= htmlspecialchars($detail['nama_pelanggan']) ?></strong></p>
    <p><?= htmlspecialchars($detail['telepon_pelanggan']) ?><br><?= htmlspecialchars($detail['email_pelanggan']) ?></p>
   </div>
   <div class="col-md-4">
    <h3>Pengiriman</h3>
    <p><strong><?= htmlspecialchars($detail['nama_kota']) ?></strong></p>
    <p>Ongkos kirim: Rp <?= number_format($detail['tarif']) ?></p>
    <p>Alamat: <?= htmlspecialchars($detail['alamat_pengiriman']) ?></p>
   </div>
  </div>

  <table class="table table-bordered shadow-sm">
   <thead>
    <tr>
     <th>No</th>
     <th>Nama Produk</th>
     <th>Harga</th>
     <th>Berat (gr)</th>
     <th>Jumlah</th>
     <th>Subberat (gr)</th>
     <th>Subtotal</th>
    </tr>
   </thead>
   <tbody>
    <?php
    $nomor = 1;
    $ambil_produk = $koneksi->query("SELECT * FROM pembelian_produk WHERE id_pembelian='$id_pembelian_aman'"); // Gunakan variabel yang sudah di-escape
    while ($pecah = $ambil_produk->fetch_assoc()) {
     $subberat = $pecah['berat'] * $pecah['jumlah'];
    ?>
    <tr>
     <td><?= $nomor ?></td>
     <td><?= htmlspecialchars($pecah['nama']) ?></td>
     <td>Rp <?= number_format($pecah['harga']) ?></td>
     <td><?= $pecah['berat'] ?></td>
     <td><?= $pecah['jumlah'] ?></td>
     <td><?= $subberat ?></td>
     <td>Rp <?= number_format($pecah['subharga']) ?></td>
    </tr>
    <?php $nomor++; } ?>
   </tbody>
  </table>

  <div class="row mt-4">
   <div class="col-md-7">
    <div class="alert alert-info">
     Silakan melakukan pembayaran sebesar <strong>Rp <?= number_format($detail['total_pembelian']) ?></strong><br>
     Ke rekening <strong>BANK MANDIRI 786-001890- AN. ICKWAN ZACKY ALAWI</strong>
    </div>

    <?php if ($status_pembelian == 'pending'): ?>
    <a href="pembayaran.php?id=<?= htmlspecialchars($detail['id_pembelian']) ?>" class="btn btn-primary mt-3">
      Lakukan Pembayaran
    </a>
    <?php endif; ?>

   </div>
  </div>
 </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>