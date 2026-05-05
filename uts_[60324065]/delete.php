<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?msg=ID tidak valid");
    exit;
}

$id = $_GET['id'];

$cek = $conn->prepare("SELECT id_kategori FROM kategori WHERE id_kategori=?");
$cek->bind_param("i",$id);
$cek->execute();
$result = $cek->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?msg=Data tidak ditemukan");
    exit;
}

$stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori=?");
$stmt->bind_param("i",$id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: index.php?msg=Data berhasil dihapus");
} else {
    header("Location: index.php?msg=Gagal menghapus data");
}
exit;
?>