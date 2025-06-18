<?php
session_start();
include 'koneksi.php';


$id_kategori_baju = 0;
$ambil_id_baju = $koneksi->query("SELECT id_kategori FROM kategori WHERE nama_kategori = 'Baju'");
if ($data_id_baju = $ambil_id_baju->fetch_assoc()) {
    $id_kategori_baju = $data_id_baju['id_kategori'];
}

$id_kategori_sepatu = 0;
$ambil_id_sepatu = $koneksi->query("SELECT id_kategori FROM kategori WHERE nama_kategori = 'Sepatu'");
if ($data_id_sepatu = $ambil_id_sepatu->fetch_assoc()) {
    $id_kategori_sepatu = $data_id_sepatu['id_kategori'];
}

$id_kategori_pilihan_kami = 0;
$ambil_id_pilihan_kami = $koneksi->query("SELECT id_kategori FROM kategori WHERE nama_kategori = 'Pilihan Kami'");
if ($data_id_pilihan_kami = $ambil_id_pilihan_kami->fetch_assoc()) {
    $id_kategori_pilihan_kami = $data_id_pilihan_kami['id_kategori'];
}

$filter_kategori_id = $_GET['kategori_id'] ?? null;
$filtered_category_name = '';

if ($filter_kategori_id && is_numeric($filter_kategori_id)) {
    $stmt = $koneksi->prepare("SELECT nama_kategori FROM kategori WHERE id_kategori = ?");
    $stmt->bind_param("i", $filter_kategori_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($name_data = $result->fetch_assoc()) {
        $filtered_category_name = $name_data['nama_kategori'];
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toko Ickwan - Elegan & Modern</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>

<?php include 'menu.php'; ?>

<section class="py-5 mt-4">
    <div class="container">
        <div class="text-center mb-5 bg-dark py-5 rounded-3">
            <?php if ($filter_kategori_id && $filtered_category_name): // Tampilan saat difilter kategori ?>
                <h1 class="display-4 text-warning fw-bold mb-3 animate__animated animate__fadeInDown">Kategori <?php echo htmlspecialchars($filtered_category_name); ?></h1>
                <p class="lead text-white-50 mx-auto animate__animated animate__fadeInUp" style="max-width: 700px;">
                    Jelajahi semua produk dari kategori <?php echo htmlspecialchars($filtered_category_name); ?> pilihan Anda.
                </p>
                <a href="index.php" class="btn btn-outline-warning btn-lg mt-4 animate__animated animate__fadeInUp">Kembali ke Semua Produk</a>
            <?php else: // Tampilan normal homepage, judul utama lebih umum ?>
                <h1 class="display-3 text-white fw-bolder mb-3 animate__animated animate__fadeInDown">Selamat Datang di <span class="text-warning">Toko Ickwan</span></h1>
                <p class="lead text-white-50 mx-auto mb-4 animate__animated animate__fadeInUp" style="max-width: 800px;">
                    Temukan koleksi produk inovatif dan berkualitas tinggi yang dirancang untuk meningkatkan gaya hidup Anda. Belanja sekarang dan rasakan perbedaannya!
                </p>
            <?php endif; ?>
        </div>

        <?php if ($filter_kategori_id && $filtered_category_name): // Jika ada kategori yang difilter, tampilkan hanya itu ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php
                // Mengambil produk dari kategori yang difilter
                $stmt_filtered = $koneksi->prepare("SELECT * FROM produk WHERE id_kategori = ? ORDER BY id_produk ASC");
                $stmt_filtered->bind_param("i", $filter_kategori_id);
                $stmt_filtered->execute();
                $ambil_produk_filtered = $stmt_filtered->get_result();

                if ($ambil_produk_filtered->num_rows > 0):
                    while ($perproduk = $ambil_produk_filtered->fetch_assoc()):
                        $harga = $perproduk['harga_produk'];
                        $diskon = isset($perproduk['diskon_produk']) && is_numeric($perproduk['diskon_produk']) ? $perproduk['diskon_produk'] : 0;
                        $harga_diskon = $harga - ($harga * $diskon / 100);
                ?>
                <div class="col">
                    <div class="card h-100 product-card">
                        <?php if (isset($perproduk['terjual']) && $perproduk['terjual'] > 100): ?>
                            <div class="badge-best"><i class="fas fa-star me-1"></i> Best Seller</div>
                        <?php endif; ?>
                        <?php if (!empty($perproduk['foto_produk']) && file_exists('foto_produk/' . $perproduk['foto_produk'])): ?>
                            <img src="foto_produk/<?php echo htmlspecialchars($perproduk['foto_produk']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($perproduk['nama_produk']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/250x250?text=No+Image" class="card-img-top" alt="Gambar Tidak Tersedia">
                        <?php endif; ?>
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($perproduk['nama_produk']); ?></h5>
                            <?php if($diskon > 0): ?>
                                <p class="card-text">
                                    Rp. <?php echo number_format($harga_diskon,0,',','.'); ?><br>
                                    <span class="price-old">Rp. <?php echo number_format($harga,0,',','.'); ?></span>
                                </p>
                            <?php else: ?>
                                <p class="card-text">Rp. <?php echo number_format($harga,0,',','.'); ?></p>
                            <?php endif; ?>
                            <div class="mt-auto d-flex justify-content-center pt-2">
                                <a href="detail.php?id=<?php echo $perproduk['id_produk']; ?>" class="btn btn-outline-light btn-sm me-2">Detail</a>
                                <a href="beli.php?id=<?php echo $perproduk['id_produk']; ?>" class="btn btn-warning btn-sm">Beli</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile;
                else: ?>
                    <div class="col-12 text-center">
                        <p class="text-white-50">Belum ada produk untuk kategori ini.</p>
                    </div>
                <?php endif;
                $stmt_filtered->close(); ?>
            </div>

        <?php else: // Jika tidak ada filter, tampilkan "Produk Pilihan Kami" dan kemudian kategori lainnya ?>

            <?php if ($id_kategori_pilihan_kami != 0): ?>
            <div id="produk-pilihan" class="mb-5 text-center">
                <h2 class="section-heading animate__animated animate__fadeInDown">Produk Pilihan Kami</h2>
                <p class="lead section-description animate__animated animate__fadeInUp">Jangan lewatkan koleksi terbaru dan terlaris dari kami.</p>
                <div class="row g-4 justify-content-center">
                    <?php
                    // Mengambil produk dari kategori 'Pilihan Kami' (LIMIT 4 dipertahankan)
                    $ambil_pilihan = $koneksi->query("SELECT * FROM produk WHERE id_kategori = '$id_kategori_pilihan_kami' ORDER BY id_produk DESC LIMIT 4");
                    if ($ambil_pilihan->num_rows > 0):
                        while ($perproduk_pilihan = $ambil_pilihan->fetch_assoc()):
                            $harga = $perproduk_pilihan['harga_produk'];
                            $diskon = isset($perproduk_pilihan['diskon_produk']) && is_numeric($perproduk_pilihan['diskon_produk']) ? $perproduk_pilihan['diskon_produk'] : 0;
                            $harga_diskon = $harga - ($harga * $diskon / 100);
                    ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 product-card animate__animated animate__zoomIn">
                            <?php if (isset($perproduk_pilihan['terjual']) && $perproduk_pilihan['terjual'] > 100): ?>
                                <div class="badge-best"><i class="fas fa-star me-1"></i> Best Seller</div>
                            <?php endif; ?>
                            <?php if (!empty($perproduk_pilihan['foto_produk']) && file_exists('foto_produk/' . $perproduk_pilihan['foto_produk'])): ?>
                                <img src="foto_produk/<?php echo htmlspecialchars($perproduk_pilihan['foto_produk']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($perproduk_pilihan['nama_produk']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/250x250?text=No+Image" class="card-img-top" alt="Gambar Tidak Tersedia">
                            <?php endif; ?>
                            <div class="card-body text-center d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($perproduk_pilihan['nama_produk']); ?></h5>
                                <?php if($diskon > 0): ?>
                                    <p class="card-text">
                                        Rp. <?php echo number_format($harga_diskon,0,',','.'); ?><br>
                                        <span class="price-old">Rp. <?php echo number_format($harga,0,',','.'); ?></span>
                                    </p>
                                <?php else: ?>
                                    <p class="card-text">Rp. <?php echo number_format($harga,0,',','.'); ?></p>
                                <?php endif; ?>
                                <div class="mt-auto d-flex justify-content-center pt-2">
                                    <a href="detail.php?id=<?php echo $perproduk_pilihan['id_produk']; ?>" class="btn btn-outline-light btn-sm me-2">Detail</a>
                                    <a href="beli.php?id=<?php echo $perproduk_pilihan['id_produk']; ?>" class="btn btn-warning btn-sm">Beli</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile;
                    else: ?>
                        <div class="col-12 text-center">
                            <p class="text-white-50">Belum ada produk dalam kategori Pilihan Kami.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($id_kategori_baju != 0): ?>
            <div class="text-center mb-5 mt-5">
                <h2 class="section-heading animate__animated animate__fadeInDown">Kategori Baju</h2>
                <p class="lead section-description animate__animated animate__fadeInUp">Temukan koleksi pakaian terbaru dan trendi.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php
                // Mengambil produk kategori 'Baju' dari database (LIMIT 4 dihapus)
                $ambil_baju = $koneksi->query("SELECT * FROM produk WHERE id_kategori = '$id_kategori_baju' ORDER BY id_produk DESC");
                if ($ambil_baju->num_rows > 0):
                    while ($perproduk = $ambil_baju->fetch_assoc()):
                        $harga = $perproduk['harga_produk'];
                        $diskon = isset($perproduk['diskon_produk']) && is_numeric($perproduk['diskon_produk']) ? $perproduk['diskon_produk'] : 0;
                        $harga_diskon = $harga - ($harga * $diskon / 100);
                ?>
                <div class="col">
                    <div class="card h-100 product-card animate__animated animate__fadeInUp">
                        <?php if (isset($perproduk['terjual']) && $perproduk['terjual'] > 100): ?>
                            <div class="badge-best"><i class="fas fa-star me-1"></i> Best Seller</div>
                        <?php endif; ?>
                        <?php if (!empty($perproduk['foto_produk']) && file_exists('foto_produk/' . $perproduk['foto_produk'])): ?>
                            <img src="foto_produk/<?php echo htmlspecialchars($perproduk['foto_produk']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($perproduk['nama_produk']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/250x250?text=No+Image" class="card-img-top" alt="Gambar Tidak Tersedia">
                        <?php endif; ?>
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($perproduk['nama_produk']); ?></h5>
                            <?php if($diskon > 0): ?>
                                <p class="card-text">
                                    Rp. <?php echo number_format($harga_diskon,0,',','.'); ?><br>
                                    <span class="price-old">Rp. <?php echo number_format($harga,0,',','.'); ?></span>
                                </p>
                            <?php else: ?>
                                <p class="card-text">Rp. <?php echo number_format($harga,0,',','.'); ?></p>
                            <?php endif; ?>
                            <div class="mt-auto d-flex justify-content-center pt-2">
                                <a href="detail.php?id=<?php echo $perproduk['id_produk']; ?>" class="btn btn-outline-light btn-sm me-2">Detail</a>
                                <a href="beli.php?id=<?php echo $perproduk['id_produk']; ?>" class="btn btn-warning btn-sm">Beli</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile;
                else: ?>
                    <div class="col-12 text-center">
                        <p class="text-white-50">Belum ada produk untuk kategori Baju.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($id_kategori_sepatu != 0): ?>
            <div class="text-center mb-5 mt-5">
                <h2 class="section-heading text-light animate__animated animate__fadeInDown">Kategori Sepatu</h2>
                <p class="lead section-description animate__animated animate__fadeInUp">Jelajahi berbagai pilihan sepatu stylish dan nyaman.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php
                // Mengambil produk kategori 'Sepatu' dari database (LIMIT 4 dihapus)
                $ambil_sepatu = $koneksi->query("SELECT * FROM produk WHERE id_kategori = '$id_kategori_sepatu' ORDER BY id_produk DESC");
                if ($ambil_sepatu->num_rows > 0):
                    while ($perproduk = $ambil_sepatu->fetch_assoc()):
                        $harga = $perproduk['harga_produk'];
                        $diskon = isset($perproduk['diskon_produk']) && is_numeric($perproduk['diskon_produk']) ? $perproduk['diskon_produk'] : 0;
                        $harga_diskon = $harga - ($harga * $diskon / 100);
                ?>
                <div class="col">
                    <div class="card h-100 product-card animate__animated animate__fadeInUp">
                        <?php if (isset($perproduk['terjual']) && $perproduk['terjual'] > 100): ?>
                            <div class="badge-best"><i class="fas fa-star me-1"></i> Best Seller</div>
                        <?php endif; ?>
                        <?php if (!empty($perproduk['foto_produk']) && file_exists('foto_produk/' . $perproduk['foto_produk'])): ?>
                            <img src="foto_produk/<?php echo htmlspecialchars($perproduk['foto_produk']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($perproduk['nama_produk']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/250x250?text=No+Image" class="card-img-top" alt="Gambar Tidak Tersedia">
                        <?php endif; ?>
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($perproduk['nama_produk']); ?></h5>
                            <?php if($diskon > 0): ?>
                                <p class="card-text">
                                    Rp. <?php echo number_format($harga_diskon,0,',','.'); ?><br>
                                    <span class="price-old">Rp. <?php echo number_format($harga,0,',','.'); ?></span>
                                </p>
                            <?php else: ?>
                                <p class="card-text">Rp. <?php echo number_format($harga,0,',','.'); ?></p>
                            <?php endif; ?>
                            <div class="mt-auto d-flex justify-content-center pt-2">
                                <a href="detail.php?id=<?php echo $perproduk['id_produk']; ?>" class="btn btn-outline-light btn-sm me-2">Detail</a>
                                <a href="beli.php?id=<?php echo $perproduk['id_produk']; ?>" class="btn btn-warning btn-sm">Beli</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile;
                else: ?>
                    <div class="col-12 text-center">
                        <p class="text-white-50">Belum ada produk untuk kategori Sepatu.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<section class="py-5 bg-dark">
    <div class="container text-center">
        <h2 class="section-heading text-light animate__animated animate__fadeInDown">Jelajahi Kategori Kami</h2>
        <p class="lead section-description animate__animated animate__fadeInUp">Temukan berbagai produk menarik sesuai kebutuhan Anda.</p>
        <div class="row justify-content-center g-4">
            <?php if ($id_kategori_baju != 0): ?>
            <div class="col-md-4">
                <div class="card h-100 category-card animate__animated animate__zoomIn">
                    <div class="card-body">
                        <i class="fas fa-tshirt fa-3x mb-3 text-warning"></i>
                        <h5 class="card-title text-light">Baju</h5>
                        <p class="card-text category-card-description">Koleksi pakaian terbaru dan trendi.</p>
                        <a href="index.php?kategori_id=<?php echo $id_kategori_baju; ?>" class="btn btn-outline-warning btn-sm">Lihat Lebih Lanjut</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($id_kategori_sepatu != 0): ?>
            <div class="col-md-4">
                <div class="card h-100 category-card animate__animated animate__zoomIn">
                    <div class="card-body">
                        <i class="fas fa-shoe-prints fa-3x mb-3 text-warning"></i>
                        <h5 class="card-title text-light">Sepatu</h5>
                        <p class="card-text category-card-description">Koleksi sepatu stylish dan nyaman.</p>
                        <a href="index.php?kategori_id=<?php echo $id_kategori_sepatu; ?>" class="btn btn-outline-warning btn-sm">Lihat Lebih Lanjut</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($id_kategori_pilihan_kami != 0): ?>
            <div class="col-md-4">
                <div class="card h-100 category-card animate__animated animate__zoomIn">
                    <div class="card-body">
                        <i class="fas fa-star fa-3x mb-3 text-warning"></i>
                        <h5 class="card-title text-light">Pilihan Kami</h5>
                        <p class="card-text category-card-description">Produk pilihan terbaik dari toko kami.</p>
                        <a href="index.php?kategori_id=<?php echo $id_kategori_pilihan_kami; ?>" class="btn btn-outline-warning btn-sm">Lihat Lebih Lanjut</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
                <p><a href="https://www.instagram.com/ickwanzz?igsh=eHhjczVka2NqdG1h" class="text-white-50 text-decoration-none" target="_blank">Instagram</a></p>
            </div>

            <div class="col-md-4 mx-auto mb-4 text-center">
                <h6 class="text-uppercase fw-bold text-light">Kontak</h6>
                <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: var(--accent-color); height: 2px"/>
                <p><i class="fas fa-home me-3"></i> Lubuklinggau, Sumatera Selatan</p>
                <p><i class="fas fa-envelope me-3"></i> info@tokoickwan.com</p>
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