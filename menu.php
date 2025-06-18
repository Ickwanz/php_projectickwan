<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>

<!-- navbar modern - Bootstrap 5 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">Ickwan Store</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
      aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="keranjang.php">Keranjang</a>
        </li>

        <?php if (isset($_SESSION['pelanggan'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="riwayat.php">Riwayat Belanja</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-warning" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="daftar.php">Daftar</a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link" href="checkout.php">Checkout</a>
        </li>
      </ul>

      <form class="d-flex" action="pencarian.php" method="get">
        <input class="form-control me-2" type="search" name="keyword" placeholder="Cari produk..." aria-label="Search" required>
        <button class="btn btn-outline-light" type="submit">Cari</button>
      </form>
    </div>
  </div>
</nav>
