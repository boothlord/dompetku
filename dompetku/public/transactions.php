<?php
require_once __DIR__ . '/../db.php';
require_login();
$uid = $_SESSION['user_id'];

// === Proses hapus kalau ada parameter id di URL ===
if (isset($_GET['delete_id'])) {
    $id = (int) $_GET['delete_id'];
    $del = $pdo->prepare('DELETE FROM transactions WHERE id=? AND user_id=?');
    $del->execute([$id, $uid]);
    header('Location: transactions.php');
    exit;
}

// === Ambil data kategori ===
$catStmt = $pdo->prepare('SELECT * FROM categories WHERE user_id = ?');
$catStmt->execute([$uid]);
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// === Ambil data transaksi ===
$transStmt = $pdo->prepare('
    SELECT t.*, c.name AS categories_name 
    FROM transactions t 
    LEFT JOIN categories c ON t.category_id=c.id  WHERE t.user_id=? 
    ORDER BY t.date DESC
');
$transStmt->execute([$uid]);
$trans = $transStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Transaksi - DompetKu</title>
  <style>
    :root {
      --primary-color: #17a2b8; /* Biru muda/Cyan untuk elemen utama */
      --primary-dark: #117a8b;
      --bg-light: #f8f9fa; /* Latar belakang sangat terang */
      --text-dark: #343a40; /* Teks gelap */
      --card-bg: #fff;
      --border-color: #e9ecef; /* Warna garis tipis */
      --income-color: #28a745; /* Hijau untuk Pemasukan */
      --expense-color: #dc3545; /* Merah untuk Pengeluaran */
    }

    /* Reset dan Global Styles */
    * {
      box-sizing: border-box;
    }

    body {
      /* Menggunakan font sistem yang bersih */
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background-color: var(--bg-light);
      color: var(--text-dark);
      max-width: 960px; /* Lebar konten yang lebih ideal */
      margin: 30px auto;
      padding: 0 16px;
      line-height: 1.6;
    }

    h2, h3 {
      color: var(--primary-dark);
      margin-top: 0;
    }

    a {
      color: var(--primary-color);
      text-decoration: none;
      transition: color 0.2s;
    }

    a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    /* Header */
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      margin-bottom: 25px;
      border-bottom: 3px solid var(--primary-color); /* Garis tegas di bawah header */
    }

    header h2 {
      font-weight: 700;
      margin: 0;
    }

    header div a {
      margin-left: 10px;
      padding: 5px 8px;
      border-radius: 4px;
    }

    /* Card */
    .card {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 20px;
      margin: 20px 0;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); /* Bayangan lembut */
    }

    .card h3 {
      padding-bottom: 10px;
      margin-bottom: 15px;
      border-bottom: 1px solid var(--border-color);
    }

    /* Form Styles */
    .form-row {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      align-items: center;
    }

    input,
    select {
      padding: 10px 12px;
      border: 1px solid #ced4da;
      border-radius: 6px;
      flex-grow: 1; /* Biarkan input mengambil ruang */
      font-size: 1em;
      min-width: 150px;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    input:focus, select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
      outline: none;
    }

    button {
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      background-color: var(--primary-color);
      color: white;
      font-weight: 600;
      transition: background-color 0.2s;
    }

    button[type="submit"] {
        min-width: 100px;
    }

    button:hover {
      background-color: var(--primary-dark);
    }

    /* Dashboard Specific Styles (index.php) */
    .card .form-row {
      justify-content: space-between;
    }

    .card .form-row > .card {
      flex: 1;
      min-width: 200px; /* Kartu tidak terlalu kecil */
      text-align: center;
      margin: 0;
      padding: 18px;
    }

    .card .form-row > .card strong {
      display: block;
      margin-bottom: 8px;
      font-size: 1em;
      color: #6c757d;
    }

    .small {
      font-size: 1.8em; /* Membuat jumlah lebih besar dan menonjol */
      font-weight: 700;
      color: var(--text-dark);
      line-height: 1.2;
    }

    /* Warna Pemasukan/Pengeluaran */
    .income {
      color: var(--income-color) !important;
    }

    .expense {
      color: var(--expense-color) !important;
    }

    /* Table Styles */
    .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th,
    .table td {
      padding: 12px;
      border-bottom: 1px solid var(--border-color);
      text-align: left;
    }

    .table th {
      background-color: var(--primary-color);
      color: white;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.9em;
    }

    .table tr:nth-child(even) {
      background-color: #f6f6f6; /* Zebra striping */
    }

    .table tr:hover {
      background-color: #e9ecef;
    }

    /* Catatan Transaksi */
    .small.note { 
      font-size: 0.9em;
      color: #6c757d;
    }

    /* Badge Styles */
    .badge {
      padding: 5px 10px;
      border-radius: 15px; /* Lebih membulat */
      font-size: 0.85em;
      font-weight: 600;
      display: inline-block;
      white-space: nowrap;
      color: white;
    }


    /* Footer */
    .footer {
      margin-top: 30px;
      padding: 10px 0;
      text-align: center;
      border-top: 1px solid var(--border-color);
      color: #6c757d;
      font-size: 0.85em;
    }

    /* Login/Register Specifics */
    /* Posisikan formulir login/register di tengah */
    .login-card {
      max-width: 400px;
      margin: 50px auto;
    }

    .login-card .form-row {
      display: block;
    }

    .login-card input {
      width: 100%;
      margin-bottom: 10px;
    }
    </style>
</head>
<body>
<header>
  <h2>Transaksi</h2>
  <div><a href="index.php">Dashboard</a> | <a href="logout.php">Keluar</a></div>
</header>

<div class="card">
  <h3>Tambah Transaksi</h3>
  <form action="add_transaction.php" method="post">
    <div class="form-row">
      <select name="type">
        <option value="income">Pemasukan</option>
        <option value="expense">Pengeluaran</option>
      </select>
      <input name="amount" placeholder="Jumlah" required>
      <input name="date" type="date" value="<?= date('Y-m-d') ?>">
      <select name="category_id"> <option value="">--Kategori (opsional)--</option>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= $c['type'] ?>)</option>
        <?php endforeach; ?>
      </select>
      <input name="note" placeholder="Catatan">
      <button type="submit">Tambah</button>
    </div>
  </form>
</div>

<div class="card">
  <h3>Riwayat</h3>
  <table class="table">
    <thead><tr><th>Tgl</th><th>Kategori</th><th>Catatan</th><th>Jumlah</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach($trans as $t): ?>
      <tr>
        <td><?= htmlspecialchars($t['date']) ?></td>
        <td><?= htmlspecialchars($t['categories_name'] ?: '—') ?></td>
        <td class="small note"><?= htmlspecialchars($t['note']) ?></td> <td>
          <?= ($t['type']=='income' ? '<span class="badge income">+ ' : '<span class="badge expense">- ') ?>
          Rp <?= number_format($t['amount'], 0, ',', '.') ?></span>
        </td>
        <td>
          <a href="edit_transaction.php?id=<?= $t['id'] ?>">Edit</a> |
          <a href="transactions.php?delete_id=<?= $t['id'] ?>" onclick="return confirm('Hapus transaksi ini?')">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>