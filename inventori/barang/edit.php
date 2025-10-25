<?php
include '../config/koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang='$id'"));

if (isset($_POST['update'])) {
    $kode  = $_POST['kode'];
    $nama  = $_POST['nama'];
    $satuan = $_POST['satuan'];
    $beli  = $_POST['beli'];
    $jual  = $_POST['jual'];
    $stok  = $_POST['stok'];

    $query = mysqli_query($conn, "UPDATE barang SET 
                kode_barang='$kode',
                nama_barang='$nama',
                satuan='$satuan',
                harga_beli='$beli',
                harga_jual='$jual',
                stok='$stok'
            WHERE id_barang='$id'");
    if ($query) {
        header('Location: index.php');
    } else {
        echo "Gagal update data!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Edit Barang</h2>
<form method="post">
    <div class="mb-3">
        <label>Kode Barang</label>
        <input type="text" name="kode" class="form-control" value="<?= $data['kode_barang']; ?>">
    </div>
    <div class="mb-3">
        <label>Nama Barang</label>
        <input type="text" name="nama" class="form-control" value="<?= $data['nama_barang']; ?>">
    </div>
    <div class="mb-3">
        <label>Satuan</label>
        <input type="text" name="satuan" class="form-control" value="<?= $data['satuan']; ?>">
    </div>
    <div class="mb-3">
        <label>Harga Beli</label>
        <input type="number" name="beli" class="form-control" value="<?= $data['harga_beli']; ?>">
    </div>
    <div class="mb-3">
        <label>Harga Jual</label>
        <input type="number" name="jual" class="form-control" value="<?= $data['harga_jual']; ?>">
    </div>
    <div class="mb-3">
        <label>Stok</label>
        <input type="number" name="stok" class="form-control" value="<?= $data['stok']; ?>">
    </div>
    <button type="submit" name="update" class="btn btn-success">Update</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

</body>
</html>
