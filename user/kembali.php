<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'user') {
    header("Location: ../index.php");
    exit();
}

// Proses Kembali
if (isset($_GET['kembalikan'])) {
    $id_peminjaman = (int)$_GET['kembalikan'];
    $id_user = $_SESSION['id'];
    
    // Validasi: pastikan peminjaman milik user yang login
    $cek = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id='$id_peminjaman' AND user_id='$id_user' AND status='dipinjam'");
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        $id_buku = $data['buku_id'];
        
        // Update status peminjaman
        mysqli_query($koneksi, "UPDATE peminjaman SET status='dikembalikan', tgl_kembali=NOW() WHERE id='$id_peminjaman'");
        
        // Tambah stok buku
        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id='$id_buku'");
        
        echo "<script>alert('Buku berhasil dikembalikan!'); window.location='kembali.php'</script>";
    } else {
        echo "<script>alert('Data tidak valid!'); window.location='kembali.php'</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kembalikan Buku</title>
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
                    <a class="nav-link text-white" href="index.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="pinjam.php">
                        <i class="bi bi-book-plus"></i> Pinjam Buku
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white active" href="kembali.php">
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
                <h2><i class="bi bi-bookmark-check"></i> Kembalikan Buku</h2>
            </div>

            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Buku yang Sedang Anda Pinjam</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $id_user = $_SESSION['id'];
                            $sql = mysqli_query($koneksi, 
                                "SELECT p.*, b.judul 
                                 FROM peminjaman p 
                                 JOIN buku b ON p.buku_id=b.id 
                                 WHERE p.user_id='$id_user' AND p.status='dipinjam'
                                 ORDER BY p.tgl_pinjam DESC");
                            while($d = mysqli_fetch_assoc($sql)):
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($d['judul']); ?></td>
                                <td><?= date('d-m-Y', strtotime($d['tgl_pinjam'])); ?></td>
                                <td>
                                    <a href="?kembalikan=<?= $d['id']; ?>" 
                                       class="btn btn-success btn-sm" 
                                       onclick="return confirm('Kembalikan buku <?= htmlspecialchars($d['judul']); ?>?')">
                                        <i class="bi bi-arrow-return-left"></i> Kembalikan
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if(mysqli_num_rows($sql) == 0): ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada buku yang sedang dipinjam</td>
                            </tr>
                            <?php endif; ?>
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