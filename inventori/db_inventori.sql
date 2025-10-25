CREATE DATABASE db_inventori;
USE db_inventori;

-- Tabel barang
CREATE TABLE barang (
  id_barang INT AUTO_INCREMENT PRIMARY KEY,
  kode_barang VARCHAR(20) NOT NULL,
  nama_barang VARCHAR(100) NOT NULL,
  satuan VARCHAR(20),
  harga_beli DECIMAL(10,2),
  harga_jual DECIMAL(10,2),
  stok INT DEFAULT 0
);

-- Tabel stok masuk
CREATE TABLE stok_masuk (
  id_masuk INT AUTO_INCREMENT PRIMARY KEY,
  id_barang INT,
  tanggal DATE,
  jumlah INT,
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

-- Tabel stok keluar
CREATE TABLE stok_keluar (
  id_keluar INT AUTO_INCREMENT PRIMARY KEY,
  id_barang INT,
  tanggal DATE,
  jumlah INT,
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

-- Tabel transaksi kasir
CREATE TABLE transaksi (
  id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE,
  total DECIMAL(10,2)
);

-- Detail transaksi
CREATE TABLE detail_transaksi (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_transaksi INT,
  id_barang INT,
  jumlah INT,
  harga DECIMAL(10,2),
  subtotal DECIMAL(10,2),
  FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);