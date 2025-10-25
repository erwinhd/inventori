<?php
include '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $kode  = $_POST['kode'];
    $nama  = $_POST['nama'];
    $satuan = $_POST['satuan'];
    $beli  = $_POST['beli'];
    $jual  = $_POST['jual'];
    $stok  = $_POST['stok'];

    $query = mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, satuan, harga_beli, harga_jual, stok) 
                                  VALUES ('$kode','$nama','$satuan','$beli','$jual','$stok')");
    if ($query) {
        header('Location: index.php');
    } else {
        echo "Gagal menyimpan data!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Tambah Barang</h2>
<form method="post">
    <div class="mb-3">
        <label>Kode Barang</label>
        <input type="text" name="kode" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Nama Barang</label>
        <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Satuan</label>
        <input type="text" name="satuan" class="form-control" placeholder="pcs, box, dll">
    </div>
    <div class="mb-3">
        <label>Harga Beli</label>
        <input type="number" name="beli" class="form-control">
    </div>
    <div class="mb-3">
        <label>Harga Jual</label>
        <input type="number" name="jual" class="form-control">
    </div>
    <div class="mb-3">
        <label>Stok Awal</label>
        <input type="number" name="stok" class="form-control" value="0">
    </div>
    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

</body>
</html>
