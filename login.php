<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php ada dan benar
?>

<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login Pelanggan - Toko Ickwan</title>
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
		.card {
			box-shadow: 0 0 15px rgba(0,0,0,0.1); /* Shadow untuk kartu login */
		}
        /* Konten utama akan memanjang untuk mengisi ruang kosong, mendorong footer ke bawah */
        .content-wrap {
            flex-grow: 1;
            display: flex;
            align-items: center; /* Untuk memposisikan card di tengah vertikal jika ruang cukup */
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

<div class="content-wrap py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Login Pelanggan</h4>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary" name="Login">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        Belum punya akun? <a href="daftar.php">Daftar di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <?php
if (isset($_POST["Login"])) {
	$email = $_POST['email'];
	$password = $_POST['password'];

	$ambil = $koneksi->query("SELECT * FROM pelanggan WHERE email_pelanggan='$email' AND password_pelanggan='$password'");
	$akunyangcocok = $ambil->num_rows;

	if ($akunyangcocok == 1) {
		$_SESSION['pelanggan'] = $ambil->fetch_assoc();
		echo "<script>alert('Anda berhasil login');</script>";

		if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
			echo "<script>location='checkout.php';</script>";
		} else {
			echo "<script>location='riwayat.php';</script>";
		}
	} else {
		echo "<script>alert('Login gagal. Email atau password salah.');</script>";
		echo "<script>location='login.php';</script>";
	}
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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