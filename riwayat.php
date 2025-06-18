<?php
session_start();
// koneksi ke database
include 'koneksi.php';

// jk tidak ada session pelanggan(belum login) maka di larikan ke login.php
if (!isset($_SESSION["pelanggan"]) OR empty($_SESSION["pelanggan"]))
{
	echo "<script>alert('silahkan login');</script>";
	echo "<script>location='login.php';</script>";
	exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Riwayat Belanja - Toko Ickwan</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<style>
		/* CSS kustom untuk memastikan layout tetap rapi */
		body {
			background-color: #f8f9fa; /* Latar belakang body warna terang Bootstrap */
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Memastikan body mengambil tinggi penuh viewport */
		}
		/* Konten utama akan memanjang untuk mengisi ruang kosong, mendorong footer ke bawah */
        .content-wrap {
            flex-grow: 1;
            padding-top: 3rem; /* Memberi sedikit padding atas agar tidak terlalu mepet header */
            padding-bottom: 3rem; /* Memberi sedikit padding bawah agar tidak terlalu mepet footer */
        }

        /* Styling untuk tabel */
        .table-bordered {
            border-color: #dee2e6; /* Border warna default Bootstrap */
        }
        .table thead th {
            background-color: #e9ecef; /* Latar belakang header tabel sedikit gelap */
            color: #495057; /* Warna teks header tabel */
            border-color: #dee2e6;
        }
        .table tbody td {
            vertical-align: middle; /* Memastikan isi sel vertikal rata tengah */
            border-color: #dee2e6;
        }

        /* Styling untuk badge status */
        .badge {
            font-size: 0.85em;
            font-weight: 600;
            padding: 0.4em 0.7em;
            border-radius: 0.25rem;
        }

        /* Styling spesifik untuk footer agar sesuai dengan tema terang */
        footer {
            background-color: #343a40; /* Latar belakang gelap untuk footer */
            color: #f8f9fa; /* Warna teks terang untuk footer */
            padding-top: 2rem;
            padding-bottom: 2rem;
            border-top: 1px solid #495057; /* Border atas yang sedikit lebih terang */
        }
        footer h6 {
            color: #ffffff; /* Judul footer (Toko Ickwan, Link Berguna, Kontak) berwarna putih */
            font-weight: 700;
        }
        footer hr {
            background-color: #ffc107; /* Warna kuning Bootstrap untuk garis HR */
            height: 2px;
            border: none;
            opacity: 1; /* Pastikan garis terlihat penuh */
        }
        footer a {
            color: #adb5bd; /* Warna abu-abu redup untuk link */
            text-decoration: none;
            transition: color 0.3s ease;
        }
        footer a:hover {
            color: #ffffff; /* Warna putih saat link di-hover */
        }
        footer .text-center.p-3 {
            background-color: rgba(0, 0, 0, 0.2); /* Latar belakang untuk teks hak cipta */
            color: #ced4da; /* Warna teks untuk hak cipta */
            font-size: 0.9rem;
        }
	</style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="content-wrap">
    <div class="container">
        <h3 class="mb-4">Riwayat Belanja <?php echo htmlspecialchars($_SESSION['pelanggan']['nama_pelanggan']); ?></h3>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor = 1;
                    $id_pelanggan = $_SESSION['pelanggan']['id_pelanggan'];

                    $ambil = $koneksi->query("SELECT * FROM pembelian WHERE id_pelanggan='$id_pelanggan' ORDER BY tanggal_pembelian DESC");
                    while($pecah = $ambil->fetch_assoc()):
                        $status = strtolower($pecah["status_pembelian"]);
                        $badge_class = 'bg-secondary'; // Default badge color
                        if ($status == 'pending') $badge_class = 'bg-warning text-dark'; // Text dark for better contrast on yellow
                        elseif ($status == 'lunas') $badge_class = 'bg-success';
                        elseif ($status == 'dikirim') $badge_class = 'bg-primary';
                        elseif ($status == 'batal') $badge_class = 'bg-danger'; // Added for 'batal' status if needed
                    ?>
                    <tr>
                        <td><?php echo $nomor; ?></td>
                        <td><?php echo date("d M Y H:i", strtotime($pecah["tanggal_pembelian"])); ?></td>
                        <td>
                            <span class="badge <?php echo $badge_class; ?>">
                                <?php echo ucfirst($pecah["status_pembelian"]); ?>
                            </span>
                            <?php if (!empty($pecah['resi_pengiriman'])): ?>
                                <br>
                                <small class="text-muted">Resi: <?php echo htmlspecialchars($pecah['resi_pengiriman']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>Rp. <?php echo number_format($pecah["total_pembelian"]); ?></td>
                        <td>
                            <a href="nota.php?id=<?php echo $pecah['id_pembelian']; ?>" class="btn btn-info btn-sm">Nota</a>

                            <?php if ($status == "pending"): ?>
                                <a href="pembayaran.php?id=<?php echo $pecah["id_pembelian"]; ?>" class="btn btn-success btn-sm mt-1 mt-md-0 d-block d-md-inline-block">Input Pembayaran</a>
                            <?php else: ?>
                                <a href="lihat_pembayaran.php?id=<?php echo $pecah["id_pembelian"]; ?>" class="btn btn-warning btn-sm mt-1 mt-md-0 d-block d-md-inline-block">Lihat Pembayaran</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                    $nomor++;
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<footer>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4 text-center">
                <h6 class="text-uppercase fw-bold">Toko Ickwan</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;"/>
                <p>Menyediakan produk-produk berkualitas dengan harga terbaik. Kepuasan pelanggan adalah prioritas kami.</p>
            </div>

            <div class="col-md-4 mb-4 text-center">
                <h6 class="text-uppercase fw-bold">Link Berguna</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;"/>
                <p><a href="index.php">Beranda</a></p>
                <p><a href="https://www.instagram.com/ickwanzz?igsh=eHhjczVka2NqdG1h" target="_blank">Tentang Kami (Instagram)</a></p>
            </div>

            <div class="col-md-4 mb-4 text-center">
                <h6 class="text-uppercase fw-bold">Kontak</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;"/>
                <p><i class="fas fa-home me-2"></i> Lubuklinggau, Sumatera Selatan</p>
                <p><i class="fas fa-envelope me-2"></i> info@tokoickwan.com</p>
                <p><i class="fas fa-phone me-2"></i> +62 822 7906 6047</p>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © <?php echo date('Y'); ?> Toko Ickwan. All rights reserved.
    </div>
</footer>

</body>
</html>