<?php
include '../config/koneksi.php';

// Ambil daftar barang
$barang_query = $conn->query("SELECT * FROM barang ORDER BY nama_barang ASC");

// Handle transaksi
if (isset($_POST['simpan'])) {
    $tanggal = date('Y-m-d');
    $total   = 0;

    // Ambil data dari form
    $barang_id = $_POST['id_barang'];
    $jumlah    = $_POST['jumlah'];

    // Validasi
    if (!empty($barang_id) && is_array($barang_id)) {
        // Hitung total
        for ($i = 0; $i < count($barang_id); $i++) {
            $id = intval($barang_id[$i]);
            $qty = intval($jumlah[$i]);
            $barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga_jual FROM barang WHERE id_barang='$id'"));
            $total += $barang['harga_jual'] * $qty;
        }

        // Simpan transaksi utama
        $stmt = $conn->prepare("INSERT INTO transaksi (tanggal, total) VALUES (?, ?)");
        $stmt->bind_param("sd", $tanggal, $total);
        $stmt->execute();
        $id_transaksi = $stmt->insert_id;
        $stmt->close();

        // Simpan detail transaksi & kurangi stok
        for ($i = 0; $i < count($barang_id); $i++) {
            $id = intval($barang_id[$i]);
            $qty = intval($jumlah[$i]);

            $barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga_jual, stok FROM barang WHERE id_barang='$id'"));
            $harga = $barang['harga_jual'];
            $subtotal = $harga * $qty;

            // Cek stok cukup atau tidak
            if ($barang['stok'] >= $qty) {
                // Simpan ke detail_transaksi
                $stmt = $conn->prepare("INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah, harga, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiidd", $id_transaksi, $id, $qty, $harga, $subtotal);
                $stmt->execute();
                $stmt->close();

                // Kurangi stok
                $stmt2 = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
                $stmt2->bind_param("ii", $qty, $id);
                $stmt2->execute();
                $stmt2->close();
            }
        }

        echo "<script>alert('Transaksi berhasil disimpan!'); window.location='kasir.php';</script>";
        exit;
    } else {
        echo "<script>alert('Pilih barang terlebih dahulu!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kasir Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script>
        // Tambah baris input barang
        function tambahBaris() {
            const container = document.getElementById('barangContainer');
            const template = document.querySelector('.barang-item').cloneNode(true);
            template.querySelector('select').selectedIndex = 0;
            template.querySelector('input').value = '';
            container.appendChild(template);
        }

        // Hapus baris input barang
        function hapusBaris(el) {
            const container = document.getElementById('barangContainer');
            if (container.children.length > 1) {
                el.parentElement.parentElement.remove();
            } else {
                alert("Minimal satu barang dalam transaksi!");
            }
        }
    </script>
</head>
<body class="p-4">
<div class="container">
    <h3>🧾 Kasir Penjualan</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">🏠 Kembali</a>

    <form method="post">
        <div id="barangContainer">
            <div class="row g-3 align-items-end barang-item mb-2">
                <div class="col-md-6">
                    <label class="form-label">Barang</label>
                    <select name="id_barang[]" class="form-select" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php while ($b = $barang_query->fetch_assoc()): ?>
                            <option value="<?= $b['id_barang']; ?>">
                                <?= $b['kode_barang']; ?> - <?= $b['nama_barang']; ?> (Stok: <?= $b['stok']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah[]" class="form-control" min="1" required>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger" onclick="hapusBaris(this)">🗑 Hapus</button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mt-2" onclick="tambahBaris()">+ Tambah Barang</button>
        <hr>
        <button type="submit" name="simpan" class="btn btn-primary">💾 Simpan Transaksi</button>
    </form>

    <hr>
    <h5>Riwayat Transaksi Terbaru</h5>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr><th>No</th><th>Tanggal</th><th>Total</th></tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $res = $conn->query("SELECT * FROM transaksi ORDER BY id_transaksi DESC LIMIT 10");
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['tanggal']; ?></td>
                <td>Rp <?= number_format($row['total']); ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if ($no === 1): ?>
                <tr><td colspan="3" class="text-center">Belum ada transaksi</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
