<?php
include '../config/koneksi.php';

// Ambil tanggal dari form (default: hari ini)
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Query transaksi penjualan berdasarkan tanggal
$sql = "SELECT * FROM transaksi WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir' ORDER BY id_transaksi DESC";
$res = mysqli_query($conn, $sql);

// Hitung total pendapatan
$totalPendapatan = 0;
while ($r = mysqli_fetch_assoc($res)) {
    $totalPendapatan += $r['total'];
}
mysqli_data_seek($res, 0); // reset pointer untuk loop ulang tabel
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan & Stok</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <h3>📊 Laporan Penjualan & Stok</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">🏠 Kembali</a>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Tanggal Awal</label>
            <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal; ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir; ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">🔍 Tampilkan</button>
        </div>
    </form>

    <h5>🧾 Laporan Penjualan</h5>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>ID Transaksi</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['tanggal']; ?></td>
                <td>#<?= $row['id_transaksi']; ?></td>
                <td><?= number_format($row['total'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if ($no === 1): ?>
                <tr><td colspan="4" class="text-center">Tidak ada transaksi pada rentang tanggal ini</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="table-secondary">
                <th colspan="3" class="text-end">Total Pendapatan</th>
                <th>Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></th>
            </tr>
        </tfoot>
    </table>

    <hr>

    <h5>📦 Stok Barang Saat Ini</h5>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Nilai Stok (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $stok_query = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
            $total_nilai_stok = 0;
            while ($b = mysqli_fetch_assoc($stok_query)):
                $nilai = $b['stok'] * $b['harga_jual'];
                $total_nilai_stok += $nilai;
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $b['kode_barang']; ?></td>
                <td><?= $b['nama_barang']; ?></td>
                <td><?= number_format($b['harga_jual']); ?></td>
                <td><?= $b['stok']; ?></td>
                <td><?= number_format($nilai, 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="table-secondary">
                <th colspan="5" class="text-end">Total Nilai Stok</th>
                <th>Rp <?= number_format($total_nilai_stok, 0, ',', '.'); ?></th>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
