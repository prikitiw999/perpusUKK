<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'user') {
    header("Location: ../index.php");
    exit();
}

// Proses Pinjam
if (isset($_POST['pinjam'])) {
    $buku_id = (int)$_POST['buku_id'];
    $user_id = $_SESSION['id'];
    $tgl = date('Y-m-d');

    // Cek stok
    $b = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok FROM buku WHERE id='$buku_id'"));
    if ($b['stok'] > 0) {
        // Cek apakah sudah meminjam buku yang sama
        $cek = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE user_id='$user_id' AND buku_id='$buku_id' AND status='dipinjam'");
        if (mysqli_num_rows($cek) > 0) {
            echo "<script>alert('Anda sudah meminjam buku ini!');</script>";
        } else {
            // Insert transaksi
            $query = "INSERT INTO peminjaman (user_id, buku_id, tgl_pinjam, status) VALUES ('$user_id', '$buku_id', '$tgl', 'dipinjam')";
            if (mysqli_query($koneksi, $query)) {
                // Kurangi stok
                mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id='$buku_id'");
                echo "<script>alert('Peminjaman berhasil!'); window.location='index.php'</script>";
            } else {
                echo "<script>alert('Peminjaman gagal: " . mysqli_error($koneksi) . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Stok buku habis!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pinjam Buku</title>
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
                    <a class="nav-link text-white active" href="pinjam.php">
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
                <h2><i class="bi bi-book-plus"></i> Pinjam Buku</h2>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Form Peminjaman</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Pilih Buku</label>
                            <select name="buku_id" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Buku --</option>
                                <?php
                                $buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul");
                                while($b = mysqli_fetch_assoc($buku)){
                                    echo "<option value='$b[id]'>" . htmlspecialchars($b['judul']) . " (Stok: $b[stok])</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label><br>
                            <button name="pinjam" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Pinjam Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-list"></i> Daftar Buku Tersedia</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul");
                            while($d = mysqli_fetch_assoc($data)):
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($d['judul']); ?></td>
                                <td><?= htmlspecialchars($d['pengarang']); ?></td>
                                <td><?= $d['stok']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if(mysqli_num_rows($data) == 0): ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada buku tersedia</td>
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