<?php
session_start();
include('../config/db.php');

if ($_SESSION['role'] != 'customer') {
    header('Location: ../auth/login.php');
    exit;
}

$varian = mysqli_query($conn, "SELECT * FROM varian_ps WHERE tersedia = 1");

if (isset($_POST['pesan'])) {
    $user_id = $_SESSION['user_id'];
    $varian_id = $_POST['varian_id'];
    $jenis_pesanan = $_POST['jenis_pesanan'];
    $catatan = htmlspecialchars($_POST['catatan']);

    mysqli_query($conn, "INSERT INTO pesanan (user_id, varian_id, jenis_pesanan, catatan) 
                         VALUES ('$user_id', '$varian_id', '$jenis_pesanan', '$catatan')");

    $_SESSION['success'] = "Pesanan berhasil dikirim!";
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking/Sewa PlayStation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .btn-primary {
            background-color: #6c5ce7;
            border-color: #6c5ce7;
            font-weight: 500;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #5649c0;
            border-color: #5649c0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
        }
        .card-header {
            background: linear-gradient(to right, #6c5ce7, #5649c0);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .logout-link {
            color: #6c5ce7;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
            margin-top: 15px;
        }
        .logout-link:hover {
            color: #5649c0;
            text-decoration: underline;
        }
        .form-label {
            font-weight: 500;
            color: #555;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.25);
            border-color: #6c5ce7;
        }
        .alert {
            border-radius: 8px;
            font-weight: 500;
        }
        .ps-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .page-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); endif; ?>
                
                <div class="card">
                    <div class="card-header py-3">
                        <h5 class="mb-0"><i class="fas fa-gamepad ps-icon"></i> Booking/Sewa PlayStation</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="varian_id" class="form-label">Varian PlayStation</label>
                                <select class="form-select" id="varian_id" name="varian_id" required>
                                    <?php while($v = mysqli_fetch_assoc($varian)) { ?>
                                        <option value="<?= $v['id']; ?>"><?= $v['nama_varian']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_pesanan" class="form-label">Jenis Pesanan</label>
                                <select class="form-select" id="jenis_pesanan" name="jenis_pesanan" required>
                                    <option value="booking">Booking Waktu Main</option>
                                    <option value="bawa_pulang">Sewa Bawa Pulang</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="catatan" class="form-label">Informasi Kontak & Catatan</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="4" placeholder="Nama, No HP, Catatan Tambahan" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="pesan" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-white text-center py-3" style="border-radius: 0 0 15px 15px;">
                        <a href="../auth/logout.php" class="logout-link">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Otomatis hilangkan alert setelah 5 detik
        window.setTimeout(function() {
            document.querySelector(".alert").classList.add('fade');
            document.querySelector(".alert").classList.add('hide');
        }, 5000);
    </script>
</body>
</html>