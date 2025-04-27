<?php
session_start();
include('../config/db.php');

if ($_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Check for success/error messages
$statusMsg = '';
$statusClass = '';

if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'added':
            $statusMsg = 'Varian PlayStation baru berhasil ditambahkan!';
            $statusClass = 'alert-success';
            break;
        case 'updated':
            $statusMsg = 'Varian PlayStation berhasil diperbarui!';
            $statusClass = 'alert-success';
            break;
        case 'deleted':
            $statusMsg = 'Varian PlayStation berhasil dihapus!';
            $statusClass = 'alert-success';
            break;
        case 'error':
            $statusMsg = 'Terjadi kesalahan! Silakan coba lagi.';
            $statusClass = 'alert-danger';
            break;
    }
}

// Get all variants with count of active orders
$varian = mysqli_query($conn, "SELECT v.*, 
                             (SELECT COUNT(*) FROM pesanan WHERE varian_id = v.id) as order_count 
                             FROM varian_ps v
                             ORDER BY v.tersedia DESC, v.nama_varian ASC");

// Count stats
$total_varian = mysqli_num_rows($varian);
$varian_tersedia = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM varian_ps WHERE tersedia = 1"))['total'];

mysqli_data_seek($varian, 0); // Reset the result pointer
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Varian PlayStation - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .header h2 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .header h2 i {
            margin-right: 10px;
            color: #6c5ce7;
        }
        
        .stats-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            flex: 1;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .stat-icon {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 40px;
            opacity: 0.1;
            color: #6c5ce7;
        }
        
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-card .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .alert-success {
            background-color: #d1f5ea;
            color: #0c6b58;
            border-left: 4px solid #0c6b58;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #b91c1c;
        }
        
        .navigation {
            display: flex;
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin-right: 15px;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background-color: #f0efff;
            color: #6c5ce7;
        }
        
        .nav-link.active {
            background-color: #6c5ce7;
            color: white;
        }
        
        .nav-link i {
            margin-right: 8px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 15px;
        }
        
        .btn-primary {
            background-color: #6c5ce7;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #5d4ed6;
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background-color: #ff7675;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #e05d5d;
            box-shadow: 0 5px 15px rgba(255, 118, 117, 0.2);
        }
        
        .btn-warning {
            background-color: #fdcb6e;
            color: #333;
        }
        
        .btn-warning:hover {
            background-color: #f0b94d;
            box-shadow: 0 5px 15px rgba(253, 203, 110, 0.2);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        .card-tools .search-box {
            position: relative;
            margin-right: 15px;
        }
        
        .card-tools .search-box input {
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            width: 250px;
            transition: all 0.3s;
        }
        
        .card-tools .search-box input:focus {
            border-color: #6c5ce7;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
            outline: none;
        }
        
        .card-tools .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th {
            background-color: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid #eee;
        }
        
        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        table tr:hover {
            background-color: #f9f9ff;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .status-available {
            background-color: #d1f5ea;
            color: #0c6b58;
        }
        
        .status-unavailable {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .badge-primary {
            background-color: #e0dbff;
            color: #6c5ce7;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 12px;
            font-size: 13px;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }
        
        .empty-state i {
            font-size: 70px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 20px;
            color: #888;
            margin-bottom: 15px;
        }
        
        .empty-state p {
            color: #aaa;
            margin-bottom: 25px;
        }
        
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 14px;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .stats-container {
                flex-wrap: wrap;
            }
            
            .stat-card {
                flex: 1 1 calc(50% - 20px);
            }
            
            .card-tools .search-box input {
                width: 180px;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .stat-card {
                flex: 1 1 100%;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .card-tools {
                width: 100%;
            }
            
            .card-tools .search-box {
                width: 100%;
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .card-tools .search-box input {
                width: 100%;
            }
            
            .navigation {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .nav-link {
                margin-right: 0;
                flex: 1 1 calc(50% - 10px);
                justify-content: center;
            }
            
            table {
                font-size: 14px;
            }
            
            .actions {
                flex-direction: column;
            }
        }
        
        @media (max-width: 576px) {
            table th:nth-child(3), 
            table td:nth-child(3) {
                display: none;
            }
            
            .btn-sm {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-tags"></i> Manajemen Varian PlayStation</h2>
            <a href="tambah_varian.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Varian Baru
            </a>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-gamepad stat-icon"></i>
                <div class="stat-value"><?= $total_varian ?></div>
                <div class="stat-label">Total Varian</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-value"><?= $varian_tersedia ?></div>
                <div class="stat-label">Varian Tersedia</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-times-circle stat-icon"></i>
                <div class="stat-value"><?= $total_varian - $varian_tersedia ?></div>
                <div class="stat-label">Varian Tidak Tersedia</div>
            </div>
        </div>
        
        <!-- Notification Section -->
        <?php if($statusMsg != ''): ?>
        <div class="alert <?= $statusClass ?>">
            <i class="fas <?= $statusClass == 'alert-success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= $statusMsg ?>
        </div>
        <?php endif; ?>
        
        <!-- Navigation -->
        <div class="navigation">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="varian.php" class="nav-link active">
                <i class="fas fa-tags"></i> Varian PlayStation
            </a>
            <a href="../auth/logout.php" class="nav-link" style="margin-left: auto;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        
        <!-- Varian Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Varian PlayStation</h3>
                <div class="card-tools">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari varian..." onkeyup="searchTable()">
                    </div>
                </div>
            </div>
            
            <?php if(mysqli_num_rows($varian) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nama Varian</th>
                        <th>Status</th>
                        <th>Jumlah Pesanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($v = mysqli_fetch_assoc($varian)): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($v['nama_varian']); ?></strong>
                        </td>
                        <td>
                            <span class="status-badge <?= $v['tersedia'] ? 'status-available' : 'status-unavailable' ?>">
                                <i class="fas <?= $v['tersedia'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                <?= $v['tersedia'] ? 'Tersedia' : 'Tidak Tersedia'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-primary">
                                <i class="fas fa-shopping-cart"></i>
                                <?= $v['order_count']; ?> Pesanan
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="edit_varian.php?id=<?= $v['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="hapus_varian.php?id=<?= $v['id']; ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin ingin menghapus varian <?= addslashes(htmlspecialchars($v['nama_varian'])); ?>?');">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>Belum ada varian PlayStation</h3>
                <p>Mulai tambahkan varian PlayStation untuk ditampilkan di sini</p>
                <a href="tambah_varian.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Varian Baru
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            &copy; <?= date('Y') ?> PlayStation Store Admin Panel | Semua hak cipta dilindungi
        </div>
    </div>
    
    <script>
    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");
        
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>
</body>
</html>