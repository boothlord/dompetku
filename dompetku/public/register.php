<?php
require_once __DIR__ . '/../db.php';


if (is_logged_in()) header('Location: index.php');


$err='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$pass = $_POST['password'];


if (!$name || !$email || !$pass) $err = 'Lengkapi semua field.';
else {
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
try {
$stmt->execute([$name,$email,$hash]);
header('Location: login.php?registered=1');
exit;
} catch (Exception $e) {
$err = 'Email sudah digunakan.';
}
}
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register - DompetKu</title>
<link rel="stylesheet" href="styles.css"></head><body>
<header><h2>DompetKu — Daftar</h2><a href="login.php">Masuk</a></header>
<div class="card">
<?php if($err):?><div class="small" style="color:red"><?=htmlspecialchars($err)?></div><?php endif; ?>
<form method="post">
<div class="form-row"><input name="name" placeholder="Nama"></div>
<div class="form-row"><input name="email" placeholder="Email"></div>
<div class="form-row"><input name="password" type="password" placeholder="Password"></div>
<div class="form-row"><button type="submit">Daftar</button></div>
</form>
</div>
</body></html>