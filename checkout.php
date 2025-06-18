<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['pelanggan'])) {
    echo "<script>alert('Silakan login terlebih dahulu');</script>";
    echo "<script>location='login.php';</script>";
    exit();
}

// Tambahkan kode ini untuk mengecek apakah keranjang kosong
if (empty($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang belanja kosong. Silakan belanja terlebih dahulu.');</script>";
    echo "<script>location='index.php';</script>"; // Anda bisa ganti ke 'produk.php' atau halaman lain
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Toko Ickwan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'menu.php'; ?>

<section class="py-5">
    <div class="container d-flex flex-column align-items-center">
        <h2 class="section-heading mb-4" id="keranjangBelanjaHeading">Keranjang Belanja Anda</h2>
        <p class="lead section-description text-center mb-5">Periksa kembali pesanan Anda sebelum melanjutkan ke pembayaran.</p>

        <div class="card mb-4 w-100"> <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-hover text-center align-middle product-table">
                        <thead class="table-header-custom">
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nomor = 1;
                            $totalbelanja = 0;
                            foreach ($_SESSION["keranjang"] as $id_produk => $jumlah) {
                                $ambil = $koneksi->query("SELECT * FROM produk WHERE id_produk='$id_produk'");
                                $pecah = $ambil->fetch_assoc();
                                $subharga = $pecah["harga_produk"] * $jumlah;
                            ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td class="text-start d-flex align-items-center">
                                    <img src="foto_produk/<?= htmlspecialchars($pecah['foto_produk']); ?>" alt="<?= htmlspecialchars($pecah['nama_produk']); ?>" class="img-fluid rounded-3 me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?= htmlspecialchars($pecah['nama_produk']); ?>
                                </td>
                                <td>Rp <?= number_format($pecah["harga_produk"], 0, ',', '.'); ?></td>
                                <td><?= $jumlah; ?></td>
                                <td>Rp <?= number_format($subharga, 0, ',', '.'); ?></td>
                            </tr>
                            <?php $totalbelanja += $subharga; } ?>
                        </tbody>
                        <tfoot class="table-footer-custom">
                            <tr>
                                <th colspan="4" class="text-end">Total Belanja</th>
                                <th>Rp <?= number_format($totalbelanja, 0, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <form method="post" class="w-100"> <div class="card mt-5">
                <div class="card-header bg-accent-custom text-white">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-truck me-2"></i> Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="nama_pelanggan" class="form-label text-muted-custom"><small>Nama Lengkap</small></label>
                            <input type="text" id="nama_pelanggan" readonly value="<?= htmlspecialchars($_SESSION['pelanggan']['nama_pelanggan']); ?>" class="form-control form-control-dark">
                        </div>
                        <div class="col-md-6">
                            <label for="telepon_pelanggan" class="form-label text-muted-custom"><small>No. Telepon</small></label>
                            <input type="text" id="telepon_pelanggan" readonly value="<?= htmlspecialchars($_SESSION['pelanggan']['telepon_pelanggan']); ?>" class="form-control form-control-dark">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="id_ongkir" class="form-label text-muted-custom">Pilih Ongkos Kirim</label>
                        <select class="form-select form-control-dark" name="id_ongkir" id="id_ongkir" required>
                            <option value="">-- Pilih Ongkos Kirim --</option>
                            <?php
                            $ambil = $koneksi->query("SELECT * FROM ongkir");
                            while ($perongkir = $ambil->fetch_assoc()) {
                            ?>
                            <option value="<?= $perongkir["id_ongkir"]; ?>">
                                <?= htmlspecialchars($perongkir['nama_kota']); ?> - Rp <?= number_format($perongkir['tarif'], 0, ',', '.'); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="alamat_pengiriman" class="form-label text-muted-custom">Alamat Lengkap Pengiriman</label>
                        <textarea class="form-control form-control-dark" name="alamat_pengiriman" id="alamat_pengiriman" rows="3" placeholder="Masukkan alamat lengkap pengiriman (termasuk kode pos)" required><?= htmlspecialchars($_SESSION['pelanggan']['alamat_pelanggan']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="d-grid mt-4">
                <button class="btn btn-warning btn-lg" name="checkout"><i class="fas fa-shopping-cart me-2"></i> Checkout Sekarang</button>
            </div>
        </form>

        <?php
        if (isset($_POST["checkout"])) {
            $id_pelanggan = $_SESSION['pelanggan']["id_pelanggan"];
            $id_ongkir = $_POST["id_ongkir"];
            $tanggal_pembelian = date("Y-m-d H:i:s");
            $alamat_pengiriman = $koneksi->real_escape_string($_POST['alamat_pengiriman']);

            $ambil = $koneksi->query("SELECT * FROM ongkir WHERE id_ongkir='$id_ongkir'");
            $arrayongkir = $ambil->fetch_assoc();
            $nama_kota = $koneksi->real_escape_string($arrayongkir['nama_kota']);
            $tarif = $arrayongkir['tarif'];

            $total_pembelian = $totalbelanja + $tarif;

            $koneksi->query("INSERT INTO pembelian (id_pelanggan, id_ongkir, tanggal_pembelian, total_pembelian, nama_kota, tarif, alamat_pengiriman, status_pembelian)
                VALUES ('$id_pelanggan', '$id_ongkir', '$tanggal_pembelian', '$total_pembelian', '$nama_kota', '$tarif', '$alamat_pengiriman', 'pending')");

            $id_pembelian_barusan = $koneksi->insert_id;

            foreach ($_SESSION['keranjang'] as $id_produk => $jumlah) {
                $ambil = $koneksi->query("SELECT * FROM produk WHERE id_produk='$id_produk'");
                $perproduk = $ambil->fetch_assoc();

                if ($perproduk['stok_produk'] < $jumlah) {
                    echo "<script>alert('Stok produk " . htmlspecialchars($perproduk['nama_produk']) . " tidak mencukupi. Silakan sesuaikan jumlah.');</script>";
                    echo "<script>location='keranjang.php';</script>";
                    exit();
                }

                $nama = $koneksi->real_escape_string($perproduk['nama_produk']);
                $harga = $perproduk['harga_produk'];
                $berat = $perproduk['berat_produk'];

                $subberat = $berat * $jumlah;
                $subharga = $harga * $jumlah;

                $koneksi->query("INSERT INTO pembelian_produk (id_pembelian, id_produk, nama, harga, berat, subberat, subharga, jumlah)
                    VALUES ('$id_pembelian_barusan', '$id_produk', '$nama', '$harga', '$berat', '$subberat', '$subharga', '$jumlah')");

                $koneksi->query("UPDATE produk SET stok_produk = stok_produk - $jumlah WHERE id_produk='$id_produk'");
            }

            unset($_SESSION['keranjang']);

            echo "<script>alert('Pembelian sukses! Mohon segera lakukan pembayaran.');</script>";
            echo "<script>location='nota.php?id=$id_pembelian_barusan';</script>";
        }
        ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>