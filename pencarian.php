<?php include "koneksi.php"; ?>
<?php 
$keyword = $_GET['keyword'];

$semuadata = array();
$ambil = $koneksi->query("SELECT * FROM produk WHERE nama_produk LIKE '%$keyword%' OR deskripsi_produk LIKE '%$keyword%'");
while ($pecah = $ambil->fetch_assoc()) {
	$semuadata[] = $pecah;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<title>Pencarian Produk - Toko Ickwan</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "menu.php"; ?>

<div class="container py-5">
	<h2 class="mb-4 text-center">Hasil Pencarian: <span class="text-info">"<?php echo htmlspecialchars($keyword); ?>"</span></h2>

	<?php if(empty($semuadata)): ?>
		<div class="alert alert-danger text-center shadow">Produk dengan kata "<strong><?php echo htmlspecialchars($keyword); ?></strong>" tidak ditemukan.</div>
	<?php endif; ?>

	<div class="row g-4">
		<?php foreach ($semuadata as $value): ?>
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card h-100 shadow-sm">
					<img src="foto_produk/<?php echo $value["foto_produk"] ?>" class="card-img-top" alt="<?php echo $value["nama_produk"] ?>">
					<div class="card-body text-center d-flex flex-column">
						<h5 class="card-title"><?php echo $value["nama_produk"] ?></h5>
						<p class="card-text">Rp. <?php echo number_format($value['harga_produk'], 0, ',', '.') ?></p>
						<div class="mt-auto">
							<a href="detail.php?id=<?php echo $value["id_produk"]; ?>" class="btn btn-outline-secondary btn-sm me-1">Detail</a>
							<a href="beli.php?id=<?php echo $value['id_produk']; ?>" class="btn btn-primary btn-sm">Beli</a>
						</div>
					</div>
				</div>
			</div>	
		<?php endforeach; ?>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
