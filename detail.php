<?php
session_start();
include 'koneksi.php';

// Memastikan id_produk ada dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php'); // Redirect jika ID tidak valid
    exit;
}

$id_produk = (int)$_GET['id'];

// Query ambil data produk
$ambil = $koneksi->prepare("SELECT * FROM produk WHERE id_produk = ?");
$ambil->bind_param("i", $id_produk);
$ambil->execute();
$result = $ambil->get_result();
$detail = $result->fetch_assoc();

// Jika produk tidak ditemukan
if (!$detail) {
    header('Location: index.php'); // Redirect jika produk tidak ditemukan
    exit;
}

// Proses penambahan ke keranjang
if (isset($_POST['beli'])) {
    $jumlah = (int)$_POST['jumlah'];

    if ($jumlah > 0 && $jumlah <= (int)$detail['stok_produk']) {
        // Tambahkan atau update jumlah produk di keranjang
        if (isset($_SESSION['keranjang'][$id_produk])) {
            $_SESSION['keranjang'][$id_produk] += $jumlah;
        } else {
            $_SESSION['keranjang'][$id_produk] = $jumlah;
        }
        echo "<script>alert('Produk telah ditambahkan ke keranjang!');</script>";
        echo "<script>location='keranjang.php';</script>";
    } else {
        echo "<script>alert('Jumlah tidak valid atau melebihi stok yang tersedia.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detail Produk - <?= htmlspecialchars($detail['nama_produk']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif; /* Menggunakan font yang lebih modern */
            color: #333;
        }
        section.konten {
            padding: 60px 0;
        }
        .card-detail {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .produk-img-container {
            background-color: #fff;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom-left-radius: 12px;
            border-top-left-radius: 12px;
        }
        .produk-img {
            max-width: 100%;
            height: auto; /* Mengatur tinggi otomatis agar gambar tidak terdistorsi */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 15px;
            font-size: 2.2rem;
        }
        .harga {
            font-size: 2rem;
            color: #28a745; /* Warna hijau yang lebih menonjol */
            font-weight: 800;
            margin-bottom: 20px;
        }
        .stok {
            font-size: 1.1rem;
            margin-bottom: 25px;
            color: #555;
            display: flex;
            align-items: center;
        }
        .stok i {
            margin-right: 8px;
            color: #6c757d;
        }
        .deskripsi {
            margin-top: 30px;
            line-height: 1.8;
            color: #444;
            background-color: #f1f6fc;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #0d6efd;
        }
        .deskripsi strong {
            color: #0d6efd;
        }
        .form-jumlah {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .form-control-jumlah {
            width: 80px;
            text-align: center;
            font-size: 1.1rem;
            border-radius: 6px;
        }
        .btn-beli {
            background-color: #0d6efd;
            border-color: #0d6efd;
            font-size: 1.1rem;
            padding: 10px 25px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .btn-beli:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }
        .btn-qty {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-qty:hover {
            background-color: #dee2e6;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<section class="konten">
    <div class="container">
        <div class="card card-detail">
            <div class="row g-0">
                <div class="col-md-6 produk-img-container">
                    <img src="foto_produk/<?= htmlspecialchars($detail['foto_produk']) ?>" alt="<?= htmlspecialchars($detail['nama_produk']) ?>" class="produk-img" />
                </div>
                <div class="col-md-6 p-4">
                    <h2><?= htmlspecialchars($detail['nama_produk']) ?></h2>
                    <div class="harga">Rp <?= number_format($detail['harga_produk'], 0, ',', '.') ?></div>
                    <div class="stok">
                        <i class="fas fa-cubes"></i> Stok Tersedia: <span id="stok-produk"><?= (int)$detail['stok_produk'] ?></span>
                    </div>

                    <form method="post" class="form-jumlah">
                        <label for="jumlah-input" class="form-label mb-0">Jumlah:</label>
                        <div class="input-group" style="width: auto;">
                            <button type="button" class="btn btn-qty" id="btn-minus"><i class="fas fa-minus"></i></button>
                            <input type="number" name="jumlah" id="jumlah-input" class="form-control form-control-jumlah" value="1" min="1" max="<?= (int)$detail['stok_produk'] ?>" required />
                            <button type="button" class="btn btn-qty" id="btn-plus"><i class="fas fa-plus"></i></button>
                        </div>
                        <button type="submit" name="beli" class="btn btn-primary btn-beli">
                            <i class="fas fa-cart-plus me-2"></i> Tambah ke Keranjang
                        </button>
                    </form>

                    <div class="deskripsi">
                        <strong>Deskripsi Produk:</strong>
                        <p><?= nl2br(htmlspecialchars($detail['deskripsi_produk'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahInput = document.getElementById('jumlah-input');
        const btnMinus = document.getElementById('btn-minus');
        const btnPlus = document.getElementById('btn-plus');
        const stokProduk = parseInt(document.getElementById('stok-produk').innerText);

        btnMinus.addEventListener('click', function() {
            let currentValue = parseInt(jumlahInput.value);
            if (currentValue > 1) {
                jumlahInput.value = currentValue - 1;
            }
        });

        btnPlus.addEventListener('click', function() {
            let currentValue = parseInt(jumlahInput.value);
            if (currentValue < stokProduk) {
                jumlahInput.value = currentValue + 1;
            }
        });

        jumlahInput.addEventListener('change', function() {
            let currentValue = parseInt(this.value);
            if (isNaN(currentValue) || currentValue < 1) {
                this.value = 1;
            } else if (currentValue > stokProduk) {
                this.value = stokProduk;
                alert('Jumlah melebihi stok yang tersedia!');
            }
        });
    });
</script>
</body>
</html>