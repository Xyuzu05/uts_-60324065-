<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once 'config/database.php';

$errors = [];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?msg=ID tidak valid");
    exit;
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM kategori WHERE id_kategori=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?msg=Data tidak ditemukan");
    exit;
}

$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = htmlspecialchars(trim($_POST['kode']));
    $nama = htmlspecialchars(trim($_POST['nama']));
    $deskripsi = htmlspecialchars(trim($_POST['deskripsi']));
    $status = $_POST['status'] ?? 'Aktif';

    if (empty($kode)) {
        $errors[] = "Kode kategori wajib diisi";
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors[] = "Kode kategori harus 4-10 karakter";
    } elseif (strpos($kode, "KAT-") !== 0) {
        $errors[] = "Kode harus diawali 'KAT-'";
    }

    if (empty($nama)) {
        $errors[] = "Nama kategori wajib diisi";
    } elseif (strlen($nama) < 3) {
        $errors[] = "Nama yang di masukkin minimal 3 karakter";
    } elseif (strlen($nama) > 50) {
        $errors[] = "Nama yang di masukkin maksimal 50 karakter";
    }

    if (!empty($deskripsi) && strlen($deskripsi) > 200) {
        $errors[] = "Deskripsi maksimal 200 karakter";
    }

    if (!in_array($status, ['Aktif','Nonaktif'])) {
        $errors[] = "Status tidak valid";
    }

    if (empty($errors)) {
        $cek = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori=? AND id_kategori!=?");
        $cek->bind_param("si",$kode,$id);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $errors[] = "Kode sudah ada";
        }
    }

    if (empty($errors)) {
        $update = $conn->prepare("UPDATE kategori SET kode_kategori=?, nama_kategori=?, deskripsi=?, status=? WHERE id_kategori=?");
        $update->bind_param("ssssi",$kode,$nama,$deskripsi,$status,$id);
        if ($update->execute()) {
            header("Location: index.php?msg=Data berhasil diupdate");
            exit;
        } else {
            $errors[] = "Gagal update";
        }
    }

    $data['kode_kategori']=$kode;
    $data['nama_kategori']=$nama;
    $data['deskripsi']=$deskripsi;
    $data['status']=$status;
}
?>

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
<div class="card-header">
<h4>Edit Kategori</h4>
</div>
<div class="card-body">

<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
<ul class="mb-0">
<?php foreach($errors as $e): ?>
<li><?= $e ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label>Kode Kategori</label>
<input type="text" name="kode" class="form-control" value="<?= $data['kode_kategori'] ?>" required>
</div>

<div class="mb-3">
<label>Nama Kategori</label>
<input type="text" name="nama" class="form-control" value="<?= $data['nama_kategori'] ?>" required>
</div>

<div class="mb-3">
<label>Deskripsi</label>
<textarea name="deskripsi" class="form-control"><?= $data['deskripsi'] ?></textarea>
</div>

<div class="mb-3">
<label>Status</label><br>
<input type="radio" name="status" value="Aktif" <?= $data['status']=='Aktif'?'checked':'' ?>> Aktif
<input type="radio" name="status" value="Nonaktif" <?= $data['status']=='Nonaktif'?'checked':'' ?>> Nonaktif
</div>

<div class="d-flex gap-2">
<button type="submit" class="btn btn-warning">Update</button>
<a href="index.php" class="btn btn-secondary">Kembali</a>
</div>

</form>
</div>
</div>
</div>
</div>
</div>
</body>
</html>