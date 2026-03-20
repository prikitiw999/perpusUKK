<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'user') {
    header("Location: ../index.php");
    exit();
}

// Hitung jumlah buku yang dipinjam
$id_user = $_SESSION['id'];
$pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id='$id_user' AND status='dipinjam'");
$jml_pinjam = mysqli_fetch_assoc($pinjam)['total'];

// Hitung total buku yang pernah dipinjam
$history = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id='$id_user'");
$jml_history = mysqli_fetch_assoc($history)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
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
                <h4><i class="bi bi-person-circle"></i> User Panel</h4>
                <hr class="border-light">
            </div>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-white active" href="index.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="pinjam.php">
                        <i class="bi bi-book-plus"></i> Pinjam Buku
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="kembali.php">
                        <i class="bi bi-bookmark-check"></i> Kembalikan Buku
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-white" href="../logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Konten -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-grid"></i> Dashboard</h2>
                <div class="text-muted">
                    <i class="bi bi-person-circle"></i> <?= $_SESSION['nama']; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card stat-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Buku Dipinjam</h6>
                                    <h2 class="mb-0"><?= $jml_pinjam; ?></h2>
                                </div>
                                <i class="bi bi-book fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Riwayat Peminjaman</h6>
                                    <h2 class="mb-0"><?= $jml_history; ?></h2>
                                </div>
                                <i class="bi bi-clock-history fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Peminjaman Anda</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Buku</th>
                                        <th>Tgl Pinjam</th>
                                        <th>Tgl Kembali</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $history = mysqli_query($koneksi, 
                                        "SELECT p.*, b.judul 
                                         FROM peminjaman p 
                                         JOIN buku b ON p.buku_id=b.id 
                                         WHERE p.user_id='$id_user' 
                                         ORDER BY p.id DESC LIMIT 10");
                                    while($h = mysqli_fetch_assoc($history)):
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($h['judul']); ?></td>
                                        <td><?= date('d-m-Y', strtotime($h['tgl_pinjam'])); ?></td>
                                        <td>
                                            <?= $h['tgl_kembali'] ? date('d-m-Y', strtotime($h['tgl_kembali'])) : '-'; ?>
                                        </td>
                                        <td>
                                            <?php if($h['status'] == 'dipinjam'): ?>
                                                <span class="badge bg-warning">Dipinjam</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Dikembalikan</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if(mysqli_num_rows($history) == 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada riwayat peminjaman</td>
                                    </tr>
                                    <?php endif; ?>
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