<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Cek apakah anggota masih meminjam buku
    $cek = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE user_id='$id' AND status='dipinjam'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Anggota masih meminjam buku, tidak dapat dihapus!'); window.location='anggota.php';</script>";
    } else {
        mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");
        echo "<script>alert('Anggota berhasil dihapus!'); window.location='anggota.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kelola Anggota</title>
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
                    <a class="nav-link text-white active" href="anggota.php">
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
        
        <!-- Konten -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-people"></i> Data Anggota</h2>
                <a href="../register.php" class="btn btn-success">
                    <i class="bi bi-person-plus"></i> Tambah Anggota
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $sql = mysqli_query($koneksi, "SELECT * FROM users WHERE level='user' ORDER BY id DESC");
                            while($d = mysqli_fetch_assoc($sql)):
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($d['nama']); ?></td>
                                <td><?= htmlspecialchars($d['username']); ?></td>
                                <td><?= date('d-m-Y', strtotime($d['created_at'])); ?></td>
                                <td>
                                    <a href="?hapus=<?= $d['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Hapus anggota <?= htmlspecialchars($d['nama']); ?>?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
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