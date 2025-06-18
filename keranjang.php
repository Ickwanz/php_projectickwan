<?php
session_start();
include 'koneksi.php';

if(empty($_SESSION['keranjang']) OR !isset($_SESSION["keranjang"])) {
	echo "<script>alert('Keranjang kosong, silakan belanja dulu');</script>";
	echo "<script>location='index.php';</script>";
	exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Keranjang Belanja - Toko Ickwan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    </head>
<body>

<?php include 'menu.php'; ?>

<section class="py-5">
	<div class="container">
		<h2 class="mb-4 text-center">🛒 Keranjang Belanja Anda</h2>
		<div class="table-responsive">
			<table class="table table-bordered align-middle text-center">
				<thead class="table-dark">
					<tr>
						<th>No</th>
						<th>Produk</th>
						<th>Harga</th>
						<th>Jumlah</th>
						<th>Subtotal</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php $nomor = 1; ?>
					<?php foreach ($_SESSION['keranjang'] as $id_produk => $jumlah): ?>
						<?php
						$ambil = $koneksi->query("SELECT * FROM produk WHERE id_produk='$id_produk'");
						$pecah = $ambil->fetch_assoc();
						$subharga = $pecah["harga_produk"] * $jumlah;
						?>
						<tr>
							<td><?php echo $nomor++; ?></td>
							<td class="text-start"><?php echo htmlspecialchars($pecah['nama_produk']); ?></td>
							<td>Rp. <?php echo number_format($pecah["harga_produk"], 0, ',', '.'); ?></td>
							<td><?php echo $jumlah; ?></td>
							<td class="text-success fw-bold">Rp. <?php echo number_format($subharga, 0, ',', '.'); ?></td>
							<td>
								<a href="hapuskeranjang.php?id=<?php echo $id_produk ?>" class="btn btn-sm btn-danger">Hapus</a>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>

		<div class="d-flex justify-content-between mt-4">
			<a href="index.php" class="btn btn-outline-secondary">← Lanjutkan Belanja</a>
			<a href="checkout.php" class="btn btn-primary">Checkout →</a>
		</div>
	</div>
</section>

<footer class="bg-dark text-white-50 py-4 mt-5">
    <div class="container text-center text-md-start">
        <div class="row">
            <div class="col-md-4 mx-auto mb-4 text-center">
                <h6 class="text-uppercase fw-bold text-light">Toko Ickwan</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: var(--accent-color); height: 2px"/>
                <p>Menyediakan produk-produk berkualitas dengan harga terbaik. Kepuasan pelanggan adalah prioritas kami.</p>
            </div>

            <div class="col-md-4 mx-auto mb-4 text-center">
                <h6 class="text-uppercase fw-bold text-light">Link Berguna</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: var(--accent-color); height: 2px"/>
                <p><a href="index.php" class="text-white-50 text-decoration-none">Beranda</a></p>
                <p><a href="produk.php" class="text-white-50 text-decoration-none">Produk</a></p>
                <p><a href="kontak.php" class="text-white-50 text-decoration-none">Kontak</a></p>
                <p><a href="https://www.instagram.com/ickwanzz?igsh=eHhjczVka2NqdG1h" class="text-white-50 text-decoration-none" target="_blank">Tentang Kami (Instagram)</a></p>
            </div>

            <div class="col-md-4 mx-auto mb-4 text-center">
                <h6 class="text-uppercase fw-bold text-light">Kontak</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: var(--accent-color); height: 2px"/>
                <p><i class="fas fa-home me-3"></i> Lubuklinggau, Sumatera Selatan</p>
                <p><i class="fas fa-envelope me-3"></i> info@tokoickwan.com</p>
                <p><i class="fas fa-phone me-3"></i> +62 822 7906 6047</p>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © <?php echo date('Y'); ?> Toko Ickwan. All rights reserved.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>