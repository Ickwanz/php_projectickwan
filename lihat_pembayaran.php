<?php
session_start();
// Pastikan tidak ada spasi/baris kosong/karakter sebelum tag <?php di file ini atau di koneksi.php
include 'koneksi.php';

// Amankan id_pembelian dari URL
$id_pembelian_aman = isset($_GET['id']) ? $koneksi->real_escape_string($_GET['id']) : null;

// Validasi dasar id_pembelian dari URL
if (empty($id_pembelian_aman)) {
    echo "<script>alert('ID Pembelian tidak ditemukan. Silakan kembali ke riwayat belanja Anda.');</script>";
    echo "<script>location='riwayat.php';</script>";
    exit();
}

// Ambil detail pembayaran dan pembelian dari database
$ambil = $koneksi->query("SELECT * FROM pembayaran 
    LEFT JOIN pembelian ON pembayaran.id_pembelian = pembelian.id_pembelian 
    WHERE pembelian.id_pembelian='$id_pembelian_aman'"); // Gunakan variabel yang aman
$detbay = $ambil->fetch_assoc();

// jk blm ada data pembayaran
if (empty($detbay)) {
    echo "<script>alert('Belum ada data pembayaran untuk pesanan ini.');</script>";
    echo "<script>location='riwayat.php';</script>";
    exit();
}

// jika data pelanggan tidak sesuai dengan yang login
// Pastikan sesi pelanggan ada dan id_pelanggan terdefinisi
if (!isset($_SESSION["pelanggan"]['id_pelanggan']) || $_SESSION["pelanggan"]['id_pelanggan'] != $detbay["id_pelanggan"]) {
    echo "<script>alert('Anda tidak berhak melihat pembayaran untuk pesanan orang lain.');</script>";
    echo "<script>location='riwayat.php';</script>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Lihat Pembayaran - TokoDaffa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            padding-top: 20px; /* Sedikit padding atas agar tidak terlalu mepet menu */
        }
        .img-responsive {
            max-width: 100%;
            height: auto;
            display: block; /* Agar img tidak memiliki spasi ekstra di bawahnya */
        }
        /* Tambahkan style jika ada error-message seperti di pembayaran.php */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: .25rem;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <?php include 'menu.php'; // Pastikan menu.php juga menggunakan Bootstrap 5 class ?>

    <div class="container">
        <h2 class="mb-4">Detail Pembayaran</h2>
        
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered"> <tr>
                        <th>Nama Penyetor</th>
                        <td><?php echo htmlspecialchars($detbay["nama"]); ?></td>
                    </tr>
                    <tr>
                        <th>Bank Tujuan</th>
                        <td><?php echo htmlspecialchars($detbay["bank"]); ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Pembayaran</th>
                        <td><?php echo htmlspecialchars($detbay["tanggal"]); ?></td>
                    </tr>
                    <tr>
                        <th>Jumlah Pembayaran</th>
                        <td>Rp. <?php echo number_format($detbay["jumlah"], 0, ',', '.'); ?></td>
                    </tr> 
                    <tr>
                        <th>Total Pembelian</th>
                        <td>Rp. <?php echo number_format($detbay["total_pembelian"], 0, ',', '.'); ?></td>
                    </tr> 
                    <tr>
                        <th>Status Pembelian</th>
                        <td><?php echo htmlspecialchars($detbay["status_pembelian"]); ?></td>
                    </tr> 
                </table>
            </div>
            <div class="col-md-6 text-center"> <h4>Bukti Pembayaran</h4>
                <?php if (!empty($detbay["bukti"])): ?>
                    <img src="bukti_pembayaran/<?php echo htmlspecialchars($detbay["bukti"]); ?>" 
                         alt="Bukti Pembayaran" class="img-fluid rounded shadow-sm mt-2"> <?php else: ?>
                    <p class="text-danger">Bukti pembayaran tidak tersedia.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>