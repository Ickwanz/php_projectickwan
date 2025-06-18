<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pelanggan - Toko Ickwan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh; 
        }
        .card {
            box-shadow: 0 0 15px rgba(0,0,0,0.1); 
        }

        .content-wrap {
            flex-grow: 1;
            display: flex;
            align-items: center; 
        }


        footer {
            background-color: #343a40; 
            color: #f8f9fa; 
            padding-top: 2rem;
            padding-bottom: 2rem;
            border-top: 1px solid #495057; 
        }
        footer h6 {
            color: #ffffff; 
            font-weight: 700;
        }
        footer hr {
            background-color: #ffc107; 
            height: 2px;
            border: none;
            opacity: 1; 
        }
        footer a {
            color: #adb5bd; 
            text-decoration: none;
            transition: color 0.3s ease;
        }
        footer a:hover {
            color: #ffffff; 
        }
        footer .text-center.p-3 {
            background-color: rgba(0, 0, 0, 0.2); 
            color: #ced4da; 
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="content-wrap py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Daftar Pelanggan</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" required />
                                <div class="invalid-feedback">Nama wajib diisi.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required />
                                <div class="invalid-feedback">Email wajib diisi dan harus valid.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required />
                                <div class="invalid-feedback">Password wajib diisi.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3" required></textarea>
                                <div class="invalid-feedback">Alamat wajib diisi.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telp/Hp</label>
                                <input type="text" name="telepon" class="form-control" required />
                                <div class="invalid-feedback">Nomor telepon wajib diisi.</div>
                            </div>
                            <button type="submit" name="daftar" class="btn btn-primary w-100">Daftar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <script>
// Bootstrap form validation
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', e => {
            if (!form.checkValidity()) {
                e.preventDefault()
                e.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})();
</script>

<?php
if (isset($_POST["daftar"])) {
    $nama = $koneksi->real_escape_string($_POST['nama']);
    $email = $koneksi->real_escape_string($_POST['email']);
    $password = $koneksi->real_escape_string($_POST['password']);
    $alamat = $koneksi->real_escape_string($_POST['alamat']);
    $telepon = $koneksi->real_escape_string($_POST['telepon']);

    $ambil = $koneksi->query("SELECT * FROM pelanggan WHERE email_pelanggan='$email'");
    $yangcocok = $ambil->num_rows;

    if ($yangcocok == 1) {
        echo "<script>alert('Pendaftaran gagal, email sudah digunakan');</script>";
        echo "<script>location='daftar.php';</script>";
    } else {
        $koneksi->query("INSERT INTO pelanggan (email_pelanggan, password_pelanggan, nama_pelanggan, telepon_pelanggan, alamat_pelanggan) VALUES ('$email', '$password', '$nama', '$telepon', '$alamat')");
        echo "<script>alert('Pendaftaran sukses, silakan login');</script>";
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
                <p><a href="index.php">Beranda</a></p>
                <p><a href="produk.php">Produk</a></p>
                <p><a href="kontak.php">Kontak</a></p>
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