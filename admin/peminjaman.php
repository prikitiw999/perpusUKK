<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transaksi Peminjaman</title>
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
                    <a class="nav-link text-white" href="index.php">
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
                    <a class="nav-link text-white active" href="peminjaman.php">
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

        <!-- Konten -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-arrow-left-right"></i> Semua Transaksi</h2>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $sql = mysqli_query($koneksi, 
                                "SELECT p.*, u.nama, b.judul 
                                 FROM peminjaman p 
                                 JOIN users u ON p.user_id=u.id 
                                 JOIN buku b ON p.buku_id=b.id 
                                 ORDER BY p.id DESC");
                            while($d = mysqli_fetch_assoc($sql)):
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($d['nama']); ?></td>
                                <td><?= htmlspecialchars($d['judul']); ?></td>
                                <td><?= date('d-m-Y', strtotime($d['tgl_pinjam'])); ?></td>
                                <td>
                                    <?= $d['tgl_kembali'] ? date('d-m-Y', strtotime($d['tgl_kembali'])) : '-'; ?>
                                </td>
                                <td>
                                    <?php if($d['status'] == 'dipinjam'): ?>
                                        <span class="badge bg-warning">Dipinjam</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Dikembalikan</span>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>