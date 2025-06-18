<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi.php sudah benar dan berfungsi

// Memastikan pelanggan sudah login
if (!isset($_SESSION["pelanggan"]) || empty($_SESSION["pelanggan"])) {
    echo "<script>alert('Anda harus login untuk melakukan konfirmasi pembayaran.');</script>";
    echo "<script>location='login.php';</script>";
    exit();
}

// Amankan id_pembelian dari URL
$id_pembelian_aman = isset($_GET['id']) ? $koneksi->real_escape_string($_GET['id']) : null;

// Validasi dasar id_pembelian dari URL
if (empty($id_pembelian_aman)) {
    echo "<script>alert('ID Pembelian tidak ditemukan. Silakan kembali ke riwayat belanja Anda.');</script>";
    echo "<script>location='riwayat.php';</script>";
    exit();
}

// Ambil detail pembelian dari database
$ambil = $koneksi->query("SELECT * FROM pembelian WHERE id_pembelian='$id_pembelian_aman'");
$detpem = $ambil->fetch_assoc();

// Validasi apakah data pembelian ditemukan di database
if (empty($detpem)) {
    echo "<script>alert('Data pembelian tidak ditemukan atau ID tidak valid.');</script>";
    echo "<script>location='riwayat.php';</script>";
    exit();
}

// VALIDASI PENTING: Pastikan ID Pelanggan yang login sesuai dengan ID Pembelian yang ingin dibayar
$id_pelanggan_beli = $detpem["id_pelanggan"]; // ID pelanggan dari data pembelian di database
$id_pelanggan_login = $_SESSION["pelanggan"]["id_pelanggan"]; // ID pelanggan dari sesi login

if ($id_pelanggan_login !== $id_pelanggan_beli) {
    echo "<script>alert('Anda tidak berhak mengakses pembayaran untuk pesanan ini.');</script>";
    echo "<script>location='riwayat.php';</script>";
    exit();
}

// Cek status pembelian: jika sudah lunas atau sudah ada pembayaran, arahkan ke nota atau riwayat
if ($detpem['status_pembelian'] !== 'pending') {
    echo "<script>alert('Pesanan ini sudah " . htmlspecialchars($detpem['status_pembelian']) . ". Anda tidak dapat mengirim bukti lagi.');</script>";
    echo "<script>location='nota.php?id=$id_pembelian_aman';</script>";
    exit();
}

// Ambil total_pembelian untuk diisi otomatis
$total_tagihan = $detpem['total_pembelian'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pembayaran - TokoDaffa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
        }
        .alert-info {
            background-color: #e0f2f7;
            border-color: #b3e0ed;
            color: #0c5460;
            text-align: center;
        }
        /* Style untuk pesan kesalahan, agar lebih user-friendly tanpa debug-error */
        .error-message {
            background-color: #f8d7da; /* Warna merah muda untuk error */
            color: #721c24; /* Warna teks merah gelap */
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: .25rem;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; // Pastikan file menu.php ada ?>

<div class="container py-5">
    <h2 class="mb-4 text-center">Konfirmasi Pembayaran</h2>
    <p class="text-center">Kirim bukti pembayaran Anda di sini</p>

    <div class="alert alert-info">
        Total tagihan Anda <strong>Rp. <?php echo number_format($total_tagihan, 0, ',', '.') ?></strong>
    </div>

    <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama penyetor</label>
            <input type="text" class="form-control" name="nama" id="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="bank" class="form-label">Bank</label>
            <input type="text" class="form-control" name="bank" id="bank" required value="<?= htmlspecialchars($_POST['bank'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" required 
                   value="<?= $total_tagihan ?>" readonly> 
            <div class="form-text text-muted">Jumlah pembayaran otomatis terisi sesuai total tagihan Anda.</div>
        </div>
        <div class="mb-3">
            <label for="bukti" class="form-label">Foto Bukti</label>
            <input type="file" class="form-control" name="bukti" id="bukti" accept=".jpg,.jpeg,.png,.gif" required>
            <div class="form-text text-danger">Foto bukti harus JPG, JPEG, PNG, atau GIF. Maksimal 2MB.</div>
        </div>
        <button class="btn btn-primary w-100" name="kirim" type="submit">Kirim</button>
    </form>

    <?php
    if (isset($_POST["kirim"])) {
        $nama = htmlspecialchars($_POST["nama"]);
        $bank = htmlspecialchars($_POST["bank"]);
        // Jumlah diambil langsung dari hidden field atau dari total_tagihan
        $jumlah = (int)$_POST["jumlah"]; // Tetap ambil dari POST jika ada validasi tambahan atau untuk konsistensi,
                                         // tapi nilai ini akan selalu sama dengan $total_tagihan karena readonly.
        $tanggal = date("Y-m-d");

        $namabukti = $_FILES["bukti"]["name"];
        $lokasibukti = $_FILES["bukti"]["tmp_name"];
        $ukuranbukti = $_FILES["bukti"]["size"];
        $errorbukti = $_FILES["bukti"]["error"];
        $tipebukti = pathinfo($namabukti, PATHINFO_EXTENSION);

        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        $errors = []; // Array untuk menampung pesan kesalahan

        // --- VALIDASI DATA INPUT FORM ---
        if (empty($nama)) { $errors[] = "Nama penyetor tidak boleh kosong."; }
        if (empty($bank)) { $errors[] = "Nama bank tidak boleh kosong."; }
        
        // Validasi jumlah pembayaran HARUS SAMA dengan total pembelian
        // Meskipun input readonly, validasi di sisi server tetap penting
        if ($jumlah != $total_tagihan) {
            $errors[] = "Terjadi ketidaksesuaian jumlah pembayaran. Harap hubungi administrator.";
            // Anda bisa tambahkan log di sini jika ingin melacak upaya manipulasi
        }

        // --- VALIDASI UPLOAD FILE ---
        if ($errorbukti === UPLOAD_ERR_NO_FILE) {
            $errors[] = "Anda belum memilih file bukti pembayaran.";
        } elseif ($errorbukti !== UPLOAD_ERR_OK) {
            // Untuk user, kita kasih pesan umum. Jika ingin detail, bisa pakai kode error.
            $errors[] = "Terjadi kesalahan saat mengunggah file. Mohon coba lagi.";
        } else {
            if (!in_array(strtolower($tipebukti), $allowed_extensions)) {
                $errors[] = "Format file tidak didukung. Hanya JPG, JPEG, PNG, dan GIF yang diizinkan.";
            }
            if ($ukuranbukti > 2 * 1024 * 1024) { // 2MB
                $errors[] = "Ukuran file terlalu besar. Maksimal 2MB.";
            }
        }

        // --- TAMPILKAN SEMUA ERROR (JIKA ADA) ---
        if (!empty($errors)) {
            echo "<div class='error-message mt-3'>";
            echo "<h4>Kesalahan Input:</h4>";
            echo "<ul>";
            foreach ($errors as $err) {
                echo "<li>" . htmlspecialchars($err) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        } else {
            // --- JIKA TIDAK ADA ERROR, LANJUTKAN PROSES UPLOAD DAN INSERT DATA ---
            $namafiks = date("YmdHis") . "_" . uniqid() . "." . $tipebukti; // Nama unik untuk file

            // Pastikan folder "bukti_pembayaran" ada dan memiliki izin tulis (chmod 755 atau 777)
            $upload_dir = "bukti_pembayaran/";
            if (!is_dir($upload_dir)) {
                // Mencoba membuat folder jika tidak ada
                if (!mkdir($upload_dir, 0777, true)) {
                    echo "<script>alert('ERROR: Gagal membuat folder bukti_pembayaran. Mohon hubungi administrator.');</script>";
                    exit(); // Hentikan proses jika gagal membuat folder
                }
            }

            if (move_uploaded_file($lokasibukti, $upload_dir . $namafiks)) {
                // Query INSERT dan UPDATE Pembelian
                $insert_query = "INSERT INTO pembayaran (id_pembelian, nama, bank, jumlah, tanggal, bukti)
                                 VALUES ('$id_pembelian_aman', '$nama', '$bank', '$jumlah', '$tanggal', '$namafiks')";
                $update_query = "UPDATE pembelian SET status_pembelian='Sudah Kirim Pembayaran' WHERE id_pembelian='$id_pembelian_aman'";

                if ($koneksi->query($insert_query) && $koneksi->query($update_query)) {
                    echo "<script>alert('Terima kasih. Bukti pembayaran Anda berhasil terkirim. Mohon menunggu konfirmasi dari admin.');</script>";
                    echo "<script>location='riwayat.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('ERROR: Gagal menyimpan data pembayaran ke database. Mohon coba lagi atau hubungi administrator.');</script>";
                    exit(); // Hentikan jika query gagal
                }
            } else {
                echo "<script>alert('ERROR: Gagal mengunggah file bukti pembayaran. Pastikan ukuran dan format file sudah benar, atau hubungi administrator.');</script>";
                exit(); // Hentikan jika upload gagal
            }
        }
    }
    ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>