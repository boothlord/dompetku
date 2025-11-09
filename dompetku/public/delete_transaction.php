<?php
require_once __DIR__ . '/../db.php';
require_login();
$uid = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$del = $pdo->prepare('DELETE FROM transactions WHERE id=? AND user_id=?');
$del->execute([$id,$uid]);
header('Location: transactions.php');