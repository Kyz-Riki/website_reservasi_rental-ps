
CREATE DATABASE IF NOT EXISTS reservasi_ps;
USE reservasi_ps;

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role ENUM('admin', 'customer') NOT NULL
);

-- Insert akun dummy
INSERT INTO users (username, password, role) VALUES
('admin', MD5('admin123'), 'admin'),
('customer', MD5('cust123'), 'customer');

-- Tabel varian_ps
CREATE TABLE varian_ps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_varian VARCHAR(100) NOT NULL,
    tersedia BOOLEAN DEFAULT 1
);

-- Tabel pesanan
CREATE TABLE pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    varian_id INT,
    jenis_pesanan ENUM('booking', 'bawa_pulang'),
    catatan TEXT,
    waktu_pesanan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (varian_id) REFERENCES varian_ps(id)
);
