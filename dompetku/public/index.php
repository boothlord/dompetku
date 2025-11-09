<?php
require_once __DIR__ . '/../db.php';
require_login();

$uid = $_SESSION['user_id'];

// Hitung total pemasukan & pengeluaran bulan ini
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

$stmt = $pdo->prepare("
  SELECT type, SUM(amount) AS total
  FROM transactions
  WHERE user_id = ? AND date BETWEEN ? AND ?
  GROUP BY type
");
$stmt->execute([$uid, $monthStart, $monthEnd]);

$summary = ['income' => 0, 'expense' => 0];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $summary[$row['type']] = $row['total'];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>DompetKu - Dashboard</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <h2>DompetKu</h2>
<div>
    Halo, <?= htmlspecialchars($_SESSION['user_name']) ?> |
    <a href="transactions.php">Transaksi</a> |
    <a href="categories.php">Kategori</a> | <a href="logout.php">Keluar</a>
  </div>
</header>

<div class="card">
  <h3>Ringkasan Bulan Ini (<?= date('F Y') ?>)</h3>
  <div class="form-row">
    <div class="card" style="flex:1">
      <strong>Pemasukan</strong>
      <div class="small income">Rp <?= number_format($summary['income'], 0, ',', '.') ?></div>
    </div>
    <div class="card" style="flex:1">
      <strong>Pengeluaran</strong>
      <div class="small expense">Rp <?= number_format($summary['expense'], 0, ',', '.') ?></div>
    </div>
    <div class="card" style="flex:1">
      <strong>Saldo Saat Ini</strong>
      <div class="small">
        Rp <?= number_format($summary['income'] - $summary['expense'], 0, ',', '.') ?>
      </div>
    </div>
  </div>
</div>

<div class="footer">
  Tips: Gunakan halaman <b>Transaksi</b> untuk menambah, mengedit, dan menghapus catatan keuangan.
</div>
</body>
</html>