<?php
include '../config/koneksi.php';

// Handle form submit
if (isset($_POST['simpan'])) {
    $id_barang = intval($_POST['id_barang']);
    $jumlah    = intval($_POST['jumlah']);
    $tanggal   = $_POST['tanggal'] ?: date('Y-m-d');

    if ($id_barang > 0 && $jumlah > 0) {
        // Cek stok saat ini
        $stmtC = $conn->prepare("SELECT stok FROM barang WHERE id_barang = ?");
        $stmtC->bind_param("i", $id_barang);
        $stmtC->execute();
        $res = $stmtC->get_result()->fetch_assoc();
        $stmtC->close();

        $stok_sekarang = intval($res['stok'] ?? 0);

        if ($stok_sekarang >= $jumlah) {
            // Insert ke stok_keluar
            $stmt = $conn->prepare("INSERT INTO stok_keluar (id_barang, tanggal, jumlah) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $id_barang, $tanggal, $jumlah);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) {
                // Update stok: kurangi
                $stmt2 = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
                $stmt2->bind_param("ii", $jumlah, $id_barang);
                $stmt2->execute();
                $stmt2->close();

                header("Location: keluar.php");
                exit;
            } else {
                $error = "Gagal menyimpan data stok keluar.";
            }
        } else {
            $error = "Stok tidak mencukupi. Stok saat ini: $stok_sekarang";
        }
    } else {
        $error = "Pilih barang dan jumlah harus > 0.";
    }
}

// Ambil data barang
$barang_q = $conn->query("SELECT id_barang, kode_barang, nama_barang, stok FROM barang ORDER BY nama_barang ASC");

// Ambil riwayat stok keluar
$riwayat = $conn->query("SELECT sk.*, b.nama_barang, b.kode_barang 
                         FROM stok_keluar sk 
                         JOIN barang b ON sk.id_barang = b.id_barang
                         ORDER BY sk.id_keluar DESC LIMIT 50");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stok Keluar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <h3>🔽 Stok Keluar</h3>
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
                    <button type="submit" name="simpan" class="btn btn-danger">Simpan Stok Keluar</button>
                </div>
            </form>
        </div>
    </div>

    <h5>Riwayat Stok Keluar (50 terakhir)</h5>
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
                <tr><td colspan="5" class="text-center">Belum ada data stok keluar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
