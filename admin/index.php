<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Ambil statistik
$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku"))['total'];
$total_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE level='user'"))['total'];
$buku_dipinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'"))['total'];
$buku_dikembalikan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dikembalikan'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .stat-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar text-white min-vh-100 p-3">
            <div class="text-center mb-4 mt-2">
                <h4><i class="bi bi-shield-lock"></i> Admin Panel</h4>
                <hr class="border-light">
            </div>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-white active" href="index.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="buku.php">
                        <i class="bi bi-book"></i> Kelola Buku
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="anggota.php">
                        <i class="bi bi-people"></i> Kelola Anggota
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="peminjaman.php">
                        <i class="bi bi-arrow-left-right"></i> Transaksi
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-white" href="../logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Konten Utama -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-grid"></i> Dashboard</h2>
                <div class="text-muted">
                    <i class="bi bi-person-circle"></i> <?= $_SESSION['nama']; ?> (Admin)
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Buku</h6>
                                    <h2 class="mb-0"><?= $total_buku; ?></h2>
                                </div>
                                <i class="bi bi-book fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Anggota</h6>
                                    <h2 class="mb-0"><?= $total_anggota; ?></h2>
                                </div>
                                <i class="bi bi-people fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Dipinjam</h6>
                                    <h2 class="mb-0"><?= $buku_dipinjam; ?></h2>
                                </div>
                                <i class="bi bi-arrow-right-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Dikembalikan</h6>
                                    <h2 class="mb-0"><?= $buku_dikembalikan; ?></h2>
                                </div>
                                <i class="bi bi-check-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Transaksi Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Peminjam</th>
                                        <th>Buku</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $transaksi = mysqli_query($koneksi, 
                                        "SELECT p.*, u.nama, b.judul 
                                         FROM peminjaman p 
                                         JOIN users u ON p.user_id=u.id 
                                         JOIN buku b ON p.buku_id=b.id 
                                         ORDER BY p.id DESC LIMIT 5");
                                    while($t = mysqli_fetch_assoc($transaksi)):
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($t['nama']); ?></td>
                                        <td><?= htmlspecialchars($t['judul']); ?></td>
                                        <td>
                                            <?php if($t['status'] == 'dipinjam'): ?>
                                                <span class="badge bg-warning">Dipinjam</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Kembali</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>