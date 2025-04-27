<?php
session_start();
include('../config/db.php');

if ($_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
}

// First, check if status column exists, if not, add it
$checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM pesanan LIKE 'status'");
if(mysqli_num_rows($checkColumn) == 0) {
    // Add status column if it doesn't exist
    $addColumn = mysqli_query($conn, "ALTER TABLE pesanan ADD COLUMN status VARCHAR(20) DEFAULT 'Pending'");
    if(!$addColumn) {
        $_SESSION['message'] = "Gagal menambahkan kolom status: " . mysqli_error($conn);
        $_SESSION['msg_type'] = "danger";
    }
}

// Handle status update if form is submitted
if (isset($_POST['update_status'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $new_status = $_POST['status'];
    
    $update_query = mysqli_query($conn, "UPDATE pesanan SET status = '$new_status' WHERE id = $pesanan_id");
    
    if ($update_query) {
        $_SESSION['message'] = "Status pesanan berhasil diperbarui!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal memperbarui status: " . mysqli_error($conn);
        $_SESSION['msg_type'] = "danger";
    }
    
    header('Location: dashboard.php');
    exit();
}

// Filter for completed orders if requested
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where_clause = '';

if ($filter == 'completed') {
    $where_clause = " WHERE p.status = 'Selesai'";
} elseif ($filter == 'pending') {
    $where_clause = " WHERE p.status = 'Pending'";
} elseif ($filter == 'process') {
    $where_clause = " WHERE p.status = 'Diproses'";
} elseif ($filter == 'rejected') {
    $where_clause = " WHERE p.status = 'Ditolak'";
}

$pesanan = mysqli_query($conn, "SELECT p.*, u.username, v.nama_varian 
                                FROM pesanan p 
                                JOIN users u ON p.user_id = u.id 
                                JOIN varian_ps v ON p.varian_id = v.id
                                $where_clause
                                ORDER BY p.waktu_pesanan DESC");

// Hitung statistik
$total_pesanan_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan");
$total_pesanan_data = mysqli_fetch_assoc($total_pesanan_query);
$total_pesanan = $total_pesanan_data['total'];

$completed_pesanan_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Selesai'");
$completed_pesanan_data = mysqli_fetch_assoc($completed_pesanan_query);
$completed_pesanan = $completed_pesanan_data['total'];

$jenis_query = mysqli_query($conn, "SELECT COUNT(DISTINCT jenis_pesanan) as total FROM pesanan");
$jenis_data = mysqli_fetch_assoc($jenis_query);
$total_jenis = $jenis_data['total'];

$user_query = mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as total FROM pesanan");
$user_data = mysqli_fetch_assoc($user_query);
$total_customers = $user_data['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Playstation Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .header h2 {
            color: #3a3a3a;
            font-size: 24px;
            font-weight: 600;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info .admin-badge {
            background-color: #6c5ce7;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 15px;
        }
        
        /* Navigation */
        .navigation {
            display: flex;
            background-color: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #555;
            margin-right: 25px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background-color: #6c5ce7;
            color: white;
        }
        
        .nav-link i {
            margin-right: 8px;
        }
        
        .nav-link.logout {
            margin-left: auto;
            background-color: #f7f7f7;
        }
        
        .nav-link.logout:hover {
            background-color: #ff7675;
            color: white;
        }
        
        /* Stats Cards */
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        
        .stat-card {
            flex: 1;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-card .stat-label {
            color: #888;
            font-size: 14px;
        }
        
        .stat-card .stat-icon {
            float: right;
            font-size: 36px;
            color: #6c5ce7;
            opacity: 0.3;
        }
        
        /* Table Section */
        .table-section {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .table-header h3 {
            color: #3a3a3a;
            font-size: 18px;
            font-weight: 600;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding: 8px 15px 8px 35px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            border-color: #6c5ce7;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }
        
        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background-color: #f8f9fb;
            padding: 12px 15px;
            text-align: left;
            color: #555;
            font-weight: 500;
            border-bottom: 1px solid #eee;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .data-table tr:hover {
            background-color: #f9f9ff;
        }
        
        .data-table .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .data-table .status.sewa {
            background-color: #e1f5fe;
            color: #0288d1;
        }
        
        .data-table .status.beli {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .data-table .status.service {
            background-color: #fff8e1;
            color: #ffa000;
        }
        
        /* Status badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }
        
        .status-badge i {
            margin-right: 4px;
        }
        
        .status-badge.pending {
            background-color: #fff8e1;
            color: #ffa000;
        }
        
        .status-badge.diproses {
            background-color: #e1f5fe;
            color: #0288d1;
        }
        
        .status-badge.selesai {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .status-badge.ditolak {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        /* Status dropdown */
        .status-select {
            padding: 6px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #fff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .status-select:focus {
            border-color: #6c5ce7;
            box-shadow: 0 0 0 2px rgba(108, 92, 231, 0.1);
        }
        
        /* Action buttons */
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-left: 5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #6c5ce7;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #5b4cc4;
        }
        
        /* Filter tabs */
        .filter-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .filter-tab {
            padding: 8px 15px;
            margin-right: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #555;
        }
        
        .filter-tab:hover, .filter-tab.active {
            background-color: #6c5ce7;
            color: white;
        }
        
        /* Alert messages */
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #388e3c;
            border-left: 4px solid #388e3c;
        }
        
        .alert-danger {
            background-color: #ffebee;
            color: #d32f2f;
            border-left: 4px solid #d32f2f;
        }
        
        /* Responsive styles */
        @media (max-width: 900px) {
            .stats-container {
                flex-wrap: wrap;
            }
            
            .stat-card {
                flex: 1 1 calc(50% - 20px);
            }
            
            .filter-tabs {
                flex-wrap: wrap;
            }
            
            .filter-tab {
                margin-bottom: 10px;
            }
        }
        
        @media (max-width: 700px) {
            .navigation {
                flex-wrap: wrap;
            }
            
            .nav-link {
                margin-bottom: 10px;
            }
            
            .nav-link.logout {
                margin-left: 0;
                width: 100%;
                margin-top: 10px;
                text-align: center;
                justify-content: center;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
            
            .data-table {
                font-size: 14px;
            }
            
            .data-table th, .data-table td {
                padding: 10px 8px;
            }
            
            .action-cell {
                display: flex;
                flex-direction: column;
            }
            
            .action-cell select, .action-cell button {
                margin-bottom: 5px;
                margin-left: 0;
            }
        }
        
        @media (max-width: 500px) {
            .stat-card {
                flex: 1 1 100%;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .data-table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h2><i class="fas fa-gamepad"></i> Dashboard Admin</h2>
            <div class="user-info">
                <span class="admin-badge"><i class="fas fa-user-shield"></i> Admin</span>
                <span><?= $_SESSION['username'] ?? 'Admin' ?></span>
            </div>
        </div>
        
        <!-- Navigation Section -->
        <div class="navigation">
            <a href="dashboard.php" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="varian.php" class="nav-link">
                <i class="fas fa-tags"></i> Manajemen Varian PS
            </a>
            <a href="users.php" class="nav-link">
                <i class="fas fa-users"></i> Manajemen User
            </a>
            <a href="../auth/logout.php" class="nav-link logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        
        <!-- Alert Message -->
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
            <i class="fas fa-<?= $_SESSION['msg_type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= $_SESSION['message'] ?>
        </div>
        <?php 
        unset($_SESSION['message']);
        unset($_SESSION['msg_type']);
        endif; 
        ?>
        
        <!-- Stats Section -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-shopping-cart stat-icon"></i>
                <div class="stat-value"><?= $total_pesanan ?></div>
                <div class="stat-label">Total Pesanan</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-value"><?= $completed_pesanan ?></div>
                <div class="stat-label">Pesanan Selesai</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-value"><?= $total_customers ?></div>
                <div class="stat-label">Total Customers</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-list-alt stat-icon"></i>
                <div class="stat-value"><?= $total_jenis ?></div>
                <div class="stat-label">Jenis Layanan</div>
            </div>
        </div>
        
        <!-- Table Section -->
        <div class="table-section">
            <div class="filter-tabs">
                <a href="dashboard.php?filter=all" class="filter-tab <?= ($filter == 'all') ? 'active' : '' ?>">
                    <i class="fas fa-th-list"></i> Semua Pesanan
                </a>
                <a href="dashboard.php?filter=pending" class="filter-tab <?= ($filter == 'pending') ? 'active' : '' ?>">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="dashboard.php?filter=process" class="filter-tab <?= ($filter == 'process') ? 'active' : '' ?>">
                    <i class="fas fa-spinner"></i> Diproses
                </a>
                <a href="dashboard.php?filter=completed" class="filter-tab <?= ($filter == 'completed') ? 'active' : '' ?>">
                    <i class="fas fa-check-circle"></i> Selesai
                </a>
                <a href="dashboard.php?filter=rejected" class="filter-tab <?= ($filter == 'rejected') ? 'active' : '' ?>">
                    <i class="fas fa-times-circle"></i> Ditolak
                </a>
            </div>
            
            <div class="table-header">
                <h3>Daftar Pesanan</h3>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari pesanan..." onkeyup="searchTable()">
                </div>
            </div>
            
            <table class="data-table" id="pesananTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Varian PS</th>
                        <th>Jenis Pesanan</th>
                        <th>Catatan</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if (mysqli_num_rows($pesanan) > 0) {
                    while($row = mysqli_fetch_assoc($pesanan)) { 
                ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['nama_varian']); ?></td>
                        <td>
                            <?php
                            $jenis = strtolower($row['jenis_pesanan']);
                            $statusClass = '';
                            $icon = '';
                            
                            if ($jenis == 'booking') {
                                $statusClass = 'status sewa';
                                $icon = '<i class="fas fa-calendar-check"></i> ';
                            } else {
                                $statusClass = 'status service';
                                $icon = '<i class="fas fa-tools"></i> ';
                            }
                            ?>
                            <span class="<?= $statusClass ?>"><?= $icon . htmlspecialchars($row['jenis_pesanan']); ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['catatan']); ?></td>
                        <td><?= date('d M Y H:i', strtotime($row['waktu_pesanan'])); ?></td>
                        <td>
                            <?php
                            // Get actual status from database (or default to "Pending" if null)
                            $status = isset($row['status']) ? $row['status'] : 'Pending';
                            
                            // Set appropriate badge classes and icons based on status
                            switch($status) {
                                case 'Diproses':
                                    $badgeClass = 'status-badge diproses';
                                    $icon = '<i class="fas fa-spinner fa-spin"></i>';
                                    break;
                                case 'Selesai':
                                    $badgeClass = 'status-badge selesai';
                                    $icon = '<i class="fas fa-check-circle"></i>';
                                    break;
                                case 'Ditolak':
                                    $badgeClass = 'status-badge ditolak';
                                    $icon = '<i class="fas fa-times-circle"></i>';
                                    break;
                                default: // Pending
                                    $badgeClass = 'status-badge pending';
                                    $icon = '<i class="fas fa-clock"></i>';
                            }
                            ?>
                            <span class="<?= $badgeClass ?>"><?= $icon ?> <?= $status ?></span>
                        </td>
                        <td class="action-cell">
                            <form method="POST" action="dashboard.php" style="display: flex; align-items: center;">
                                <input type="hidden" name="pesanan_id" value="<?= $row['id'] ?>">
                                <select name="status" class="status-select">
                                    <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="Diproses" <?= ($status == 'Diproses') ? 'selected' : '' ?>>Diproses</option>
                                    <option value="Selesai" <?= ($status == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                    <option value="Ditolak" <?= ($status == 'Ditolak') ? 'selected' : '' ?>>Ditolak</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php 
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada pesanan ditemukan</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("pesananTable");
        tr = table.getElementsByTagName("tr");
        
        for (i = 0; i < tr.length; i++) {
            // Skip header row
            if (i === 0) continue;
            
            let matched = false;
            // Loop through all table columns (excluding the action column)
            for (let j = 0; j < 6; j++) { // Reduced from 7 to 6 columns as we removed Nama Customer
                td = tr[i].getElementsByTagName("td")[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        matched = true;
                        break;
                    }
                }
            }
            
            if (matched) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
    
    // Auto-hide alert messages after 5 seconds
    document.addEventListener("DOMContentLoaded", function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    });
    </script>
</body>
</html>