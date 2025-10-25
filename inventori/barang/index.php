<?php
include '../config/koneksi.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>📦 Data Barang</h2>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah Barang</a>
<a href="../index.php" class="btn btn-secondary mb-3">🏠 Kembali</a>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = mysqli_query($conn, "SELECT * FROM barang ORDER BY id_barang DESC");
        while ($data = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $data['kode_barang']; ?></td>
            <td><?= $data['nama_barang']; ?></td>
            <td><?= $data['satuan']; ?></td>
            <td><?= number_format($data['harga_beli']); ?></td>
            <td><?= number_format($data['harga_jual']); ?></td>
            <td><?= $data['stok']; ?></td>
            <td>
                <a href="edit.php?id=<?= $data['id_barang']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="hapus.php?id=<?= $data['id_barang']; ?>" onclick="return confirm('Yakin hapus data ini?')" class="btn btn-sm btn-danger">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
