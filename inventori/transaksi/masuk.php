<?php
include '../config/koneksi.php';

// Handle form submit
if (isset($_POST['simpan'])) {
    $id_barang = intval($_POST['id_barang']);
    $jumlah    = intval($_POST['jumlah']);
    $tanggal   = $_POST['tanggal'] ?: date('Y-m-d');

    if ($id_barang > 0 && $jumlah > 0) {
        // Insert ke tabel stok_masuk
        $stmt = $conn->prepare("INSERT INTO stok_masuk (id_barang, tanggal, jumlah) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $id_barang, $tanggal, $jumlah);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            // Update stok barang: tambah
            $stmt2 = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
            $stmt2->bind_param("ii", $jumlah, $id_barang);
            $stmt2->execute();
            $stmt2->close();

            header("Location: masuk.php");
            exit;
        } else {
            $error = "Gagal menyimpan data stok masuk.";
        }
    } else {
        $error = "Pilih barang dan jumlah harus > 0.";
    }
}

// Ambil data barang untuk dropdown
$barang_q = $conn->query("SELECT id_barang, kode_barang, nama_barang, stok FROM barang ORDER BY nama_barang ASC");

// Ambil riwayat stok masuk (terbaru)
$riwayat = $conn->query("SELECT sm.*, b.nama_barang, b.kode_barang 
                        FROM stok_masuk sm 
                        JOIN barang b ON sm.id_barang = b.id_barang
                        ORDER BY sm.id_masuk DESC LIMIT 50");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stok Masuk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <h3>🔼 Stok Masuk</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">🏠 Kembali</a>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Pilih Barang</label>
                    <select name="id_barang" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php while($b = $barang_q->fetch_assoc()): ?>
                            <option value="<?= $b['id_barang']; ?>">
                                <?= $b['kode_barang']; ?> - <?= $b['nama_barang']; ?> (stok: <?= $b['stok']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>">
                </div>
                <div class="col-12">
                    <button type="submit" name="simpan" class="btn btn-success">Simpan Stok Masuk</button>
                </div>
            </form>
        </div>
    </div>

    <h5>Riwayat Stok Masuk (50 terakhir)</h5>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr><th>No</th><th>Tanggal</th><th>Kode</th><th>Nama Barang</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
            <?php $no=1; while($r = $riwayat->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $r['tanggal']; ?></td>
                    <td><?= $r['kode_barang']; ?></td>
                    <td><?= $r['nama_barang']; ?></td>
                    <td><?= $r['jumlah']; ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if ($no === 1): ?>
                <tr><td colspan="5" class="text-center">Belum ada data stok masuk.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
