<?php include 'config/koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistem Inventori & Kasir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2 class="text-center">💼 Sistem Inventori & Kasir</h2>
    <hr>
    <div class="d-flex justify-content-center">
        <a href="barang/index.php" class="btn btn-primary m-2">Data Barang</a>
        <a href="transaksi/masuk.php" class="btn btn-success m-2">Stok Masuk</a>
        <a href="transaksi/keluar.php" class="btn btn-danger m-2">Stok Keluar</a>
        <a href="transaksi/kasir.php" class="btn btn-warning m-2">Kasir</a>
        <a href="laporan/index.php" class="btn btn-dark m-2">Laporan</a>
    </div>
</body>
</html>
